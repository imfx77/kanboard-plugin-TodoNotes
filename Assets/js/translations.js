/**
 * @author  Im[F(x)]
 */

class _TodoNotes_Translations_ {

//------------------------------------------------
// Translation Export to JS
//------------------------------------------------
static #translationsExportToJS;

static msgLoadingSpinner;

//------------------------------------------------
static initialize() {
    // lazy init the translations ONCE
    if (_TodoNotes_Translations_.#translationsExportToJS) return;

    _TodoNotes_Translations_.#translationsExportToJS = JSON.parse( $("#_TodoNotes_TranslationsExportToJS_").html() );
    $("#_TodoNotes_TranslationsExportToJS_").remove();

    _TodoNotes_Translations_.msgLoadingSpinner = _TodoNotes_Translations_.getSpinnerMsg('BoardNotes_JS_LOADING_MSG');
}

//------------------------------------------------
static getTranslationExportToJS(textId) {
    return _TodoNotes_Translations_.#translationsExportToJS[textId];
}

//------------------------------------------------
static getSpinnerMsg(textId) {
    const msg = _TodoNotes_Translations_.getTranslationExportToJS(textId);
    return '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="' + msg + '"></i> ' + msg;
}

//------------------------------------------------

} // class _TodoNotes_Translations_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Translations_.initialize );
