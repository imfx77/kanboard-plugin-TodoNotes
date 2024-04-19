class _BoardNotes_Translations_ {

//------------------------------------------------
// Translation Export to JS
//------------------------------------------------
static #translationsExportToJS;

static msgLoadingSpinner;

//------------------------------------------------
static initialize() {
    // lazy init the translations ONCE
    if (this.#translationsExportToJS) return;

    this.#translationsExportToJS = JSON.parse( $("#_BoardNotes_TranslationsExportToJS_").html() );
    $("#_BoardNotes_TranslationsExportToJS_").remove();

    this.msgLoadingSpinner = this.getSpinnerMsg('BoardNotes_JS_LOADING_MSG');
}

//------------------------------------------------
static getTranslationExportToJS(textId) {
    return this.#translationsExportToJS[textId];
}

//------------------------------------------------
static getSpinnerMsg(textId) {
    var msg = this.getTranslationExportToJS(textId);
    return '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="' + msg + '"></i> ' + msg;
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _BoardNotes_Translations_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Translations_._dummy_ );
