let _BoardNotes_Dropdown_ = {}; // namespace

_BoardNotes_Dropdown_.refreshStatsWidget = function() {
    // refresh ONLY if the opened dropdown has stats widget and it is visible
    if ( ($(".BoardNotes_ProjectDropdown_StatsWidget").length == 2) &&
         ($(".BoardNotes_ProjectDropdown_StatsWidget:last").is(":visible")) ) {

        var project_id = $(".BoardNotes_ProjectDropdown_StatsWidget:last").attr('data-project');

        // don't cache ajax or content won't be fresh
        $.ajaxSetup ({
          cache: false
        });
        var ajax_load = '<i class="fa fa-refresh fa-spin" aria-hidden="true" '
                    + 'alt="' + _BoardNotes_.getTranslationExportToJS('BoardNotes_JS_LOADING_MSG') + '"></i>';
        var loadUrl = '/?controller=BoardNotesController&action=boardNotesRefreshStatsWidget&plugin=BoardNotes'
                    + '&stats_project_id=' + project_id;
        $(".BoardNotes_ProjectDropdown_StatsWidget:last").html(ajax_load).load(loadUrl);
    }

    // re-schedule
    setTimeout(function() {
        _BoardNotes_Dropdown_.refreshStatsWidget();
    }, 15 * 1000); // 15 sec
}

_BoardNotes_Dropdown_.prepareDocument = function() {
    _BoardNotes_Dropdown_.refreshStatsWidget();
}

$( document ).ready( _BoardNotes_Dropdown_.prepareDocument );
