<?php

/**
 * Class TodoNotesModel
 * @package Kanboard\Plugin\TodoNotes\Model
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\CategoryModel;
use Kanboard\Plugin\TodoNotes\Plugin;

class TodoNotesModel extends Base
{
    public const TABLE_NOTES_CUSTOM_PROJECTS    = 'todonotes_custom_projects';
    public const TABLE_NOTES_ENTRIES            = 'todonotes_entries';
    public const TABLE_NOTES_ARCHIVE_ENTRIES    = 'todonotes_archive_entries';

    private const TABLE_PROJECTS                = ProjectModel::TABLE;
    private const TABLE_COLUMNS                 = ColumnModel::TABLE;
    private const TABLE_SWIMLANES               = SwimlaneModel::TABLE;
    private const TABLE_CATEGORIES              = CategoryModel::TABLE;
    private const TABLE_ACCESS                  = ProjectUserRoleModel::TABLE;

    public const PROJECT_TYPE_NONE              = 0;
    public const PROJECT_TYPE_NATIVE            = 1;
    public const PROJECT_TYPE_CUSTOM_GLOBAL     = 2;
    public const PROJECT_TYPE_CUSTOM_PRIVATE    = 3;

    private const REINDEX_USLEEP_INTERVAL       = 250000; // 0.25s

    // Check unique note
    public function IsUniqueNote($project_id, $user_id, $note_id): bool
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

    // Get the name of a project related to user
    public function GetProjectNameForUser($project_id, $user_id): string
    {
        $all_user_projects = $this->GetAllProjectIds($user_id);
        // fetch the project_name of the requested project
        foreach ($all_user_projects as $project) {
            if ($project_id == $project['project_id']) {
                return $project['project_name'];
            }
        }
        // if nothing found
        return t('TodoNotes__PROJECT_NOT_FOUND');
    }

    // Get note related to user and project
    public function GetProjectNoteForUser($project_id, $user_id, $note_id, $doFormatDates = true)
    {
        $note = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->eq('id', $note_id)
            ->gte('is_active', 0) // -1 == deleted
            ->findOne();

        if ($doFormatDates) {
            $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
            $note['notifications_alert_timestamp'] = $note['date_notified']; // keep the timestamp
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_notified', 'last_notified', 'date_restored'), $userDateTimeFormat);
        }

        return $note;
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

        $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
        foreach ($result as &$note) {
            $note['notifications_alert_timestamp'] = $note['date_notified']; // keep the timestamp
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_notified', 'last_notified', 'date_restored'), $userDateTimeFormat);
        }

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

        $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
        foreach ($result as &$note) {
            $note['notifications_alert_timestamp'] = $note['date_notified']; // keep the timestamp
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_notified', 'last_notified', 'date_restored'), $userDateTimeFormat);
        }

        return $result;
    }

    // Get archived note related to user and project
    public function GetArchivedProjectNoteForUser($project_id, $user_id, $archived_note_id, $doFormatDates = true)
    {
        $note = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->eq('id', $archived_note_id)
            ->gte('date_modified', 0) // -1 == deleted
            ->findOne();

        if ($doFormatDates) {
            $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_archived'), $userDateTimeFormat);
        }

        return $note;
    }

    // Get archived notes related to user and project
    public function GetArchivedProjectNotesForUser($project_id, $user_id)
    {
        $result = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES);
        $result = $result->eq('user_id', $user_id);
        $result = $result->eq('project_id', $project_id);
        $result = $result->gte('date_modified', 0); // -1 == deleted
        $result = $result->desc('date_archived');
        $result = $result->findAll();

        $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
        foreach ($result as &$note) {
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_archived'), $userDateTimeFormat);
        }

        return $result;
    }

    // Get all archived notes related to user
    public function GetAllArchivedNotesForUser($projectsAccess, $user_id)
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

        $result = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES);
        $result = $result->eq('user_id', $user_id);
        $result = $result->in('project_id', $projectsAccessList);
        $result = $result->gte('date_modified', 0); // -1 == deleted
        $result = $result->orderBy($orderCaseClause); // order notes by projects as listed in $projectsAccess
        $result = $result->desc('date_archived');
        $result = $result->findAll();

        $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
        foreach ($result as &$note) {
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_archived'), $userDateTimeFormat);
        }

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

    // Get the tab number of certain project
    public function GetTabForProject($project_id, $user_id): int
    {
        $count = 1;
        $all_user_projects = $this->GetAllProjectIds($user_id);
        // recover the tab_id of the requested project_id
        foreach ($all_user_projects as $project) {
            if ($project_id == $project['project_id']) {
                return $count;
            }
            $count++;
        }
        // if nothing found leave 0
        return 0;
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

    // Get last note position for project and user
    private function GetLastNotePosition($project_id, $user_id)
    {
        $lastPosition = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->gte('is_active', 0) // -1 == deleted
            ->desc('position')
            ->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 0;
        }

        return $lastPosition;
    }

    // Add note
    public function AddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position
        $lastPosition = $this->GetLastNotePosition($project_id, $user_id) + 1;

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
            'date_notified' => 0,
            'last_notified' => 0,
            'flags_notified' => 0,
            'date_restored' => 0
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
            return 0;
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

        return ($this->db->table(self::TABLE_NOTES_ENTRIES)
                        ->eq('id', $note_id)
                        ->eq('project_id', $project_id)
                        ->eq('user_id', $user_id)
                        ->update($values)) ? $timestamp : 0;
    }

    // Update note Status
    public function UpdateNoteStatus($project_id, $user_id, $note_id, $is_active)
    {
        $is_unique = $this->IsUniqueNote($project_id, $user_id, $note_id);
        if (!$is_unique) {
            return 0;
        }

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => $is_active,
            'date_modified' => $timestamp,
        );

        return ($this->db->table(self::TABLE_NOTES_ENTRIES)
                        ->eq('id', $note_id)
                        ->eq('project_id', $project_id)
                        ->eq('user_id', $user_id)
                        ->update($values)) ? $timestamp : 0;
    }

    // Update note Notifications Alert Time
    public function UpdateNoteNotificationsAlertTimeAndOptions($project_id, $user_id, $note_id, $notifications_alert_timestring, $notification_options_bitflags)
    {
        $is_unique = $this->IsUniqueNote($project_id, $user_id, $note_id);
        if (!$is_unique) {
            return 0;
        }

        $notifications_alert_timestamp = $this->dateParser->getTimestamp($notifications_alert_timestring);

        $values = array(
            'date_notified' => $notifications_alert_timestamp,
            'flags_notified' => $notification_options_bitflags,
        );

        return ($this->db->table(self::TABLE_NOTES_ENTRIES)
                        ->eq('id', $note_id)
                        ->eq('project_id', $project_id)
                        ->eq('user_id', $user_id)
                        ->update($values)) ? $notifications_alert_timestamp : 0;
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

        return $result ? $timestamp : 0;
    }

    // Transfer note
    public function TransferNote($project_id, $user_id, $note_id, $target_project_id)
    {
        // Get last position number for target project
        $lastPosition = $this->GetLastNotePosition($target_project_id, $user_id) + 1;

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

        // delete archived notes
        $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
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

    // Emulate a global force refresh by updating a modified/archived timestamp to the special `zero` entry
    public function EmulateForceRefresh()
    {
        // Get current unixtime
        $timestamp = time();

        $notes_entries = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('position', 0)
            ->eq('is_active', -1)
            ->update(array('date_modified' => $timestamp));

        $notes_archive_entries = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('date_modified', -1)
            ->update(array('date_archived' => $timestamp));

        return $notes_entries && $notes_archive_entries;
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

    public function ReadReindexProgress()
    {
        return file_get_contents(__DIR__ . '/../.cache/reindexProgress');
    }

    private function WriteReindexProgress($value)
    {
        file_put_contents(__DIR__ . '/../.cache/reindexProgress', $value);
        usleep(self::REINDEX_USLEEP_INTERVAL);
    }

    public function ReindexNotesAndLists($doWriteProgress): bool
    {
        $result = true;

        $schemaPrefix = '\Kanboard\Plugin\\' . Plugin::NAME . '\Schema\\';
        $lastVersion = constant($schemaPrefix . 'VERSION');

        $reindexSequence = array(
            'Reindex_AddAndUpdate_OldProjectIds',
            'Reindex_CreateAndInsert_NewShrunkCutomProjects',
            'Reindex_CreateAndInsert_NewShrunkEntries',
            'Reindex_CreateAndInsert_NewShrunkArchiveEntries',
            'Reindex_CrossUpdate_ReindexedProjectIds',
            'Reindex_Drop_OldProjectIds',
            'Reindex_Drop_OldTables',
            'Reindex_Rename_NewTables',
            'Reindex_RecreateIndices_CustomProjects',
            'Reindex_RecreateIndices_Entries',
            'Reindex_RecreateIndices_ArchiveEntries',
        );

        if ($doWriteProgress) {
            $this->WriteReindexProgress(''); // init empty
        }
        //------------------------------------------
        foreach ($reindexSequence as $reindexRoutine) {
            $functionName = $schemaPrefix . $reindexRoutine . '_' . $lastVersion;
            if (function_exists($functionName)) {
                try {
                    $this->db->startTransaction();
                    $this->db->getDriver()->disableForeignKeys();

                    if ($doWriteProgress) {
                        $this->WriteReindexProgress($reindexRoutine);
                    }
                    call_user_func($functionName, $this->db->getConnection());

                    $this->db->getDriver()->enableForeignKeys();
                    $this->db->closeTransaction();

                    $this->flash->success(t('TodoNotes__DASHBOARD_REINDEX_SUCCESS'));
                } catch (PDOException $e) {
                    $this->db->cancelTransaction();
                    $this->db->getDriver()->enableForeignKeys();

                    $this->flash->failure(t('TodoNotes__DASHBOARD_REINDEX_FAILURE') . ' => ' . $e->getMessage());
                    $result = false;
                }
            } else {
                $this->flash->failure(t('TodoNotes__DASHBOARD_REINDEX_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_REINDEX_METHOD_NOT_IMPLEMENTED') . ' ' . $reindexRoutine . '[v.' . $lastVersion . ']');
                $result = false;
                break;
            }
        }

        //------------------------------------------
        if ($doWriteProgress) {
            $this->WriteReindexProgress('#'); // complete mark
        }

        return $result;
    }

    // Move note to Archive
    public function MoveNoteToArchive($project_id, $user_id, $note_id)
    {
        // Get current unixtime
        $timestamp = time();

        $note = $this->GetProjectNoteForUser($project_id, $user_id, $note_id, false);
        $values = array(
            'project_id' => $note['project_id'],
            'user_id' => $note['user_id'],
            'title' => $note['title'],
            'description' => $note['description'],
            'category' => $note['category'],
            'date_created' => $note['date_created'],
            'date_modified' => $note['date_modified'],
            'date_archived' => $timestamp,
        );

        $is_archived = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->insert($values) ? true : false;

        if ($is_archived) {
            $this->DeleteNote($project_id, $user_id, $note_id);
        }
    }

    // Restore note from Archive
    public function RestoreNoteFromArchive($project_id, $user_id, $archived_note_id, $target_project_id)
    {
        // Get last position number for target project
        $lastPosition = $this->GetLastNotePosition($target_project_id, $user_id) + 1;

        // Get current unixtime
        $timestamp = time();

        $archived_note = $this->GetArchivedProjectNoteForUser($project_id, $user_id, $archived_note_id, false);
        $values = array(
            'project_id' => $target_project_id,
            'user_id' => $archived_note['user_id'],
            'position' => $lastPosition,
            'is_active' => 1, // open
            'title' => $archived_note['title'],
            'description' => $archived_note['description'],
            'category' => $archived_note['category'],
            'date_created' => $archived_note['date_created'],
            'date_modified' => $timestamp,
            'date_notified' => 0,
            'last_notified' => 0,
            'flags_notified' => 0,
            'date_restored' => $timestamp,
        );

        $is_restored = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->insert($values) ? true : false;

        if ($is_restored) {
            $this->DeleteNoteFromArchive($project_id, $user_id, $archived_note_id);
        }
    }

    // Delete archived note
    public function DeleteNoteFromArchive($project_id, $user_id, $archived_note_id)
    {
        // purge previously marked as deleted archive notes
        $purged = $this->PurgeNotesFromArchive($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'date_modified' => -1,
            'date_archived' => $timestamp,
        );

        // mark archive note as deleted
        $deleted = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->eq('id', $archived_note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);

        return $purged && $deleted;
    }

    // Actually PURGE the archived notes marked as deleted
    private function PurgeNotesFromArchive($project_id, $user_id)
    {
        return $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('date_modified', -1) // previously marked as deleted
            ->remove();
    }

    // Get last archived timestamp
    public function GetLastArchivedTimestamp($project_id, $user_id)
    {
        $result = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES);
        $result = $result->columns('date_archived');
        $result = $result->eq('user_id', $user_id);
        if ($project_id != 0) {
            $result = $result->eq('project_id', $project_id);
        }
        // including 'date_modified' == -1 i.e. lately deleted notes
        $result = $result->desc('date_archived');
        $result = $result->findOne();

        $timestampNotes = 0;
        if ($result && count($result) == 1) {
            $timestampNotes = $result['date_archived'];
        }

        $forceRefresh = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->columns('date_archived')
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('date_modified', -1)
            ->findOne();

        $timestampProjects = 0;
        if ($forceRefresh && count($forceRefresh) == 1) {
            $timestampProjects = $forceRefresh['date_archived'];
        }

        return array(
            'notes' => $timestampNotes,
            'projects' => $timestampProjects,
            'max' => max($timestampNotes, $timestampProjects),
        );
    }
}
