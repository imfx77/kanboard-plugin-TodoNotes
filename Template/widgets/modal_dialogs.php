<?php

//---------------------------------------------

print '<div class="hideMe" id="dialogDeleteNote" title="' . t('TodoNotes__PROJECT_NOTE_DELETE') . '">';
print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_DELETE_NOTE_MSG');
print '</p>';
print '</div>';

//---------------------------------------------

print '<div class="hideMe" id="dialogDeleteAllDone" title="' . t('TodoNotes__PROJECT_DELETE_ALL_DONE_NOTES') . '">';
print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_DELETE_ALL_DONE_MSG');
print '</p>';
print '</div>';

//---------------------------------------------

print '<div class="hideMe" id="dialogStats" title="' . t('TodoNotes__PROJECT_NOTES_STATS') . '">';
print '<div id="dialogStatsInside"></div>';
print '</div>';

//---------------------------------------------

print '<div class="hideMe" id="dialogCreateTaskFromNote-P' . $project_id . '" title="' . t('TodoNotes__PROJECT_NOTE_CREATE_TASK') . '">';

print '<div id="dialogCreateTaskParams">';

print '<label for="listCatCreateTask-P' . $project_id . '">' . t('Category') . ' : &nbsp;</label>';
print '<select id="listCatCreateTask-P' . $project_id . '">';
// Only allow blank select if there's other selectable options
if (!empty($listCategoriesById)) {
    print '<option></option>';
}
print $listCategoriesById;
print '</select>';
print '<br>';

print '<label for="listColCreateTask-P' . $project_id . '">' . t('Column') . ' : &nbsp;</label>';
print '<select id="listColCreateTask-P' . $project_id . '">';
print $listColumnsById;
print '</select>';
print '<br>';

print '<label for="listSwimCreateTask-P' . $project_id . '">' . t('Swimlane') . ' : &nbsp;</label>';
print '<select id="listSwimCreateTask-P' . $project_id . '">';
print $listSwimlanesById;
print '</select>';
print '<br>';

print '<input type="checkbox" checked id="removeNote-P' . $project_id . '">';
print '<label for="removeNote-P' . $project_id . '">&nbsp;&nbsp;' . t('TodoNotes__DIALOG_CREATE_TASK_CHECKBOX_REMOVE_NOTE') . '</label>';

print '</div>'; // params

print '<div id="deadloading" class="hideMe"></div>';
print '</div>';

//---------------------------------------------

print '<div class="hideMe" id="dialogTransferNote-P' . $project_id . '" title="' . t('TodoNotes__PROJECT_NOTE_MOVE_TO_PROJECT') . '">';

print '<label for="listNoteProject-P' . $project_id . '">' . t('TodoNotes__DIALOG_TRANSFER_NOTE_TARGET_PROJECT') . ' : &nbsp&nbsp;</label>';
print '<select id="listNoteProject-P' . $project_id . '">';
foreach ($projectsTabsById as $key => $projectTab) {
    if ($key != $project_id) {
        print '<option value="';
        print $key;
        print '">';
        print $projectTab['name'];
        print '</option>';
    }
}
print '</select>';
print '<br><br>';
print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_TRANSFER_NOTE_MSG');
print '</p>';

print '</div>';

//---------------------------------------------

print '<div class="hideMe" id="dialogReport-P' . $project_id . '" title="' . t('TodoNotes__PROJECT_CREATE_REPORT') . '">';

print '<label for="catReport-P' . $project_id . '">' . t('TodoNotes__DIALOG_REPORT_CATEGORY_FILTER') . ' :</label><br>';
print '<select id="catReport-P' . $project_id . '">';
print '<option></option>'; // add an empty category option
if (!empty($listCategoriesById)) {
    print $listCategoriesById;
}
print '</select>';

print '</div>';

//---------------------------------------------

print '<div class="hideMe" id="dialogNotificationsSetup-P' . $project_id . '" title="' . t('TodoNotes__DIALOG_NOTIFICATIONS_SETUP_TITLE') . '">';

print $this->helper->form->datetime(t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_TIME') . ' :&nbsp;&nbsp;', 'alerttimeNotificationsSetup-P' . $project_id, array(), array(), array('tabindex="-1"'));

print '</div>';

//---------------------------------------------
