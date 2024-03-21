function updateProjectsStats_OnCheckDoneHandler() {
    $( "button" + ".checkDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');

      $checkDone = $("#noteDoneCheckmarkP" + project_id + "-" + id);
      $statsWidgetAll = $("#BoarNotes_StatsWidget_P0");
      $statsWidgetCurrent = $("#BoarNotes_StatsWidget_P" + project_id);

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

function updateNotesTabs() {
    var tab_id = $( "#tab_id" ).attr('data');

    $( ".singleTab" ).removeClass( 'active' );
    $( "#singleTab" + tab_id ).addClass( 'active' );
    $( "#myNotesHeader h2" ).text( 'My notes > ' + $( "#singleTab" + tab_id + " a" ).text());

    var numTabs = $( "#tabs li" ).length + 1; // add +1 because of the separator lines
    var tabHeight = $( "#tabs li:eq(0)" ).outerHeight();
    $( "#tabs" ).height(numTabs * tabHeight);

    // TODO: reload stats data for all tabs !!!
}

function prepareDocumentForDashboard() {
    var isMobile = IsMobile();

    if(isMobile) {
        // choose mobile view
        $('#mainholderDashboard').removeClass('mainholderDashboard').addClass('mainholderMobileDashboard');
    }

    updateProjectsStats_OnCheckDoneHandler();

    updateNotesTabs();
}
