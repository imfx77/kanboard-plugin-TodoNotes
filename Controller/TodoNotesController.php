<?php

/**
 * Class TodoNotesController
 * @package Kanboard\Plugin\TodoNotes\Controller
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Controller;

use Kanboard\Controller\BaseController;

class TodoNotesController extends BaseController
{
    private function ResolveUserId()
    {
        $user_id = ''; // init empty string
        $use_cached = $this->request->getStringParam('use_cached');

        $cache = (isset($_SESSION['_TodoNotes_Cache_'])) ? $_SESSION['_TodoNotes_Cache_'] : array();

        // use cached
        if (!empty($use_cached) && array_key_exists('user_id', $cache)) {
            $user_id = $cache['user_id'];
        }

        // try get param from URL
        if (empty($user_id)) {
            $user_id = $this->request->getStringParam('user_id');
        }

        // as last resort get the current user
        if (empty($user_id)) {
            $user_id = $this->getUser()['id'];
        }

        $cache['user_id'] = $user_id;
        $_SESSION['_TodoNotes_Cache_'] = $cache;

        return $user_id;
    }

    private function ResolveProject($user_id)
    {
        $project_id = intval($this->request->getStringParam('project_custom_id'));

        if (empty($project_id)) {
            $project_id = $this->request->getIntegerParam('project_id');
        }
        $projectsAccess = $this->todoNotesModel->GetAllProjectIds($user_id);

        // search requested project VS access
        foreach ($projectsAccess as $projectAccess) {
            if ($project_id == $projectAccess['project_id']) {
                break;
            }
        }

        // if we didn't find the requested project, switch by default to the first one (i.e. Global Notes custom list)
        if ($projectAccess['project_id'] != $project_id) {
            $projectAccess = $projectsAccess[0];
        }

        // assemble project
        return array("id" => $project_id, 'tab_id' => $projectAccess['tab_id'], 'name' => $projectAccess['project_name']);
    }

    private function GetUserIconAndName($user_id): string
    {
        $user_details = $this->userModel->getById($user_id);
        $icon = $this->helper->avatar->small(
            $user_details['id'],
            $user_details['username'],
            $user_details['name'],
            $user_details['email'],
            $user_details['avatar_path'],
            'avatar-inline'
        );
        $name = $this->helper->text->e($user_details['name'] ?: $user_details['username']);
        return $icon . $name;
    }

    private function ShowProjectWithRefresh($is_refresh)
    {
        $user = $this->getUser();
        $user_id = $this->ResolveUserId();

        if ($is_refresh) {
            $project = $this->ResolveProject($user_id);
        } else {
            $project = $this->getProject();
            $project['tab_id'] = $this->todoNotesModel->GetTabForProject($project['id'], $user_id);
        }
        $project_id = $project['id'];

        $projectsAccess = $this->todoNotesModel->GetAllProjectIds($user_id);
        $usersAccess = $this->todoNotesModel->GetSharingPermissions($project_id, $user_id);

        if ($projectsAccess[$project['tab_id'] - 1]['is_custom']) {
            $categories = $this->todoNotesModel->GetAllCategories();
        } else {
            $categories = $this->todoNotesModel->GetCategories($project_id);
        }
        $columns = $this->todoNotesModel->GetColumns($project_id);
        $swimlanes = $this->todoNotesModel->GetSwimlanes($project_id);

        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $doShowArchive = $todonotesSettingsHelper->GetToggleableSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
            $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
        );

        if ($project_id == 0) {
            $data = $doShowArchive
                ? $this->todoNotesModel->GetAllArchivedNotesForUser($user_id, $projectsAccess)
                : $this->todoNotesModel->GetAllNotesForUser($user_id, $projectsAccess);
        } else {
            $data = $doShowArchive
                ? $this->todoNotesModel->GetArchivedProjectNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess)
                : $this->todoNotesModel->GetProjectNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess);
        }

        return $this->response->html($this->helper->layout->app('TodoNotes:project/data', array(
            'title' => $project['name'], // rather keep the project name as title
            'projectsAccess' => $projectsAccess,
            'project' => $project,
            'project_id' => $project_id,
            'usersAccess' => $usersAccess,
            'user' => $user,
            'user_id' => $user_id,
            'user_datetime_format' => $this->dateParser->getUserDateTimeFormat(),

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,

            'is_refresh' => $is_refresh,
            'is_dashboard_view' => 0,
        )));
    }

    public function ShowProject()
    {
        $this->ShowProjectWithRefresh(false);
    }

    public function RefreshProject()
    {
        $this->ShowProjectWithRefresh(true);
    }

    public function ShowDashboard()
    {
        $user = $this->getUser();
        $user_id = $this->ResolveUserId();

        $tab_id = $this->request->getStringParam('tab_id');
        if (empty($tab_id)) {
            $tab_id = 0;
        } else {
            $tab_id = intval($tab_id);
        }

        $projectsAccess = $this->todoNotesModel->EvaluateSharingForAllUserProjects($user_id);
        $project_id = ($tab_id > 0) ? $projectsAccess[$tab_id - 1]['project_id'] : 0;
        $usersAccess = $this->todoNotesModel->GetSharingPermissions($project_id, $user_id);

        if ($tab_id > 0 && !$projectsAccess[$tab_id - 1]['is_custom']) {
            $categories = $this->todoNotesModel->GetCategories($project_id);
            $columns = $this->todoNotesModel->GetColumns($project_id);
            $swimlanes = $this->todoNotesModel->GetSwimlanes($project_id);
        } else {
            $categories = $this->todoNotesModel->GetAllCategories();
            $columns = array();
            $swimlanes = array();
        }

        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $doShowArchive = $todonotesSettingsHelper->GetToggleableSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
            $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
        );

        if ($project_id == 0) {
            $data = $doShowArchive
                ? $this->todoNotesModel->GetAllArchivedNotesForUser($user_id, $projectsAccess)
                : $this->todoNotesModel->GetAllNotesForUser($user_id, $projectsAccess);
        } else {
            $data = $doShowArchive
                ? $this->todoNotesModel->GetArchivedProjectNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess)
                : $this->todoNotesModel->GetProjectNotesForUser($project_id, $user_id, $projectsAccess, $usersAccess);
        }

        return $this->response->html($this->helper->layout->dashboard('TodoNotes:dashboard/data', array(
            'title' => t('TodoNotes__DASHBOARD_TITLE', $this->helper->user->getFullname($user)),
            'projectsAccess' => $projectsAccess,
            'usersAccess' => $usersAccess,
            'user' => $user,
            'user_id' => $user_id,
            'user_datetime_format' => $this->dateParser->getUserDateTimeFormat(),

            'tab_id' => $tab_id,
            'note_id' => $this->request->getStringParam('note_id') ?: '0',
            'is_sharing_view' => 0,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,
        )));
    }

    public function ShowDashboardSharing()
    {
        $user = $this->getUser();
        $user_id = $this->ResolveUserId();

        $tab_id = $this->request->getStringParam('tab_id');
        if (empty($tab_id)) {
            $tab_id = 0;
        } else {
            $tab_id = intval($tab_id);
        }

        // NO sharing for overview mode
        if ($tab_id == 0) {
            return $this->response->redirect($this->helper->url->to('TodoNotesController', 'ShowDashboard', array(
                'plugin' => 'TodoNotes',
                'user_id' => $user_id,
                'tab_id' => $tab_id,
            )));
        }

        $projectsAccess = $this->todoNotesModel->EvaluateSharingForAllUserProjects($user_id);
        $project_id = $projectsAccess[$tab_id - 1]['project_id'];
        $usersAccess = $this->todoNotesModel->GetSharingPermissions($project_id, $user_id);

        $data = $this->todoNotesModel->GetGrantedSharingPermissions($project_id, $user_id);

        return $this->response->html($this->helper->layout->dashboard('TodoNotes:dashboard/data', array(
            'title' => t('TodoNotes__DASHBOARD_TITLE', $this->helper->user->getFullname($user)),
            'projectsAccess' => $projectsAccess,
            'usersAccess' => $usersAccess,
            'user' => $user,
            'user_id' => $user_id,

            'tab_id' => $tab_id,
            'is_sharing_view' => 1,

            'data' => $data,
        )));
    }

    public function RefreshTabs()
    {
        $user_id = $this->ResolveUserId();
        $projectsAccess = $this->todoNotesModel->EvaluateSharingForAllUserProjects($user_id);

        return $this->response->html($this->helper->layout->app('TodoNotes:dashboard/tabs', array(
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,
        )));
    }

    public function AddNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        $is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $category = $this->request->getStringParam('category');

        return $this->todoNotesModel->AddNote($project_id, $selectedUser, $user_id, $is_active, $title, $description, $category);
    }

    public function DeleteNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        $note_id = $this->request->getStringParam('note_id');

        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $doShowArchive = $todonotesSettingsHelper->GetToggleableSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
            $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
        );

        return (!$doShowArchive) ? $this->todoNotesModel->DeleteNote($project_id, $selectedUser, $user_id, $note_id) : null;
    }

    public function DeleteAllDoneNotes()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        return $this->todoNotesModel->DeleteAllDoneNotes($project_id, $selectedUser, $user_id);
    }

    public function UpdateNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            echo(json_encode(array('timestamp' => -1)));
            return -1;
        }

        $note_id = $this->request->getStringParam('note_id');

        $is_active = $this->request->getStringParam('is_active');
        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $category = $this->request->getStringParam('category');

        $timestamp = $this->todoNotesModel->UpdateNote($project_id, $selectedUser, $user_id, $note_id, $is_active, $title, $description, $category);
        echo(json_encode(array(
            'timestamp' => $timestamp,
            'timestring' => date($this->dateParser->getUserDateTimeFormat(), $timestamp),
            'userinfo' => $this->GetUserIconAndName($user_id),
        )));
        return $timestamp;
    }

    public function UpdateNoteStatus()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            echo(json_encode(array('timestamp' => -1)));
            return -1;
        }

        $note_id = $this->request->getStringParam('note_id');

        $is_active = $this->request->getStringParam('is_active');

        $timestamp = $this->todoNotesModel->UpdateNoteStatus($project_id, $selectedUser, $user_id, $note_id, $is_active);
        echo(json_encode(array(
            'timestamp' => $timestamp,
            'timestring' => date($this->dateParser->getUserDateTimeFormat(), $timestamp),
            'userinfo' => $this->GetUserIconAndName($user_id),
        )));
        return $timestamp;
    }

    public function UpdateNoteNotificationsAlertTimeAndOptions()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            echo(json_encode(array('timestamp' => -1)));
            return -1;
        }

        $note_id = $this->request->getStringParam('note_id');

        $notifications_alert_timestring = $this->request->getStringParam('notifications_alert_timestring');
        $notification_options_bitflags = intval($this->request->getStringParam('notification_options_bitflags'));

        $notifications_alert_timestamp = $this->todoNotesModel->UpdateNoteNotificationsAlertTimeAndOptions($project_id, $selectedUser, $user_id, $note_id, $notifications_alert_timestring, $notification_options_bitflags);
        echo(json_encode(array(
            'timestamp' => $notifications_alert_timestamp,
            'timestring' => ($notifications_alert_timestamp > 0) ? date($this->dateParser->getUserDateTimeFormat(), $notifications_alert_timestamp) : '',
            'options_bitflags' => $notification_options_bitflags,
        )));
        return $notifications_alert_timestamp;
    }

    public function UpdateNotesPositions()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            echo(json_encode(array('timestamp' => -1)));
            return -1;
        }

        $notesPositions = array_map('intval', explode(',', $this->request->getStringParam('order')));

        $timestamp = $this->todoNotesModel->UpdateNotesPositions($project_id, $selectedUser, $user_id, $notesPositions);
        echo(json_encode(array(
            'timestamp' => $timestamp,
            'timestring' => date($this->dateParser->getUserDateTimeFormat(), $timestamp),
            'userinfo' => $this->GetUserIconAndName($user_id),
        )));
        return $timestamp;
    }

    public function TransferNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser != $user_id) { // NOT allowed with shared projects
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        $note_id = $this->request->getStringParam('note_id');
        $target_project_id = $this->request->getStringParam('target_project_id');

        return $this->todoNotesModel->TransferNote($project_id, $user_id, $note_id, $target_project_id);
    }

    public function CreateTaskFromNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser != $user_id) { // NOT allowed with shared projects
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return $this->response->html($this->helper->layout->app('TodoNotes:widgets/flash_msg', array()));
        }

        $task_title = $this->request->getStringParam('task_title');
        $task_description = $this->request->getStringParam('task_description');
        $category_id = $this->request->getStringParam('category_id');
        $column_id = $this->request->getStringParam('column_id');
        $swimlane_id = $this->request->getStringParam('swimlane_id');

        $task_id = $this->taskCreationModel->create(array(
            'project_id' => $project_id,
            'creator_id' => $user_id,
            'owner_id' => $user_id,
            'title' => $task_title,
            'description' => $task_description,
            'category_id' => $category_id,
            'column_id' => $column_id,
            'swimlane_id' => $swimlane_id,
        ));

        return $this->response->html($this->helper->layout->app('TodoNotes:project/post', array(
            //'title' => t('Post'),
            'task_id' => $task_id,
            'project_name' => $this->projectModel->getById($project_id)["name"],
            'user_name' => $this->userModel->getById($user_id)["username"],
            'task_title' => $task_title,
            'task_description' => $task_description,
            'category' => $this->categoryModel->getNameById($category_id),
            'column' => $this->columnModel->getColumnTitleById($column_id),
            'swimlane' => $this->swimlaneModel->getNameById($swimlane_id),
        )));
    }

    public function ShowReport()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id, $this->todoNotesModel::PROJECT_SHARING_PERMISSION_VIEW);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return $this->RefreshProject();
        }

        $category = $this->request->getStringParam('category');
        $projectsAccess = $this->todoNotesModel->GetAllProjectIds($user_id);
        $usersAccess = $this->todoNotesModel->GetSharingPermissions($project_id, $user_id);

        $data = $this->todoNotesModel->GetReportNotesForUser($project_id, $selectedUser, $projectsAccess, $usersAccess, $category);

        return $this->response->html($this->helper->layout->app('TodoNotes:project/report', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user_id' => $user_id,
            'data' => $data,
        )));
    }

    public function ShowStats()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id, $this->todoNotesModel::PROJECT_SHARING_PERMISSION_VIEW);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return $this->response->html($this->helper->layout->app('TodoNotes:widgets/flash_msg', array()));
        }

        $statsData = $this->todoNotesModel->GetProjectStatsForUser($project_id, $selectedUser);

        return $this->response->html($this->helper->layout->app('TodoNotes:project/stats', array(
            //'title' => t('Stats'),
            'statsData' => $statsData
        )));
    }

    public function RefreshStatsWidget()
    {
        $stats_project_id = $this->request->getIntegerParam('stats_project_id');
        $stats_user_id = $this->request->getIntegerParam('stats_user_id');
        return $this->response->html($this->helper->layout->app('TodoNotes:widgets/stats', array(
            'stats_project_id' => $stats_project_id,
            'stats_user_id' => $stats_user_id,
        )));
    }

    public function RefreshMarkdownPreviewWidget()
    {
        $markdown_text = $this->request->getStringParam('markdown_text');
        return $this->response->html($this->helper->layout->app('TodoNotes:widgets/markdown_preview', array(
            'markdown_text' => $markdown_text,
        )));
    }

    public function RefreshReindexProgress()
    {
        echo $this->todoNotesModel->ReadReindexProgress();
    }

    public function ReindexNotesAndLists()
    {
        $user_id = $this->ResolveUserId();

        if (!$this->userModel->isAdmin($user_id)) {
            $this->flash->failure(t('TodoNotes__DASHBOARD_REINDEX_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES'));
            return;
        }

        $this->todoNotesModel->ReindexNotesAndLists(true);
    }

    private function CustomNoteListOperationNotification($validation, $is_global)
    {
        if ($validation) {
            if ($is_global) {
                $this->flash->success(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_SUCCESS'));
            } else {
                $this->flash->success(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTPRIVATE_SUCCESS'));
            }
        } else {
            if ($is_global) {
                $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE'));
            } else {
                $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTPRIVATE_FAILURE'));
            }
        }
    }

    public function CreateCustomNoteList()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $custom_note_list_name = $this->request->getStringParam('custom_note_list_name');
        $custom_note_list_is_global = ($this->request->getStringParam('custom_note_list_is_global') == 'true');

        if (empty($custom_note_list_name)) {
            // empty name !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($custom_note_list_is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to create a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->todoNotesModel->CreateCustomNoteList(!$custom_note_list_is_global ? $user_id : 0, $custom_note_list_name);
            if ($validation) {
                $this->todoNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $custom_note_list_is_global);
        }

        $this->response->redirect($this->helper->url->to('TodoNotesController', 'ShowDashboard', array(
            'plugin' => 'TodoNotes',
            'user_id' => $user_id,
            'tab_id' => $this->todoNotesModel->GetTabForProject($project_tab_id, $user_id),
        )));
    }

    public function RenameCustomNoteList()
    {
        $user_id = $this->ResolveUserId();
        $project_id = intval($this->request->getStringParam('project_custom_id'));
        $custom_note_list_name = $this->request->getStringParam('custom_note_list_name');

        $is_global = $this->todoNotesModel->IsCustomGlobalProject($project_id);
        $is_owner = $this->todoNotesModel->IsCustomProjectOwner($project_id, $user_id);
        $is_admin = $this->userModel->isAdmin($user_id);

        if (empty($custom_note_list_name) || $project_id >= 0) {
            // empty name or non-custom project !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$is_admin) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES'));
        } elseif (!$is_global && !$is_owner) {
            // non-Owner attempting to rename a Private note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES'));
        } elseif (($is_global && $is_admin) || (!$is_global && $is_owner)) {
            // Admin attempting to rename Global OR Owner attempting to rename a Private note list !
            $validation = $this->todoNotesModel->RenameCustomNoteList($project_id, $custom_note_list_name);
            if ($validation) {
                $this->todoNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->RefreshAll();
    }

    public function DeleteCustomNoteList()
    {
        $user_id = $this->ResolveUserId();
        $project_id = intval($this->request->getStringParam('project_custom_id'));

        $is_global = $this->todoNotesModel->IsCustomGlobalProject($project_id);
        $is_owner = $this->todoNotesModel->IsCustomProjectOwner($project_id, $user_id);
        $is_admin = $this->userModel->isAdmin($user_id);

        if ($project_id >= 0) {
            // non-custom project!
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$is_admin) {
            // non-Admin attempting to delete a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES'));
        } elseif (!$is_global && !$is_owner) {
            // non-Owner attempting to delete a Private note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES'));
        } elseif (($is_global && $is_admin) || (!$is_global && $is_owner)) {
            // Admin attempting to delete Global OR Owner attempting to delete a Private note list !
            $validation = $this->todoNotesModel->DeleteCustomNoteList($project_id);
            if ($validation['projects']) {
                $this->todoNotesModel->EmulateForceRefresh($validation['permissions'] ? 'permissions' : 'projects');
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->RefreshAll();
    }

    public function UpdateCustomNoteListsPositions()
    {
        $user_id = $this->ResolveUserId();
        $customListsPositions = array_map('intval', explode(',', $this->request->getStringParam('order')));

        $project_id = intval($customListsPositions[0]); // use the first project_id in the array as reference
        $is_global = $this->todoNotesModel->IsCustomGlobalProject($project_id);
        $is_owner = $this->todoNotesModel->IsCustomProjectOwner($project_id, $user_id);
        $is_admin = $this->userModel->isAdmin($user_id);

        if ($project_id >= 0) {
            // non-custom project!
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$is_admin) {
            // non-Admin attempting to reorder a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES'));
        } elseif (!$is_global && !$is_owner) {
            // non-Owner attempting to reorder a Private note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES'));
        } elseif (($is_global && $is_admin) || (!$is_global && $is_owner)) {
            // Admin attempting to reorder Global OR Owner attempting to reorder a Private note list !
            $validation = $this->todoNotesModel->UpdateCustomNoteListsPositions(!$is_global ? $user_id : 0, $customListsPositions);
            if ($validation) {
                $this->todoNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->RefreshAll();
    }

    public function RefreshAll()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $is_sharing = intval($this->request->getStringParam('is_sharing'));

        $this->response->redirect($this->helper->url->to('TodoNotesController', ($is_sharing ? 'ShowDashboardSharing' : 'ShowDashboard'), array(
            'plugin' => 'TodoNotes',
            'user_id' => $user_id,
            'tab_id' => $this->todoNotesModel->GetTabForProject($project_tab_id, $user_id),
        )));
    }

    public function SetSharingPermission()
    {
        $user_id = $this->ResolveUserId();
        $project_id = intval($this->request->getStringParam('project_custom_id'));
        $shared_user_id = intval($this->request->getStringParam('shared_user_id'));
        $shared_permission = intval($this->request->getStringParam('shared_permission'));

        $is_private = $this->todoNotesModel->IsCustomPrivateProject($project_id);
        $is_owner = $this->todoNotesModel->IsCustomProjectOwner($project_id, $user_id);

        $timestamp = 0;
        if ($user_id == $shared_user_id) {
            // trying to share to self !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_SHARING_NOTE_LIST_FAILURE') . ' => ' . t('TodoNotes__GENERIC_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_private && !$is_owner) {
            // trying to share permissions for non-Owned Private note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_SHARING_NOTE_LIST_FAILURE') . ' => ' . t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES'));
        } else {
            // All other cases Global/Private+Owner/Regular should be valid for sharing !
            $this->todoNotesModel->SetSharingPermission($project_id, $user_id, $shared_user_id, $shared_permission);
            $timestamp = $this->todoNotesModel->EmulateForceRefresh('permissions');
            $this->flash->success(t('TodoNotes__DASHBOARD_OPERATION_SHARING_NOTE_LIST_SUCCESS'));
        }

        echo(json_encode(array(
            'timestamp' => $timestamp,
            'flash_msg' => $this->helper->app->flashMessage(),
        )));
    }

    public function ToggleSettings(): bool
    {
        $user_id = $this->ResolveUserId();
        $project_id = intval($this->request->getStringParam('project_custom_id'));

        $settings_group_key = intval($this->request->getStringParam('settings_group_key'));
        $settings_key = intval($this->request->getStringParam('settings_key'));
        $settings_exclusive = (intval($this->request->getStringParam('settings_exclusive')) != 0) ? true : false;

        $this->helper->todonotesSessionAndCookiesSettingsHelper->ToggleSettings(
            $user_id,
            $project_id,
            $settings_group_key,
            $settings_key,
            $settings_exclusive
        );
        return true;
    }

    public function GetLastTimestamp()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $doShowArchive = $todonotesSettingsHelper->GetToggleableSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
            $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
        );
        $userGroup = $todonotesSettingsHelper->GetGroupSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_USER
        );
        $selectedUser = (count($userGroup) == 1) ? $userGroup[0] : 0;

        $lastTimestamps = $doShowArchive
            ? $this->todoNotesModel->GetLastArchivedTimestamp($project_id, $selectedUser)
            : $this->todoNotesModel->GetLastModifiedTimestamp($project_id, $selectedUser);
        echo(json_encode($lastTimestamps));

        return $lastTimestamps;
    }

    public function MoveNoteToArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        $note_id = $this->request->getStringParam('note_id');

        return $this->todoNotesModel->MoveNoteToArchive($project_id, $selectedUser, $user_id, $note_id);
    }

    public function MoveAllDoneNotesToArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        return $this->todoNotesModel->MoveAllDoneNotesToArchive($project_id, $selectedUser, $user_id);
    }

    public function RestoreNoteFromArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        $archived_note_id = $this->request->getStringParam('archived_note_id');
        $target_project_id = $this->request->getStringParam('target_project_id');

        return $this->todoNotesModel->RestoreNoteFromArchive($project_id, $selectedUser, $user_id, $archived_note_id, $target_project_id);
    }

    public function DeleteNoteFromArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $selectedUser = $this->todoNotesModel->VerifySharingPermissions($project_id, $user_id);
        if ($selectedUser == 0) {
            $this->flash->failure(t('TodoNotes__GENERIC_NO_OWNER_PRIVILEGES') . ' ' . t('TodoNotes__GENERIC_NO_SHARING_PERMISSIONS'));
            return null;
        }

        $archived_note_id = $this->request->getStringParam('archived_note_id');

        $todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;
        $doShowArchive = $todonotesSettingsHelper->GetToggleableSettings(
            $user_id,
            $project_id,
            $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
            $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
        );

        return ($doShowArchive) ? $this->todoNotesModel->DeleteNoteFromArchive($project_id, $selectedUser, $user_id, $archived_note_id) : null;
    }
}
