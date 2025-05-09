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
    public const TABLE_NOTES_CUSTOM_PROJECTS        = 'todonotes_custom_projects';
    public const TABLE_NOTES_SHARING_PERMISSIONS    = 'todonotes_sharing_permissions';
    public const TABLE_NOTES_ENTRIES                = 'todonotes_entries';
    public const TABLE_NOTES_ARCHIVE_ENTRIES        = 'todonotes_archive_entries';

    private const TABLE_PROJECTS                    = ProjectModel::TABLE;
    private const TABLE_COLUMNS                     = ColumnModel::TABLE;
    private const TABLE_SWIMLANES                   = SwimlaneModel::TABLE;
    private const TABLE_CATEGORIES                  = CategoryModel::TABLE;
    private const TABLE_ACCESS                      = ProjectUserRoleModel::TABLE;

    public const PROJECT_TYPE_NONE                  = 0;
    public const PROJECT_TYPE_NATIVE                = 1;
    public const PROJECT_TYPE_CUSTOM_GLOBAL         = 2;
    public const PROJECT_TYPE_CUSTOM_PRIVATE        = 3;
    public const PROJECT_TYPE_CUSTOM_SHARED         = 4;

    public const PROJECT_SHARING_PERMISSION_NONE    = 0;
    public const PROJECT_SHARING_PERMISSION_VIEW    = 1;
    public const PROJECT_SHARING_PERMISSION_EDIT    = 2;

    private const REINDEX_USLEEP_INTERVAL           = 250000; // 0.25s

    // cached projects access
    private bool $isCachedProjectsAccess = false;
    private array $cachedProjectsAccess = array();


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

    // Check regular project
    public function IsRegularProject($project_id): bool
    {
        if ($project_id <= 0) { // regular projects have positive Ids
            return false;
        }
        $result = $this->db->table(self::TABLE_PROJECTS)
            ->eq('id', $project_id)
            ->eq('is_active', 1)
            ->findOneColumn('id');

        if ($result == $project_id) {
            return true;
        }
        return false;
    }

    // Check custom project
    public function IsCustomProject($project_id): bool
    {
        if ($project_id >= 0) { // custom projects have negative Ids
            return false;
        }
        $result = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->findOneColumn('id');

        if ($result == -$project_id) {
            return true;
        }
        return false;
    }

    // Check custom global project
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

    // Check custom private project
    public function IsCustomPrivateProject($project_id): bool
    {
        if ($project_id >= 0) { // custom projects have negative Ids
            return false;
        }
        $result = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->findOneColumn('owner_id');

        if ($result != 0) {
            return true;
        }
        return false;
    }

    // Check custom project owner
    public function IsCustomProjectOwner($project_id, $user_id): bool
    {
        if ($project_id >= 0) { // custom projects have negative Ids
            return false;
        }
        $result = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->findOneColumn('owner_id');

        if ($result == $user_id) {
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
    public function GetProjectNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess)
    {
        $selectedUser = $this->EvaluateSharing($project_id, $user_id, $projectsAccess, $usersAccess);
        $paramsSoring = $this->EvaluateSorting($project_id, $user_id);

        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->eq('user_id', $selectedUser);
        $result = $result->eq('project_id', $project_id);
        $result = $result->gte('is_active', 0); // -1 == deleted
        if ($paramsSoring['asc']) {
            $result = $result->asc($paramsSoring['sort'] . '=0, ' . $paramsSoring['sort']);
        } else {
            $result = $result->desc($paramsSoring['sort']);
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
    public function GetAllNotesForUser($user_id, $projectsAccess)
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

        $selectedUser = $this->EvaluateSharing(0 /*overview*/, $user_id, $projectsAccess, array() /*no user sharing*/);
        $paramsSoring = $this->EvaluateSorting(0 /*overview*/, $user_id);

        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->eq('user_id', $selectedUser);
        $result = $result->in('project_id', $projectsAccessList);
        $result = $result->gte('is_active', 0); // -1 == deleted
        $result = $result->orderBy($orderCaseClause); // order notes by projects as listed in $projectsAccess
        if ($paramsSoring['asc']) {
            $result = $result->asc($paramsSoring['sort'] . '=0, ' . $paramsSoring['sort']);
        } else {
            $result = $result->desc($paramsSoring['sort']);
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
            $note['notifications_alert_timestamp'] = $note['date_notified']; // keep the timestamp
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_notified', 'last_notified', 'date_archived'), $userDateTimeFormat);
        }

        return $note;
    }

    // Get archived notes related to user and project
    public function GetArchivedProjectNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess)
    {
        $selectedUser = $this->EvaluateSharing($project_id, $user_id, $projectsAccess, $usersAccess);
        $paramsSoring = $this->EvaluateSorting($project_id, $user_id);

        $result = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES);
        $result = $result->eq('user_id', $selectedUser);
        $result = $result->eq('project_id', $project_id);
        $result = $result->gte('date_modified', 0); // -1 == deleted
        if ($paramsSoring['asc']) {
            $result = $result->asc($paramsSoring['sort'] . '=0, ' . $paramsSoring['sort']);
        } else {
            $result = $result->desc($paramsSoring['sort']);
        }
        $result = $result->desc('date_archived');
        $result = $result->findAll();

        $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
        foreach ($result as &$note) {
            $note['notifications_alert_timestamp'] = $note['date_notified']; // keep the timestamp
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_notified', 'last_notified', 'date_archived'), $userDateTimeFormat);
        }

        return $result;
    }

    // Get all archived notes related to user
    public function GetAllArchivedNotesForUser($user_id, $projectsAccess)
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

        $selectedUser = $this->EvaluateSharing(0 /*overview*/, $user_id, $projectsAccess, array() /*no user sharing*/);
        $paramsSoring = $this->EvaluateSorting(0 /*overview*/, $user_id);

        $result = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES);
        $result = $result->eq('user_id', $selectedUser);
        $result = $result->in('project_id', $projectsAccessList);
        $result = $result->gte('date_modified', 0); // -1 == deleted
        $result = $result->orderBy($orderCaseClause); // order notes by projects as listed in $projectsAccess
        if ($paramsSoring['asc']) {
            $result = $result->asc($paramsSoring['sort'] . '=0, ' . $paramsSoring['sort']);
        } else {
            $result = $result->desc($paramsSoring['sort']);
        }
        $result = $result->desc('date_archived');
        $result = $result->findAll();

        $userDateTimeFormat = $this->dateParser->getUserDateTimeFormat();
        foreach ($result as &$note) {
            $note['notifications_alert_timestamp'] = $note['date_notified']; // keep the timestamp
            $note = $this->dateParser->format($note, array('date_created', 'date_modified', 'date_notified', 'last_notified', 'date_archived'), $userDateTimeFormat);
        }

        return $result;
    }

    // Get notes related to user project report
    public function GetReportNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess, $category)
    {
        $selectedUser = $this->EvaluateSharing($project_id, $user_id, $projectsAccess, $usersAccess);
        $paramsSoring = $this->EvaluateSorting($project_id, $user_id);

        $result = $this->db->table(self::TABLE_NOTES_ENTRIES);
        $result = $result->eq('user_id', $selectedUser);
        $result = $result->eq('project_id', $project_id);
        if (!empty($category)) {
            $result = $result->eq('category', $category);
        }
        $result = $result->gte('is_active', 0); // -1 == deleted
        if ($paramsSoring['asc']) {
            $result = $result->asc($paramsSoring['sort'] . '=0, ' . $paramsSoring['sort']);
        } else {
            $result = $result->desc($paramsSoring['sort']);
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

    // Get all project_id where user has regular access
    private function GetRegularProjectIds($user_id)
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
            $projectId['is_owner'] = false;
        }
        return $projectIds;
    }

    // Get all project_id where all users have custom Global access
    private function GetCustomGlobalProjectIds()
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
            $projectId['is_owner'] = false;
        }
        return $projectIdsGlobal;
    }

    // Get all project_id where the user has custom Private access
    private function GetCustomPrivateProjectIds($user_id)
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
            $projectId['is_owner'] = true;
        }
        return $projectIdsPrivate;
    }

    // Get all project_id where the user has custom Shared access
    private function GetCustomSharedProjectIds($user_id)
    {
        $sharingPermissions = $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
            ->columns('project_id', 'permissions')
            ->eq('shared_to_user_id', $user_id)
            ->gt('permissions', self::PROJECT_SHARING_PERMISSION_NONE)
            ->findAll();

        $projectsSharedList = array();
        $projectsSharedMap = array();
        foreach ($sharingPermissions as $u) {
            if (!array_key_exists($u['project_id'], $projectsSharedMap)) {
                $projectsSharedList[] = -$u['project_id'];
                $projectsSharedMap[$u['project_id']] = $u['permissions'];
            }
        }

        $projectIdsShared = array();
        if (count($projectsSharedList) > 0) {
            $projectIdsShared = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
                ->columns('id AS project_id', 'project_name', 'owner_id')
                ->in('project_id', $projectsSharedList)
                ->neq('owner_id', 0)        // exclude GLOBAL custom projects
                ->neq('owner_id', $user_id) // exclude PRIVATE custom projects, managed by this user
                ->asc('owner_id')
                ->asc('position')
                ->findAll();
        }

        foreach ($projectIdsShared as &$projectId) {
            $projectId['project_id'] = -$projectId['project_id']; // custom project Ids are denoted as NEGATIVE values !!!
            $projectId['is_custom'] = true;
            $projectId['is_global'] = false;
            $projectId['is_owner'] = false;
            $projectId['permissions'] = $projectsSharedMap[$projectId['project_id']];
        }

        return $projectIdsShared;
    }

    // Get all project_id where user has custom access
    private function GetCustomProjectIds($user_id)
    {
        return array_merge(
            $this->GetCustomGlobalProjectIds(),
            $this->GetCustomPrivateProjectIds($user_id),
            $this->GetCustomSharedProjectIds($user_id)
        );
    }

    // Get all project_id where user has regular or custom access
    public function GetAllProjectIds($user_id)
    {
        // use cached, saving DB queries
        if ($this->isCachedProjectsAccess) {
            return $this->cachedProjectsAccess;
        }

        $projectsAccess = array_merge($this->GetCustomProjectIds($user_id), $this->GetRegularProjectIds($user_id));

        $tab_id = 1;
        foreach ($projectsAccess as &$projectId) {
            $projectId['tab_id'] = $tab_id;
            $tab_id++;
        }

        // cache
        $this->cachedProjectsAccess = $projectsAccess;
        $this->isCachedProjectsAccess = true;

        return $projectsAccess;
    }

    // Get projects count by type
    public function GetProjectsCountByType($user_id)
    {
        $numProjects = array();
        $numProjects[self::PROJECT_TYPE_NATIVE] = count($this->GetRegularProjectIds($user_id));
        $numProjects[self::PROJECT_TYPE_CUSTOM_GLOBAL] = count($this->GetCustomGlobalProjectIds());
        $numProjects[self::PROJECT_TYPE_CUSTOM_PRIVATE] = count($this->GetCustomPrivateProjectIds($user_id));
        $numProjects[self::PROJECT_TYPE_CUSTOM_SHARED] = count($this->GetCustomSharedProjectIds($user_id));
        return $numProjects;
    }

    // Get the tab number of certain project
    public function GetTabForProject($project_id, $user_id): int
    {
        $projectsAccess = $this->GetAllProjectIds($user_id);
        // recover the tab_id of the requested project_id
        foreach ($projectsAccess as $projectAccess) {
            if ($project_id == $projectAccess['project_id']) {
                return $projectAccess['tab_id'];
            }
        }
        // if nothing found leave 0
        return 0;
    }

    // Get all owners with view/edit permissions for given project and user
    public function GetSharingPermissions($project_id, $user_id)
    {
        $sharingPermissions = $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
            ->columns('shared_from_user_id AS user_id, permissions')
            ->eq('project_id', $project_id)
            ->eq('shared_to_user_id', $user_id)
            ->gt('permissions', self::PROJECT_SHARING_PERMISSION_NONE)
            ->asc('user_id')
            ->findAll();

        $usersAccess = array();
        foreach ($sharingPermissions as $sharing_permission) {
            $usersAccess[$sharing_permission['user_id']] = $sharing_permission['permissions'];
        }

        return $usersAccess;
    }

    // Get the sharing permission for given project and user by specific owner
    public function GetSharingPermissionsByOwner($project_id, $user_id, $owner_id): int
    {
        $sharingPermissions = $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
            ->columns('permissions')
            ->eq('project_id', $project_id)
            ->eq('shared_to_user_id', $user_id)
            ->eq('shared_from_user_id', $owner_id)
            ->gt('permissions', self::PROJECT_SHARING_PERMISSION_NONE)
            ->findAll();

        return (count($sharingPermissions) == 1) ? $sharingPermissions[0]['permissions'] : self::PROJECT_SHARING_PERMISSION_NONE;
    }

    // Get the granted sharing permissions for given project to anyone else
    public function GetGrantedSharingPermissions($project_id, $user_id)
    {
        $sharingPermissions = $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
            ->columns('shared_to_user_id AS user_id', 'permissions')
            ->eq('project_id', $project_id)
            ->eq('shared_from_user_id', $user_id)
            ->gt('permissions', self::PROJECT_SHARING_PERMISSION_NONE)
            ->asc('user_id')
            ->findAll();

        $grantedPermissions = array();
        foreach ($sharingPermissions as $sharing_permission) {
            $grantedPermissions[$sharing_permission['user_id']] = $sharing_permission['permissions'];
        }

        return $grantedPermissions;
    }

    // Set sharing permission for a project From user To user
    public function SetSharingPermission($project_id, $user_id, $shared_user_id, $shared_permission)
    {
        if ($shared_permission == self::PROJECT_SHARING_PERMISSION_NONE) {
            $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
                ->eq('project_id', $project_id)
                ->eq('shared_from_user_id', $user_id)
                ->eq('shared_to_user_id', $shared_user_id)
                ->remove();
        } else {
            $hasRecord = count(
                $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
                    ->eq('project_id', $project_id)
                    ->eq('shared_from_user_id', $user_id)
                    ->eq('shared_to_user_id', $shared_user_id)
                    ->findAll()
            ) > 0;

            if ($hasRecord) {
                $values = array(
                    'permissions' => $shared_permission,
                );
                $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
                    ->eq('project_id', $project_id)
                    ->eq('shared_from_user_id', $user_id)
                    ->eq('shared_to_user_id', $shared_user_id)
                    ->update($values);
            } else {
                $values = array(
                    'project_id' => $project_id,
                    'shared_from_user_id' => $user_id,
                    'shared_to_user_id' => $shared_user_id,
                    'permissions' => $shared_permission,
                );
                $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
                    ->insert($values);
            }
        }
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
    public function AddNote($project_id, $user_id, $requested_user_id, $is_active, $title, $description, $category)
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
            'date_restored' => 0,
            'last_change_user_id' => $requested_user_id,
        );

        return $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->insert($values);
    }

    // Delete note
    public function DeleteNote($project_id, $user_id, $requested_user_id, $note_id)
    {
        // purge previously marked as deleted notes
        $purged = $this->PurgeNotes($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => -1,
            'position' => -1,
            'date_modified' => $timestamp,
            'last_change_user_id' => $requested_user_id,
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
    public function DeleteAllDoneNotes($project_id, $user_id, $requested_user_id)
    {
        // purge previously marked as deleted notes
        $purged = $this->PurgeNotes($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'is_active' => -1,
            'position' => -1,
            'date_modified' => $timestamp,
            'last_change_user_id' => $requested_user_id,
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
    public function UpdateNote($project_id, $user_id, $requested_user_id, $note_id, $is_active, $title, $description, $category)
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
            'last_change_user_id' => $requested_user_id,
        );

        return ($this->db->table(self::TABLE_NOTES_ENTRIES)
                        ->eq('id', $note_id)
                        ->eq('project_id', $project_id)
                        ->eq('user_id', $user_id)
                        ->update($values)) ? $timestamp : 0;
    }

    // Update note Status
    public function UpdateNoteStatus($project_id, $user_id, $requested_user_id, $note_id, $is_active)
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
            'last_change_user_id' => $requested_user_id,
        );

        return ($this->db->table(self::TABLE_NOTES_ENTRIES)
                        ->eq('id', $note_id)
                        ->eq('project_id', $project_id)
                        ->eq('user_id', $user_id)
                        ->update($values)) ? $timestamp : 0;
    }

    // Update note Notifications Alert Time
    public function UpdateNoteNotificationsAlertTimeAndOptions($project_id, $user_id, $requested_user_id, $note_id, $notifications_alert_timestring, $notification_options_bitflags)
    {
        $is_unique = $this->IsUniqueNote($project_id, $user_id, $note_id);
        if (!$is_unique) {
            return 0;
        }

        $notifications_alert_timestamp = $this->dateParser->getTimestamp($notifications_alert_timestring);

        $values = array(
            'date_notified' => $notifications_alert_timestamp,
            'flags_notified' => $notification_options_bitflags
        );

        return ($this->db->table(self::TABLE_NOTES_ENTRIES)
                        ->eq('id', $note_id)
                        ->eq('project_id', $project_id)
                        ->eq('user_id', $user_id)
                        ->update($values)) ? $notifications_alert_timestamp : 0;
    }

    // Update notes positions
    public function UpdateNotesPositions($project_id, $user_id, $requested_user_id, $notesPositions)
    {
        $num = count($notesPositions);
        $timestamp = time();

        $result = true;
        // Loop through all positions
        foreach ($notesPositions as $row_id) {
            $values = array(
                'position' => $num,
                'date_modified' => $timestamp,
                'last_change_user_id' => $requested_user_id,
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
        $validation = array();

        // delete notes
        $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->remove();

        // delete archived notes
        $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->eq('project_id', $project_id)
            ->remove();

        // delete sharing permissions
        $validation['permissions'] = $this->db->table(self::TABLE_NOTES_SHARING_PERMISSIONS)
            ->eq('project_id', $project_id)
            ->remove();

        // delete custom list
        $validation['projects'] = $this->db->table(self::TABLE_NOTES_CUSTOM_PROJECTS)
            ->eq('id', -$project_id)
            ->remove();

        return $validation;
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
    public function EmulateForceRefresh($type = 'projects')
    {
        // Get current unixtime
        $timestamp = time();

        $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('position', 0)
            ->eq('is_active', -1)
            ->update(array('category' => $type, 'date_modified' => $timestamp));

        $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('date_modified', -1)
            ->update(array('category' => $type, 'date_archived' => $timestamp));

        return $timestamp;
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
            ->columns('date_modified', 'category')
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('position', 0)
            ->eq('is_active', -1)
            ->findOne();

        $timestampProjects = 0;
        $timestampPermissions = 0;
        if ($forceRefresh && count($forceRefresh) == 2) {
            if ($forceRefresh['category'] == "projects") {
                $timestampProjects = $forceRefresh['date_modified'];
            }
            if ($forceRefresh['category'] == 'permissions') {
                $timestampPermissions = $forceRefresh['date_modified'];
            }
        }

        return array(
            'notes' => $timestampNotes,
            'projects' => $timestampProjects,
            'permissions' => $timestampPermissions,
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
            'Reindex_Rename_OldTables',
            'Reindex_AddAndUpdate_OldProjectIds',
            'Reindex_CreateAndInsert_NewShrunkCustomProjects',
            'Reindex_CrossUpdate_ReindexedProjectIds',
            'Reindex_CreateAndInsert_NewShrunkSharingPermissions',
            'Reindex_CreateAndInsert_NewShrunkEntries',
            'Reindex_CreateAndInsert_NewShrunkArchiveEntries',
            'Reindex_Drop_OldTables',
            'Reindex_RecreateIndices_CustomProjects',
            'Reindex_RecreateIndices_SharingPermissions',
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

                    //error_log($reindexRoutine);
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
    public function MoveNoteToArchive($project_id, $user_id, $requested_user_id, $note_id)
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
            'date_notified' => $note['date_notified'],
            'last_notified' => $note['last_notified'],
            'date_archived' => $timestamp,
            'last_change_user_id' => $requested_user_id,
        );

        $is_archived = $this->db->table(self::TABLE_NOTES_ARCHIVE_ENTRIES)
            ->insert($values) ? true : false;

        if ($is_archived) {
            $this->DeleteNote($project_id, $user_id, $requested_user_id, $note_id);
        }
    }

    // Move ALL done notes to Archive
    public function MoveAllDoneNotesToArchive($project_id, $user_id, $requested_user_id)
    {
        // select done notes in default order
        $done_notes = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->eq('project_id', $project_id)
            ->eq('user_id', $user_id)
            ->eq('is_active', 0)
            ->desc('position')
            ->findAll();

        foreach ($done_notes as $note) {
            $this->MoveNoteToArchive($project_id, $user_id, $requested_user_id, $note['id']);
        }
    }

    // Restore note from Archive
    public function RestoreNoteFromArchive($project_id, $user_id, $requested_user_id, $archived_note_id, $target_project_id)
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
            'date_modified' => $archived_note['date_modified'],
            'date_notified' => $archived_note['date_notified'],
            'last_notified' => $archived_note['last_notified'],
            'flags_notified' => 0,
            'date_restored' => $timestamp,
            'last_change_user_id' => $requested_user_id,
        );

        $is_restored = $this->db->table(self::TABLE_NOTES_ENTRIES)
            ->insert($values) ? true : false;

        if ($is_restored) {
            $this->DeleteNoteFromArchive($project_id, $user_id, $requested_user_id, $archived_note_id);
        }

        $this->EmulateForceRefresh();
    }

    // Delete archived note
    public function DeleteNoteFromArchive($project_id, $user_id, $requested_user_id, $archived_note_id)
    {
        // purge previously marked as deleted archive notes
        $purged = $this->PurgeNotesFromArchive($project_id, $user_id);

        // Get current unixtime
        $timestamp = time();

        $values = array(
            'date_modified' => -1,
            'date_archived' => $timestamp,
            'last_change_user_id' => $requested_user_id,
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
            ->columns('date_archived', 'category')
            ->eq('project_id', 0)
            ->eq('user_id', 0)
            ->eq('date_modified', -1)
            ->findOne();

        $timestampProjects = 0;
        $timestampPermissions = 0;
        if ($forceRefresh && count($forceRefresh) == 2) {
            if ($forceRefresh['category'] == 'projects') {
                $timestampProjects = $forceRefresh['date_archived'];
            }
            if ($forceRefresh['category'] == 'permissions') {
                $timestampPermissions = $forceRefresh['date_archived'];
            }
        }

        return array(
            'notes' => $timestampNotes,
            'projects' => $timestampProjects,
            'permissions' => $timestampPermissions,
            'max' => max($timestampNotes, $timestampProjects),
        );
    }

    public function VerifySharingPermissions($project_id, $user_id, $permissions = self::PROJECT_SHARING_PERMISSION_EDIT): int
    {
        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $userGroup = $todonotesSettingsHelper->GetGroupSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_USER
        );
        $selectedUser = (count($userGroup) == 1) ? $userGroup[0] : 0;

        // when accessing project for editing with curren user
        if ($selectedUser == $user_id) {
            return ($this->IsRegularProject($project_id)
                || $this->IsCustomGlobalProject($project_id)
                || $this->IsCustomProjectOwner($project_id, $user_id)
            )
                ? $selectedUser
                : 0;
        }

        // check current user for the required permission with selected user and shared project contents
        return ($this->GetSharingPermissionsByOwner($project_id, $user_id, $selectedUser) >= $permissions)
            ? $selectedUser
            : 0;
    }

    public function EvaluateSharingForAllUserProjects($user_id)
    {
        $projectsAccess = $this->GetAllProjectIds($user_id);
        foreach ($projectsAccess as $projectAccess) {
            $project_id = $projectAccess['project_id'];
            $usersAccess = $this->GetSharingPermissions($project_id, $user_id);
            $this->EvaluateSharing($project_id, $user_id, $projectsAccess, $usersAccess, true);
        }

        return $projectsAccess;
    }

    private function EvaluateSharing($project_id, $user_id, $projectsAccess, $usersAccess, $to_session_only = false): int
    {
        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $todonotesSettingsHelper->SyncSettingsToSession($user_id, $project_id);

        $userGroup = $todonotesSettingsHelper->GetGroupSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_USER
        );
        $selectedUser = (count($userGroup) == 1) ? $userGroup[0] : 0;

        // check accessibility for selected user
        $isSelectedUserAccessible = array_key_exists($selectedUser, $usersAccess) && $usersAccess[$selectedUser] > self::PROJECT_SHARING_PERMISSION_NONE;

        // force set selected user
        if (!$isSelectedUserAccessible) {
            // search requested project VS access
            foreach ($projectsAccess as $projectAccess) {
                if ($project_id == $projectAccess['project_id']) {
                    break;
                }
            }

            // if requested project is a shared one, switch to its owner user
            if ($projectAccess['project_id'] == $project_id
                && $projectAccess['is_custom']
                && !$projectAccess['is_global']
                && !$projectAccess['is_owner']
            ) {
                $selectedUser = $projectAccess['owner_id'];
            } else {
                $selectedUser = $user_id;
            }

            $todonotesSettingsHelper->ToggleSettings(
                $user_id,
                $project_id,
                $todonotesSettingsHelper::SETTINGS_GROUP_USER,
                $selectedUser,
                true /*settings_exclusive*/,
                $to_session_only
            );
        }

        return $selectedUser;
    }

    private function EvaluateSorting($project_id, $user_id)
    {
        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $todonotesSettingsHelper->SyncSettingsToSession($user_id, $project_id);

        $isArchive = $todonotesSettingsHelper->GetToggleableSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
            $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
        );
        $sortGroup = $todonotesSettingsHelper->GetGroupSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_SORT
        );
        $sortKey = (count($sortGroup) == 1) ? $sortGroup[0] : $todonotesSettingsHelper::SETTINGS_SORT_MANUAL;

        // force sorting
        if ($isArchive) {
            if ($sortKey == $todonotesSettingsHelper::SETTINGS_SORT_MANUAL
                || $sortKey == $todonotesSettingsHelper::SETTINGS_SORT_STATUS
                || $sortKey == $todonotesSettingsHelper::SETTINGS_SORT_DATE_RESTORED
            ) {
                $todonotesSettingsHelper->ToggleSettings(
                    $user_id,
                    $project_id,
                    $todonotesSettingsHelper::SETTINGS_GROUP_SORT,
                    $todonotesSettingsHelper::SETTINGS_SORT_DATE_ARCHIVED,
                    true /*settings_exclusive*/
                );
                $sortKey = $todonotesSettingsHelper::SETTINGS_SORT_DATE_ARCHIVED;
            }
        } else {
            if ($sortKey == $todonotesSettingsHelper::SETTINGS_SORT_DATE_ARCHIVED) {
                $todonotesSettingsHelper->ToggleSettings(
                    $user_id,
                    $project_id,
                    $todonotesSettingsHelper::SETTINGS_GROUP_SORT,
                    $todonotesSettingsHelper::SETTINGS_SORT_MANUAL,
                    true /*settings_exclusive*/
                );
                $sortKey = $todonotesSettingsHelper::SETTINGS_SORT_MANUAL;
            }
        }

        // evaluate the sorting parameters
        $sortField = 'date_created';
        $sortAscending = false;
        switch ($sortKey) {
            case $todonotesSettingsHelper::SETTINGS_SORT_STATUS:
                $sortField = 'is_active';
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_DATE_CREATED:
                $sortField = 'date_created';
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_DATE_MODIFIED:
                $sortField = 'date_modified';
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_DATE_NOTIFIED:
                $sortField = 'date_notified';
                $sortAscending = true;
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_DATE_LAST_NOTIFIED:
                $sortField = 'last_notified';
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_DATE_ARCHIVED:
                $sortField = 'date_archived';
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_DATE_RESTORED:
                $sortField = 'date_restored';
                break;
            case $todonotesSettingsHelper::SETTINGS_SORT_MANUAL:
            default:
                $sortField = 'position';
                break;
        }

        return array(
            'sort' => $sortField,
            'asc' => $sortAscending,
        );
    }
}
