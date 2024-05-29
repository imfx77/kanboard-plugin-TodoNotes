/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Modals_');
//////////////////////////////////////////////////
class _TodoNotes_Modals_ {

//---------------------------------------------
// Notes related modal dialogs
//---------------------------------------------

//------------------------------------------------
static DeleteNote(project_id, user_id, id) {
    $("#dialogDeleteNote").removeClass( 'hideMe' );
    $("#dialogDeleteNote").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click: function() {
                    _TodoNotes_Requests_.DeleteNote(project_id, user_id, id);
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static DeleteAllDoneNotes(project_id, user_id) {
    $("#dialogDeleteAllDone").removeClass( 'hideMe' );
    $("#dialogDeleteAllDone").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click: function() {
                    _TodoNotes_Requests_.DeleteAllDoneNotes(project_id, user_id);
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static TransferNote(project_id, user_id, id) {
    $("#dialogTransferNote-P" + project_id).removeClass( 'hideMe' );
    $("#dialogTransferNote-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_MOVE_BTN'),
                click : function() {
                    const target_project_id = $("#listNoteProject-P" + project_id + " option:selected").val();
                    _TodoNotes_Requests_.TransferNote(project_id, user_id, id, target_project_id);
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static CreateTaskFromNote(project_id, user_id, id, is_active, title, description, category_id) {
    $.ajaxSetup ({
        cache: false
    });
    $("#dialogCreateTaskParams").removeClass( 'hideMe' );
    $("#deadloading").addClass( 'hideMe' );
    $("#listCatCreateTask-P" + project_id).val(category_id).change();
    $("#dialogCreateTaskFromNote-P" + project_id).removeClass( 'hideMe' );
    $("#dialogCreateTaskFromNote-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
                click: function() {
                    const categoryCreateTask = $("#listCatCreateTask-P" + project_id + " option:selected").val();
                    const columnCreateTask = $("#listColCreateTask-P" + project_id + " option:selected").val();
                    const swimlaneCreateTask = $("#listSwimCreateTask-P" + project_id + " option:selected").val();
                    const removeNote = $("#removeNote-P" + project_id).is(":checked");

                    const loadUrl = '/?controller=TodoNotesController&action=CreateTaskFromNote&plugin=TodoNotes'
                                + '&project_custom_id=' + project_id
                                + '&user_id=' + user_id
                                + '&task_title=' + encodeURIComponent(title)
                                + '&task_description=' + encodeURIComponent(description)
                                + '&category_id=' + categoryCreateTask
                                + '&column_id=' + columnCreateTask
                                + '&swimlane_id=' + swimlaneCreateTask;

                    $("#dialogCreateTaskFromNote-P" + project_id).dialog({
                        title: _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_RESULT_TITLE'),
                        buttons: [
                            {
                                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CLOSE_BTN'),
                                click: function() { $( this ).dialog( "close" ); }
                            },
                        ]
                    });
                    $("#dialogCreateTaskParams").addClass( 'hideMe' );
                    $("#deadloading").removeClass( 'hideMe' );
                    $("#deadloading").html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl);
                    if (removeNote) {
                        _TodoNotes_Requests_.DeleteNote(project_id, user_id, id);
                        _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                        _TodoNotes_Requests_.RefreshTabs(user_id);
                    }
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static NotificationsSetup(project_id, user_id, id, notifications_alert_timestring) {
    $.ajaxSetup ({
        cache: false
    });
    $("#form-alerttimeNotificationsSetup-P" + project_id).val(notifications_alert_timestring);
    $("#dialogNotificationsSetup-P" + project_id).removeClass( 'hideMe' );
    $("#dialogNotificationsSetup-P" + project_id).dialog({
        resizable: false,
        width: "auto",
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_SET_BTN'),
                click: function() {
                    const new_notifications_alert_timestring = $("#form-alerttimeNotificationsSetup-P" + project_id).val();
                    _TodoNotes_Requests_.UpdateNoteNotificationsAlertTime(project_id, user_id, id, new_notifications_alert_timestring);
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_RESET_BTN'),
                click: function() {
                    _TodoNotes_Requests_.UpdateNoteNotificationsAlertTime(project_id, user_id, id, ''); // empty timestring
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return false;
}

//---------------------------------------------
// Stats & Report modal dialogs
//---------------------------------------------

//------------------------------------------------
static Stats(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=TodoNotesController&action=ShowStats&plugin=TodoNotes'
                + '&project_custom_id=' + project_id
                + '&user_id=' + user_id;
    $("#dialogStatsInside").html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl,
        function() {
            _TodoNotes_Stats_.prepareDocument();
        });

    $("#dialogStats").removeClass( 'hideMe' );
    $("#dialogStats").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CLOSE_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static Report(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    $("#dialogReport-P" + project_id).removeClass( 'hideMe' );
    $("#dialogReport-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
                click: function() {
                    const category = $("#catReport-P" + project_id + " option:selected").text();
                    const loadUrl = "/?controller=TodoNotesController&action=ShowReport&plugin=TodoNotes"
                                + "&project_custom_id=" + project_id
                                + "&user_id=" + user_id
                                + "&category=" + encodeURIComponent(category);
                    $("#result" + project_id).html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl,
                        function() {
                            _TodoNotes_Report_.prepareDocument();
                        });
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return true;
}

//---------------------------------------------
// Dashboard system modal dialogs
//---------------------------------------------

//------------------------------------------------
static ReindexNotesAndLists(user_id,) {
    $("#dialogReindexNotesAndLists").removeClass( 'hideMe' );
    $("#dialogReindexNotesAndLists").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_REINDEX_BTN'),
                click : function() {
                    _TodoNotes_Requests_.ReindexNotesAndLists(user_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//---------------------------------------------
// Lists related modal dialogs
//---------------------------------------------

//------------------------------------------------
static CreateCustomNoteList(user_id) {
    $("#dialogCreateCustomNoteList").removeClass( 'hideMe' );
    $("#dialogCreateCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
                click : function() {
                    const custom_note_list_name = $("#nameCreateCustomNoteList").val().trim();
                    const custom_note_list_is_global = $("#globalCreateCustomNoteList").is(":checked");
                    _TodoNotes_Requests_.CreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static RenameCustomNoteList(user_id, project_id, default_name) {
    $("#nameRenameCustomNoteList").val(default_name);
    $("#dialogRenameCustomNoteList").removeClass( 'hideMe' );
    $("#dialogRenameCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_RENAME_BTN'),
                click : function() {
                    const custom_note_list_name = $("#nameRenameCustomNoteList").val().trim();
                    _TodoNotes_Requests_.RenameCustomNoteList(user_id, project_id, custom_note_list_name);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static DeleteCustomNoteList(user_id, project_id) {
    $("#dialogDeleteCustomNoteList").removeClass( 'hideMe' );
    $("#dialogDeleteCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click : function() {
                    _TodoNotes_Requests_.DeleteCustomNoteList(user_id, project_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static ReorderCustomNoteList(user_id, order) {
    $("#dialogReorderCustomNoteList").removeClass( 'hideMe' );
    $("#dialogReorderCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_REORDER_BTN'),
                click : function() {
                    _TodoNotes_Requests_.UpdateCustomNoteListsPositions(user_id, order);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Modals_

//////////////////////////////////////////////////
_TodoNotes_Modals_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
