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
        // Global base app URL
        //------------------------------------------------
        static baseAppDir;

        //------------------------------------------------
        // Global constants for settings
        //------------------------------------------------
        static GROUP = {
            TABS: 0,
            USER: 1,
            FILTER: 2,
            SORT: 3,
            VIEW: 4,
        };
        static TABS = {
            STATS: 0,
            GLOBAL: 1,
            PRIVATE: 2,
            SHARED: 3,
            REGULAR: 4,
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
        static hideTabsShared = false;
        static hideTabsRegular = false;

        static selectedUser = 0;

        static hideStatusDone = false;
        static hideStatusOpen = false;
        static hideStatusInProgress = false;
        static showArchive = false;

        static sortManual = false;
        static sortByStatus = false;
        static sortByDateCreated = false;
        static sortByDateModified = false;
        static sortByDateNotified = false;
        static sortByDateLastNotified = false;
        static sortByDateArchived = false;
        static sortByDateRestored = false;

        static showCategoryColors = false;
        static showStandardStatusMarks = false;

        //------------------------------------------------
        static Initialize() {
            // console.log('_TodoNotes_Settings_.Initialize');

            const baseAppDirField = $("#_TodoNotes_BaseAppDir_");
            if (baseAppDirField.length === 1) {
                // console.log(baseAppDirField.html());
                _TodoNotes_Settings_.baseAppDir = baseAppDirField.html();
                baseAppDirField.remove();
            }

            const overviewSettings = $("#_TodoNotes_OverviewSettingsExportToJS_");
            if (overviewSettings.length === 1) {
                // console.log(overviewSettings.html());
                _TodoNotes_Settings_.#overview_settingsExportToJS = JSON.parse(overviewSettings.html());
                overviewSettings.remove();
            }

            const projectSettings = $("#_TodoNotes_ProjectSettingsExportToJS_");
            if (projectSettings.length === 1) {
                // console.log(projectSettings.html());
                _TodoNotes_Settings_.#project_settingsExportToJS = JSON.parse(projectSettings.html());
                projectSettings.remove();
            }

            _TodoNotes_Settings_.showTabsStats              = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.STATS, true);
            _TodoNotes_Settings_.hideTabsGlobal             = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.GLOBAL, true);
            _TodoNotes_Settings_.hideTabsPrivate            = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.PRIVATE, true);
            _TodoNotes_Settings_.hideTabsShared             = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.SHARED, true);
            _TodoNotes_Settings_.hideTabsRegular            = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.TABS, _TodoNotes_Settings_.TABS.REGULAR, true);

            _TodoNotes_Settings_.selectedUser               = _TodoNotes_Settings_.#GetToggleableSettingsKey(_TodoNotes_Settings_.GROUP.USER);

            _TodoNotes_Settings_.hideStatusDone             = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.DONE);
            _TodoNotes_Settings_.hideStatusOpen             = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.OPEN);
            _TodoNotes_Settings_.hideStatusInProgress       = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.IN_PROGRESS);
            _TodoNotes_Settings_.showArchive                = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.FILTER, _TodoNotes_Settings_.FILTER.ARCHIVED);

            _TodoNotes_Settings_.sortManual                 = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.MANUAL);
            _TodoNotes_Settings_.sortByStatus               = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.STATUS);
            _TodoNotes_Settings_.sortByDateCreated          = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.DATE_CREATED);
            _TodoNotes_Settings_.sortByDateModified         = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.DATE_MODIFIED);
            _TodoNotes_Settings_.sortByDateNotified         = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.DATE_NOTIFIED);
            _TodoNotes_Settings_.sortByDateLastNotified     = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.DATE_LAST_NOTIFIED);
            _TodoNotes_Settings_.sortByDateArchived         = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.DATE_ARCHIVED);
            _TodoNotes_Settings_.sortByDateRestored         = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.SORT, _TodoNotes_Settings_.SORT.DATE_RESTORED);

            _TodoNotes_Settings_.showCategoryColors         = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.VIEW, _TodoNotes_Settings_.VIEW.CATEGORY_COLORS);
            _TodoNotes_Settings_.showStandardStatusMarks    = _TodoNotes_Settings_.#GetToggleableSettings(_TodoNotes_Settings_.GROUP.VIEW, _TodoNotes_Settings_.VIEW.STANDARD_STATUS_MARKS);
        }

        //------------------------------------------------
        static #GetToggleableSettings(settings_group_key, settings_key, is_overview = false) {
            const settings = is_overview
                ? _TodoNotes_Settings_.#overview_settingsExportToJS
                : _TodoNotes_Settings_.#project_settingsExportToJS;
            const settings_group = settings[settings_group_key]
            return (Array.isArray(settings_group) && settings_group.includes(settings_key)) ? true : false;
        }

        //------------------------------------------------
        static #GetToggleableSettingsKey(settings_group_key, is_overview = false) {
            const settings = is_overview
                ? _TodoNotes_Settings_.#overview_settingsExportToJS
                : _TodoNotes_Settings_.#project_settingsExportToJS;
            const settings_group = settings[settings_group_key]
            return (Array.isArray(settings_group) && settings_group.length === 1) ? parseInt(settings_group[0]) : 0;
        }

        //------------------------------------------------

    } // class _TodoNotes_Settings_

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Settings_
