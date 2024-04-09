class _BoardNotes_Translations_ {

//------------------------------------------------
// Translation Export to JS
//------------------------------------------------
static #translationsExportToJS = null;

//------------------------------------------------
static initialize() {
    // lazy init the translations ONCE
    if (this.#translationsExportToJS !== null) return;

    this.#translationsExportToJS = JSON.parse( $("#_BoardNotes_TranslationsExportToJS_").html() );
    $("#_BoardNotes_TranslationsExportToJS_").remove();
}

//------------------------------------------------
static getTranslationExportToJS(textId) {
    _BoardNotes_Translations_.initialize();
    return this.#translationsExportToJS[textId];
}

//------------------------------------------------

} // class _BoardNotes_Translations_