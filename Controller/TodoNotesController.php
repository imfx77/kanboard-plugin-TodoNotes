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

        // use cached
        if(!isset($_SESSION['_TodoNotes_Cache_'])) {
           $_SESSION['_TodoNotes_Cache_'] = array();
        }
        if (!empty($use_cached) && array_key_exists('user_id', $_SESSION['_TodoNotes_Cache_'])) {
            $user_id = $_SESSION['_TodoNotes_Cache_']['user_id'];
        }

        // try get param from URL
        if (empty($user_id)) {
            $user_id = $this->request->getStringParam('user_id');
        }

        // as last resort get the current user
        if (empty($user_id)) {
            $user_id = $this->getUser()['id'];
        }

        $_SESSION['_TodoNotes_Cache_']['user_id'] = $user_id;

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
            if ($projectAccess['project_id'] == $project_id) {
                break;
            }
        }

        // if we didn't find the requested project, switch by default to the first one (i.e. Global Notes custom list)
        if ($projectAccess['project_id'] != $project_id) {
            $projectAccess = $projectsAccess[0];
        }

        if ($projectAccess['is_custom']) {
            // assemble a fake custom list
            return array("id" => $project_id, "name" => $projectAccess['project_name'], "is_custom" => true, "is_global" => false);
        } else {
            // get all the data of existing project and mark it as NOT custom
            $project = $this->todoNotesModel->GetRegularProjectById($project_id);
            $project['is_custom'] = false;
            $project['is_global'] = false;
            return $project;
        }
    }

    private function ShowProjectWithRefresh($is_refresh)
    {
        $user = $this->getUser();
        $user_id = $this->ResolveUserId();

        if ($is_refresh) {
            $project = $this->ResolveProject($user_id);
        } else {
            $project = $this->getProject();
            $project['is_custom'] = false;
            $project['is_global'] = false;
        }
        $project_id = $project['id'];

        $projectsAccess = $this->todoNotesModel->GetAllProjectIds($user_id);

        if ($project['is_custom']) {
            $categories = $this->todoNotesModel->GetAllCategories();
        } else {
            $categories = $this->todoNotesModel->GetCategories($project_id);
        }
        $columns = $this->todoNotesModel->GetColumns($project_id);
        $swimlanes = $this->todoNotesModel->GetSwimlanes($project_id);

        if (!array_key_exists('todonotesSettings_ArchiveView', $_SESSION)) {
            $_SESSION['todonotesSettings_ArchiveView'] = false;
        }
        $isArchiveView = $_SESSION['todonotesSettings_ArchiveView'];

        if (!array_key_exists('todonotesSettings_SortByStatus', $_SESSION)) {
            $_SESSION['todonotesSettings_SortByStatus'] = false;
        }
        $doSortByStatus = $_SESSION['todonotesSettings_SortByStatus'];

        if ($project_id == 0) {
            $data = $isArchiveView
                ? $this->todoNotesModel->GetAllArchivedNotesForUser($projectsAccess, $user_id)
                : $this->todoNotesModel->GetAllNotesForUser($projectsAccess, $user_id, $doSortByStatus);
        } else {
            $data = $isArchiveView
                ? $this->todoNotesModel->GetArchivedProjectNotesForUser($project_id, $user_id)
                : $this->todoNotesModel->GetProjectNotesForUser($project_id, $user_id, $doSortByStatus);
        }

        return $this->response->html($this->helper->layout->app('TodoNotes:project/data', array(
            'title' => $project['name'], // rather keep the project name as title
            'projectsAccess' => $projectsAccess,
            'project' => $project,
            'project_id' => $project_id,
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

        $projectsAccess = $this->todoNotesModel->GetAllProjectIds($user_id);

        $project_id = ($tab_id > 0) ? $projectsAccess[$tab_id - 1]['project_id'] : 0;

        if ($tab_id > 0 && !$projectsAccess[$tab_id - 1]['is_custom']) {
            $categories = $this->todoNotesModel->GetCategories($project_id);
            $columns  = $this->todoNotesModel->GetColumns($project_id);
            $swimlanes  = $this->todoNotesModel->GetSwimlanes($project_id);
        } else {
            $categories = $this->todoNotesModel->GetAllCategories();
            $columns  = array();
            $swimlanes  = array();
        }

        if (!array_key_exists('todonotesSettings_ArchiveView', $_SESSION)) {
            $_SESSION['todonotesSettings_ArchiveView'] = false;
        }
        $isArchiveView = $_SESSION['todonotesSettings_ArchiveView'];

        if (!array_key_exists('todonotesSettings_SortByStatus', $_SESSION)) {
            $_SESSION['todonotesSettings_SortByStatus'] = false;
        }
        $doSortByStatus = $_SESSION['todonotesSettings_SortByStatus'];

        if ($project_id == 0) {
            $data = $isArchiveView
                ? $this->todoNotesModel->GetAllArchivedNotesForUser($projectsAccess, $user_id)
                : $this->todoNotesModel->GetAllNotesForUser($projectsAccess, $user_id, $doSortByStatus);
        } else {
            $data = $isArchiveView
                ? $this->todoNotesModel->GetArchivedProjectNotesForUser($project_id, $user_id)
                : $this->todoNotesModel->GetProjectNotesForUser($project_id, $user_id, $doSortByStatus);
        }

        return $this->response->html($this->helper->layout->dashboard('TodoNotes:dashboard/data', array(
            'title' => t('TodoNotes__DASHBOARD_TITLE', $this->helper->user->getFullname($user)),
            'user' => $user,
            'user_id' => $user_id,
            'user_datetime_format' => $this->dateParser->getUserDateTimeFormat(),
            'tab_id' => $tab_id,
            'note_id' => $this->request->getStringParam('note_id') ?: '0',
            'projectsAccess' => $projectsAccess,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,
        )));
    }

    public function RefreshTabs()
    {
        $user_id = $this->ResolveUserId();
        $projectsAccess = $this->todoNotesModel->GetAllProjectIds($user_id);

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

        $is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $category = $this->request->getStringParam('category');

        return $this->todoNotesModel->AddNote($project_id, $user_id, $is_active, $title, $description, $category);
    }

    public function DeleteNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        if (!array_key_exists('todonotesSettings_ArchiveView', $_SESSION)) {
            $_SESSION['todonotesSettings_ArchiveView'] = false;
        }
        $isArchiveView = $_SESSION['todonotesSettings_ArchiveView'];

        return (!$isArchiveView) ? $this->todoNotesModel->DeleteNote($project_id, $user_id, $note_id) : null;
    }

    public function DeleteAllDoneNotes()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        return $this->todoNotesModel->DeleteAllDoneNotes($project_id, $user_id);
    }

    public function UpdateNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $is_active = $this->request->getStringParam('is_active');
        $title = $this->request->getStringParam('title');
        $description = $this->request->getStringParam('description');
        $category = $this->request->getStringParam('category');

        $timestamp = $this->todoNotesModel->UpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category);
        echo(json_encode(array('timestamp' => $timestamp,
                               'timestring' => date($this->dateParser->getUserDateTimeFormat(), $timestamp))));
        return $timestamp;
    }

    public function UpdateNoteStatus()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $is_active = $this->request->getStringParam('is_active');

        $timestamp = $this->todoNotesModel->UpdateNoteStatus($project_id, $user_id, $note_id, $is_active);
        print(json_encode(array('timestamp' => $timestamp,
                                'timestring' => date($this->dateParser->getUserDateTimeFormat(), $timestamp))));
        return $timestamp;
    }

    public function UpdateNoteNotificationsAlertTimeAndOptions()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $notifications_alert_timestring = $this->request->getStringParam('notifications_alert_timestring');
        $notification_options_bitflags = intval($this->request->getStringParam('notification_options_bitflags'));

        $notifications_alert_timestamp = $this->todoNotesModel->UpdateNoteNotificationsAlertTimeAndOptions($project_id, $user_id, $note_id, $notifications_alert_timestring, $notification_options_bitflags);
        print(json_encode(array('timestamp' => $notifications_alert_timestamp,
                                'timestring' => ($notifications_alert_timestamp > 0) ? date($this->dateParser->getUserDateTimeFormat(), $notifications_alert_timestamp) : '',
                                'options_bitflags' => $notification_options_bitflags)));
        return $notifications_alert_timestamp;
    }

    public function UpdateNotesPositions()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];
        $notesPositions = array_map('intval', explode(',', $this->request->getStringParam('order')));

        $timestamp = $this->todoNotesModel->UpdateNotesPositions($project_id, $user_id, $notesPositions);
        print(json_encode(array('timestamp' => $timestamp,
                                'timestring' => date($this->dateParser->getUserDateTimeFormat(), $timestamp))));
        return $timestamp;
    }

    public function TransferNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');
        $target_project_id = $this->request->getStringParam('target_project_id');

        return $this->todoNotesModel->TransferNote($project_id, $user_id, $note_id, $target_project_id);
    }

    public function CreateTaskFromNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $task_title = $this->request->getStringParam('task_title');
        $task_description = $this->request->getStringParam('task_description');
        $category_id = $this->request->getStringParam('category_id');
        $column_id = $this->request->getStringParam('column_id');
        $swimlane_id = $this->request->getStringParam('swimlane_id');

        $task_id = $this->taskCreationModel->create(array(
            'project_id'  => $project_id,
            'creator_id'  => $user_id,
            'owner_id'    => $user_id,
            'title'       => $task_title,
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

        if (!array_key_exists('todonotesSettings_SortByStatus', $_SESSION)) {
            $_SESSION['todonotesSettings_SortByStatus'] = false;
        }
        $doSortByStatus = $_SESSION['todonotesSettings_SortByStatus'];
        $category = $this->request->getStringParam('category');
        $data = $this->todoNotesModel->GetReportNotesForUser($project_id, $user_id, $doSortByStatus, $category);

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

        $statsData = $this->todoNotesModel->GetProjectStatsForUser($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('TodoNotes:project/stats', array(
            //'title' => t('Stats'),
            'statsData' => $statsData
        )));
    }

    public function RefreshStatsWidget()
    {
        $stats_project_id = $this->request->getIntegerParam('stats_project_id');
        return $this->response->html($this->helper->layout->app('TodoNotes:widgets/stats', array(
            'stats_project_id' => $stats_project_id,
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
            $this->flash->failure(t('TodoNotes__DASHBOARD_REINDEX_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES'));
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
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($custom_note_list_is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to create a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES'));
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
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $project_id = intval($this->request->getStringParam('project_custom_id'));
        $custom_note_list_name = $this->request->getStringParam('custom_note_list_name');

        $is_global = $this->todoNotesModel->IsCustomGlobalProject($project_id);

        if (empty($custom_note_list_name) || $project_id >= 0) {
            // empty name or non-custom project!
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->todoNotesModel->RenameCustomNoteList($project_id, $custom_note_list_name);
            if ($validation) {
                $this->todoNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->response->redirect($this->helper->url->to('TodoNotesController', 'ShowDashboard', array(
            'plugin' => 'TodoNotes',
            'user_id' => $user_id,
            'tab_id' => $this->todoNotesModel->GetTabForProject($project_tab_id, $user_id),
        )));
    }

    public function DeleteCustomNoteList()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $project_id = intval($this->request->getStringParam('project_custom_id'));

        $is_global = $this->todoNotesModel->IsCustomGlobalProject($project_id);

        if ($project_id >= 0) {
            // non-custom project!
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->todoNotesModel->DeleteCustomNoteList($project_id);
            if ($validation) {
                $this->todoNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->response->redirect($this->helper->url->to('TodoNotesController', 'ShowDashboard', array(
            'plugin' => 'TodoNotes',
            'user_id' => $user_id,
            'tab_id' => $this->todoNotesModel->GetTabForProject($project_tab_id, $user_id),
        )));
    }

    public function UpdateCustomNoteListsPositions()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $customListsPositions = array_map('intval', explode(',', $this->request->getStringParam('order')));

        $project_id = intval($customListsPositions[0]); // use the first project_id in the array as reference
        $is_global = $this->todoNotesModel->IsCustomGlobalProject($project_id);

        if ($project_id >= 0) {
            // non-custom project!
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->todoNotesModel->UpdateCustomNoteListsPositions(!$is_global ? $user_id : 0, $customListsPositions);
            if ($validation) {
                $this->todoNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->response->redirect($this->helper->url->to('TodoNotesController', 'ShowDashboard', array(
            'plugin' => 'TodoNotes',
            'user_id' => $user_id,
            'tab_id' => $this->todoNotesModel->GetTabForProject($project_tab_id, $user_id),
        )));
    }

    public function ToggleSessionSettings(): bool
    {
        $session_settings_var = $this->request->getStringParam('session_settings_var');
        if (empty($session_settings_var)) {
            print_r($_SESSION);
            return false;
        }

        // toggle settings are expected to be boolean i.e. to only have values of 'true' of 'false'
        if (!array_key_exists($session_settings_var, $_SESSION) || !is_bool($_SESSION[$session_settings_var])) {
            // set initial value
            print_r($_SESSION);
            $_SESSION[$session_settings_var] = false;
            return true;
        }

        // toggle settings
        $_SESSION[$session_settings_var] = !$_SESSION[$session_settings_var];
        print_r($_SESSION);
        return true;
    }

    public function GetLastTimestamp()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        if (!array_key_exists('todonotesSettings_ArchiveView', $_SESSION)) {
            $_SESSION['todonotesSettings_ArchiveView'] = false;
        }
        $isArchiveView = $_SESSION['todonotesSettings_ArchiveView'];

        $lastTimestamps = $isArchiveView
            ? $this->todoNotesModel->GetLastArchivedTimestamp($project_id, $user_id)
            : $this->todoNotesModel->GetLastModifiedTimestamp($project_id, $user_id);
        print(json_encode($lastTimestamps));

        return $lastTimestamps;
    }

    public function MoveNoteToArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        return $this->todoNotesModel->MoveNoteToArchive($project_id, $user_id, $note_id);
    }

    public function MoveAllDoneNotesToArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        return $this->todoNotesModel->MoveAllDoneNotesToArchive($project_id, $user_id);
    }

    public function RestoreNoteFromArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $archived_note_id = $this->request->getStringParam('archived_note_id');
        $target_project_id = $this->request->getStringParam('target_project_id');

        return $this->todoNotesModel->RestoreNoteFromArchive($project_id, $user_id, $archived_note_id, $target_project_id);
    }

    public function DeleteNoteFromArchive()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $archived_note_id = $this->request->getStringParam('archived_note_id');

        if (!array_key_exists('todonotesSettings_ArchiveView', $_SESSION)) {
            $_SESSION['todonotesSettings_ArchiveView'] = false;
        }
        $isArchiveView = $_SESSION['todonotesSettings_ArchiveView'];

        return ($isArchiveView) ? $this->todoNotesModel->DeleteNoteFromArchive($project_id, $user_id, $archived_note_id) : null;
    }
}
