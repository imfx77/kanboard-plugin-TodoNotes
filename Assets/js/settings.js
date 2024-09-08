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
        static showArchive = false;
        static showCategoryColors = false;
        static sortByStatus = false;
        static showStatusDone = false;
        static showTabsStats = false;

        //------------------------------------------------
        static Initialize() {
            // console.log('_TodoNotes_Settings_.Initialize');

            _TodoNotes_Settings_.#settingsExportToJS = JSON.parse($("#_TodoNotes_SettingsExportToJS_").html());
            $("#_TodoNotes_SettingsExportToJS_").remove();

            _TodoNotes_Settings_.showArchive = _TodoNotes_Settings_.GetSettingsExportToJS('archive', 'showArchive');
            _TodoNotes_Settings_.showCategoryColors = _TodoNotes_Settings_.GetSettingsExportToJS('view', 'showCategoryColors');
            _TodoNotes_Settings_.sortByStatus = _TodoNotes_Settings_.GetSettingsExportToJS('sort', 'sortByStatus');
            _TodoNotes_Settings_.showStatusDone = _TodoNotes_Settings_.GetSettingsExportToJS('filter', 'showStatusDone');
            _TodoNotes_Settings_.showTabsStats = _TodoNotes_Settings_.GetSettingsExportToJS('tabs', 'showTabsStats');
        }

        //------------------------------------------------
        static GetSettingsExportToJS(settings_group_name, settings_name) {
            const settings_group = _TodoNotes_Settings_.#settingsExportToJS[settings_group_name];
            const settings_value = settings_group ? settings_group[settings_name] : false;
            return settings_value ? settings_value :false;
        }

        //------------------------------------------------

    } // class _TodoNotes_Settings_

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Settings_
