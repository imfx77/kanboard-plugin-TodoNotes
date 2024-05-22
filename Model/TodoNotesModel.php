<?php

/**
 * Class TodoNotesModel
 * @package Kanboard\Plugin\BoardNotes\Model
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\BoardNotes\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\CategoryModel;

class TodoNotesModel extends Base
{
    private const TABLE_NOTES_ENTRIES           = 'todonotes_entries';
    private const TABLE_NOTES_CUSTOM_PROJECTS   = 'todonotes_custom_projects';
    private const TABLE_PROJECTS                = ProjectModel::TABLE;
    private const TABLE_COLUMNS                 = ColumnModel::TABLE;
    private const TABLE_SWIMLANES               = SwimlaneModel::TABLE;
    private const TABLE_CATEGORIES              = CategoryModel::TABLE;
    private const TABLE_ACCESS                  = ProjectUserRoleModel::TABLE;

    public const PROJECT_TYPE_NONE              = 0;
    public const PROJECT_TYPE_NATIVE            = 1;
    public const PROJECT_TYPE_CUSTOM_GLOBAL     = 2;
    public const PROJECT_TYPE_CUSTOM_PRIVATE    = 3;

    // Check unique note
    private function IsUniqueNote($project_id, $user_id, $note_id): bool
    {
        $result = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('id', $note_id)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->gte('is_active', 0) // -1 == deleted
            ->findAll();

        if (!$result) {
            return false;
        }
        if (count($result) != 1) {
            return false;
        }
        return true;
    }

    // Check global project
    public function IsCustomGlobalProject($project_id): bool
    {
        if ($project_id >= 0) { // custom projects have negative Ids
            return false;
        }
        $result = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->findOneColumn('owner_id');

        if ($result == 0) {
            return true;
        }
        return false;
    }

    // Get notes related to user and project
    public function GetProjectNotesForUser($project_id, $user_id, $doSortByStatus)
    {
        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->eq('user_id', $user_id);
        $result = $result->eq('project_id', $project_id);
        $result = $result->gte('is_active', 0); // -1 == deleted
        if ($doSortByStatus) {
            $result = $result->desc('is_active');
        }
        $result = $result->desc('position');
        $result = $result->findAll();

        return $result;
    }

    // Get all notes related to user
    public function GetAllNotesForUser($projectsAccess, $user_id, $doSortByStatus)
    {
        $projectsAccessList = array();
        $orderCaseClause = 'CASE project_id';
        $orderCaseNum = 1;
        foreach ($projectsAccess as $u) {
            $projectsAccessList[] = $u['project_id'];
            $orderCaseClause .= ' WHEN ' . $u['project_id'] . ' THEN ' . $orderCaseNum;
            $orderCaseNum++;
        }
        $orderCaseClause .= ' END';

        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->eq('user_id', $user_id);
        $result = $result->in('project_id', $projectsAccessList);
        $result = $result->gte('is_active', 0); // -1 == deleted
        $result = $result->orderBy($orderCaseClause); // order notes by projects as listed in $projectsAccess
        if ($doSortByStatus) {
            $result = $result->desc('is_active');
        }
        $result = $result->desc('position');
        $result = $result->findAll();

        return $result;
    }

    // Get notes related to user project report
    public function GetReportNotesForUser($project_id, $user_id, $doSortByStatus, $category)
    {
        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->eq('user_id', $user_id);
        $result = $result->eq('project_id', $project_id);
        if (!empty($category)) {
            $result = $result->eq('category', $category);
        }
        $result = $result->gte('is_active', 0); // -1 == deleted
        if ($doSortByStatus) {
            $result = $result->desc('is_active');
        }
        $result = $result->desc('position');
        $result = $result->findAll();

        return $result;
    }

    // Get stats related to user and project
    public function GetProjectStatsForUser($project_id, $user_id)
    {
        $statsData = $this->db->table(self::TABLE_NOTES_ENTRIES);
        if ($project_id != 0) {
            $statsData = $statsData->eq('project_id', $project_id);
        }
        $statsData = $statsData->eq('user_id', $user_id);
        $statsData = $statsData->gte('is_active', 0); // -1 == deleted
        $statsData = $statsData->findAll();

        $statDone = 0;
        $statOpen = 0;
        $statProgress = 0;
        $statTotal = 0;

        foreach ($statsData as $qq) {
            if ($qq['is_active'] == 0) {
                $statDone++;
            }
            if ($qq['is_active'] == 1) {
                $statOpen++;
            }
            if ($qq['is_active'] == 2) {
                $statProgress++;
            }
            $statTotal++;
        }

        return array(
            'statDone' => $statDone,
            'statOpen' => $statOpen,
            'statProgress' => $statProgress,
            'statTotal' => $statTotal,
        );
    }

    // Get regular project data by project_id
    public function GetRegularProjectById($project_id)
    {
        return $this->db->table(self::TABLE_PROJECTS)
            ->eq('id', $project_id)
            ->findOne();
    }

    // Get all project_id where user has regular access
    public function GetRegularProjectIds($user_id)
    {
        $projectIds = $this->db->table(self::TABLE_ACCESS)
            ->columns(self::TABLE_ACCESS . '.project_id', 'alias_projects_table.name AS project_name')
            ->eq('user_id', $user_id)
            ->left(self::TABLE_PROJECTS, 'alias_projects_table', 'id', self::TABLE_ACCESS, 'project_id')
            ->asc('project_id')
            ->findAll();
        foreach ($projectIds as &$projectId) {
            $projectId['is_custom'] = false;
            $projectId['is_global'] = false;
        }
        return $projectIds;
    }

    // Get all project_id where all users have custom Global access
    public function GetCustomGlobalProjectIds()
    {
        $projectIdsGlobal = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->columns('id AS project_id', 'project_name')
            ->eq('owner_id', 0)         // GLOBAL custom projects, managed by Admin only!
            ->asc('position')
            ->findAll();
        foreach ($projectIdsGlobal as &$projectId) {
            $projectId['project_id'] = -$projectId['project_id']; // custom project Ids are denoted as NEGATIVE values !!!
            $projectId['is_custom'] = true;
            $projectId['is_global'] = true;
        }
        return $projectIdsGlobal;
    }

    // Get all project_id where each user has custom Private access
    public function GetCustomPrivateProjectIds($user_id)
    {
        $projectIdsPrivate = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->columns('id AS project_id', 'project_name')
            ->eq('owner_id', $user_id)  // PRIVATE custom projects, managed by each user
            ->asc('position')
            ->findAll();
        foreach ($projectIdsPrivate as &$projectId) {
            $projectId['project_id'] = -$projectId['project_id']; // custom project Ids are denoted as NEGATIVE values !!!
            $projectId['is_custom'] = true;
            $projectId['is_global'] = false;
        }
        return $projectIdsPrivate;
    }

    // Get all project_id where user has custom access
    public function GetCustomProjectIds($user_id)
    {
        return array_merge($this->GetCustomGlobalProjectIds(), $this->GetCustomPrivateProjectIds($user_id));
    }

    // Get all project_id where user has regular or custom access
    public function GetAllProjectIds($user_id)
    {
        return array_merge($this->GetCustomProjectIds($user_id), $this->GetRegularProjectIds($user_id));
    }

    // Get projects count by type
    public function GetProjectsCountByType($user_id)
    {
        $numProjects = array();
        $numProjects[self::PROJECT_TYPE_NATIVE] = count($this->GetRegularProjectIds($user_id));
        $numProjects[self::PROJECT_TYPE_CUSTOM_GLOBAL] = count($this->GetCustomGlobalProjectIds());
        $numProjects[self::PROJECT_TYPE_CUSTOM_PRIVATE] = count($this->GetCustomPrivateProjectIds($user_id));
        return $numProjects;
    }

    // Get a list of categories for a project
    public function GetCategories($project_id)
    {
        return $this->db->table(self::TABLE_CATEGORIES)
            ->columns('id', 'name', 'project_id', 'color_id')
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    // Get a list of ALL categories
    public function GetAllCategories()
    {
        return $this->db->table(self::TABLE_CATEGORIES)
            ->columns('id', 'name', 'project_id', 'color_id')
            ->asc('name')
            ->findAll();
    }

    // Get a list of columns for a project
    public function GetColumns($project_id)
    {
        return $this->db->table(self::TABLE_COLUMNS)
            ->columns('id', 'title')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    // Get a list of swimlanes for a project
    public function GetSwimlanes($project_id)
    {
        return $this->db->table(self::TABLE_SWIMLANES)
            ->columns('id', 'name')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    // Add note
    public function AddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position number
        $lastPosition = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->gte('is_active', 0) // -1 == deleted
            ->desc('position')
            ->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 0;
        }

        // Add 1 to position
        $lastPosition++;

        // Get current unixtime
        $timestamp = time();

        // Define values
        $values = array(
            'project_id' => $project_id,
            'user_id' => $user_id,
            'position' => $lastPosition,
            'is_active' => $is_active,
            'title' => $title,
            'category' => $category,
            'description' => $description,
            'date_created' => $timestamp,
            'date_modified' => $timestamp,
        );

        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->insert($values);
    }

    // Delete note
    public function DeleteNote($project_id, $user_id, $note_id)
    {
        // purge previously marked as deleted notes
        $purged = $this->PurgeNotes($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => -1,
            'position' => -1,
            'date_modified' => $timestamp,
        );

        // mark note as deleted
        $deleted = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);

        return $purged && $deleted;
    }

    // Delete ALL done notes
    public function DeleteAllDoneNotes($project_id, $user_id)
    {
        // purge previously marked as deleted notes
        $purged = $this->PurgeNotes($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => -1,
            'position' => -1,
            'date_modified' => $timestamp,
        );

        // mark done notes as deleted
        $deleted = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', 0)
            ->update($values);

        return $purged && $deleted;
    }

    // Actually PURGE the notes marked as deleted
    private function PurgeNotes($project_id, $user_id)
    {
        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', "-1") // previously marked as deleted
            ->remove();
    }

    // Update note
    public function UpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category)
    {
        $is_unique = $this->IsUniqueNote($project_id, $user_id, $note_id);
        if (!$is_unique) {
            return false;
        }

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => $is_active,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'date_modified' => $timestamp,
        );

        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update note Status
    public function UpdateNoteStatus($project_id, $user_id, $note_id, $is_active)
    {
        $is_unique = $this->IsUniqueNote($project_id, $user_id, $note_id);
        if (!$is_unique) {
            return false;
        }

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => $is_active,
            'date_modified' => $timestamp,
        );

        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update notes positions
    public function UpdateNotesPositions($project_id, $user_id, $notesPositions)
    {
        $num = count($notesPositions);
        $timestamp = time();

        $result = true;
        // Loop through all positions
        foreach ($notesPositions as $row_id) {
            $values = array(
                'position' => $num,
                'date_modified' => $timestamp,
            );

            $result = $result && $this->db->table(self::TABLE_NOTES_ENTRIES)
                ->eq('project_id', $project_id)
                ->eq('user_id', $user_id)
                ->eq('id', $row_id)
                ->gte('is_active', 0) // -1 == deleted
                ->update($values);

            $num--;
        }

        return $result;
    }

    // Transfer note
    public function TransferNote($project_id, $user_id, $note_id, $target_project_id)
    {
        // Get last position number for target project
        $lastPosition = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $target_project_id)
            ->gte('is_active', 0) // -1 == deleted
            ->desc('position')
            ->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 0;
        }

        // Add 1 to position
        $lastPosition++;

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'project_id' => $target_project_id,
            'position' => $lastPosition,
            'date_modified' => $timestamp,
        );

        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Create Custom Note List
    public function CreateCustomNoteList($user_id, $custom_note_list_name)
    {
        // Get last position number for project and user
        $lastPosition = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('owner_id', $user_id)
            ->desc('position')
            ->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 0;
        }

        // Add 1 to position
        $lastPosition++;

        // Define values
        $values = array(
            'owner_id' => $user_id,
            'position' => $lastPosition,
            'project_name' => $custom_note_list_name,
        );

        return $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->insert($values);
    }

    // Rename Custom Note List
    public function RenameCustomNoteList($project_id, $custom_note_list_name)
    {
        $values = array(
            'project_name' => $custom_note_list_name,
        );

        return $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->update($values);
    }

    // Delete Custom Note List
    public function DeleteCustomNoteList($project_id)
    {
        // delete notes
        $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->remove();

        // delete custom list
        return $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->remove();
    }

    // Update Custom Note Lists Positions
    public function UpdateCustomNoteListsPositions($user_id, $customListsPositions)
    {
        $num = 1;

        $result = true;
        // Loop through all positions
        foreach ($customListsPositions as $row_id) {
            $values = array(
                'position' => $num,
            );

            $result = $result && $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
                ->eq('owner_id', $user_id)
                ->eq('id', -$row_id)
                ->update($values);

            $num++;
        }

        return $result;
    }

    // Emulate a global force refresh by updating a modified timestamp to the special `zero` entry
    public function EmulateForceRefresh()
    {
        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('position', 0)
            ->eq('is_active', -1)
            ->update(array('date_modified' => time()));
    }

    // Get last modified timestamp
    public function GetLastModifiedTimestamp($project_id, $user_id)
    {
        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->columns('date_modified');
        $result = $result->eq('user_id', $user_id);
        if ($project_id != 0) {
            $result = $result->eq('project_id', $project_id);
        }
        // including 'is_active' == -1 i.e. lately deleted notes
        $result = $result->desc('date_modified');
        $result = $result->findOne();

        $timestampNotes = 0;
        if ($result && count($result) == 1) {
            $timestampNotes = $result['date_modified'];
        }

        $forceRefresh = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->columns('date_modified')
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('position', 0)
            ->eq('is_active', -1)
            ->findOne();

        $timestampProjects = 0;
        if ($forceRefresh && count($forceRefresh) == 1) {
            $timestampProjects = $forceRefresh['date_modified'];
        }

        return array(
            'notes' => $timestampNotes,
            'projects' => $timestampProjects,
            'max' => max($timestampNotes, $timestampProjects),
        );
    }
}
