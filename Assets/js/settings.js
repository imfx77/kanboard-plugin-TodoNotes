/**
 * @author  Im[F(x)]
 */

var _TodoNotes_Settings_;

if (typeof(_TodoNotes_Settings_) === 'undefined') {

    // console.log('define _TodoNotes_Settings_');
    //////////////////////////////////////////////////
    _TodoNotes_Settings_ = class {

        //------------------------------------------------
        // Settings Export to JS
        //------------------------------------------------
        static #settingsExportToJS;

        //------------------------------------------------
        // Global vars for settings
        //------------------------------------------------
        static ArchiveView = false;
        static ShowCategoryColors = false;
        static SortByStatus = false;
        static ShowAllDone = false;
        static ShowTabStats = false;

        //------------------------------------------------
        static Initialize() {
            // console.log('_TodoNotes_Settings_.Initialize');

            _TodoNotes_Settings_.#settingsExportToJS = JSON.parse($("#_TodoNotes_SettingsExportToJS_").html());
            $("#_TodoNotes_SettingsExportToJS_").remove();

            _TodoNotes_Settings_.ArchiveView = _TodoNotes_Settings_.GetSettingsExportToJS('todonotesSettings_ArchiveView');
            _TodoNotes_Settings_.ShowCategoryColors = _TodoNotes_Settings_.GetSettingsExportToJS('todonotesSettings_ShowCategoryColors');
            _TodoNotes_Settings_.SortByStatus = _TodoNotes_Settings_.GetSettingsExportToJS('todonotesSettings_SortByStatus');
            _TodoNotes_Settings_.ShowAllDone = _TodoNotes_Settings_.GetSettingsExportToJS('todonotesSettings_ShowAllDone');
            _TodoNotes_Settings_.ShowTabStats = _TodoNotes_Settings_.GetSettingsExportToJS('todonotesSettings_ShowTabStats');
        }

        //------------------------------------------------
        static GetSettingsExportToJS(settings_name) {
            return _TodoNotes_Settings_.#settingsExportToJS[settings_name];
        }

        //------------------------------------------------

    } // class _TodoNotes_Settings_

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Settings_
