<?php

return array(
    //
    // GENERAL
    //
    'TodoNotes__PLUGIN_DESCRIPTION' => 'The plugin allows to keep TODO-style notes on every KB project and as standalone lists.
                                        The notes that may appear unsuitable for creating board tasks are totally fine on the custom TODO list.
                                        They are easy and fast to create, change and rearrange, with convenient visual aids.
                                        Every user can privately see and operate ONLY his own notes, even if notes of multiple users are are bound to the same project.',
    //
    // Template/project
    //
    'TodoNotes__PROJECT_NOT_FOUND' => '⚠️ Project NOT found!',
    //
    'TodoNotes__PROJECT_TITLE' => 'Notes',
    'TodoNotes__PROJECT_OVERVIEW_MODE_TITLE' => 'Overview Mode',
    'TodoNotes__PROJECT_OVERVIEW_MODE_TEXT' => '⚠️ ONLY manage notes Status and lists order!',
    'TodoNotes__PROJECT_OVERVIEW_MODE_TEXT_REORDERING_DISABLED' => '⚠️ ONLY manage notes Status! DISABLED lists reordering!',
    //
    'TodoNotes__PROJECT_NEW_NOTE_LABEL' => 'Create New Note',
    'TodoNotes__PROJECT_NEW_NOTE_TEXT_REORDERING_DISABLED' => '⚠️ DISABLED list reordering by Status sort!',
    'TodoNotes__PROJECT_NEW_NOTE_TITLE_PLACEHOLDER' => 'What needs to be done',
    //
    'TodoNotes__PROJECT_TOGGLE_COLORIZE_BY_CATEGORY' => 'Toggle Colorize by Category',
    'TodoNotes__PROJECT_TOGGLE_SHOW_ALL_DONE' => 'Toggle Show all Done Notes',
    'TodoNotes__PROJECT_TOGGLE_SORT_BY_STATUS' => 'Toggle Sort by Status',
    'TodoNotes__PROJECT_TOGGLE_TAB_STAT' => 'Toggle Show Stats',
    'TodoNotes__PROJECT_EXPAND_ALL_NOTES' => 'Expand all Notes',
    'TodoNotes__PROJECT_COLLAPSE_ALL_NOTES' => 'Collapse all Notes',
    'TodoNotes__PROJECT_CREATE_REPORT' => 'Create Report',
    'TodoNotes__PROJECT_NOTES_STATS' => 'Notes Stats',
    'TodoNotes__PROJECT_DELETE_ALL_DONE_NOTES' => '⚠️ Delete ALL Done Notes!',
    'TodoNotes__PROJECT_TOGGLE_LIST' => 'Toggle List',
    //
    'TodoNotes__PROJECT_NOTE_REFRESH_ORDER' => 'Refresh Order',
    'TodoNotes__PROJECT_NOTE_CREATE_TASK' => 'Create Task from Note',
    'TodoNotes__PROJECT_NOTE_MOVE_TO_PROJECT' => 'Move Note to Project',
    'TodoNotes__PROJECT_NOTE_DELETE' => 'Delete Note',
    'TodoNotes__PROJECT_NOTE_SAVE' => 'Save Note',
    'TodoNotes__PROJECT_NOTE_TOGGLE_DETAILS' => 'Toggle Details',
    //
    'TodoNotes__PROJECT_NOTE_SWITCH_STATUS' => 'Switch Status',
    'TodoNotes__PROJECT_NOTE_TITLE_EDIT_HINT' => 'Edit Title',
    'TodoNotes__PROJECT_NOTE_TITLE_SAVE_HINT' => 'Press ENTER to save changes, or Press TAB to edit details',
    'TodoNotes__PROJECT_NOTE_EDIT_DETAILS' => 'Edit Note Details',
    'TodoNotes__PROJECT_NOTE_DETAILS_EDIT_HINT' => '... add Note details here ...',
    'TodoNotes__PROJECT_NOTE_DETAILS_SAVE_HINT' => 'Press TAB to save changes',
    'TodoNotes__PROJECT_NOTE_BUSY_ICON_HINT' => 'Polling data changes ...
Might be waiting for an unsubmitted New Note data input!',
    //
    // Template/dashboard
    //
    'TodoNotes__DASHBOARD_TITLE' => 'Notes overview for %s',
    'TodoNotes__DASHBOARD_MY_NOTES' => 'My notes',
    'TodoNotes__DASHBOARD_ALL_TAB' => 'All Lists',
    'TodoNotes__DASHBOARD_LIST_GROUP_GLOBAL' => 'Global Custom Lists',
    'TodoNotes__DASHBOARD_LIST_GROUP_PRIVATE' => 'Private Custom Lists',
    'TodoNotes__DASHBOARD_LIST_GROUP_REGULAR' => 'Regular Projects Lists',
    'TodoNotes__DASHBOARD_TOGGLE_LIST_GROUP' => 'Toggle List Group',
    //
    'TodoNotes__DASHBOARD_ADMIN_ONLY' => '(Admin only)',
    'TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES' => '⚠️ Current user has no Admin privileges!',
    'TodoNotes__DASHBOARD_INVALID_OR_EMPTY_PARAMETER' => '⚠️ There was an Invalid or Empty parameter to the request!',
    //
    'TodoNotes__DASHBOARD_REINDEX' => '⚠️ Reindex Notes and Lists!',
    'TodoNotes__DASHBOARD_REINDEX_SUCCESS' => 'Reindexing Notes and Lists finished successfully.',
    'TodoNotes__DASHBOARD_REINDEX_FAILURE' => '⚠️ Reindexing Notes and Lists failed!',
    'TodoNotes__DASHBOARD_REINDEX_METHOD_NOT_IMPLEMENTED' => 'Schema version method is NOT implemented!',
    //
    'TodoNotes__DASHBOARD_CREATE_CUSTOM_NOTE_LIST' => 'Create custom note list',
    'TodoNotes__DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST' => '⚠️ Rename custom global note list!',
    'TodoNotes__DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST' => '⚠️ Delete custom global note list!',
    'TodoNotes__DASHBOARD_RENAME_CUSTOM_PRIVATE_LIST' => 'Rename custom private note list',
    'TodoNotes__DASHBOARD_DELETE_CUSTOM_PRIVATE_LIST' => 'Delete custom private note list',
    //
    'TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTPRIVATE_SUCCESS' => 'Private custom note list operation succeeded.',
    'TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTPRIVATE_FAILURE' => '⚠️ Private custom note list operation failed!',
    'TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_SUCCESS' => 'Global custom note list operation succeeded.',
    'TodoNotes__DASHBOARD_OPERATION_CUSTOM_NOTE_LISTGLOBAL_FAILURE' => '⚠️ Global custom note list operation failed!',
    //
    // Template/report
    //
    'TodoNotes__REPORT_HIDE_ROW' => 'Hide this Row',
    //
    // Template dialogs
    //
    'TodoNotes__DIALOG_REINDEX_MSG' => 'This routine optimizes the Notes and Note Lists tables by shrinking the deleted records and reindexing all list and note Ids.
&#10;⚠️ BE AWARE that it rewrites those tables from scratch, so it may take some time, during which the routine MUST NOT be interrupted!
&#10;⚠️ IT IS VERY LIKELY that reindexing will break the list references of your Archived Notes, if you have any, thus making them orphaned!
&#10;Continue?',
    //
    'TodoNotes__DIALOG_DELETE_NOTE_MSG' => '⚠️ This note will be permanently deleted and cannot be recovered!
&#10;Are you sure?',
    'TodoNotes__DIALOG_DELETE_ALL_DONE_MSG' => '⚠️ ALL Done notes will be permanently deleted and cannot be recovered!
&#10;Are you sure?',
    //
    'TodoNotes__DIALOG_TRANSFER_NOTE_TARGET_PROJECT' => 'Target Project',
    'TodoNotes__DIALOG_TRANSFER_NOTE_MSG' => '⚠️ Bear in mind that the target project may NOT have the category that is assigned to the note!
&#10;If so, the category will be displayed greyed and will be ignored until a valid one from the target project is set.
&#10;Continue?',
    //
    'TodoNotes__DIALOG_CREATE_TASK_CHECKBOX_REMOVE_NOTE' => '⚠️ Remove the Note',
    'TodoNotes__DIALOG_POST_SUCCESS_TITLE' => 'Success!',
    'TodoNotes__DIALOG_POST_FAILURE_TITLE' => 'Ooops, something went wrong ;/',
    'TodoNotes__DIALOG_POST_SUCCESS_TEXT' => 'Created task',
    'TodoNotes__DIALOG_POST_FAILURE_TEXT' => 'The task could not be created for:',
    'TodoNotes__DIALOG_REPORT_CATEGORY_FILTER' => 'Filter by Category',
    //
    'TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_NAME_PLACEHOLDER' => 'type custom note list name',
    'TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_GLOBAL_CHECKBOX' => '⚠️ Make it Global',
    'TodoNotes__DIALOG_CREATE_CUSTOM_NOTE_LIST_MSG' => 'You can create a Private custom list for your personal usage.
&#10;⚠️ If you have Admin privileges, you can create a Global custom list - and it will be visible for all users to work with!',
    'TodoNotes__DIALOG_RENAME_CUSTOM_NOTE_LIST_TITLE' => 'Rename custom note list',
    'TodoNotes__DIALOG_RENAME_CUSTOM_NOTE_LIST_MSG' => 'Change the custom list name in the edit box above.
&#10;⚠️ If you have Admin privileges, you can rename Global custom lists - yet, bare in mind that they are visible to other users and renaming them may lead to confusion!,
&#10;Continue?',
    'TodoNotes__DIALOG_DELETE_CUSTOM_NOTE_LIST_TITLE' => '⚠️ Delete custom note list!',
    'TodoNotes__DIALOG_DELETE_CUSTOM_NOTE_LIST_MSG' => '⚠️ Deleting this list will permanently delete ALL the notes it contains, those cannot be recovered!
&#10;⚠️ If you have Admin privileges, you can delete Global custom lists - but BE AWARE that this action will delete ALL the notes of ALL the users that share those Global custom lists! 
&#10;⚠️ This operation will also leave orphaned Archived Notes, if you have any for that list!
&#10;Continue?',
    'TodoNotes__DIALOG_REORDER_CUSTOM_NOTE_LIST_TITLE' => 'Reorder custom note list',
    'TodoNotes__DIALOG_REORDER_CUSTOM_NOTE_LIST_MSG' => 'Reordering custom lists is a persistent operation, the list will stay ordered that way!
&#10;⚠️ This order will also determine the order in which Notes List will be shown in Overview Mode!
&#10;⚠️ If you have Admin privileges, you can reorder Global custom lists - this change will be displayed for ALL users! 
&#10;Continue?',
    //
    'TodoNotes__DIALOG_NOTIFICATIONS_SETUP_TITLE' => 'Setup Notifications for Note',
    'TodoNotes__DIALOG_NOTIFICATIONS_ALERT_TIME' => 'Alert Time',
    'TodoNotes__DIALOG_NOTIFICATIONS_ALERT_POSTPONE' => 'Postpone Alert Time',
    //
    // Notifications
    //
    'TodoNotes__NOTIFICATIONS_EMAIL_TITLE' => 'TodoNotes Alert',
    'TodoNotes__NOTIFICATIONS_EMAIL_CONTENT' => '<a href="%s"><h2><u>%s</u></h2></a>
<div style="color: darkred"><b>🔔 %s</b></div><br>
<div><b>Project :</b> %s</div>
<div><b>Category :</b> %s</div>
<div>%s</div>',
    //
    // Specific EXPORTS FOR JS
    //
    'TodoNotes__JS_LOADING_MSG' => 'Loading ...',
    'TodoNotes__JS_REINDEXING_MSG' => 'Reindexing ...',
    //
    'TodoNotes__JS_NOTE_ADD_TITLE_EMPTY_MSG' => '⚠️
Note title is empty !
Skipping note addition !',
    'TodoNotes__JS_NOTE_UPDATE_TITLE_EMPTY_MSG' => '⚠️
Note title is empty !
Keeping the current one !',
    'TodoNotes__JS_NOTE_UPDATE_INVALID_MSG' => '⚠️
The note you are trying to update is INVALID !
The page will forcefully refresh now !',
    //
    'TodoNotes__JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG' => '⚠️
Custom list name is empty !
Aborting operation !',
    //
    'TodoNotes__JS_DIALOG_CANCEL_BTN' => 'Cancel',
    'TodoNotes__JS_DIALOG_CLOSE_BTN' => 'Close',
    'TodoNotes__JS_DIALOG_SET_BTN' => '✔️ Set',
    'TodoNotes__JS_DIALOG_RESET_BTN' => '⭕ Reset',
    'TodoNotes__JS_DIALOG_MOVE_BTN' => '⚠️ Move',
    'TodoNotes__JS_DIALOG_CREATE_BTN' => '✔️ Create',
    'TodoNotes__JS_DIALOG_RENAME_BTN' => '✏️ Rename',
    'TodoNotes__JS_DIALOG_DELETE_BTN' => '⚠️ Delete',
    'TodoNotes__JS_DIALOG_REINDEX_BTN' => '♻️ Reindex',
    'TodoNotes__JS_DIALOG_REORDER_BTN' => '📑 Reorder',
    'TodoNotes__JS_DIALOG_RESULT_TITLE' => 'Result ...',
);
