/**
 * @author  Im[F(x)]
 */

class _BoardNotes_Dropdown_ {

//------------------------------------------------
static #refreshStatsWidget() {
    // refresh ONLY if the opened dropdown has stats widget and it is visible
    if ( ($(".BoardNotes_ProjectDropdown_StatsWidget").length === 2) &&
         ($(".BoardNotes_ProjectDropdown_StatsWidget:last").is(":visible")) ) {

        const project_id = $(".BoardNotes_ProjectDropdown_StatsWidget:last").attr('data-project');

        // don't cache ajax or content won't be fresh
        $.ajaxSetup ({
          cache: false
        });
        const loadUrl = '/?controller=BoardNotesController&action=RefreshStatsWidget&plugin=BoardNotes'
                    + '&stats_project_id=' + project_id;
        $(".BoardNotes_ProjectDropdown_StatsWidget:last").html(_BoardNotes_Translations_.msgLoadingSpinner).load(loadUrl);
    }

    // re-schedule
    setTimeout(function() {
        _BoardNotes_Dropdown_.#refreshStatsWidget();
    }, 15 * 1000); // 15 sec
}

//------------------------------------------------
static prepareDocument() {
    _BoardNotes_Translations_.initialize();

    _BoardNotes_Dropdown_.#refreshStatsWidget();
}

//------------------------------------------------

} // class _BoardNotes_Dropdown_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Dropdown_.prepareDocument );
