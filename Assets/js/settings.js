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
        static #overview_settingsExportToJS;
        static #project_settingsExportToJS;

        //------------------------------------------------
        // Global vars for settings
        //------------------------------------------------
        static showTabsStats = false;

        static hideStatusDone = false;
        static hideStatusOpen = false;
        static hideStatusInProgress = false;
        static showArchive = false;

        static sortManual = false;
        static sortByStatus = false;

        static showCategoryColors = false;
        static showStandardStatusMarks = false;

        //------------------------------------------------
        static Initialize() {
            // console.log('_TodoNotes_Settings_.Initialize');

            _TodoNotes_Settings_.#overview_settingsExportToJS = JSON.parse($("#_TodoNotes_OverviewSettingsExportToJS_").html());
            $("#_TodoNotes_OverviewSettingsExportToJS_").remove();
            _TodoNotes_Settings_.#project_settingsExportToJS = JSON.parse($("#_TodoNotes_ProjectSettingsExportToJS_").html());
            $("#_TodoNotes_ProjectSettingsExportToJS_").remove();

            _TodoNotes_Settings_.showTabsStats              = _TodoNotes_Settings_.GetSettingsExportToJS(0 /*tabs*/, 0 /*Stats*/, true);

            _TodoNotes_Settings_.hideStatusDone             = _TodoNotes_Settings_.GetSettingsExportToJS(1 /*filter*/, 0 /*Done*/);
            _TodoNotes_Settings_.hideStatusOpen             = _TodoNotes_Settings_.GetSettingsExportToJS(1 /*filter*/, 1 /*Open*/);
            _TodoNotes_Settings_.hideStatusInProgress       = _TodoNotes_Settings_.GetSettingsExportToJS(1 /*filter*/, 2 /*InProgress*/);
            _TodoNotes_Settings_.showArchive                = _TodoNotes_Settings_.GetSettingsExportToJS(1 /*filter*/, 3 /*Archived*/);

            _TodoNotes_Settings_.sortManual                 = _TodoNotes_Settings_.GetSettingsExportToJS(2 /*sort*/, 0 /*Manual*/);
            _TodoNotes_Settings_.sortByStatus               = _TodoNotes_Settings_.GetSettingsExportToJS(2 /*sort*/, 1 /*Status*/);

            _TodoNotes_Settings_.showCategoryColors         = _TodoNotes_Settings_.GetSettingsExportToJS(3 /*view*/, 0 /*CategoryColors*/);
            _TodoNotes_Settings_.showStandardStatusMarks    = _TodoNotes_Settings_.GetSettingsExportToJS(3 /*view*/, 1 /*StandardStatusMarks*/);
        }

        //------------------------------------------------
        static GetSettingsExportToJS(settings_group_key, settings_key, is_overview = false) {
            const settings = is_overview
                ? _TodoNotes_Settings_.#overview_settingsExportToJS
                : _TodoNotes_Settings_.#project_settingsExportToJS;
            const settings_group = settings[settings_group_key]
            return (Array.isArray(settings_group) && settings_group.includes(settings_key)) ? true : false;
        }

        //------------------------------------------------

    } // class _TodoNotes_Settings_

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Settings_
