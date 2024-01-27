<?php

namespace Kanboard\Plugin\BoardNotes\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\CategoryModel;

class BoardNotesModel extends Base
{
    const TABLE_notes           = 'boardnotes';
    const TABLE_notes_custom    = 'boardnotes_cus';
    const TABLE_projects        = ProjectModel::TABLE;
    const TABLE_columns         = ColumnModel::TABLE;
    const TABLE_swimlanes       = SwimlaneModel::TABLE;
    const TABLE_categories      = CategoryModel::TABLE;
    const TABLE_access          = ProjectUserRoleModel::TABLE;

    // Check unique note
    private function boardNotesIsUniqueNote($project_id, $user_id, $note_id)
    {
        $result = $this->db->table(self::TABLE_notes);
        $result = $result->eq('id', $note_id);
        $result = $result->eq('user_id', $user_id);
        $result = $result->eq('project_id', $project_id);
        $result = $result->gte('is_active', "0"); // -1 == deleted
        $result = $result->findAll();

        if (!$result) return false;
        if (count($result) != 1) return false;
        return true;
    }

    // Show all notes related to project
    public function boardNotesShowProject($project_id, $user_id, $doSortByState)
    {
        $result = $this->db->table(self::TABLE_notes);
        $result = $result->eq('user_id', $user_id);
        if ($project_id > 0)
        {
            $result = $result->eq('project_id', $project_id);
        }
        $result = $result->desc('project_id');
        $result = $result->gte('is_active', "0"); // -1 == deleted
        if ($doSortByState)
        {
            $result = $result->desc('is_active');
        }
        $result = $result->desc('position');
        $result = $result->findAll();

        return $result;
    }

    // Show all notes
    public function boardNotesShowAll($projectsAccess, $user_id, $doSortByState)
    {
        $projectsAccessList = array();
        foreach ($projectsAccess as $u) $projectsAccessList[] = $u['project_id'];

        $result = $this->db->table(self::TABLE_notes);
        $result = $result->eq('user_id', $user_id);
        $result = $result->in('project_id', $projectsAccessList);
        $result = $result->desc('project_id');
        $result = $result->gte('is_active', "0"); // -1 == deleted
        if ($doSortByState)
        {
            $result = $result->desc('is_active');
        }
        $result = $result->desc('position');
        $result = $result->findAll();

        return $result;
    }

    // Show report
    public function boardNotesReport($project_id, $user_id, $doSortByState, $category)
    {
        $result = $this->db->table(self::TABLE_notes);
        $result = $result->eq('user_id', $user_id);
        $result = $result->eq('project_id', $project_id);
        if (!empty($category)) {
            $result = $result->eq('category', $category);
        }
        $result = $result->gte('is_active', "0"); // -1 == deleted
        if ($doSortByState)
        {
            $result = $result->desc('is_active');
        }
        $result = $result->desc('position');
        $result = $result->findAll();

        return $result;
    }

    // Get project data by project_id
    public function boardNotesGetProjectById($project_id)
    {
        return $this->db->table(self::TABLE_projects)
            ->eq('id', $project_id)
            ->findOne();
    }

    // Get all project_id where user has assigned access
    public function boardNotesGetProjectIds($user_id)
    {
        $projectIds = $this->db->table(self::TABLE_access)
            ->columns(self::TABLE_access.'.project_id', 'alias_projects_table.name AS project_name')
            ->eq('user_id', $user_id)
            ->left(self::TABLE_projects, 'alias_projects_table', 'id', self::TABLE_access, 'project_id')
            ->asc('project_name')
            ->findAll();
        foreach($projectIds as &$projectId){ $projectId['is_custom'] = false; }
        return $projectIds;
    }

    // Get all project_id where user has custom access
    public function boardNotesGetCustomProjectIds()
    {
        $projectIds = $this->db->table(self::TABLE_notes_custom)
            ->columns('project_id', 'project_name')
            ->findAll();
        foreach($projectIds as &$projectId){ $projectId['is_custom'] = true; }
        return $projectIds;
    }

    // Get all project_id where user has assigned or custom access
    public function boardNotesGetAllProjectIds($user_id)
    {
        $projectCustomAccess = $this->boardNotesGetCustomProjectIds();
        $projectAssignedAccess = $this->boardNotesGetProjectIds($user_id);
        $projectsAccess = array_merge($projectCustomAccess, $projectAssignedAccess);
        return $projectsAccess;
    }

    // Get a list of all categories in project
    public function boardNotesGetCategories($project_id)
    {
        return $this->db->table(self::TABLE_categories)
            ->columns('id', 'name', 'project_id', 'color_id')
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    // Get a list of ALL categories
    public function boardNotesGetAllCategories()
    {
        return $this->db->table(self::TABLE_categories)
            ->columns('id', 'name', 'project_id', 'color_id')
            ->asc('name')
            ->findAll();
    }

    // Get a list of all columns in project
    public function boardNotesGetColumns($project_id)
    {
        return $this->db->table(self::TABLE_columns)
            ->columns('id', 'title')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    // Get a list of all swimlanes in project
    public function boardNotesGetSwimlanes($project_id)
    {
        $swimlanes = $this->db->table(self::TABLE_swimlanes)
            ->columns('id', 'name')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();

        return $swimlanes;
    }

    // Get last modified timestamp
    public function boardNotesGetLastModifiedTimestamp($project_id, $user_id)
    {
        $result = $this->db->table(self::TABLE_notes);
        $result = $result->columns('date_modified');
        $result = $result->eq('user_id', $user_id);
        if ($project_id > 0)
        {
            $result = $result->eq('project_id', $project_id);
        }
        // including 'is_active' == -1 i.e. lately deleted notes
        $result = $result->desc('date_modified');
        $result = $result->findOne();

        return $result;
    }

    // Delete note
    public function boardNotesDeleteNote($project_id, $user_id, $note_id)
    {
        // purge previously marked as deleted notes
        $purged = $this->boardNotesPurgeNotes($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => -1,
            'date_modified' => $timestamp,
        );

        // mark note as deleted
        $deleted = $this->db->table(self::TABLE_notes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);

        return $purged && $deleted;
    }

    // Delete ALL done notes
    public function boardNotesDeleteAllDoneNotes($project_id, $user_id)
    {
        // purge previously marked as deleted notes
        $purged = $this->boardNotesPurgeNotes($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => -1,
            'date_modified' => $timestamp,
        );

        // mark done notes as deleted
        $deleted = $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', "0")
            ->update($values);

        return $purged && $deleted;
    }

    // Actually PURGE the notes marked as deleted
    private function boardNotesPurgeNotes($project_id, $user_id)
    {
        return $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', "-1") // previously marked as deleted
            ->remove();
    }

    // Add note
    public function boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position number
        $lastPosition = $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->gte('is_active', "0") // -1 == deleted
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
            'description' => $description,
            'date_created' => $timestamp,
            'date_modified' => $timestamp,
            'category' => $category,
        );

        return $this->db->table(self::TABLE_notes)
            ->insert($values);
    }

    // Transfer note
    public function boardNotesTransferNote($project_id, $user_id, $note_id, $target_project_id)
    {
        // Get current unixtime
        $timestamp = time();

        $values = array(
            'project_id' => $target_project_id,
            'date_modified' => $timestamp,
        );

        return $this->db->table(self::TABLE_notes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update note
    public function boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category)
    {
        $is_unique = $this->boardNotesIsUniqueNote($project_id, $user_id, $note_id);
        if (!$is_unique) return false;

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => $is_active,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'date_modified' => $timestamp,
        );

        return $this->db->table(self::TABLE_notes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update note positions
    public function boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes)
    {
        unset($num);
        unset($note_id);

        // Ser $num to nr of notes to max
        $num = $nrNotes;

        //  Explode all positions
        $note_ids = explode(',', $notePositions);

        // Loop through all positions
        foreach ($note_ids as $row) {
            $values = array('position' => $num);

            $this->db->table(self::TABLE_notes)
                ->eq('project_id', $project_id)
                ->eq('user_id', $user_id)
                ->eq('id', $row)
                ->gte('is_active', "0") // -1 == deleted
                ->update($values);
            $num--;
        }
    }

    // Get Notes for Analytics
    public function boardNotesAnalytics($project_id, $user_id)
    {
        return $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->gte('is_active', "0") // -1 == deleted
            ->findAll();
    }
}
