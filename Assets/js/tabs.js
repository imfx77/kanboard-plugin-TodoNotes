/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Tabs_');
//////////////////////////////////////////////////
class _TodoNotes_Tabs_ {

//------------------------------------------------
// Tabs routines
//------------------------------------------------

//------------------------------------------------
static UpdateTabs() {
    const project_tab_id = $("#tabId").attr('data-project');

    $(".singleTab").removeClass( 'active' );
    $("#singleTab-P" + project_tab_id).addClass( 'active' );
    $("#myNotesHeader h2").text(
        _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__DASHBOARD_MY_NOTES')
        + ' > ' + $("#singleTab-P" + project_tab_id + " a").text()
    );
}

//------------------------------------------------
static UpdateTabStats() {
    if (_TodoNotes_.optionShowTabStats) {
        $("#settingsTabStats").find("a i").addClass( 'buttonToggled' );
        $(".tabStatsWidget").removeClass( 'hideMe' );
    } else {
        $("#settingsTabStats").find("a i").removeClass( 'buttonToggled' );
        $(".tabStatsWidget").addClass( 'hideMe' );
    }

    _TodoNotes_Tabs_.#UpdateTabsContainer();
}

//------------------------------------------------
static #UpdateTabsContainer() {
    const tabHeight = $("#tabs li:eq(0)").outerHeight();

    const numListsGlobal = parseInt( $("#groupGlobal li").length );
    const numListsPrivate = parseInt( $("#groupPrivate li").length );
    const numListsRegular = parseInt( $("#groupRegular li").length );

    const numTabs = 1 + 3 * 1.5 // ALL tab + 3x group headers
        + ($("#groupGlobal").hasClass( 'accordionHide' ) ? -0.25 : numListsGlobal)     // conditional on groupGlobal visibility
        + ($("#groupPrivate").hasClass( 'accordionHide' ) ? -0.25 : numListsPrivate)   // conditional on groupPrivate visibility
        + ($("#groupRegular").hasClass( 'accordionHide' ) ? -0.25 : numListsRegular);  // conditional on groupRegular visibility

    $("#tabs").height(numTabs * tabHeight);
}

//------------------------------------------------
// Tabs Stats handlers
//------------------------------------------------

//------------------------------------------------
static #TabStatsHandlers() {
    // console.log('_TodoNotes_Tabs_.TabStatsHandlers');

    // update tabs stats widgets on clicking buttonStatus
    $("button" + ".buttonStatus").click(function() {
        // this handler would be called AFTER actual value switch of the checkmark
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');

        const noteCheckmark = $("#noteCheckmark-P" + project_id + "-" + id);
        const statsWidgetAll = $("#TodoNotes-StatsWidget-P0");
        const statsWidgetCurrent = $("#TodoNotes-StatsWidget-P" + project_id);

        if (noteCheckmark.attr('data-id') === '0') {
            // done++, progress--
            statsWidgetAll.find(".statDone b").text(parseInt(statsWidgetAll.find(".statDone b").text()) + 1)
            statsWidgetAll.find(".statProgress b").text(parseInt(statsWidgetAll.find(".statProgress b").text()) - 1)
            statsWidgetCurrent.find(".statDone b").text(parseInt(statsWidgetCurrent.find(".statDone b").text()) + 1)
            statsWidgetCurrent.find(".statProgress b").text(parseInt(statsWidgetCurrent.find(".statProgress b").text()) - 1)
        }
        if (noteCheckmark.attr('data-id') === '1') {
            // open++, done--
            statsWidgetAll.find(".statOpen b").text(parseInt(statsWidgetAll.find(".statOpen b").text()) + 1)
            statsWidgetAll.find(".statDone b").text(parseInt(statsWidgetAll.find(".statDone b").text()) - 1)
            statsWidgetCurrent.find(".statOpen b").text(parseInt(statsWidgetCurrent.find(".statOpen b").text()) + 1)
            statsWidgetCurrent.find(".statDone b").text(parseInt(statsWidgetCurrent.find(".statDone b").text()) - 1)
        }
        if (noteCheckmark.attr('data-id') === '2') {
            // progress++, open--
            statsWidgetAll.find(".statProgress b").text(parseInt(statsWidgetAll.find(".statProgress b").text()) + 1)
            statsWidgetAll.find(".statOpen b").text(parseInt(statsWidgetAll.find(".statOpen b").text()) - 1)
            statsWidgetCurrent.find(".statProgress b").text(parseInt(statsWidgetCurrent.find(".statProgress b").text()) + 1)
            statsWidgetCurrent.find(".statOpen b").text(parseInt(statsWidgetCurrent.find(".statOpen b").text()) - 1)
        }

        // update progress icons
        const statusAliasAll =  (statsWidgetAll.find(".statProgress b").text() !== '0') ? 'statusInProgress' : 'statusSuspended';
        statsWidgetAll.find(".statProgress i").addClass(statusAliasAll);
        const statusAliasCurrent =  (statsWidgetCurrent.find(".statProgress b").text() !== '0') ? 'statusInProgress' : 'statusSuspended';
        statsWidgetCurrent.find(".statProgress i").addClass(statusAliasCurrent);

        _TodoNotes_Statuses_.ExpandStatusAliases();
    });
}

//------------------------------------------------
// Tabs Action handlers
//------------------------------------------------

//------------------------------------------------
static #TabActionHandlers() {
    // toggle visibility of tabs stats widgets
    $("button" + "#settingsTabStats").click(function() {
        const user_id = $(this).attr('data-user');
        _TodoNotes_Requests_.ToggleSessionOption(-1 /* not used */, user_id, 'todonotesOption_ShowTabStats');

        _TodoNotes_.optionShowTabStats = !_TodoNotes_.optionShowTabStats;
        $("#session_vars").attr('data-optionShowTabStats', _TodoNotes_.optionShowTabStats);

        _TodoNotes_Tabs_.UpdateTabStats();
    });

    // start DB optimization routine on system reindex button
    $("button" + "#reindexNotesAndLists").click(function() {
        const isAdmin = $("#tabId").attr('data-admin');
        if (isAdmin !== '1') {
            alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        const user_id = $(this).attr('data-user');
        _TodoNotes_Modals_.ReindexNotesAndLists(user_id);
    });

    //------------------------------------------------

    // create custom list (global or private)
    $("button" + "#customNoteListCreate").click(function() {
        const user_id = $(this).attr('data-user');
        _TodoNotes_Modals_.CreateCustomNoteList(user_id);
    });

    //------------------------------------------------

    // rename custom list (global)
    $("button" + ".customNoteListRenameGlobal").click(function() {
        const isAdmin = $("#tabId").attr('data-admin');
        if (isAdmin !== '1') {
            alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        const default_name = $(this).closest('.singleTab').find('a').text();
        _TodoNotes_Modals_.RenameCustomNoteList(user_id, project_id, default_name);
    });

    // rename custom list (private)
    $("button" + ".customNoteListRenamePrivate").click(function() {
        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        const default_name = $(this).closest('.singleTab').find('a').text();
        _TodoNotes_Modals_.RenameCustomNoteList(user_id, project_id, default_name);
    });

    //------------------------------------------------

    // delete custom list (global)
    $("button" + ".customNoteListDeleteGlobal").click(function() {
        const isAdmin = $("#tabId").attr('data-admin');
        if (isAdmin !== '1') {
            alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        _TodoNotes_Modals_.DeleteCustomNoteList(user_id, project_id);
    });

    // delete custom list (private)
    $("button" + ".customNoteListDeletePrivate").click(function() {
        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        _TodoNotes_Modals_.DeleteCustomNoteList(user_id, project_id);
    });
}

//------------------------------------------------
// Tabs Groups routines & handlers
//------------------------------------------------

//------------------------------------------------
static #ToggleTabGroup(group) {
    $("#group" + group).toggleClass( 'accordionShow' );
    $("#group" + group).toggleClass( 'accordionHide' );
    $("#toggleGroup" + group).find('i').toggleClass( "fa-chevron-circle-up" );
    $("#toggleGroup" + group).find('i').toggleClass( "fa-chevron-circle-down" );
    setTimeout(function() {
        _TodoNotes_Tabs_.#UpdateTabsContainer();
    }, 300);
}

//------------------------------------------------
static #HandleTabGroup(group) {
    $("#headerGroup" + group).dblclick(function() {
        _TodoNotes_Tabs_.#ToggleTabGroup(group);
    });

    $("#toggleGroup" + group).click(function() {
        _TodoNotes_Tabs_.#ToggleTabGroup(group);
    });
}

//------------------------------------------------
static #TabGroupHandlers() {
    // disable click & dblclick propagation for all marked sub-elements
    $(".disableTabsEventsPropagation").click(function (/*event*/) {
        //event.stopPropagation();
    });

    $(".disableTabsEventsPropagation").dblclick(function (event) {
        event.stopPropagation();
    });

    //------------------------------------------------

    // toggle visibility of tab groups
    _TodoNotes_Tabs_.#HandleTabGroup( 'Global' );
    _TodoNotes_Tabs_.#HandleTabGroup( 'Private' );
    _TodoNotes_Tabs_.#HandleTabGroup( 'Regular' );
}

//------------------------------------------------
// Global routines
//------------------------------------------------

//------------------------------------------------
static AttachStatusUpdateHandlers() {
    // console.log('_TodoNotes_Tabs_.AttachStatusUpdateHandlers');

    _TodoNotes_Tabs_.#TabStatsHandlers();
}

//------------------------------------------------
static AttachTabHandlers() {
    // console.log('_TodoNotes_Tabs_.AttachTabHandlers');

    _TodoNotes_Tabs_.#TabActionHandlers();
    _TodoNotes_Tabs_.#TabGroupHandlers();
}

//------------------------------------------------
static AttachAllHandlers() {
    // console.log('_TodoNotes_Tabs_.AttachAllHandlers');

    _TodoNotes_Tabs_.#TabStatsHandlers();
    _TodoNotes_Tabs_.#TabActionHandlers();
    _TodoNotes_Tabs_.#TabGroupHandlers();
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Tabs_

//////////////////////////////////////////////////
_TodoNotes_Tabs_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
