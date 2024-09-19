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
        // Global constants for settings
        //------------------------------------------------
        static GROUP = {
            TABS: 0,
            FILTER: 1,
            SORT: 2,
            VIEW: 3,
        };
        static TABS = {
            STATS: 0,
            GLOBAL: 1,
            PRIVATE: 2,
            REGULAR: 3,
        };
        static FILTER = {
            DONE: 0,
            OPEN: 1,
            IN_PROGRESS: 2,
            ARCHIVED: 3,
        };
        static SORT = {
            MANUAL: 0,
            STATUS: 1,
            DATE_CREATED: 2,
            DATE_MODIFIED: 3,
            DATE_NOTIFIED: 4,
            DATE_LAST_NOTIFIED: 5,
            DATE_ARCHIVED: 6,
            DATE_RESTORED: 7,
        };
        static VIEW = {
            CATEGORY_COLORS: 0,
            STANDARD_STATUS_MARKS: 1,
        };
        //------------------------------------------------
        // Global vars for settings
        //------------------------------------------------
        static showTabsStats = false;
        static hideTabsGlobal = false;
        static hideTabsPrivate = false;
        static hideTabsRegular = false;

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

            _TodoNotes_Settings_.showTabsStats              = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.STATS, true);
            _TodoNotes_Settings_.hideTabsGlobal             = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.GLOBAL, true);
            _TodoNotes_Settings_.hideTabsPrivate            = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.PRIVATE, true);
            _TodoNotes_Settings_.hideTabsRegular            = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.REGULAR, true);

            _TodoNotes_Settings_.hideStatusDone             = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.DONE);
            _TodoNotes_Settings_.hideStatusOpen             = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.OPEN);
            _TodoNotes_Settings_.hideStatusInProgress       = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.IN_PROGRESS);
            _TodoNotes_Settings_.showArchive                = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.ARCHIVED);

            _TodoNotes_Settings_.sortManual                 = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.MANUAL);
            _TodoNotes_Settings_.sortByStatus               = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.STATUS);

            _TodoNotes_Settings_.showCategoryColors         = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.VIEW, _TodoNotes_Settings_.VIEW.CATEGORY_COLORS);
            _TodoNotes_Settings_.showStandardStatusMarks    = _TodoNotes_Settings_.GetSettingsExportToJS(_TodoNotes_Settings_.GROUP.VIEW, _TodoNotes_Settings_.VIEW.STANDARD_STATUS_MARKS);
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
