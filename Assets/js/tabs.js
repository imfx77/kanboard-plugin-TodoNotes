class _BoardNotes_Tabs_ {

//------------------------------------------------
// Tabs Buttons handlers
//------------------------------------------------

//------------------------------------------------
static #TabActionHandlers() {

    // start DB optimization routine on system reindex button
    $("button" + ".reindexNotesAndLists").click(function() {
        var user_id = $(this).attr('data-user');
        _BoardNotes_Tabs_.#sqlReindexNotesAndLists(user_id);
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
        setTimeout(function() {
            location.replace(loadUrl);
        }, 10000);
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

} // class _BoardNotes_Tabs_

//////////////////////////////////////////////////
$(function() {
    // attach all handlers on load page
    _BoardNotes_Tabs_.attachAllHandlers();
});
