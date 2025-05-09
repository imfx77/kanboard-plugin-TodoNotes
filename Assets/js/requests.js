/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Requests_');
//////////////////////////////////////////////////
class _TodoNotes_Requests_ {

//------------------------------------------------
// Notes related requests
//------------------------------------------------

//------------------------------------------------
static MoveNoteToArchive(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=MoveNoteToArchive&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.MoveNoteToArchive');
            alert(e);
        }
    });
}

//------------------------------------------------
static MoveAllDoneNotesToArchive(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=MoveAllDoneNotesToArchive&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.MoveAllDoneNotesToArchive');
            alert(e);
        }
    });
}

//------------------------------------------------
static RestoreNoteFromArchive(project_id, user_id, id, target_project_id) {
    const archived_note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=RestoreNoteFromArchive&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&archived_note_id=' + archived_note_id
            + '&target_project_id=' + target_project_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.RestoreNoteFromArchive');
            alert(e);
        }
    });
}

//------------------------------------------------
static DeleteNoteFromArchive(project_id, user_id, id) {
    const archived_note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=DeleteNoteFromArchive&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&archived_note_id=' + archived_note_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.DeleteNoteFromArchive');
            alert(e);
        }
    });
}

//------------------------------------------------
static AddNote(project_id, user_id) {
    const title = $("#inputNewNote").val().trim();
    const description = $('[name="editorMarkdownDetailsNewNote"]').val();
    const category = $("#catNewNote" + " option:selected").text();
    const is_active = "1";

    if (!title) {
        alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_NOTE_ADD_TITLE_EMPTY_MSG') );
        return;
    }

    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=AddNote&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
        success: function() {
            _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
            _TodoNotes_Requests_.RefreshTabs(user_id);
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.AddNote');
            alert(e);
        }
    });
}

//------------------------------------------------
static DeleteNote(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=DeleteNote&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.DeleteNote');
            alert(e);
        }
    });
}

//------------------------------------------------
static DeleteAllDoneNotes(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=DeleteAllDoneNotes&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.DeleteAllDoneNotes');
            alert(e);
        }
    });
}

//------------------------------------------------
// note update (title, description, category and status)
static UpdateNote(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    let title = $("#noteTitleInput-P" + project_id + "-" + id).val().trim();
    const description = $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').val();
    const category = $("#cat-P" + project_id + "-" + id + " option:selected").text();
    const is_active = $("#noteCheckmark-P" + project_id + "-" + id).attr('data-id');

    if (!title) {
        alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_TITLE_EMPTY_MSG') );
        title = $("#noteTitleLabel-P" + project_id + "-" + id).html();
        $("#noteTitleInput-P" + project_id + "-" + id).val(title);
    }
    $("#noteTitleLabel-P" + project_id + "-" + id).html(title);

    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=UpdateNote&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
        success: function(response) {
            const lastModified = JSON.parse(response);
            if (lastModified.timestamp > 0) {
                _TodoNotes_.UpdateNoteTimestamps(lastModified, project_id, id);
                _TodoNotes_.UpdateNoteUserInfo(lastModified, project_id, user_id, id);
                _TodoNotes_.RefreshNoteNotificationsState(project_id, id);
                // refresh and render the details markdown preview
                $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").html(_TodoNotes_Translations_.msgLoadingSpinner);
                $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").load(
                    _TodoNotes_Settings_.baseAppDir
                        + '?controller=TodoNotesController&action=RefreshMarkdownPreviewWidget&plugin=TodoNotes'
                        + '&markdown_text=' + encodeURIComponent(description),
                ).css('height', 'auto');
            } else {
                alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_INVALID_MSG') );
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                _TodoNotes_Requests_.RefreshTabs(user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNote');
            alert(e);
        }
    });
}

//------------------------------------------------
// note update Status only!
static UpdateNoteStatus(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    const is_active = $("#noteCheckmark-P" + project_id + "-" + id).attr('data-id');

    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=UpdateNoteStatus&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&is_active=' + is_active,
        success: function(response) {
            const lastModified = JSON.parse(response);
            if (lastModified.timestamp > 0) {
                _TodoNotes_.UpdateNoteTimestamps(lastModified, project_id, id);
                _TodoNotes_.UpdateNoteUserInfo(lastModified, project_id, user_id, id);
                _TodoNotes_.RefreshNoteNotificationsState(project_id, id);
            } else {
                alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_INVALID_MSG') );
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                _TodoNotes_Requests_.RefreshTabs(user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNoteStatus');
            alert(e);
        }
    });
}

//------------------------------------------------
// note update Notifications Alert Time and Options
static UpdateNoteNotificationsAlertTimeAndOptions(project_id, user_id, id, notifications_alert_timestring, notification_options_bitflags) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');

    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=UpdateNoteNotificationsAlertTimeAndOptions&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&notifications_alert_timestring=' + encodeURIComponent(notifications_alert_timestring)
            + '&notification_options_bitflags=' + notification_options_bitflags,
        success: function(response) {
            const notificationsAlertTimeAndOptions = JSON.parse(response);
            if (notificationsAlertTimeAndOptions.timestamp >= 0) {
                _TodoNotes_.RefreshNoteNotificationsAlertTimeAndOptions(notificationsAlertTimeAndOptions, project_id, id);
                _TodoNotes_.RefreshNoteNotificationsState(project_id, id);
            } else {
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNoteNotificationsAlertTimeAndOptions');
            alert(e);
        }
    });
}

//------------------------------------------------
// note test All Notification Alerts
static TestNoteNotifications(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');

    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesNotificationsController&action=TestNoteNotifications&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id,
        success: function(response) {
            console.log(response);
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.TestNoteNotifications');
            alert(e);
        }
    });
}

//------------------------------------------------
// update a WebPN subscription for user (if changed or missing)
static UpdateWebPNSubscription(user_id, subscription) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesNotificationsController&action=UpdateWebPNSubscription&plugin=TodoNotes'
            + '&user_id=' + user_id
            + '&webpn_subscription=' + encodeURIComponent(JSON.stringify(subscription)),
        success: function(/*response*/) {
            // console.log(response);
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateWebPNSubscription');
            alert(e);
        }
    });
}

//------------------------------------------------
// update notes positions
static UpdateNotesPositions(project_id, user_id, order) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=UpdateNotesPositions&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&order=' + order,
        success: function(response) {
            const lastModified = JSON.parse(response);
            if (lastModified.timestamp > 0) {
                _TodoNotes_.UpdateAllNotesTimestamps(lastModified, project_id);
                _TodoNotes_.UpdateAllNotesUserInfo(lastModified, project_id, user_id);
            } else {
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNotesPositions');
            alert(e);
        }
    });
}

//------------------------------------------------
// note transfer (to another project)
static TransferNote(project_id, user_id, id, target_project_id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=TransferNote&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&target_project_id=' + target_project_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.TransferNote');
            alert(e);
        }
    });
}

//------------------------------------------------
static RefreshNotes(project_id, user_id) {
    // console.log('_TodoNotes_Requests_.RefreshNotes()');

    $("#result" + project_id).html(_TodoNotes_Translations_.msgLoadingSpinner);

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=RefreshProject&plugin=TodoNotes'
                + '&project_custom_id=' + project_id
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#result" + project_id).load(loadUrl,
            function() {
                _TodoNotes_.InitializeLocalTimeOffset();
                _TodoNotes_Project_.prepareDocument_SkipDashboardHandlers();
                _TodoNotes_Tabs_.AttachStatusUpdateHandlers();
            });
    }, 100);
}

//------------------------------------------------
// Lists related requests
//------------------------------------------------

//------------------------------------------------
// reindex notes and lists DB routine
static ReindexNotesAndLists(user_id) {
    const project_id = $("#tabId").attr('data-project');
    $("#result" + project_id).html(_TodoNotes_Translations_.GetSpinnerMsg('TodoNotes__JS_REINDEXING_MSG'));

    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=ReindexNotesAndLists&plugin=TodoNotes'
            + '&user_id=' + user_id,
        success: function() {
            location.reload();
        }
    });

    let lastReindexProgress = '';
    function RefreshReindexProgress(){
        $.ajax({
            cache: false,
            type: "POST",
            url: _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=RefreshReindexProgress&plugin=TodoNotes',
            success: function(progress) {
                const elem = $("#result" + project_id + " span");
                if(progress != '#') { // complete mark
                    if (progress != lastReindexProgress) {
                        elem.html(elem.html().replace('fa fa-cog fa-spin', 'fa fa-check-square-o'));
                        elem.html(elem.html() + '<br> => <i class="fa fa-cog fa-spin" aria-hidden="true"></i> ' + progress);
                        lastReindexProgress = progress;
                    }
                    RefreshReindexProgress();
                } else {
                    elem.html(elem.html().replace('fa fa-cog fa-spin', 'fa fa-check-square-o'));
                }
            }
        });
    }
    setTimeout(function() {
        RefreshReindexProgress();
    }, 50);
}

//------------------------------------------------
// create custom note list
static CreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global) {
    if (!custom_note_list_name) {
        alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG') );
        return;
    }

    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=CreateCustomNoteList&plugin=TodoNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&custom_note_list_name=' + encodeURIComponent(custom_note_list_name)
                + '&custom_note_list_is_global=' + custom_note_list_is_global;
    setTimeout(function() {
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// rename custom note list
static RenameCustomNoteList(user_id, project_id, custom_note_list_name) {
    if (!custom_note_list_name) {
        alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_CUSTOM_NOTE_LIST_NAME_EMPTY_MSG') );
        return;
    }

    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=RenameCustomNoteList&plugin=TodoNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id
                + '&custom_note_list_name=' + encodeURIComponent(custom_note_list_name);
    setTimeout(function() {
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// delete custom note list
static DeleteCustomNoteList(user_id, project_id) {
    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=DeleteCustomNoteList&plugin=TodoNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id;
    setTimeout(function() {
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// update custom note lists positions
static UpdateCustomNoteListsPositions(user_id, order) {
    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=UpdateCustomNoteListsPositions&plugin=TodoNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&order=' + order;
    setTimeout(function() {
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
static RefreshTabs(user_id) {
    // console.log('_TodoNotes_Requests_.RefreshTabs()');

    // refresh ONLY if notes are viewed via dashboard and project tabs are present
    if ($("#tabs").length === 0) return;

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=RefreshTabs&plugin=TodoNotes'
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#tabs").load(loadUrl,
            function() {
                _TodoNotes_Dashboard_.prepareDocument_SkipDashboardHandlers();
                _TodoNotes_Tabs_.AttachTabHandlers();
            });
    }, 200);
}

//------------------------------------------------
static RefreshAll(project_id, user_id, is_sharing) {
    const project_tab_id = $("#tabId").attr('data-project');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = _TodoNotes_Settings_.baseAppDir
                + '?controller=TodoNotesController&action=RefreshAll&plugin=TodoNotes'
                + '&user_id=' + user_id
                + '&project_tab_id=' + project_tab_id
                + '&project_custom_id=' + project_id
                + '&is_sharing=' + (is_sharing ? 1 : 0);
    setTimeout(function() {
        location.replace(loadUrl);
    }, 50);
}

//------------------------------------------------
// Sharing Permissions requests
//------------------------------------------------

//------------------------------------------------
// set sharing permission for a project From user To user
static SetSharingPermission(project_id, user_id, shared_user_id, shared_permission) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=SetSharingPermission&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&shared_user_id=' + shared_user_id
            + '&shared_permission=' + shared_permission,
        success: function(response) {
            const permissionModified = JSON.parse(response);
            if (permissionModified.timestamp > 0) {
                $("#refProjectId").attr('data-timestamp', permissionModified.timestamp);
            }
            $("#containerFlashMessage").html(permissionModified.flash_msg);
            _TodoNotes_Requests_.RefreshTabs(user_id);
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.SetSharingPermission');
            alert(e);
        }
    });
}

//------------------------------------------------
// General requests
//------------------------------------------------

//------------------------------------------------
// toggle & store settings variable into the session
static ToggleSettings(project_id, user_id, settings_group_key, settings_key, settings_exclusive = false) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=ToggleSettings&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&settings_group_key=' + settings_group_key
            + '&settings_key=' + settings_key
            + '&settings_exclusive=' + (settings_exclusive ? 1 : 0),
        success: function(/*response*/) {
            // console.log(response);
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.ToggleSettings');
            alert(e);
        }
    });
}

//------------------------------------------------
// get last timestamp
static GetLastTimestamp(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: _TodoNotes_Settings_.baseAppDir
            + '?controller=TodoNotesController&action=GetLastTimestamp&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function(response) {
            _TodoNotes_.CheckAndTriggerRefresh(JSON.parse(response));
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.GetLastTimestamp');
            alert(e);
        }
    });
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Requests_

//////////////////////////////////////////////////
_TodoNotes_Requests_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
