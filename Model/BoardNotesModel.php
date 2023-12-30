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

    // Show single note
    public function boardNotesShowNote($note_id)
    {
        return $this->db->table(self::TABLE_notes)
            ->eq('id', $note_id)
            ->findAll();
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

    // Delete note
    public function boardNotesDeleteNote($project_id, $user_id, $note_id)
    {
        return $this->db->table(self::TABLE_notes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->remove();
    }

    // Delete note
    public function boardNotesDeleteAllDoneNotes($project_id, $user_id)
    {
        return $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', "0")
            ->remove();
    }

    // Add note
    public function boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category)
    {
        // Get last position number
        $lastPosition = $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->desc('position')
            ->findOneColumn('position');

        if (empty($lastPosition)) {
            $lastPosition = 0;
        }

        // Add 1 to position
        $lastPosition++;

        // Get current unixtime
        $t = time();

        // Define values
        $values = array(
            'project_id' => $project_id,
            'user_id' => $user_id,
            'position' => $lastPosition,
            'is_active' => $is_active,
            'title' => $title,
            'description' => $description,
            'date_created' => $t,
            'date_modified' => $t,
            'category' => $category,
        );

        return $this->db->table(self::TABLE_notes)
            ->insert($values);
    }

    // Transfer note
    public function boardNotesTransferNote($project_id, $user_id, $note_id, $target_project_id)
    {
        // Get current unixtime
        $t = time();
        $values = array('project_id' => $target_project_id, 'date_modified' => $t,);

        return $this->db->table(self::TABLE_notes)
            ->eq('id', $note_id)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->update($values);
    }

    // Update note
    public function boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category)
    {
        // Get current unixtime
        $t = time();
        $values = array('is_active' => $is_active, 'title' => $title, 'description' => $description, 'category' => $category, 'date_modified' => $t,);

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
                ->update($values);
            $num--;
        }
    }

    // Delete note ???
    public function boardNotesAnalytics($project_id, $user_id)
    {
        return $this->db->table(self::TABLE_notes)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->findAll();
    }
}
