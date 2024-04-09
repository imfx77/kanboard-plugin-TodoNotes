let _BoardNotes_Dashboard_ = {}; // namespace

_BoardNotes_Dashboard_.updateProjectsStats_OnCheckDoneHandler = function() {
    $("button" + ".checkDone").click(function() {
        var project_id = $(this).attr('data-project');
        //var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');

        $checkDone = $("#noteDoneCheckmark-P" + project_id + "-" + id);
        $statsWidgetAll = $("#BoardNotes-StatsWidget-P0");
        $statsWidgetCurrent = $("#BoardNotes-StatsWidget-P" + project_id);

        // cycle through states
        if ($checkDone.hasClass( "fa-circle-thin" )) {
            // open++, done--
            $statsWidgetAll.find(".statOpen b").text(parseInt($statsWidgetAll.find(".statOpen b").text()) + 1)
            $statsWidgetAll.find(".statDone b").text(parseInt($statsWidgetAll.find(".statDone b").text()) - 1)
            $statsWidgetCurrent.find(".statOpen b").text(parseInt($statsWidgetCurrent.find(".statOpen b").text()) + 1)
            $statsWidgetCurrent.find(".statDone b").text(parseInt($statsWidgetCurrent.find(".statDone b").text()) - 1)
        }
        if ($checkDone.hasClass( "fa-spinner fa-pulse" )) {
            // progress++, open--
            $statsWidgetAll.find(".statProgress b").text(parseInt($statsWidgetAll.find(".statProgress b").text()) + 1)
            $statsWidgetAll.find(".statOpen b").text(parseInt($statsWidgetAll.find(".statOpen b").text()) - 1)
            $statsWidgetCurrent.find(".statProgress b").text(parseInt($statsWidgetCurrent.find(".statProgress b").text()) + 1)
            $statsWidgetCurrent.find(".statOpen b").text(parseInt($statsWidgetCurrent.find(".statOpen b").text()) - 1)
        }
        if ($checkDone.hasClass( "fa-check" )) {
            // done++, progress--
            $statsWidgetAll.find(".statDone b").text(parseInt($statsWidgetAll.find(".statDone b").text()) + 1)
            $statsWidgetAll.find(".statProgress b").text(parseInt($statsWidgetAll.find(".statProgress b").text()) - 1)
            $statsWidgetCurrent.find(".statDone b").text(parseInt($statsWidgetCurrent.find(".statDone b").text()) + 1)
            $statsWidgetCurrent.find(".statProgress b").text(parseInt($statsWidgetCurrent.find(".statProgress b").text()) - 1)
        }
    });
}

_BoardNotes_Dashboard_.updateNotesTabs = function() {
    var tab_id = $("#tab_id").attr('data');

    $(".singleTab").removeClass( 'active' );
    $("#singleTab" + tab_id).addClass( 'active' );
    $("#myNotesHeader h2").text(
        get_BoardNotes_Translations().getTranslationExportToJS('BoardNotes_DASHBOARD_MY_NOTES')
        + ' > ' + $("#singleTab" + tab_id + " a").text()
    );

    var numTabs = $("#tabs li").length + 1; // add +1 because of the separator lines
    var tabHeight = $("#tabs li:eq(0)").outerHeight();
    $("#tabs").height(numTabs * tabHeight);
}

_BoardNotes_Dashboard_.prepareDocument = function() {
    var isMobile = _BoardNotes_.isMobile();

    if(isMobile) {
        // choose mobile view
        $("#mainholderDashboard").removeClass('mainholderDashboard').addClass('mainholderMobileDashboard');
    }

    _BoardNotes_Dashboard_.updateProjectsStats_OnCheckDoneHandler();

    _BoardNotes_Dashboard_.updateNotesTabs();
}
