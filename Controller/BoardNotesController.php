<?php

/**
 * Class BoardNotesController
 * @package Kanboard\Plugin\BoardNotes\Controller
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\BoardNotes\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Plugin\BoardNotes\Plugin;

class BoardNotesController extends BaseController
{
    private function ResolveUserId()
    {
        $user_id = ''; // init empty string
        $use_cached = $this->request->getStringParam('use_cached');

        // use cached
        if (!empty($use_cached) && isset($_SESSION['cached_user_id'])) {
            $user_id = $_SESSION['cached_user_id'];
        }

        // try get param from URL
        if (empty($user_id)) {
            $user_id = $this->request->getStringParam('user_id');
        }

        // as last resort get the current user
        if (empty($user_id)) {
            $user_id = $this->getUser()['id'];
        }

        $_SESSION['cached_user_id'] = $user_id;

        return $user_id;
    }

    private function ResolveProject($user_id)
    {
        $project_id = intval($this->request->getStringParam('project_custom_id'));

        if (empty($project_id)) {
            $project_id = $this->request->getIntegerParam('project_id');
        }
        $projectsAccess = $this->boardNotesModel->GetAllProjectIds($user_id);

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
            $project = $this->boardNotesModel->GetRegularProjectById($project_id);
            $project['is_custom'] = false;
            $project['is_global'] = false;
            return $project;
        }
    }

    private function FetchTabForProject($user_id, $project_id): int
    {
        $count = 1;
        $all_user_projects = $this->boardNotesModel->GetAllProjectIds($user_id);
        // recover the tab_id of the last selected project
        foreach ($all_user_projects as $project) {
            if ($project_id == $project['project_id']) {
                return $count;
            }
            $count++;
        }
        // if nothing found leave 0
        return 0;
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

        $projectsAccess = $this->boardNotesModel->GetAllProjectIds($user_id);

        if ($project['is_custom']) {
            $categories = $this->boardNotesModel->GetAllCategories();
        } else {
            $categories = $this->boardNotesModel->GetCategories($project_id);
        }
        $columns = $this->boardNotesModel->GetColumns($project_id);
        $swimlanes = $this->boardNotesModel->GetSwimlanes($project_id);

        if (!array_key_exists('boardnotesSortByStatus', $_SESSION)) {
            $_SESSION['boardnotesSortByStatus'] = false;
        }
        $doSortByStatus = $_SESSION['boardnotesSortByStatus'];

        if ($project_id == 0) {
            $data = $this->boardNotesModel->GetAllNotesForUser($projectsAccess, $user_id, $doSortByStatus);
        } else {
            $data = $this->boardNotesModel->GetProjectNotesForUser($project_id, $user_id, $doSortByStatus);
        }

        return $this->response->html($this->helper->layout->app('BoardNotes:project/data', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,

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

    public function RefreshTabs()
    {
        $user_id = $this->ResolveUserId();
        $projectsAccess = $this->boardNotesModel->GetAllProjectIds($user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:dashboard/tabs', array(
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,
        )));
    }

    public function RefreshStatsWidget()
    {
        $stats_project_id = $this->request->getIntegerParam('stats_project_id');
        return $this->response->html($this->helper->layout->app('BoardNotes:widgets/stats', array(
            'stats_project_id' => $stats_project_id,
        )));
    }

    public function RefreshMarkdownPreviewWidget()
    {
        $markdown_text = $this->request->getStringParam('markdown_text');
        return $this->response->html($this->helper->layout->app('BoardNotes:widgets/markdown_preview', array(
            'markdown_text' => $markdown_text,
        )));
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

        $projectsAccess = $this->boardNotesModel->GetAllProjectIds($user_id);

        if ($tab_id > 0 && !$projectsAccess[$tab_id - 1]['is_custom']) {
            $project_id = $projectsAccess[$tab_id - 1]['project_id'];
            $categories = $this->boardNotesModel->GetCategories($project_id);
            $columns  = $this->boardNotesModel->GetColumns($project_id);
            $swimlanes  = $this->boardNotesModel->GetSwimlanes($project_id);
        } else {
            $categories = $this->boardNotesModel->GetAllCategories();
            $columns  = array();
            $swimlanes  = array();
        }

        if (!array_key_exists('boardnotesSortByStatus', $_SESSION)) {
            $_SESSION['boardnotesSortByStatus'] = false;
        }
        $doSortByStatus = $_SESSION['boardnotesSortByStatus'];
        $data = $this->boardNotesModel->GetAllNotesForUser($projectsAccess, $user_id, $doSortByStatus);

        return $this->response->html($this->helper->layout->dashboard('BoardNotes:dashboard/data', array(
            'title' => t('BoardNotes_DASHBOARD_TITLE', $this->helper->user->getFullname($user)),
            'user' => $user,
            'user_id' => $user_id,
            'tab_id' => $tab_id,
            'projectsAccess' => $projectsAccess,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,
        )));
    }

    public function ToggleSessionOption(): bool
    {
        $session_option = $this->request->getStringParam('session_option');
        if (empty($session_option)) {
            return false;
        }

        // toggle options are expected to be boolean i.e. to only have values of 'true' of 'false'
        if (!array_key_exists($session_option, $_SESSION) ||    // key not exist
            !is_bool($_SESSION[$session_option])                // value not bool
        ) {
            // set initial value
            $_SESSION[$session_option] = false;
            return true;
        }

        // toggle option
        $_SESSION[$session_option] = !$_SESSION[$session_option];
        return true;
    }

    public function GetLastModifiedTimestamp()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $validation = $this->boardNotesModel->GetLastModifiedTimestamp($project_id, $user_id);
        print(json_encode($validation));

        return $validation;
    }

    public function DeleteNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        return $this->boardNotesModel->DeleteNote($project_id, $user_id, $note_id);
    }

    public function DeleteAllDoneNotes()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        return $this->boardNotesModel->DeleteAllDoneNotes($project_id, $user_id);
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

        return $this->boardNotesModel->AddNote($project_id, $user_id, $is_active, $title, $description, $category);
    }

    public function TransferNote()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');
        $target_project_id = $this->request->getStringParam('target_project_id');

        return $this->boardNotesModel->TransferNote($project_id, $user_id, $note_id, $target_project_id);
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

        $validation = $this->boardNotesModel->UpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category);
        print $validation ? time() : 0;

        return $validation;
    }

    public function UpdateNoteStatus()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $is_active = $this->request->getStringParam('is_active');

        $validation = $this->boardNotesModel->UpdateNoteStatus($project_id, $user_id, $note_id, $is_active);
        print $validation ? time() : 0;

        return $validation;
    }

    public function ShowStats()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        $statsData = $this->boardNotesModel->GetProjectStatsForUser($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/stats', array(
            //'title' => t('Stats'),
            'statsData' => $statsData
        )));
    }

    public function boardNotesCreateTask()
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

        return $this->response->html($this->helper->layout->app('BoardNotes:project/post', array(
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

    public function UpdateNotesPositions()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];
        $notesPositions = array_map('intval', explode(',', $this->request->getStringParam('order')));

        $validation = $this->boardNotesModel->UpdateNotesPositions($project_id, $user_id, $notesPositions);
        print $validation ? time() : 0;

        return $validation;
    }

    public function ShowReport()
    {
        $user_id = $this->ResolveUserId();
        $project = $this->ResolveProject($user_id);
        $project_id = $project['id'];

        if (!array_key_exists('boardnotesSortByStatus', $_SESSION)) {
            $_SESSION['boardnotesSortByStatus'] = false;
        }
        $doSortByStatus = $_SESSION['boardnotesSortByStatus'];
        $category = $this->request->getStringParam('category');
        $data = $this->boardNotesModel->GetReportNotesForUser($project_id, $user_id, $doSortByStatus, $category);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/report', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user_id' => $user_id,
            'data' => $data,
        )));
    }

    public function ReindexNotesAndLists()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));

        if ($this->userModel->isAdmin($user_id)) {
            $lastVersion = constant('\Kanboard\Plugin\\' . Plugin::NAME . '\Schema\VERSION');
            $functionName = '\Kanboard\Plugin\\' . Plugin::NAME . '\Schema\reindexNotesAndLists_' . $lastVersion;

            if (function_exists($functionName)) {
                try {
                    $this->db->startTransaction();
                    $this->db->getDriver()->disableForeignKeys();

                    call_user_func($functionName, $this->db->getConnection());

                    $this->db->getDriver()->enableForeignKeys();
                    $this->db->closeTransaction();

                    $this->flash->success(t('BoardNotes_DASHBOARD_REINDEX_SUCCESS'));
                } catch (PDOException $e) {
                    $this->db->cancelTransaction();
                    $this->db->getDriver()->enableForeignKeys();

                    $this->flash->failure(t('BoardNotes_DASHBOARD_REINDEX_FAILURE') . ' => ' . $e->getMessage());
                }
            } else {
                $this->flash->failure(t('BoardNotes_DASHBOARD_REINDEX_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_REINDEX_METHOD_NOT_IMPLEMENTED') . ' [v.' . $lastVersion . ']');
            }
        } else {
            $this->flash->failure(t('BoardNotes_DASHBOARD_REINDEX_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES'));
        }

        $this->response->redirect($this->helper->url->to('BoardNotesController', 'ShowDashboard', array(
            'plugin' => 'BoardNotes',
            'user_id' => $user_id,
            'tab_id' => $this->FetchTabForProject($user_id, $project_tab_id),
        )));
    }

    private function CustomNoteListOperationNotification($validation, $is_global)
    {
        if ($validation) {
            if ($is_global) {
                $this->flash->success(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_SUCCESS'));
            } else {
                $this->flash->success(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTPRIVATE_SUCCESS'));
            }
        } else {
            if ($is_global) {
                $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE'));
            } else {
                $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTPRIVATE_FAILURE'));
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
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($custom_note_list_is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to create a Global note list !
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->boardNotesModel->CreateCustomNoteList(!$custom_note_list_is_global ? $user_id : 0, $custom_note_list_name);
            if ($validation) {
                $this->boardNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $custom_note_list_is_global);
        }

        $this->response->redirect($this->helper->url->to('BoardNotesController', 'ShowDashboard', array(
            'plugin' => 'BoardNotes',
            'user_id' => $user_id,
            'tab_id' => $this->FetchTabForProject($user_id, $project_tab_id),
        )));
    }

    public function RenameCustomNoteList()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $project_id = intval($this->request->getStringParam('project_custom_id'));
        $custom_note_list_name = $this->request->getStringParam('custom_note_list_name');

        $is_global = $this->boardNotesModel->IsCustomGlobalProject($project_id);

        if (empty($custom_note_list_name) || $project_id >= 0) {
            // empty name or non-custom project!
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->boardNotesModel->RenameCustomNoteList($project_id, $custom_note_list_name);
            if ($validation) {
                $this->boardNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->response->redirect($this->helper->url->to('BoardNotesController', 'ShowDashboard', array(
            'plugin' => 'BoardNotes',
            'user_id' => $user_id,
            'tab_id' => $this->FetchTabForProject($user_id, $project_tab_id),
        )));
    }

    public function DeleteCustomNoteList()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $project_id = intval($this->request->getStringParam('project_custom_id'));

        $is_global = $this->boardNotesModel->IsCustomGlobalProject($project_id);

        if ($project_id >= 0) {
            // non-custom project!
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->boardNotesModel->DeleteCustomNoteList($project_id);
            if ($validation) {
                $this->boardNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->response->redirect($this->helper->url->to('BoardNotesController', 'ShowDashboard', array(
            'plugin' => 'BoardNotes',
            'user_id' => $user_id,
            'tab_id' => $this->FetchTabForProject($user_id, $project_tab_id),
        )));
    }

    public function UpdateCustomNoteListsPositions()
    {
        $user_id = $this->ResolveUserId();
        $project_tab_id = intval($this->request->getStringParam('project_tab_id'));
        $customListsPositions = array_map('intval', explode(',', $this->request->getStringParam('order')));

        $project_id = intval($customListsPositions[0]); // use the first project_id in the array as reference
        $is_global = $this->boardNotesModel->IsCustomGlobalProject($project_id);

        if ($project_id >= 0) {
            // non-custom project!
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_INVALID_OR_EMPTY_PARAMETER'));
        } elseif ($is_global && !$this->userModel->isAdmin($user_id)) {
            // non-Admin attempting to rename a Global note list !
            $this->flash->failure(t('BoardNotes_DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE') . ' => ' . t('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES'));
        } else {
            $validation = $this->boardNotesModel->UpdateCustomNoteListsPositions(!$is_global ? $user_id : 0, $customListsPositions);
            if ($validation) {
                $this->boardNotesModel->EmulateForceRefresh();
            }
            $this->CustomNoteListOperationNotification($validation, $is_global);
        }

        $this->response->redirect($this->helper->url->to('BoardNotesController', 'ShowDashboard', array(
            'plugin' => 'BoardNotes',
            'user_id' => $user_id,
            'tab_id' => $this->FetchTabForProject($user_id, $project_tab_id),
        )));
    }
}
