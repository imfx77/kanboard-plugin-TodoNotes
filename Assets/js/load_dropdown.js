function prepareDocumentFor_BoardNotes_ProjectDropdown() {

    function BoardNotes_ProjectDropdown_StatsWidget_Refresh() {
        if ( ($(".BoardNotes_ProjectDropdown_StatsWidget").length === 2) &&        // ONLY if the opened dropdown has stats widget
             ($(".BoardNotes_ProjectDropdown_StatsWidget:last").is(":visible")) ) { // ONLY if the stats widget is visible

            var project_id = $(".BoardNotes_ProjectDropdown_StatsWidget:last").attr('data-project');

            // don't cache ajax or content won't be fresh
            $.ajaxSetup ({
              cache: false
            });
            var ajax_load = '<i class="fa fa-refresh fa-spin" aria-hidden="true" alt="loading..."></i>';
            var loadUrl = '/?controller=BoardNotesController&action=boardNotesRefreshStatsWidget&plugin=BoardNotes'
                        + '&stats_project_id=' + project_id;
            $(".BoardNotes_ProjectDropdown_StatsWidget:last").html(ajax_load).load(loadUrl);
        }

        setTimeout(function() {
            BoardNotes_ProjectDropdown_StatsWidget_Refresh();
        }, 15 * 1000); // 15 sec
    }

    BoardNotes_ProjectDropdown_StatsWidget_Refresh();
}

$( document ).ready( prepareDocumentFor_BoardNotes_ProjectDropdown );