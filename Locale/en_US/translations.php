<?php

return array(
    //
    // GENERAL
    //
    'BoardNotes_PLUGIN_DESCRIPTION' => 'The plugin allows to keep TODO-style notes on every KB project and as standalone lists.
                                        The notes that may appear unsuitable for creating board tasks are totally fine on the custom TODO list.
                                        They are easy and fast to create, change and rearrange, with convenient visual aids.
                                        Every user can privately see and operate ONLY his own notes, even if notes of multiple users are are bound to the same project.',
    //
    // Template/project
    //
    'BoardNotes_PROJECT_TITLE' => 'Notes',
    'BoardNotes_PROJECT_OVERVIEW_MODE_TITLE' => 'Overview Mode',
    'BoardNotes_PROJECT_OVERVIEW_MODE_TEXT' => '⚠ ONLY manage notes Status and lists order!',
    'BoardNotes_PROJECT_OVERVIEW_MODE_TEXT_REORDERING_DISABLED' => '⚠ ONLY manage notes Status! DISABLED lists reordering!',
    'BoardNotes_PROJECT_NEW_NOTE_LABEL' => 'Create New Note',
    'BoardNotes_PROJECT_NEW_NOTE_TEXT_REORDERING_DISABLED' => '⚠ DISABLED list reordering by Status sort!',
    'BoardNotes_PROJECT_NEW_NOTE_TITLE_PLACEHOLDER' => 'What needs to be done',
    'BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES' => '⚠ Delete ALL Done Notes!',
    'BoardNotes_PROJECT_NOTES_STATS' => 'Notes Stats',
    'BoardNotes_PROJECT_CREATE_REPORT' => 'Create Report',
    'BoardNotes_PROJECT_COLLAPSE_ALL_NOTES' => 'Collapse all Notes',
    'BoardNotes_PROJECT_EXPAND_ALL_NOTES' => 'Expand all Notes',
    'BoardNotes_PROJECT_TOGGLE_SHOW_ALL_DONE' => 'Toggle Show all Done Notes',
    'BoardNotes_PROJECT_TOGGLE_SORT_BY_STATUS' => 'Toggle Sort by Status',
    'BoardNotes_PROJECT_TOGGLE_COLORIZE_BY_CATEGORY' => 'Toggle Colorize by Category',
    'BoardNotes_PROJECT_NOTE_TOGGLE_DETAILS' => 'Toggle Details',
    'BoardNotes_PROJECT_NOTE_EDIT_DETAILS' => 'Edit Note Details',
    'BoardNotes_PROJECT_NOTE_CONFIRM_DETAILS' => 'Confirm Note Details',
    'BoardNotes_PROJECT_NOTE_REFRESH_ORDER' => 'Refresh Order',
    'BoardNotes_PROJECT_NOTE_SAVE' => 'Save Note',
    'BoardNotes_PROJECT_NOTE_DELETE' => 'Delete Note',
    'BoardNotes_PROJECT_NOTE_MOVE_TO_PROJECT' => 'Move Note to Project',
    'BoardNotes_PROJECT_NOTE_CREATE_TASK' => 'Create Task from Note',
    'BoardNotes_PROJECT_NOTE_SWITCH_STATUS' => 'Switch Status',
    'BoardNotes_PROJECT_NOTE_TITLE_EDIT_HINT' => 'Edit Title',
    'BoardNotes_PROJECT_NOTE_TITLE_SAVE_HINT' => 'Press ENTER to save changes, or Press TAB to edit details',
    'BoardNotes_PROJECT_NOTE_DETAILS_EDIT_HINT' => '... add Note details here ...',
    'BoardNotes_PROJECT_NOTE_DETAILS_SAVE_HINT' => 'Press TAB to save changes',
    'BoardNotes_PROJECT_NOTE_BUSY_ICON_HINT' => 'Polling data changes ...
Might be waiting for an unsubmitted New Note data input!',
    //
    // Template/dashboard
    //
    'BoardNotes_DASHBOARD_TITLE' => 'Notes overview for %s',
    'BoardNotes_DASHBOARD_MY_NOTES' => 'My notes',
    'BoardNotes_DASHBOARD_ALL_TAB' => 'All',
    'BoardNotes_DASHBOARD_ADMIN_ONLY' => '(Admin only)',
    'BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES' => '⚠ Current user has no Admin privileges!',
    'BoardNotes_DASHBOARD_REINDEX' => '⚠ Reindex Notes and Lists!',
    'BoardNotes_DASHBOARD_REINDEX_SUCCESS' => 'Reindexing Notes and Lists finished successfully.',
    'BoardNotes_DASHBOARD_REINDEX_FAILURE' => '⚠ Reindexing Notes and Lists failed!',
    'BoardNotes_DASHBOARD_REINDEX_METHOD_NOT_IMPLEMENTED' => 'Schema version method is NOT implemented!',
    'BoardNotes_DASHBOARD_NEW_CUSTOM_LIST' => 'New custom list',
    'BoardNotes_DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST' => '⚠ Rename custom global list!',
    'BoardNotes_DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST' => '⚠ Delete custom global list!',
    'BoardNotes_DASHBOARD_RENAME_CUSTOM_PRIVATE_LIST' => 'Rename custom private list',
    'BoardNotes_DASHBOARD_DELETE_CUSTOM_PRIVATE_LIST' => 'Delete custom private list',
    //
    // Template/report
    //
    'BoardNotes_REPORT_HIDE_ROW' => 'Hide this Row',
    //
    // Template dialogs
    //
    'BoardNotes_DELETEALLDONE_DIALOG_MSG' => '⚠ These items will be permanently deleted and cannot be recovered!
&#10;Are you sure?',
    'BoardNotes_TRANSFERNOTE_DIALOG_TARGET_PROJECT' => 'Target Project',
    'BoardNotes_TRANSFERNOTE_DIALOG_MSG' => '⚠ Bear in mind that the target project may NOT have the category that is assigned to the note!
&#10;If so, the category will be displayed greyed and will be ignored until a valid one from the target project is set.
&#10;Continue?',
    'BoardNotes_CREATETASK_DIALOG_CHECKBOX_REMOVE_NOTE' => '⚠ Remove the Note',
    'BoardNotes_POST_DIALOG_SUCCESS_TITLE' => 'Success!',
    'BoardNotes_POST_DIALOG_FAILURE_TITLE' => 'Ooops, something went wrong ;/',
    'BoardNotes_POST_DIALOG_SUCCESS_TEXT' => 'Created task',
    'BoardNotes_POST_DIALOG_FAILURE_TEXT' => 'The task could not be created for:',
    'BoardNotes_REPORT_DIALOG_CATEGORY_FILTER' => 'Filter by Category',
    //
    // Specific EXPORTS FOR JS
    //
    'BoardNotes_JS_LOADING_MSG' => 'Loading ...',
    'BoardNotes_JS_REINDEXING_MSG' => 'Reindexing ...',
    'BoardNotes_JS_NOTE_ADD_TITLE_EMPTY_MSG' => '⚠
Note title is empty !
Skipping note addition !',
    'BoardNotes_JS_NOTE_UPDATE_TITLE_EMPTY_MSG' => '⚠
Note title is empty !
Keeping the current one !',
    'BoardNotes_JS_NOTE_UPDATE_INVALID_MSG' => '⚠
The note you are trying to update is INVALID !
The page will forcefully refresh now !',
    'BoardNotes_JS_DIALOG_CANCEL_BTN' => 'Cancel',
    'BoardNotes_JS_DIALOG_CLOSE_BTN' => 'Close',
    'BoardNotes_JS_DIALOG_MOVE_BTN' => '⚠ Move',
    'BoardNotes_JS_DIALOG_CREATE_BTN' => '✔ Create',
    'BoardNotes_JS_DIALOG_DELETE_BTN' => '⚠ Delete',
    'BoardNotes_JS_DIALOG_RESULT_TITLE' => 'Result ...',
);
