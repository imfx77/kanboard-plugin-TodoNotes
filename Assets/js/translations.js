function get_BoardNotes_Translations() {

//================================================

let _BoardNotes_Translations_ = {}; // namespace

//------------------------------------------------
// Translation Export to JS
//------------------------------------------------

_BoardNotes_Translations_.translationsExportToJS = null;

_BoardNotes_Translations_.getTranslationExportToJS = function(textId) {
    // lazy init the translations ONCE
    if (_BoardNotes_Translations_.translationsExportToJS == null) {
        _BoardNotes_Translations_.translationsExportToJS = JSON.parse( $("#_BoardNotes_TranslationsExportToJS").html() );
        $("#BoardNotes_TranslationsExportToJS").remove();
    }
    return _BoardNotes_Translations_.translationsExportToJS[textId];
}

//================================================

return _BoardNotes_Translations_;
}