/**
 * @author  Im[F(x)]
 */

var _TodoNotes_Translations_;

if (typeof(_TodoNotes_Translations_) === 'undefined') {

    // console.log('define _TodoNotes_Translations_');
    //////////////////////////////////////////////////
    _TodoNotes_Translations_ = class {

        //------------------------------------------------
        // Translation Export to JS
        //------------------------------------------------
        static #translationsExportToJS;

        static msgLoadingSpinner;

        //------------------------------------------------
        static Initialize() {
            // console.log('_TodoNotes_Translations_.Initialize');

            // lazy init the translations ONCE
            if (_TodoNotes_Translations_.#translationsExportToJS) return;

            _TodoNotes_Translations_.#translationsExportToJS = JSON.parse($("#_TodoNotes_TranslationsExportToJS_").html());
            $("#_TodoNotes_TranslationsExportToJS_").remove();

            _TodoNotes_Translations_.msgLoadingSpinner = _TodoNotes_Translations_.GetSpinnerMsg('TodoNotes__JS_LOADING_MSG');
        }

        //------------------------------------------------
        static GetTranslationExportToJS(textId) {
            return _TodoNotes_Translations_.#translationsExportToJS[textId];
        }

        //------------------------------------------------
        static GetSpinnerMsg(textId) {
            const msg = _TodoNotes_Translations_.GetTranslationExportToJS(textId);
            return '<span class="spinnerMsg"><i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="' + msg + '"></i> ' + msg + '</span>';
        }

        //------------------------------------------------

    } // class _TodoNotes_Translations_

    //////////////////////////////////////////////////
    $(document).ready(_TodoNotes_Translations_.Initialize);

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Translations_
