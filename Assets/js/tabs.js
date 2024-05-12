class _BoardNotes_Tabs_ {

//------------------------------------------------
// Tabs routines
//------------------------------------------------

//------------------------------------------------
static updateTabs() {
    var tab_id = $("#tabId").attr('data-tab');

    $(".singleTab").removeClass( 'active' );
    $("#singleTab" + tab_id).addClass( 'active' );
    $("#myNotesHeader h2").text(
        _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_MY_NOTES')
        + ' > ' + $("#singleTab" + tab_id + " a").text()
    );
}

//------------------------------------------------
static updateTabsContainer() {
    var numTabs = $("#tabs li").length + 1; // add +1 because of the separator lines
    var tabHeight = $("#tabs li:eq(0)").outerHeight();
    $("#tabs").height(numTabs * tabHeight);
}

//------------------------------------------------
static updateTabStats() {
    if (_BoardNotes_.optionShowTabStats) {
        $("#settingsTabStats").find("a i").addClass( 'buttonToggled' );
        $(".tabStatsWidget").removeClass( 'hideMe' );
    } else {
        $("#settingsTabStats").find("a i").removeClass( 'buttonToggled' );
        $(".tabStatsWidget").addClass( 'hideMe' );
    }

    _BoardNotes_Tabs_.updateTabsContainer();

}

//------------------------------------------------
// Tabs Buttons handlers
//------------------------------------------------

//------------------------------------------------
static #TabActionHandlers() {
    // toggle visibility of tabs stats widgets
    $("#settingsTabStats").click(function() {
        _BoardNotes_.sqlToggleSessionOption('boardnotesShowTabStats');

        _BoardNotes_.optionShowTabStats = !_BoardNotes_.optionShowTabStats;

        _BoardNotes_Tabs_.updateTabStats();
    });

    // start DB optimization routine on system reindex button
    $("#reindexNotesAndLists").click(function() {
        var user_id = $(this).attr('data-user');
        _BoardNotes_Tabs_.#sqlReindexNotesAndLists(user_id);
    });

    // update tabs stats widgets on clicking checkDone
    $("button" + ".checkDone").click(function() {
        var project_id = $(this).attr('data-project');
        //var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');

        var $checkDone = $("#noteDoneCheckmark-P" + project_id + "-" + id);
        var $statsWidgetAll = $("#BoardNotes-StatsWidget-P0");
        var $statsWidgetCurrent = $("#BoardNotes-StatsWidget-P" + project_id);

        // cycle through statuses
        if ($checkDone.hasClass( 'fa-circle-thin' )) {
            // open++, done--
            $statsWidgetAll.find(".statOpen b").text(parseInt($statsWidgetAll.find(".statOpen b").text()) + 1)
            $statsWidgetAll.find(".statDone b").text(parseInt($statsWidgetAll.find(".statDone b").text()) - 1)
            $statsWidgetCurrent.find(".statOpen b").text(parseInt($statsWidgetCurrent.find(".statOpen b").text()) + 1)
            $statsWidgetCurrent.find(".statDone b").text(parseInt($statsWidgetCurrent.find(".statDone b").text()) - 1)
        }
        if ($checkDone.hasClass( 'fa-spinner fa-pulse' )) {
            // progress++, open--
            $statsWidgetAll.find(".statProgress b").text(parseInt($statsWidgetAll.find(".statProgress b").text()) + 1)
            $statsWidgetAll.find(".statOpen b").text(parseInt($statsWidgetAll.find(".statOpen b").text()) - 1)
            $statsWidgetCurrent.find(".statProgress b").text(parseInt($statsWidgetCurrent.find(".statProgress b").text()) + 1)
            $statsWidgetCurrent.find(".statOpen b").text(parseInt($statsWidgetCurrent.find(".statOpen b").text()) - 1)
        }
        if ($checkDone.hasClass( 'fa-check' )) {
            // done++, progress--
            $statsWidgetAll.find(".statDone b").text(parseInt($statsWidgetAll.find(".statDone b").text()) + 1)
            $statsWidgetAll.find(".statProgress b").text(parseInt($statsWidgetAll.find(".statProgress b").text()) - 1)
            $statsWidgetCurrent.find(".statDone b").text(parseInt($statsWidgetCurrent.find(".statDone b").text()) + 1)
            $statsWidgetCurrent.find(".statProgress b").text(parseInt($statsWidgetCurrent.find(".statProgress b").text()) - 1)
        }
    });
}

//------------------------------------------------
// SQL routines
//------------------------------------------------

//------------------------------------------------
// SQL reindex notes and lists DB routine
static #sqlReindexNotesAndLists(user_id) {
    var tab_id = $("#tabId").attr('data-tab');
    var project_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=reindexNotesAndLists&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&tab_id=' + tab_id;
    setTimeout(function() {
        $("#result" + project_id).html(_BoardNotes_Translations_.getSpinnerMsg('BoardNotes_JS_REINDEXING_MSG'));
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// Global routines
//------------------------------------------------

//------------------------------------------------
static attachAllHandlers() {
    _BoardNotes_Tabs_.#TabActionHandlers();
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _BoardNotes_Tabs_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Tabs_._dummy_ );
