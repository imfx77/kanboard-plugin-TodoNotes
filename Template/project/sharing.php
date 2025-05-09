<?php

require_once('settings.php');

// export translations to JS
print $this->render('TodoNotes:translations/export_to_js');
// load all necessary CSS and JS
print $this->asset->css('plugins/TodoNotes/Assets/css/project.css');
print $this->asset->js('plugins/TodoNotes/Assets/js/statuses.js');
print $this->asset->js('plugins/TodoNotes/Assets/js/settings.js');
print $this->asset->js('plugins/TodoNotes/Assets/js/modals.js');
print $this->asset->js('plugins/TodoNotes/Assets/js/requests.js');
print $this->asset->js('plugins/TodoNotes/Assets/js/notes.js');
print $this->asset->js('plugins/TodoNotes/Assets/js/load_sharing.js');

print '<div class="containerCenter">';
print '<section class="mainholder" id="mainholderP' . $project_id . '">';
print '<div align="left" style="width: fit-content" id="result' . $project_id . '">';

?>

<div class="page-header">
    <h2><?= t('TodoNotes__PROJECT_SHARING_PERMISSIONS') ?></h2>
</div>

<table class="tableSharing">
<tbody>

<?php

foreach ($this->model->userModel->getAll() as $u) {
    // skip the current user
    $curr_user_id = $u['id'];
    if ($user_id == $curr_user_id) {
        continue;
    }

    print '<tr>';

    // User Info
    print '<td class="tdSharing">';
    print '<h3>';
    print $this->avatar->small(
        $u['id'],
        $u['username'],
        $u['name'],
        $u['email'],
        $u['avatar_path'],
        'avatar-inline'
    );
    print $this->text->e($u['name'] ?: $u['username']);
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '</h3>';
    print '</td>';

    // Permissions option
    print '<td class="tdSharing">';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';

    print '<input type="radio" name="permission-U' . $curr_user_id . '" class="listPermission" id="listPermissionNone-U' . $curr_user_id . '"';
    print ' data-shared-user="' . $curr_user_id . '"';
    print ' data-shared-permission="' . $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_NONE . '"';
    print ((!array_key_exists($curr_user_id, $data) || $data[$curr_user_id] == $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_NONE) ? ' checked' : '') . '>';
    print '<label class="buttonPermissions" for="listPermissionNone-U' . $curr_user_id . '">&nbsp;';
    print '<i class="fa fa-ban" aria-hidden="true"></i> ' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS_NONE') . '</label>';
    print '&nbsp;&nbsp;';

    print '<input type="radio" name="permission-U' . $curr_user_id . '" class="listPermission" id="listPermissionView-U' . $curr_user_id . '"';
    print ' data-shared-user="' . $curr_user_id . '"';
    print ' data-shared-permission="' . $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_VIEW . '"';
    print ((array_key_exists($curr_user_id, $data) && $data[$curr_user_id] == $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_VIEW) ? ' checked' : '') . '>';
    print '<label class="buttonPermissions" for="listPermissionView-U' . $curr_user_id . '">&nbsp;';
    print '<i class="fa fa-eye" aria-hidden="true"></i> ' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS_VIEW') . '</label>';
    print '&nbsp;&nbsp;';

    print '<input type="radio" name="permission-U' . $curr_user_id . '" class="listPermission" id="listPermissionEdit-U' . $curr_user_id . '"';
    print ' data-shared-user="' . $curr_user_id . '"';
    print ' data-shared-permission="' . $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_EDIT . '"';
    print ((array_key_exists($curr_user_id, $data) && $data[$curr_user_id] == $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_EDIT) ? ' checked' : '') . '>';
    print '<label class="buttonPermissions" for="listPermissionEdit-U' . $curr_user_id . '">&nbsp;';
    print '<i class="fa fa-pencil" aria-hidden="true"></i> ' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS_EDIT') . '</label>';

    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '</td>';

    // Change button
    print '<td class="tdSharing">';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="setSharingPermission btn" id="setSharingPermission-U' . $curr_user_id . '" disabled';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print ' data-shared-user="' . $curr_user_id . '"';
    print '>' . t('TodoNotes__JS_DIALOG_SET_BTN') . '</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '<button class="toolbarSeparator">&nbsp;</button>';
    print '</td>';

    print '</tr>';
}

?>

</tbody>
</table>

<?php

//----------------------------------------
// hidden reference for project_id and user_id of the currently active page
print '<div class="hideMe" id="refProjectId"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print ' data-timestamp="' . time() . '"';
print '></div>';

print '<span id="refreshIcon" class="refreshIcon hideMe">';
print '&nbsp;<i class="fa fa-refresh fa-spin" title="' . t('TodoNotes__PROJECT_NOTE_BUSY_ICON_HINT') . '"></i></span>';

print '<div id="containerFlashMessage">';
print $this->app->flashMessage();
print '</div>';

//----------------------------------------
print '<div class="containerCenter">';
print '<button id="closeSharing" class="btn"';
print ' data-url="' . $this->url->to('TodoNotesController', 'ShowDashboard', array('plugin' => 'TodoNotes', 'user_id' => $user_id, 'tab_id' => $tab_id)) . '"';
print '>';
print t('TodoNotes__JS_DIALOG_CLOSE_BTN');
print '</button>';
print '</div>';

print '</div>'; // id='result'

//---------------------------------------------
// include modal dialogs
print $this->render('TodoNotes:widgets/modal_dialogs', array(
    'project_id' => $project_id,
    'user_datetime_format' => '',
    'listCategoriesById' => array(),
    'listColumnsById' => array(),
    'listSwimlanesById' => array(),
    'projectsTabsById' => array(),
));
//---------------------------------------------

print '</section>';

//---------------------------------------------
// include github buttons
print $this->render('TodoNotes:widgets/github_buttons', array());
//---------------------------------------------

print '</div>';

