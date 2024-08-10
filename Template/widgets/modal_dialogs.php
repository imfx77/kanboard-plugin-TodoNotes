<?php

$isAdmin = $this->user->isAdmin();

//---------------------------------------------
// Notes related modal dialogs
//---------------------------------------------

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

print '<div class="hideMe" id="dialogNotificationsSetup-P' . $project_id . '" title="' . t('TodoNotes__DIALOG_NOTIFICATIONS_SETUP_TITLE') . '"';
print ' data-project="' . $project_id . '" data-datetime-format="' . $user_datetime_format . '">';

print '<div style="text-align: center"><label id="note_title_NotificationsSetup-P' . $project_id . '" class="dateLabel dateLabelComplete"></label></div>';

print '<br>'; // alert datetime picker
print $this->helper->form->datetime(t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_TIME') . ' :&nbsp;&nbsp;', 'alert_time_NotificationsSetup-P' . $project_id, array(), array(), array('tabindex="-1"'));

print '<br>'; // postpone options BEGIN
print '<div id="postpone_options_NotificationsSetup-P' . $project_id . '" data-project="' . $project_id . '">';
print '<input type="checkbox" id="postpone_NotificationsSetup-P' . $project_id . '">';
print '<label for="postpone_NotificationsSetup-P' . $project_id . '">&nbsp;&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_POSTPONE') . '</label>';

print '<br>';
print '<input type="number" id="postpone_value_NotificationsSetup-P' . $project_id . '" value="1" min="1" step="1" style="text-align: center; margin-left: 30px">';
print '&nbsp;&nbsp';

print '<select id="postpone_type_NotificationsSetup-P' . $project_id . '">';
print '<option value="1">' . t('seconds') . '</option>';
print '<option value="2">' . t('minutes') . '</option>';
print '<option value="3">' . t('hours') . '</option>';
print '<option value="4" selected>' . t('Day(s)') . '</option>';
print '<option value="5">' . t('Month(s)') . '</option>';
print '<option value="6">' . t('Year(s)') . '</option>';
print '</select>';

print '<br>';
print '<input type="text" class="hideMe" id="postpone_base_NotificationsSetup-P' . $project_id . '">';
print '<label id="postpone_time_NotificationsSetup-P' . $project_id . '" style="margin-left: 30px; color: darkred"></label>';

print '</div>'; // postpone options END

print '<br>'; // alert options BEGIN
print '<details id="alert_options_NotificationsSetup-P' . $project_id . '" data-project="' . $project_id . '">';
print '<summary class="catLabelClickable">' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_OPTIONS') . '</summary>';

print '<br>';
print '<input type="checkbox" id="alert_mail_NotificationsSetup-P' . $project_id . '">';
print '<label for="alert_mail_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_USE_MAIL') . '</label>';
print '&nbsp;&nbsp;&nbsp;&nbsp;';
print '<input type="checkbox" id="alert_webpn_NotificationsSetup-P' . $project_id . '">';
print '<label for="alert_webpn_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_USE_WEBPN') . '</label>';

print '<br>';
print '<br>';
print '<input type="checkbox" id="alert_before_NotificationsSetup-P' . $project_id . '">';
print '<label for="alert_before_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_REMIND_BEFORE') . '</label>';
print '&nbsp;&nbsp;&nbsp;&nbsp;';
print '<input type="radio" name="alert_before" id="alert_before1day_NotificationsSetup-P' . $project_id . '" disabled checked>';
print '<label for="alert_before1day_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_REMIND_BEFORE_1DAY') . '</label>';
print '&nbsp;&nbsp;';
print '<input type="radio" name="alert_before" id="alert_before1hour_NotificationsSetup-P' . $project_id . '" disabled>';
print '<label for="alert_before1hour_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_REMIND_BEFORE_1HOUR') . '</label>';

print '<br>';
print '<input type="checkbox" id="alert_after_NotificationsSetup-P' . $project_id . '">';
print '<label for="alert_after_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_REMIND_AFTER') . '</label>';
print '&nbsp;&nbsp;&nbsp;&nbsp;';
print '<input type="radio" name="alert_after" id="alert_after1day_NotificationsSetup-P' . $project_id . '" disabled checked>';
print '<label for="alert_after1day_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_REMIND_AFTER_1DAY') . '</label>';
print '&nbsp;&nbsp;';
print '<input type="radio" name="alert_after" id="alert_after1hour_NotificationsSetup-P' . $project_id . '" disabled>';
print '<label for="alert_after1hour_NotificationsSetup-P' . $project_id . '">&nbsp;' . t('TodoNotes__DIALOG_NOTIFICATIONS_ALERT_REMIND_AFTER_1HOUR') . '</label>';

print '</details>'; // alert options END

print '</div>';

//---------------------------------------------
// Stats & Report modal dialogs
//---------------------------------------------

//---------------------------------------------

print '<div class="hideMe" id="dialogStats" title="' . t('TodoNotes__PROJECT_NOTES_STATS') . '">';
print '<div id="dialogStatsInside"></div>';
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
// Dashboard system modal dialogs
//---------------------------------------------

//----------------------------------------

print '<div class="hideMe" id="dialogReindexNotesAndLists" title="' . t('TodoNotes__DASHBOARD_REINDEX') . '">';

print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_REINDEX_MSG');
print '</p>';

print '</div>';

//---------------------------------------------
// Lists related modal dialogs
//---------------------------------------------

//----------------------------------------

print '<div class="hideMe" id="dialogCreateCustomNoteList" title="' . t('TodoNotes__DASHBOARD_CREATE_CUSTOM_NOTE_LIST') . '">';

print '<input type="text" id="nameCreateCustomNoteList" placeholder="' . t('TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_NAME_PLACEHOLDER') . '">';
print '<br>';
if ($isAdmin) {
    print '<input type="checkbox" id="globalCreateCustomNoteList">';
    print '<label for="globalCreateCustomNoteList">&nbsp;&nbsp;' . t('TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_GLOBAL_CHECKBOX') . '</label>';
} else {
    print '<input type="checkbox" disabled id="globalCreateCustomNoteList">';
    print '<label for="globalCreateCustomNoteList">&nbsp;&nbsp;' . t('TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_GLOBAL_CHECKBOX') . ' ' . t('TodoNotes__DASHBOARD_ADMIN_ONLY') . '</label>';
}
print '<br><br>';
print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_MSG');
print '</p>';

print '</div>';

//----------------------------------------

print '<div class="hideMe" id="dialogRenameCustomNoteList" title="' . t('TodoNotes__DIALOG_RENAME_CUSTOM_NOTE_LIST_TITLE') . '">';

print '<input type="text" id="nameRenameCustomNoteList">';
print '<br><br>';
print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_RENAME_CUSTOM_NOTE_LIST_MSG');
print '</p>';

print '</div>';

//----------------------------------------

print '<div class="hideMe" id="dialogDeleteCustomNoteList" title="' . t('TodoNotes__DIALOG_DELETE_CUSTOM_NOTE_LIST_TITLE') . '">';

print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_DELETE_CUSTOM_NOTE_LIST_MSG');
print '</p>';

print '</div>';

//----------------------------------------

print '<div class="hideMe" id="dialogReorderCustomNoteList" title="' . t('TodoNotes__DIALOG_REORDER_CUSTOM_NOTE_LIST_TITLE') . '">';

print '<p style="white-space: pre-wrap;">';
print t('TodoNotes__DIALOG_REORDER_CUSTOM_NOTE_LIST_MSG');
print '</p>';

print '</div>';

//---------------------------------------------
