<?php

// list here ALL the required translationTextIds for use with JS
$translationTextIds = array(
    'BoardNotes_DASHBOARD_MY_NOTES',
    'BoardNotes_DASHBOARD_ALL_TAB',
    'BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES',
    'BoardNotes_JS_LOADING_MSG',
    'BoardNotes_JS_REINDEXING_MSG',
    'BoardNotes_JS_NOTE_ADD_TITLE_EMPTY_MSG',
    'BoardNotes_JS_NOTE_UPDATE_TITLE_EMPTY_MSG',
    'BoardNotes_JS_NOTE_UPDATE_INVALID_MSG',
    'BoardNotes_JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG',
    'BoardNotes_JS_DIALOG_CANCEL_BTN',
    'BoardNotes_JS_DIALOG_CLOSE_BTN',
    'BoardNotes_JS_DIALOG_MOVE_BTN',
    'BoardNotes_JS_DIALOG_CREATE_BTN',
    'BoardNotes_JS_DIALOG_RENAME_BTN',
    'BoardNotes_JS_DIALOG_DELETE_BTN',
    'BoardNotes_JS_DIALOG_REINDEX_BTN',
    'BoardNotes_JS_DIALOG_RESULT_TITLE',
    'BoardNotes_PROJECT_NOTE_DETAILS_SAVE_HINT',
);

$this->helper->translationsExportToJSHelper->export($translationTextIds);
