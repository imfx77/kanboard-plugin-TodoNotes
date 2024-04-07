<?php
    // list here ALL the required translationTextIds for use with JS
    $translationTextIds = array(
        'BoardNotes_JS_TEST_STRING',
    );
?>

<textarea id="_BoardNotes_TranslationsExportToJS" style="display: none">
<?= $this->helper->translationsExportToJSHelper->export($translationTextIds) ?>
</textarea>
