class _BoardNotes_Translations_ {

//------------------------------------------------
// Translation Export to JS
//------------------------------------------------
static #translationsExportToJS;

static msgLoadingSpinner;

//------------------------------------------------
static initialize() {
    // lazy init the translations ONCE
    if (_BoardNotes_Translations_.#translationsExportToJS) return;

    _BoardNotes_Translations_.#translationsExportToJS = JSON.parse( $("#_BoardNotes_TranslationsExportToJS_").html() );
    $("#_BoardNotes_TranslationsExportToJS_").remove();

    _BoardNotes_Translations_.msgLoadingSpinner = _BoardNotes_Translations_.getSpinnerMsg('BoardNotes_JS_LOADING_MSG');
}

//------------------------------------------------
static getTranslationExportToJS(textId) {
    return _BoardNotes_Translations_.#translationsExportToJS[textId];
}

//------------------------------------------------
static getSpinnerMsg(textId) {
    var msg = _BoardNotes_Translations_.getTranslationExportToJS(textId);
    return '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="' + msg + '"></i> ' + msg;
}

//------------------------------------------------

} // class _BoardNotes_Translations_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Translations_.initialize );
