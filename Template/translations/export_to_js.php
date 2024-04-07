<?php
    // list here ALL the required translationTextIds for use with JS
    $translationTextIds = array(
        'BoardNotes_DASHBOARD_MY_NOTES',
        'BoardNotes_DASHBOARD_ALL_TAB',
        'BoardNotes_JS_LOADING_MSG',
        'BoardNotes_JS_NOTE_TITLE_EMPTY_MSG',
        'BoardNotes_JS_NOTE_UPDATE_INVALID_MSG',
        'BoardNotes_JS_DIALOG_CANCEL_BTN',
        'BoardNotes_JS_DIALOG_CLOSE_BTN',
        'BoardNotes_JS_DIALOG_MOVE_BTN',
        'BoardNotes_JS_DIALOG_CREATE_BTN',
        'BoardNotes_JS_DIALOG_DELETE_BTN',
    );
?>

<textarea id="_BoardNotes_TranslationsExportToJS" style="display: none">
<?= $this->helper->translationsExportToJSHelper->export($translationTextIds) ?>
</textarea>
