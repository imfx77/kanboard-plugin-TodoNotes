/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Requests_');
//////////////////////////////////////////////////
class _TodoNotes_Requests_ {

//------------------------------------------------
static AddNote(project_id, user_id) {
    const title = $("#inputNewNote").val().trim();
    const description = $('[name="editorMarkdownDetailsNewNote"]').val();
    const category = $("#catNewNote" + " option:selected").text();
    const is_active = "1";

    if (!title) {
        alert( _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_NOTE_ADD_TITLE_EMPTY_MSG') );
        return false;
    }

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=AddNote&plugin=TodoNotes'
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
    return false;
}

//------------------------------------------------
static DeleteNote(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=DeleteNote&plugin=TodoNotes'
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
    return false;
}

//------------------------------------------------
static DeleteAllDoneNotes(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=DeleteAllDoneNotes&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.DeleteAllDoneNotes');
            alert(e);
        }
    });
    return false;
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
        alert( _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_TITLE_EMPTY_MSG') );
        title = $("#noteTitleLabel-P" + project_id + "-" + id).html();
        $("#noteTitleInput-P" + project_id + "-" + id).val(title);
    }
    $("#noteTitleLabel-P" + project_id + "-" + id).html(title);

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=UpdateNote&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
        success: function(response) {
            const lastModified = JSON.parse(response)
            if (lastModified.timestamp > 0) {
                _TodoNotes_.updateNoteTimestamps(lastModified, project_id, id);
                // refresh and render the details markdown preview
                $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").html(_TodoNotes_Translations_.msgLoadingSpinner).load(
                    '/?controller=TodoNotesController&action=RefreshMarkdownPreviewWidget&plugin=TodoNotes'
                        + '&markdown_text=' + encodeURIComponent(description),
                ).css('height', 'auto');
            } else {
                alert( _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_INVALID_MSG') );
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                _TodoNotes_Requests_.RefreshTabs(user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNote');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// note update Status only!
static UpdateNoteStatus(project_id, user_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    const is_active = $("#noteCheckmark-P" + project_id + "-" + id).attr('data-id');

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=UpdateNoteStatus&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&is_active=' + is_active,
        success: function(response) {
            const lastModified = JSON.parse(response)
            if (lastModified.timestamp > 0) {
                _TodoNotes_.updateNoteTimestamps(lastModified, project_id, id);
            } else {
                alert( _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_INVALID_MSG') );
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                _TodoNotes_Requests_.RefreshTabs(user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNoteStatus');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// note update Notification Time
static UpdateNoteNotificationsAlertTime(project_id, user_id, id, notifications_alert_timestring) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=UpdateNoteNotificationsAlertTime&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&notifications_alert_timestring=' + encodeURIComponent(notifications_alert_timestring),
        success: function(response) {
            const notificationsAlertTime = JSON.parse(response)
            _TodoNotes_.updateNoteNotificationsTimestamps(notificationsAlertTime, project_id, id);
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNoteNotificationsAlertTime');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// update notes positions
static UpdateNotesPositions(project_id, user_id, order) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=UpdateNotesPositions&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&order=' + order,
        success: function(response) {
            const lastModified = JSON.parse(response)
            if (lastModified.timestamp > 0) {
                _TodoNotes_.updateAllNotesTimestamps(lastModified, project_id);
            } else {
                _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.UpdateNotesPositions');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// note transfer (to another project)
static TransferNote(project_id, user_id, id, target_project_id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=TransferNote&plugin=TodoNotes'
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
    return false;
}

//------------------------------------------------
static RefreshNotes(project_id, user_id) {
    // console.log('_TodoNotes_Requests_.RefreshNotes(');

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=TodoNotesController&action=RefreshProject&plugin=TodoNotes'
                + '&project_custom_id=' + project_id
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#result" + project_id).html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl,
            function() {
                _TodoNotes_Project_.prepareDocument_SkipDashboardHandlers();
                _TodoNotes_Tabs_.attachStatusUpdateHandlers();
            });
    }, 100);
}

//------------------------------------------------
static RefreshTabs(user_id) {
    // console.log('_TodoNotes_Requests_.RefreshTabs(');

    // refresh ONLY if notes are viewed via dashboard and project tabs are present
    if ($("#tabs").length === 0) return;

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=TodoNotesController&action=RefreshTabs&plugin=TodoNotes'
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#tabs").html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl,
            function() {
                _TodoNotes_Dashboard_.prepareDocument_SkipDashboardHandlers();
                _TodoNotes_Tabs_.attachTabHandlers();
            });
    }, 200);
}

//------------------------------------------------
static ToggleSessionOption(session_option) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=ToggleSessionOption&plugin=TodoNotes'
            + '&session_option=' + session_option,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.ToggleSessionOption');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// get last modified timestamp
static GetLastModifiedTimestamp(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=GetLastModifiedTimestamp&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function(response) {
            _TodoNotes_.checkAndTriggerRefresh(JSON.parse(response));
        },
        error: function(xhr,textStatus,e) {
            alert('_TodoNotes_Requests_.GetLastModifiedTimestamp');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Requests_

//////////////////////////////////////////////////
_TodoNotes_Requests_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
