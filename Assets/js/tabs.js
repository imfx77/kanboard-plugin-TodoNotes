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
    var tabHeight = $("#tabs li:eq(0)").outerHeight();
    var numTabs = $("#tabs li").length + 6; // add +6 because of the separator headers
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
static toggleTabGroup(group) {
    $("#hrGroup" + group).toggleClass( 'hideMe' );
    $("#group" + group).toggleClass( 'hideMe' );

    $("#toggleGroup" + group).find('i').toggleClass( "fa-chevron-circle-up" );
    $("#toggleGroup" + group).find('i').toggleClass( "fa-chevron-circle-down" );

    _BoardNotes_Tabs_.updateTabsContainer();
}

//------------------------------------------------
static handlersTabGroup(group) {
    $("#headerGroup" + group).dblclick(function() {
        _BoardNotes_Tabs_.toggleTabGroup(group);
    });

    $("#toggleGroup" + group).click(function() {
        _BoardNotes_Tabs_.toggleTabGroup(group);
    });
}

//------------------------------------------------
// Tabs Buttons handlers
//------------------------------------------------

//------------------------------------------------
static #TabActionHandlers() {
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

    //------------------------------------------------

    // toggle visibility of tab groups
    _BoardNotes_Tabs_.handlersTabGroup( 'Global' );
    _BoardNotes_Tabs_.handlersTabGroup( 'Private' );
    _BoardNotes_Tabs_.handlersTabGroup( 'Regular' );

    //------------------------------------------------

    // toggle visibility of tabs stats widgets
    $("button" + "#settingsTabStats").click(function() {
        _BoardNotes_.sqlToggleSessionOption('boardnotesShowTabStats');

        _BoardNotes_.optionShowTabStats = !_BoardNotes_.optionShowTabStats;
        $("#session_vars").attr('data-optionShowTabStats', _BoardNotes_.optionShowTabStats);

        _BoardNotes_Tabs_.updateTabStats();
    });

    // start DB optimization routine on system reindex button
    $("button" + "#reindexNotesAndLists").click(function() {
        var is_admin = $("#tabId").attr('data-admin');
        if (is_admin != "1") {
            alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        var user_id = $(this).attr('data-user');
        _BoardNotes_Tabs_.#modalReindexNotesAndLists(user_id);
    });

    //------------------------------------------------

    // create custom list (global or private)
    $("button" + "#customNoteListCreate").click(function() {
        var user_id = $(this).attr('data-user');
        _BoardNotes_Tabs_.#modalCreateCustomNoteList(user_id);
    });

    //------------------------------------------------

    // rename custom list (global)
    $("button" + ".customNoteListRenameGlobal").click(function() {
        var is_admin = $("#tabId").attr('data-admin');
        if (is_admin != "1") {
            alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        var user_id = $(this).attr('data-user');
        var project_id = $(this).attr('data-project');
        var default_name = $(this).closest('.singleTab').find('a').text();
        _BoardNotes_Tabs_.#modalRenameCustomNoteList(user_id, project_id, default_name);
    });

    // rename custom list (private)
    $("button" + ".customNoteListRenamePrivate").click(function() {
        var user_id = $(this).attr('data-user');
        var project_id = $(this).attr('data-project');
        var default_name = $(this).closest('.singleTab').find('a').text();
        _BoardNotes_Tabs_.#modalRenameCustomNoteList(user_id, project_id, default_name);
    });

    //------------------------------------------------

    // delete custom list (global)
    $("button" + ".customNoteListDeleteGlobal").click(function() {
        var is_admin = $("#tabId").attr('data-admin');
        if (is_admin != "1") {
            alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_DASHBOARD_NO_ADMIN_PRIVILEGES') );
            return;
        }

        var user_id = $(this).attr('data-user');
        var project_id = $(this).attr('data-project');
        _BoardNotes_Tabs_.#modalDeleteCustomNoteList(user_id, project_id);
    });

    // delete custom list (private)
    $("button" + ".customNoteListDeletePrivate").click(function() {
        var user_id = $(this).attr('data-user');
        var project_id = $(this).attr('data-project');
        _BoardNotes_Tabs_.#modalDeleteCustomNoteList(user_id, project_id);
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
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_REINDEX_BTN'),
                click : function() {
                    _BoardNotes_Tabs_.#sqlReindexNotesAndLists(user_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
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
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CREATE_BTN'),
                click : function() {
                    var custom_note_list_name = $("#nameCreateCustomNoteList").val().trim();
                    var custom_note_list_is_global = $("#globalCreateCustomNoteList").is(":checked");
                    _BoardNotes_Tabs_.#sqlCreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
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
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_RENAME_BTN'),
                click : function() {
                    var custom_note_list_name = $("#nameRenameCustomNoteList").val().trim();
                    _BoardNotes_Tabs_.#sqlRenameCustomNoteList(user_id, project_id, custom_note_list_name);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
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
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_DELETE_BTN'),
                click : function() {
                    _BoardNotes_Tabs_.#sqlDeleteCustomNoteList(user_id, project_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
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
    var project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesReindexNotesAndLists&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_BoardNotes_Translations_.getSpinnerMsg('BoardNotes_JS_REINDEXING_MSG'));
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL create custom note list
static #sqlCreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global) {
    if (!custom_note_list_name) {
        alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG') );
        return;
    }

    var project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesCreateCustomNoteList&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&custom_note_list_name=' + encodeURIComponent(custom_note_list_name)
                + '&custom_note_list_is_global=' + custom_note_list_is_global;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_BoardNotes_Translations_.msgLoadingSpinner);
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL rename custom note list
static #sqlRenameCustomNoteList(user_id, project_id, custom_note_list_name) {
    if (!custom_note_list_name) {
        alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG') );
        return;
    }

    var project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesRenameCustomNoteList&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id
                + '&custom_note_list_name=' + encodeURIComponent(custom_note_list_name);
    setTimeout(function() {
        $("#result" + project_tab_id).html(_BoardNotes_Translations_.msgLoadingSpinner);
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// SQL delete custom note list
static #sqlDeleteCustomNoteList(user_id, project_id) {
    var project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesDeleteCustomNoteList&plugin=BoardNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id;
    setTimeout(function() {
        $("#result" + project_tab_id).html(_BoardNotes_Translations_.msgLoadingSpinner);
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
