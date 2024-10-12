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

?>

<div class="page-header">
    <h2><?= t('TodoNotes__PROJECT_SHARING_PERMISSIONS') ?></h2>
</div>

<table class="tableReport">
<thead class="theadReport">
<tr>
<th class="thReport thReportNr">#</th>
<th class="thReport"><?= t('Information') ?></th>
<th class="thReport thReportStatus"><?= t('Status') ?></th>
</tr>
</thead>
<tbody>

<?php

// TODO: table contents

?>

</tbody>
</table>

<?php

//----------------------------------------
print $this->app->flashMessage();

print '<div class="containerCenter">';
print '<button id="closeSharing" class="btn">' . t('TodoNotes__JS_DIALOG_CLOSE_BTN') . '</button>';
print '</div>';

// hidden reference for project_id and user_id of the currently active page
print '<div class="hideMe" id="refProjectId"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print ' data-readonly=""';
print ' data-timestamp="' . time() . '"';
print '></div>';

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

//---------------------------------------------
// include github buttons
print $this->render('TodoNotes:widgets/github_buttons', array());
//---------------------------------------------

print '<span id="refreshIcon" class="refreshIcon hideMe">';
print '&nbsp;<i class="fa fa-refresh fa-spin" title="' . t('TodoNotes__PROJECT_NOTE_BUSY_ICON_HINT') . '"></i></span>';
