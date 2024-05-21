/**
 * @author  Im[F(x)]
 */

class _TodoNotes_Tabs_ {

//------------------------------------------------
// Tabs routines
//------------------------------------------------

//------------------------------------------------
static updateTabs() {
    const project_tab_id = $("#tabId").attr('data-project');

    $(".singleTab").removeClass( 'active' );
    $("#singleTab-P" + project_tab_id).addClass( 'active' );
    $("#myNotesHeader h2").text(
        _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_MY_NOTES')
        + ' > ' + $("#singleTab-P" + project_tab_id + " a").text()
    );
}

//------------------------------------------------
static updateTabsContainer() {
    const tabHeight = $("#tabs li:eq(0)").outerHeight();

    const numListsGlobal = parseInt( $("#groupGlobal li").length );
    const numListsPrivate = parseInt( $("#groupPrivate li").length );
    const numListsRegular = parseInt( $("#groupRegular li").length );

    const numTabs = 1 + 3 + 3 // ALL tab + 3x group headers + 3x pairs of <hr>
        + ($("#groupGlobal").hasClass( 'hideMe' ) ? -0.5 : numListsGlobal)     // conditional on groupGlobal visibility
        + ($("#groupPrivate").hasClass( 'hideMe' ) ? -0.5 : numListsPrivate)   // conditional on groupPrivate visibility
        + ($("#groupRegular").hasClass( 'hideMe' ) ? -0.5 : numListsRegular);  // conditional on groupRegular visibility

    $("#tabs").height(numTabs * tabHeight);
}

//------------------------------------------------
static updateTabStats() {
    if (_TodoNotes_.optionShowTabStats) {
        $("#settingsTabStats").find("a i").addClass( 'buttonToggled' );
        $(".tabStatsWidget").removeClass( 'hideMe' );
    } else {
        $("#settingsTabStats").find("a i").removeClass( 'buttonToggled' );
        $(".tabStatsWidget").addClass( 'hideMe' );
    }

    _TodoNotes_Tabs_.updateTabsContainer();
}

//------------------------------------------------
static toggleTabGroup(group) {
    $("#hrGroup" + group).toggleClass( 'hideMe' );
    $("#group" + group).toggleClass( 'hideMe' );

    $("#toggleGroup" + group).find('i').toggleClass( "fa-chevron-circle-up" );
    $("#toggleGroup" + group).find('i').toggleClass( "fa-chevron-circle-down" );

    _TodoNotes_Tabs_.updateTabsContainer();
}

//------------------------------------------------
static handlersTabGroup(group) {
    $("#headerGroup" + group).dblclick(function() {
        _TodoNotes_Tabs_.toggleTabGroup(group);
    });

    $("#toggleGroup" + group).click(function() {
        _TodoNotes_Tabs_.toggleTabGroup(group);
    });
}

//------------------------------------------------
// Tabs Buttons handlers
//------------------------------------------------

//------------------------------------------------
static #TabActionHandlers() {
    // update tabs stats widgets on clicking buttonStatus
    $("button" + ".buttonStatus").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');

        const noteCheckmark = $("#noteCheckmark-P" + project_id + "-" + id);
        const statsWidgetAll = $("#BoardNotes-StatsWidget-P0");
        const statsWidgetCurrent = $("#BoardNotes-StatsWidget-P" + project_id);

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

        _TodoNotes_Statuses_.expandStatusAliases();
    });

    //------------------------------------------------

    // toggle visibility of tab groups
    _TodoNotes_Tabs_.handlersTabGroup( 'Global' );
    _TodoNotes_Tabs_.handlersTabGroup( 'Private' );
    _TodoNotes_Tabs_.handlersTabGroup( 'Regular' );

    //------------------------------------------------

    // toggle visibility of tabs stats widgets
    $("button" + "#settingsTabStats").click(function() {
        _TodoNotes_.sqlToggleSessionOption('boardnotesShowTabStats');

        _TodoNotes_.optionShowTabStats = !_TodoNotes_.optionShowTabStats;
        $("#session_vars").attr('data-optionShowTabStats', _TodoNotes_.optionShowTabStats);

        _TodoNotes_Tabs_.updateTabStats();
    });

    // start DB optimization routine on system reindex button
    $("button" + "#reindexNotesAndLists").click(function() {
        const isAdmin = $("#tabId").attr('data-admin');
        if (isAdmin !== '1') {
            alert( _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        const user_id = $(this).attr('data-user');
        _TodoNotes_Tabs_.#modalReindexNotesAndLists(user_id);
    });

    //------------------------------------------------

    // create custom list (global or private)
    $("button" + "#customNoteListCreate").click(function() {
        const user_id = $(this).attr('data-user');
        _TodoNotes_Tabs_.#modalCreateCustomNoteList(user_id);
    });

    //------------------------------------------------

    // rename custom list (global)
    $("button" + ".customNoteListRenameGlobal").click(function() {
        const isAdmin = $("#tabId").attr('data-admin');
        if (isAdmin !== '1') {
            alert( _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        const default_name = $(this).closest('.singleTab').find('a').text();
        _TodoNotes_Tabs_.#modalRenameCustomNoteList(user_id, project_id, default_name);
    });

    // rename custom list (private)
    $("button" + ".customNoteListRenamePrivate").click(function() {
        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        const default_name = $(this).closest('.singleTab').find('a').text();
        _TodoNotes_Tabs_.#modalRenameCustomNoteList(user_id, project_id, default_name);
    });

    //------------------------------------------------

    // delete custom list (global)
    $("button" + ".customNoteListDeleteGlobal").click(function() {
        const isAdmin = $("#tabId").attr('data-admin');
        if (isAdmin !== '1') {
            alert( _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        _TodoNotes_Tabs_.#modalDeleteCustomNoteList(user_id, project_id);
    });

    // delete custom list (private)
    $("button" + ".customNoteListDeletePrivate").click(function() {
        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        _TodoNotes_Tabs_.#modalDeleteCustomNoteList(user_id, project_id);
    });
}

//------------------------------------------------
// Modal Dialogs routines
//------------------------------------------------

//------------------------------------------------
static #modalReindexNotesAndLists(user_id,) {
    $("#dialogReindexNotesAndLists").removeClass( 'hideMe' );
    $("#dialogReindexNotesAndLists").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_REINDEX_BTN'),
                click : function() {
                    _TodoNotes_Tabs_.#sqlReindexNotesAndLists(user_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static #modalCreateCustomNoteList(user_id) {
    $("#dialogCreateCustomNoteList").removeClass( 'hideMe' );
    $("#dialogCreateCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CREATE_BTN'),
                click : function() {
                    const custom_note_list_name = $("#nameCreateCustomNoteList").val().trim();
                    const custom_note_list_is_global = $("#globalCreateCustomNoteList").is(":checked");
                    _TodoNotes_Tabs_.#sqlCreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static #modalRenameCustomNoteList(user_id, project_id, default_name) {
    $("#nameRenameCustomNoteList").val(default_name);
    $("#dialogRenameCustomNoteList").removeClass( 'hideMe' );
    $("#dialogRenameCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_RENAME_BTN'),
                click : function() {
                    const custom_note_list_name = $("#nameRenameCustomNoteList").val().trim();
                    _TodoNotes_Tabs_.#sqlRenameCustomNoteList(user_id, project_id, custom_note_list_name);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static #modalDeleteCustomNoteList(user_id, project_id) {
    $("#dialogDeleteCustomNoteList").removeClass( 'hideMe' );
    $("#dialogDeleteCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_DELETE_BTN'),
                click : function() {
                    _TodoNotes_Tabs_.#sqlDeleteCustomNoteList(user_id, project_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static modalReorderCustomNoteList(user_id, order) {
    $("#dialogReorderCustomNoteList").removeClass( 'hideMe' );
    $("#dialogReorderCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_REORDER_BTN'),
                click : function() {
                    _TodoNotes_Tabs_.#sqlUpdateCustomNoteListsPositions(user_id, order);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                    _TodoNotes_.sqlRefreshTabs(user_id);
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
// SQL routines
//------------------------------------------------

//------------------------------------------------
// SQL reindex notes and lists DB routine
static #sqlReindexNotesAndLists(user_id) {
    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=BoardNotesController&action=ReindexNotesAndLists&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_TodoNotes_Translations_.getSpinnerMsg('BoardNotes_JS_REINDEXING_MSG'));
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL create custom note list
static #sqlCreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global) {
    if (!custom_note_list_name) {
        alert( _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG') );
        return;
    }

    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=BoardNotesController&action=CreateCustomNoteList&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&custom_note_list_name=' + encodeURIComponent(custom_note_list_name)
                + '&custom_note_list_is_global=' + custom_note_list_is_global;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_TodoNotes_Translations_.msgLoadingSpinner);
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL rename custom note list
static #sqlRenameCustomNoteList(user_id, project_id, custom_note_list_name) {
    if (!custom_note_list_name) {
        alert( _TodoNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG') );
        return;
    }

    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=BoardNotesController&action=RenameCustomNoteList&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id
                + '&custom_note_list_name=' + encodeURIComponent(custom_note_list_name);
    setTimeout(function() {
        $("#result" + project_tab_id).html(_TodoNotes_Translations_.msgLoadingSpinner);
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL delete custom note list
static #sqlDeleteCustomNoteList(user_id, project_id) {
    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=BoardNotesController&action=DeleteCustomNoteList&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_TodoNotes_Translations_.msgLoadingSpinner);
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL update custom note lists positions
static #sqlUpdateCustomNoteListsPositions(user_id, order) {
    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=BoardNotesController&action=UpdateCustomNoteListsPositions&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&order=' + order;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_TodoNotes_Translations_.msgLoadingSpinner);
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// Global routines
//------------------------------------------------

//------------------------------------------------
static attachAllHandlers() {
    _TodoNotes_Tabs_.#TabActionHandlers();
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Tabs_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Tabs_._dummy_ );
