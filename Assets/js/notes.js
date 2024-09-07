/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_');
//////////////////////////////////////////////////
class _TodoNotes_ {

//------------------------------------------------
// IsMobile check
//------------------------------------------------
static #isMobileValue = null;

static IsMobile() {

    // initialize ONCE
    if (_TodoNotes_.#isMobileValue === null) {
        _TodoNotes_.#isMobileValue = false;
        // device detection
        if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
            || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw-(n|u)|c55\/|capi|ccwa|cdm-|cell|chtm|cldc|cmd-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc-s|devi|dica|dmob|do(c|p)o|ds(12|-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(-|_)|g1 u|g560|gene|gf-5|g-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd-(m|p|t)|hei-|hi(pt|ta)|hp( i|ip)|hs-c|ht(c(-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i-(20|go|ma)|i230|iac( |-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|-[a-w])|libw|lynx|m1-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|-([1-8]|c))|phil|pire|pl(ay|uc)|pn-2|po(ck|rt|se)|prox|psio|pt-g|qa-a|qc(07|12|21|32|60|-[2-7]|i-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h-|oo|p-)|sdk\/|se(c(-|0|1)|47|mc|nd|ri)|sgh-|shar|sie(-|m)|sk-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h-|v-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl-|tdg-|tel(i|m)|tim-|t-mo|to(pl|sh)|ts(70|m-|m3|m5)|tx-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas-|your|zeto|zte-/i.test(navigator.userAgent.substr(0,4))) {
          _TodoNotes_.#isMobileValue = true;
        }
    }

    return _TodoNotes_.#isMobileValue;
}

//------------------------------------------------
// Global vars for notifications ServiceWorker
//------------------------------------------------
static #swRegistration = null;

//------------------------------------------------
// Note Details routines
//------------------------------------------------

//------------------------------------------------
// Adjust scrollableContent container
static AdjustScrollableContent() {
    const scrollableContent = $("#scrollableContent");
    if (!scrollableContent.length) return; // missing scrollableContent when NOT in project screen
    scrollableContent.height(0);

    let maxHeight;
    if ( _TodoNotes_.IsMobile() ) {
        // adjust scrollableContent height
        maxHeight = 0.7 * $(window).height();
        scrollableContent.height( Math.min(maxHeight, scrollableContent.prop('scrollHeight')) );
    } else {
        // adjust scrollableContent height
        maxHeight = 0.9 * ( $(window).height() - scrollableContent.offset().top );
        scrollableContent.height( Math.min(maxHeight, scrollableContent.prop('scrollHeight')) );

        // adjust scrollableContent margins regarding scrollbar width
        const scrollbarWidth = (scrollableContent.outerWidth() - scrollableContent.prop('scrollWidth'));
        $(".liNewNote").css('margin-right', scrollbarWidth + 3); // 3px margin from CSS ".ulNotes li"
    }
}

//------------------------------------------------
// Adjust notePlaceholder container
static AdjustNotePlaceholders(project_id, id) {
    // console.log('_TodoNotes_.AdjustNotePlaceholders');
    if (id === '0') { // new note
        if (!$(".liNewNote").length) return; // missing NewNote when NOT in project screen
        if (project_id === '0' || _TodoNotes_Settings_.ArchiveView) { // Overview Mode or Archive View
            $("#placeholderNewNote").removeClass('hideMe');
        } else {
            const offsetTitle = $(".labelNewNote").offset().top;
            let offsetButtons = $("#settingsCategoryColors").offset().top;
            offsetButtons += $("#settingsCategoryColors").outerHeight();
            if (offsetTitle > offsetButtons) {
                $("#placeholderNewNote").removeClass('hideMe');
            } else {
                $("#placeholderNewNote").addClass('hideMe');
            }
        }
    } else {
        const labelTitle = $("#noteTitleLabel-P" + project_id + "-" + id);
        const buttonDetails = $("#showDetails-P" + project_id + "-" + id);
        if (labelTitle.is(":visible")) {
            if (labelTitle.offset().top >= buttonDetails.offset().top + buttonDetails.height()) {
                $("#notePlaceholder-P" + project_id + "-" + id).removeClass('hideMe');
            } else {
                $("#notePlaceholder-P" + project_id + "-" + id).addClass('hideMe');
            }
        } else {
            $("#notePlaceholder-P" + project_id + "-" + id).removeClass('hideMe');
        }
    }
}

//------------------------------------------------
// Adjust ALL notePlaceholder containers
static AdjustAllNotesPlaceholders() {
    setTimeout(function() {
        // adjust notePlaceholder containers for Title row / New Notes
        _TodoNotes_.AdjustNotePlaceholders($("#refProjectId").attr('data-project'), '0');
        $("label" + ".noteTitleLabel").each(function() {
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.AdjustNotePlaceholders(project_id, id);
        });
    }, 100);
}

//------------------------------------------------
// Adjust ALL notePlaceholder containers
static AdjustAllNotesTitleInputs() {
    _TodoNotes_.ShowTitleInputNewNote();
    $(".noteTitleLabel").each(function() {
        if ($(this).hasClass( 'hideMe' )) {
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.#showTitleInput(project_id, id, true);
        }
    });
}

//------------------------------------------------
// Show input or label visuals for titles of existing notes
static #showTitleInput(project_id, id, show_title_input) {
    if (_TodoNotes_Settings_.ArchiveView) return;

    const noteTitleLabel = $("#noteTitleLabel-P" + project_id + "-" + id);
    const noteTitleInput = $("#noteTitleInput-P" + project_id + "-" + id);

    if (show_title_input) {
        noteTitleLabel.addClass( 'hideMe' );
        noteTitleInput.removeClass( 'hideMe' );
        noteTitleInput.focus();
        noteTitleInput[0].selectionStart = 0;
        noteTitleInput[0].selectionEnd = 0;

        const liNote = noteTitleInput.parent().parent();
        let inputWidth = liNote.width() - $("#buttonStatus-P" + project_id + "-" + id).outerWidth() - 20;

        if (_TodoNotes_.IsMobile()) {
            noteTitleInput.width( inputWidth );
        } else {
            const toolbarButtons = liNote.find(".toolbarNoteButtons");
            noteTitleInput.width( inputWidth - toolbarButtons.outerWidth());
        }
    } else {
        noteTitleInput.blur();
        noteTitleInput.addClass( 'hideMe' );
        noteTitleLabel.removeClass( 'hideMe' );
    }

    _TodoNotes_.AdjustNotePlaceholders(project_id, id);
}

//------------------------------------------------
// Show editor or markdown visuals for details of existing notes
static #ShowDetailsInput(project_id, id, show_details_input) {
    if (show_details_input) {
        $("#editDetails-P" + project_id + "-" + id).addClass( 'hideMe' );
        $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").addClass( 'hideMe' );
        $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Editor").removeClass( 'hideMe' );
        $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').addClass( 'noteEditorTextarea' );
    } else {
        $("#editDetails-P" + project_id + "-" + id).removeClass( 'hideMe' );
        $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").removeClass( 'hideMe' );
        $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Editor").addClass( 'hideMe' );
    }
}

//------------------------------------------------
// Show details for existing notes (toggle class)
static #ToggleDetails(project_id, id) {
    $("#noteDetails-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteMoveToArchive-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteRestoreFromArchive-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteDeleteFromArchive-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteDelete-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    if (!_TodoNotes_.IsMobile()) {
        $("#noteSave-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    }
    $("#noteLink-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteTransfer-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteCreateTask-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#toolbarSeparator-P" + project_id + "-" + id).toggleClass( 'hideMe' );

    $("#toolbarNoteLabels-P" + project_id + "-" + id).toggleClass( 'hideMe' );

    _TodoNotes_.#showTitleInput(project_id, id, !$("#noteTitleInput-P" + project_id + "-" + id).hasClass( 'hideMe' ));

    $("#showDetails-P" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetails-P" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-up" );

    _TodoNotes_.AdjustNotePlaceholders(project_id, id);
}

//------------------------------------------------
// Blink note
static #BlinkNote(project_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    const note_item = $("#item-" + note_id);

    note_item.find(".liNoteBkgr").removeClass( 'focusMe' );
    note_item.addClass( 'blurMe' );
    setTimeout(function() { _TodoNotes_.#ToggleDetails(project_id, id); }, 100);
    setTimeout(function() { _TodoNotes_.#ToggleDetails(project_id, id); }, 200);
    setTimeout(function() { $("#item-" + note_id).removeClass( 'blurMe' ); }, 300);
}

//------------------------------------------------
// Focus note
static FocusNote(note_id) {
    if (note_id <= 0) return;
    const note_item = $("#item-" + note_id);
    if (note_item.length === 0) return;

    const project_id = note_item.attr('data-project');
    const id = note_item.attr('data-id');

    note_item.removeClass( 'hideMe' );
        note_item.find(".liNoteBkgr").addClass( 'focusMe' );
    if ($("#noteDetails-P" + project_id + "-" + id).hasClass( 'hideMe' )) {
        _TodoNotes_.#ToggleDetails(project_id, id);
    }

    setTimeout(function() {
        _TodoNotes_.AdjustScrollableContent();
        note_item[0].scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"})
    }, 1000);
}

//------------------------------------------------
// Show input visuals for title of NewNote
static ShowTitleInputNewNote() {
    const noteDetails = $("#detailsNewNote");
    const previewDetails = $("#noteMarkdownDetailsNewNote_Preview");
    const editor = $('[name="editorMarkdownDetailsNewNote"]');

    if (!noteDetails[0]) return;

    let inputWidth;
    if ( noteDetails.hasClass( 'hideMe' ) ) {
        noteDetails.toggleClass( 'hideMe' );
        inputWidth = previewDetails.width();
        if ( previewDetails.hasClass( 'hideMe' ) ) {
            previewDetails.toggleClass( 'hideMe' );
            inputWidth = previewDetails.width();
            previewDetails.toggleClass( 'hideMe' );
        }
        noteDetails.toggleClass( 'hideMe' );
    } else {
        inputWidth = previewDetails.width();
        if ( previewDetails.hasClass( 'hideMe' ) ) {
            previewDetails.toggleClass( 'hideMe' );
            inputWidth = previewDetails.width();
            previewDetails.toggleClass( 'hideMe' );
        }
    }

    $("#inputNewNote").width( inputWidth - noteDetails.parent().find(".saveNewNote").width() );
    editor.prop('title', _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__PROJECT_NOTE_DETAILS_SAVE_HINT'));
}

//------------------------------------------------
// Show details menu for NewNote (toggle class)
static #ToggleDetailsNewNote() {
    $("#detailsNewNote").toggleClass( 'hideMe' );
    if (!_TodoNotes_.IsMobile()) {
        $("#saveNewNote").toggleClass( 'hideMe' );
    }
    $("#showDetailsNewNote").find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetailsNewNote").find('i').toggleClass( "fa-angle-double-up" );

    _TodoNotes_.ShowTitleInputNewNote();
}

//------------------------------------------------
// Note Details handlers
//------------------------------------------------

//------------------------------------------------
static #noteDetailsDblclickHandlersInitialized = false;

static #NoteDetailsDblclickHandlers() {
    //------------------------------------------------

    if (_TodoNotes_.#noteDetailsDblclickHandlersInitialized) return;

    // Show details for new note by dblclick the New Note
    $(".liNewNote").dblclick(function() {
        _TodoNotes_.#ToggleDetailsNewNote();
    });

    // Show details for note by dblclick the Note
    $(".liNote").dblclick(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#ToggleDetails(project_id, id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    _TodoNotes_.#noteDetailsDblclickHandlersInitialized = true;

    //------------------------------------------------
}

static #NoteDetailsDblclickHandlersDisable() {
    // console.log('_TodoNotes_.noteDetailsDblclickHandlersDisable');
    //------------------------------------------------

    $(".liNewNote").off('dblclick');
    $(".liNote").off('dblclick');

    _TodoNotes_.#noteDetailsDblclickHandlersInitialized = false;

    //------------------------------------------------
}

//------------------------------------------------
static #NoteDetailsHandlers() {
    //------------------------------------------------

    // disable keydown propagation for title and edit controls of Notes and New Note
    $(".inputNewNote").keydown(function(event) {
        event.stopPropagation();
    });

    $(".noteEditorMarkdownNewNote").keydown(function(event) {
        event.stopPropagation();
    });

    $(".noteTitle").keydown(function(event) {
        event.stopPropagation();
    });

    $(".noteEditorMarkdown").keydown(function(event) {
        event.stopPropagation();
    });

    //------------------------------------------------

    // disable click & dblclick propagation for all marked sub-elements
    $(".disableEventsPropagation").click(function (/*event*/) {
        //event.stopPropagation();

        _TodoNotes_.#NoteDetailsDblclickHandlersDisable();

        setTimeout(function() {
            _TodoNotes_.#NoteDetailsDblclickHandlers();
        }, 500);
    });

    $(".disableEventsPropagation").dblclick(function (event) {
        event.stopPropagation();
    });

    _TodoNotes_.#NoteDetailsDblclickHandlers();

    //------------------------------------------------

    // On TAB key in document focus on New Note input
    $(document).keydown(function(event) {
        if (event.keyCode === 9) {
            setTimeout(function() {
                $("#inputNewNote").focus();
            }, 100);
        }
    });

    //------------------------------------------------

    // Show details for New Note by menu button
    $("button" + ".showDetailsNewNote").click(function() {
        _TodoNotes_.#ToggleDetailsNewNote();
    });

    // On TAB key open detailed view for New Note
    $(".inputNewNote").keydown(function(event) {
        if (event.keyCode === 9) {
            $("#editDetailsNewNote").addClass( 'hideMe' );
            $("#noteMarkdownDetailsNewNote_Preview").addClass( 'hideMe' );
            $("#noteMarkdownDetailsNewNote_Editor").removeClass( 'hideMe' );
            $('[name="editorMarkdownDetailsNewNote"]').addClass( 'noteEditorTextarea' );

            if ($("#detailsNewNote").hasClass( 'hideMe' )) {
                _TodoNotes_.#ToggleDetailsNewNote();
            }

            setTimeout(function() {
                $('[name="editorMarkdownDetailsNewNote"]').focus();
            }, 100);
        }
    });

    // On click Edit Details button for NewNote
    $(".editDetailsNewNote").click(function() {
        $("#editDetailsNewNote").addClass( 'hideMe' );
        $("#noteMarkdownDetailsNewNote_Preview").addClass( 'hideMe' );
        $("#noteMarkdownDetailsNewNote_Editor").removeClass( 'hideMe' );
        $('[name="editorMarkdownDetailsNewNote"]').addClass( 'noteEditorTextarea' );

        setTimeout(function() {
            $('[name="editorMarkdownDetailsNewNote"]').focus();
        }, 100);
    });

    //------------------------------------------------

    // Show details for Note by menu button
    $("button" + ".showDetails").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#ToggleDetails(project_id, id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    // On TAB key open detailed view for Note
    $(".noteTitle").keydown(function(event) {
        if (event.keyCode === 9) { // TAB
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');

            _TodoNotes_.#ShowDetailsInput(project_id, id, true);
            if ($("#noteDetails-P" + project_id + "-" + id).hasClass( 'hideMe' )) {
                _TodoNotes_.#ToggleDetails(project_id, id);
            }

            setTimeout(function() {
                $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').focus();
                _TodoNotes_.AdjustScrollableContent();
            }, 100);
        }
    });

    // On click Edit Details button for Note
    $(".editDetails").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');

        _TodoNotes_.#ShowDetailsInput(project_id, id, true);

        setTimeout(function() {
            $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').focus();
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    //------------------------------------------------

    // Change from label to input on click
    $("label" + ".noteTitle").click(function() {
        if ($(this).attr('data-disabled')) return;
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#showTitleInput(project_id, id, true);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    // Click on Category label to auto open details and change category
    $("label" + ".catLabelClickable").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#ToggleDetails(project_id, id);

        setTimeout(function() {
            $("#cat-P" + project_id + "-" + id + "-button").trigger('click');
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    // Click on Dates Details label to auto open details and see dates
    $("label" + ".noteDatesDetails").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#ToggleDetails(project_id, id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    // Click on Notifications Details label to auto open details and change notifications
    $("label" + ".noteNotificationsDetails").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        // just forward click handling to the noteNotificationsLabel
        $("#noteNotificationsLabel-P" + project_id + "-" + id).trigger('click');
    });

    //------------------------------------------------

    // Refresh notes order by explicit conditional button
    $("button" + ".noteRefreshOrder").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
    });

    //------------------------------------------------
}

//------------------------------------------------
// Note Status routines & handlers
//------------------------------------------------

//------------------------------------------------
// Switch note status
static #SwitchNoteStatus(project_id, id) {
    const noteCheckmark = $("#noteCheckmark-P" + project_id + "-" + id);

    // cycle through statuses
    switch (noteCheckmark.attr('data-id')) {
        case '0': // done
            noteCheckmark.attr('data-id', '1'); // open
            break;
        case '1': // open
            noteCheckmark.attr('data-id', '2'); // in progress
            break;
        case '2': // in progress
            noteCheckmark.attr('data-id', '0'); // done
            break;
    }
}

//------------------------------------------------
// Refresh note status
static #RefreshNoteStatus(project_id, id) {
    const noteCheckmark = $("#noteCheckmark-P" + project_id + "-" + id);
    const noteTitleLabel = $("#noteTitleLabel-P" + project_id + "-" + id);
    const noteMarkdownDetailsPreview = $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview");
    const noteMarkdownDetailsEditor = $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Editor");

    if( noteCheckmark.attr('data-id') === '0' ) { // done
        noteCheckmark.addClass( 'statusDone' );
        noteTitleLabel.addClass( 'noteDoneText' );
        noteMarkdownDetailsPreview.addClass( 'noteDoneMarkdown' );
        noteMarkdownDetailsEditor.addClass( 'noteDoneMarkdown' );
    }
    if( noteCheckmark.attr('data-id') === '1' ) { // open
        noteCheckmark.addClass( 'statusOpen' );
        noteTitleLabel.removeClass( 'noteDoneText' );
        noteMarkdownDetailsPreview.removeClass( 'noteDoneMarkdown' );
        noteMarkdownDetailsEditor.removeClass( 'noteDoneMarkdown' );
    }
    if( noteCheckmark.attr('data-id') === '2' ) { // in progress
        noteCheckmark.addClass( 'statusInProgress' );
        noteTitleLabel.removeClass( 'noteDoneText' );
        noteMarkdownDetailsPreview.removeClass( 'noteDoneMarkdown' );
        noteMarkdownDetailsEditor.removeClass( 'noteDoneMarkdown' );
    }

    _TodoNotes_Statuses_.ExpandStatusAliases();
}

//------------------------------------------------
static #NoteStatusHandlers() {
    //------------------------------------------------

    //Status button handler
    $("button" + ".buttonStatus").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');

        const ref_project_id = $("#refProjectId").attr('data-project');
        const readonlyNotes = (ref_project_id === '0'); // Overview Mode

        _TodoNotes_.#SwitchNoteStatus(project_id, id);
        _TodoNotes_.#RefreshNoteStatus(project_id, id);

        if (readonlyNotes) {
            _TodoNotes_Requests_.UpdateNoteStatus(project_id, user_id, id);
        } else {
            _TodoNotes_.#showTitleInput(project_id, id, false);
            _TodoNotes_.#ShowDetailsInput(project_id, id, false);
            _TodoNotes_Requests_.UpdateNote(project_id, user_id, id);
        }

        if (_TodoNotes_Settings_.SortByStatus) {
            $("#noteRefreshOrder-P" + project_id + "-" + id).removeClass( 'hideMe' );
        }

        _TodoNotes_.#BlinkNote(project_id, id);
        _TodoNotes_.RefreshShowAllDone();

        _TodoNotes_.AdjustNotePlaceholders(project_id, id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 400); // waiting for blinkNote() to finish
    });

    //------------------------------------------------
}

//------------------------------------------------
// Add/Update/Delete/Transfer/CreateTask handlers
//------------------------------------------------

//------------------------------------------------
static #NoteActionHandlers() {
    //------------------------------------------------

    // POST ADD when ENTER key on New Note title
    $(".inputNewNote").keypress(function(event) {
        if (event.keyCode === 13) { // ENTER
            const project_id = $(this).attr('data-project');
            const user_id = $(this).attr('data-user');
            $(".inputNewNote").blur();
            _TodoNotes_Requests_.AddNote(project_id, user_id);
        }
    });

    // POST ADD when TAB key on New Note description
    $(".noteEditorMarkdownNewNote").keydown(function(event) {
        if (event.keyCode === 9) {
            const project_id = $("#noteMarkdownDetailsNewNote_Editor").attr('data-project');
            const user_id = $("#noteMarkdownDetailsNewNote_Editor").attr('data-user');
            $(".inputNewNote").blur();
            _TodoNotes_Requests_.AddNote(project_id, user_id);
        }
    });

    // POST ADD on Save button for New Note
    $("button" + ".saveNewNote").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        $(".inputNewNote").blur();
        _TodoNotes_Requests_.AddNote(project_id, user_id);
    });

    //------------------------------------------------

    // POST UPDATE when ENTER on Note title
    $(".noteTitle").keydown(function(event) {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        if (event.keyCode === 13) { // ENTER
            _TodoNotes_.#showTitleInput(project_id, id, false);
            _TodoNotes_.#ShowDetailsInput(project_id, id, false);
            _TodoNotes_Requests_.UpdateNote(project_id, user_id, id);
            _TodoNotes_.#BlinkNote(project_id, id);

            setTimeout(function() {
                _TodoNotes_.AdjustScrollableContent();
            }, 400); // waiting for blinkNote() to finish
        }
    });

    // POST UPDATE when TAB key on Note description
    $(".noteEditorMarkdown").keydown(function(event) {
        if (event.keyCode === 9) {
            const project_id = $(this).attr('data-project');
            const user_id = $(this).attr('data-user');
            const id = $(this).attr('data-id');
            _TodoNotes_.#showTitleInput(project_id, id, false);
            _TodoNotes_.#ShowDetailsInput(project_id, id, false);
            _TodoNotes_Requests_.UpdateNote(project_id, user_id, id);
            _TodoNotes_.#BlinkNote(project_id, id);

            setTimeout(function() {
                _TodoNotes_.AdjustScrollableContent();
            }, 400); // waiting for blinkNote() to finish
        }
    });

    // POST UPDATE on Save button for existing notes
    $("button" + ".noteSave").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_.#showTitleInput(project_id, id, false);
        _TodoNotes_.#ShowDetailsInput(project_id, id, false);
        _TodoNotes_Requests_.UpdateNote(project_id, user_id, id);
        _TodoNotes_.#BlinkNote(project_id, id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 400); // waiting for blinkNote() to finish
    });

    //------------------------------------------------

    // MOVE Note to ARCHIVE on Archive button for existing notes
    $("button" + ".noteMoveToArchive").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_Modals_.MoveNoteToArchive(project_id, user_id, id);
    });

    // RESTORE Note from ARCHIVE on Archive button for existing notes
    $("button" + ".noteRestoreFromArchive").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_Modals_.RestoreNoteFromArchive(project_id, user_id, id);
    });

    // POST on Delete Note from Archive button
    $("button" + ".noteDeleteFromArchive").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_Modals_.DeleteNote(project_id, user_id, id, true);
    });

    //------------------------------------------------

    // POST on Delete Note button
    $("button" + ".noteDelete").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_Modals_.DeleteNote(project_id, user_id, id, false);
    });

    // POST on Transfer Note button
    $("button" + ".noteTransfer").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_Modals_.TransferNote(project_id, user_id, id);
    });

    // POST on Crate Task from Note button
    $("button" + ".noteCreateTask").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        const title = $("#noteTitleLabel-P" + project_id + "-" + id).html();
        const description = $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').val();
        const category_id = $("#cat-P" + project_id + "-" + id + " option:selected").val();
        const is_active = $("#noteCheckmark-P" + project_id + "-" + id).attr('data-id');
        _TodoNotes_Modals_.CreateTaskFromNote(project_id, user_id, id, is_active, title, description, category_id);
    });

    // COPY note url on Note Link button
    $("button" + ".noteLink").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
        navigator.clipboard.writeText(window.location.href + '/' + note_id)
            .then(() => { alert( _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_NOTE_LINK_COPIED_MSG') ); });
    });

    //------------------------------------------------

    // Selector for Category
    $(".catSelector").selectmenu({
        change: function() {
            const id = $(this).attr('data-id');
            if (id > 0) { // exclude handling the category drop down for new note
                const project_id = $(this).attr('data-project');
                const user_id = $(this).attr('data-user');
                const old_category = $("#noteCatLabel-P" + project_id + "-" + id).html();
                const new_category = $("#cat-P" + project_id + "-" + id + " option:selected").text();
                $("#noteCatLabel-P" + project_id + "-" + id).html(new_category);

                _TodoNotes_.UpdateCategoryColors(project_id, id, old_category, new_category);
                // avoid the ugly empty category label boxes
                const noteCatLabel = $("#noteCatLabel-P" + project_id + "-" + id);
                if (new_category) {
                    noteCatLabel.removeClass( 'hideMe' );
                    if (_TodoNotes_Settings_.ShowCategoryColors) {
                        noteCatLabel.addClass( 'task-board-category' );
                    }
                }
                if (!new_category) {
                    noteCatLabel.addClass( 'hideMe' );
                } else if (!_TodoNotes_Settings_.ShowCategoryColors) {
                    noteCatLabel.removeClass( 'task-board-category' );
                }

                _TodoNotes_.#showTitleInput(project_id, id, false);
                _TodoNotes_.#ShowDetailsInput(project_id, id, false);
                _TodoNotes_Requests_.UpdateNote(project_id, user_id, id);
                _TodoNotes_.#BlinkNote(project_id, id);

                setTimeout(function() {
                    _TodoNotes_.AdjustScrollableContent();
                    }, 400); // waiting for blinkNote() to finish
            }
        }
    });

    //------------------------------------------------
    //------------------------------------------------

    // Notifications date/time picker and postpone modal dialog
    $(".noteNotificationsSetup").click(function() {
        const user_id = $(this).attr('data-user');
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        const notifications_alert_timestring = $(this).attr('data-notifications-alert-timestring');
        const notifications_alert_timestamp = $(this).attr('data-notifications-alert-timestamp');
        const notification_options_bitflags = parseInt($(this).attr('data-notifications-options-bitflags'));
        _TodoNotes_Modals_.NotificationsSetup(project_id, id, user_id, notifications_alert_timestring, notifications_alert_timestamp, notification_options_bitflags);
    });

    //------------------------------------------------
}

//------------------------------------------------
// Settings handlers
//------------------------------------------------

static #SettingsCollapseAll() {
    $(".showDetails").each(function() {
        if ($(this).find('i').hasClass( 'fa-angle-double-up' ))
        {
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.#ToggleDetails(project_id, id);
        }
    });

    setTimeout(function() {
        _TodoNotes_.AdjustScrollableContent();
    }, 100);
}

static #SettingsExpandAll() {
    $(".showDetails").each(function() {
        if ($(this).find('i').hasClass( 'fa-angle-double-down' ))
        {
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.#ToggleDetails(project_id, id);
        }
    });

    setTimeout(function() {
        _TodoNotes_.AdjustScrollableContent();
    }, 100);
}

static #ToggleList(project_id) {
    $("#sortableList-P" + project_id).toggleClass( 'accordionShow' );
    $("#sortableList-P" + project_id).toggleClass( 'accordionHide' );
    $("#toggleList-P" + project_id).find('i').toggleClass( "fa-chevron-circle-up" );
    $("#toggleList-P" + project_id).find('i').toggleClass( "fa-chevron-circle-down" );
}

//------------------------------------------------
static #SettingsHandlers() {
    //------------------------------------------------

    // POST delete all done
    $("#settingsDeleteAllDone").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_Modals_.DeleteAllDoneNotes(project_id, user_id);
    });

    // POST archive all done
    $("#settingsArchiveAllDone").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_Modals_.MoveAllDoneNotesToArchive(project_id, user_id);
    });

    // POST stats
    $("#settingsStats").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_Modals_.Stats(project_id, user_id);
    });

    // Sort and filter for report
    $("#settingsReport").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_Modals_.Report(project_id, user_id);
    });

    //------------------------------------------------

    $("#settingsCollapseAll").click(function() {
        _TodoNotes_.#SettingsCollapseAll();
    });

    $(document).keydown(function(event) {
        if (event.keyCode !== 109) return; // [-] key
        _TodoNotes_.#SettingsCollapseAll();
    });

    $("#settingsExpandAll").click(function() {
        _TodoNotes_.#SettingsExpandAll();
    });

    $(document).keydown(function(event) {
        if (event.keyCode !== 107) return; // [+] key
        _TodoNotes_.#SettingsExpandAll();
    });

    //------------------------------------------------

    $("#settingsSortByStatus").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');

        _TodoNotes_Requests_.ToggleSessionSettings(project_id, user_id, 'todonotesSettings_SortByStatus');

        _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
    });

    $("#settingsShowAllDone").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');

        _TodoNotes_Requests_.ToggleSessionSettings(project_id, user_id, 'todonotesSettings_ShowAllDone');

        _TodoNotes_Settings_.ShowAllDone = !_TodoNotes_Settings_.ShowAllDone;
        _TodoNotes_.RefreshShowAllDone();

        _TodoNotes_.AdjustAllNotesPlaceholders();

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    $("#settingsCategoryColors").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');

        _TodoNotes_Requests_.ToggleSessionSettings(project_id, user_id, 'todonotesSettings_ShowCategoryColors');

        _TodoNotes_Settings_.ShowCategoryColors = !_TodoNotes_Settings_.ShowCategoryColors;
        _TodoNotes_.RefreshCategoryColors();
    });

    $("#settingsArchiveView").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');

        _TodoNotes_Requests_.ToggleSessionSettings(project_id, user_id, 'todonotesSettings_ArchiveView');

        _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
    });

    //------------------------------------------------

    // Toogle lists in OverviewMode
    $(".headerList").dblclick(function() {
        const project_id = $(this).find("button" + ".toggleList").attr('data-project');
        _TodoNotes_.#ToggleList(project_id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    $("button" + ".toggleList").click(function() {
        const project_id = $(this).attr('data-project');
        _TodoNotes_.#ToggleList(project_id);

        setTimeout(function() {
            _TodoNotes_.AdjustScrollableContent();
        }, 100);
    });

    //------------------------------------------------

    // Hide note in report view
    $("button" + "#reportHide").click(function() {
        const id = $(this).attr('data-id');
        $("#trReportNr" + id).addClass( 'hideMe' );
    });

    //------------------------------------------------
}

//------------------------------------------------
// Refresh hide/sort/colorizing routines
//------------------------------------------------

//------------------------------------------------
// Refresh hide All Done
static RefreshShowAllDone() {
    if (_TodoNotes_Settings_.ShowAllDone) {
        $("#settingsShowAllDone").addClass( 'buttonToggled' );
        $(".liNote").each(function() {
            if ($(this).find(".buttonStatus").children().attr('data-id') === '0') {
                $(this).removeClass( 'hideMe' );
            }
        });
    } else {
        $("#settingsShowAllDone").removeClass( 'buttonToggled' );
        $(".liNote").each(function() {
            if ($(this).find(".buttonStatus").children().attr('data-id') === '0') {
                $(this).addClass( 'hideMe' );
            }
        });
    }
}

//------------------------------------------------
// Refresh Archive View
static RefreshArchiveView() {
    if (_TodoNotes_Settings_.ArchiveView) {
        $("#settingsArchiveView").addClass( 'buttonToggled' );
    } else {
        $("#settingsArchiveView").removeClass( 'buttonToggled' );
    }
}

//------------------------------------------------
// Refresh sort by Status
static RefreshSortByStatus() {
    if (_TodoNotes_Settings_.SortByStatus) {
        $("#settingsSortByStatus").addClass( 'buttonToggled' );
    } else {
        $("#settingsSortByStatus").removeClass( 'buttonToggled' );
    }
}

//------------------------------------------------
// Refresh category colors
static RefreshCategoryColors() {
    if (_TodoNotes_Settings_.ShowCategoryColors) {
        $("#settingsCategoryColors").addClass( 'buttonToggled' );
        $(".tdReport .reportBkgr").addClass( 'task-board' );
        $(".liNote .liNoteBkgr").addClass( 'task-board' );
        // avoid the ugly empty category label boxes
        $(".catLabel").each(function() {
            if ($(this).html()) {
                $(this).addClass( 'task-board-category' );
            }
        });
    } else {
        $("#settingsCategoryColors").removeClass( 'buttonToggled' );
        $(".tdReport .reportBkgr").removeClass( 'task-board' );
        $(".liNote .liNoteBkgr").removeClass( 'task-board' );
        $(".catLabel").removeClass( 'task-board-category' );
    }
}

// Update category colors
static UpdateCategoryColors(project_id, id, old_category, new_category) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    const old_color = $("#category-" + old_category).attr('data-color');
    const new_color = $("#category-" + new_category).attr('data-color');

    $("#trReportNr" + id + " .reportBkgr").removeClass( 'color-' + old_color );
    $("#item-" + note_id + " .liNoteBkgr").removeClass( 'color-' + old_color );
    $("#noteCatLabel-P" + project_id + "-" + id).removeClass( 'color-' + old_color );

    $("#trReportNr" + id + " .reportBkgr").addClass( 'color-' + new_color );
    $("#item-" + note_id + " .liNoteBkgr").addClass( 'color-' + new_color );
    $("#noteCatLabel-P" + project_id + "-" + id).addClass( 'color-' + new_color);
}

//------------------------------------------------
// Update Timestamp routines
//------------------------------------------------

//------------------------------------------------
// note update timestamp + #refProjectId
static UpdateNoteTimestamps(lastModified, project_id, id) {
    const updatedTimeString = _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__NOTE_DATE_MODIFIED') + lastModified.timestring;

    $("#noteDatesDetails-P" + project_id + "-" + id).attr('title', updatedTimeString);
    $("#noteModifiedLabel-P" + project_id + "-" + id + " i").text(' ' + updatedTimeString);

    $("#refProjectId").attr('data-timestamp', lastModified.timestamp);
}

//------------------------------------------------
// all notes update timestamps + #refProjectId
static UpdateAllNotesTimestamps(lastModified, project_id) {
    const updatedTimeString = _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__NOTE_DATE_MODIFIED') + lastModified.timestring;

    $("[id^=noteDatesDetails-P" + project_id + "]").attr('title', updatedTimeString);
    $("[id^=noteModifiedLabel-P" + project_id + "] i").text(' ' + updatedTimeString);

    $("#refProjectId").attr('data-timestamp', lastModified.timestamp);
}

//------------------------------------------------
// note update notifications alert timestamps
static RefreshNoteNotificationsAlertTimeAndOptions(notificationsAlertTimeAndOptions, project_id, id) {
    const hasNotifications = (notificationsAlertTimeAndOptions.timestamp > 0);
    const updatedTimeString = _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__NOTE_DATE_NOTIFIED')
        + (hasNotifications ? notificationsAlertTimeAndOptions.timestring : '🔕');

    const noteNotificationsDetails = $("#noteNotificationsDetails-P" + project_id + "-" + id);
    noteNotificationsDetails.attr('title', updatedTimeString);

    const noteNotificationsLabel = $("#noteNotificationsLabel-P" + project_id + "-" + id );
    noteNotificationsLabel.attr('data-notifications-alert-timestamp', notificationsAlertTimeAndOptions.timestamp);
    noteNotificationsLabel.attr('data-notifications-alert-timestring', notificationsAlertTimeAndOptions.timestring);
    noteNotificationsLabel.attr('data-notifications-options-bitflags', notificationsAlertTimeAndOptions.options_bitflags);
    noteNotificationsLabel.find(" i").text(' ' + updatedTimeString);
}

//------------------------------------------------
// Notifications routines
//------------------------------------------------

//------------------------------------------------
// note refresh notifications vis state
static RefreshNoteNotificationsState(project_id, id) {
    const noteNotificationsDetails = $("#noteNotificationsDetails-P" + project_id + "-" + id);
    const noteNotificationsLabel = $("#noteNotificationsLabel-P" + project_id + "-" + id );
    const notifications_alert_timestamp = noteNotificationsLabel.attr('data-notifications-alert-timestamp');
    const hasNotifications = (notifications_alert_timestamp > 0);

    noteNotificationsDetails.find("i").removeClass('fa fa-bell-o').removeClass('fa fa-bell-slash-o');
    // toolbar bell/slashed icon
    if (hasNotifications) {
        noteNotificationsDetails.find("i").addClass('fa fa-bell-o');
    } else {
        noteNotificationsDetails.find("i").addClass('fa fa-bell-slash-o');
    }

    noteNotificationsDetails.removeClass('dateLabelComplete').removeClass('dateLabelExpired');
    noteNotificationsLabel.removeClass('dateLabelComplete').removeClass('dateLabelExpired');
    if ($("#noteCheckmark-P" + project_id + "-" + id).attr('data-id') === '0') {
        // state Complete
        noteNotificationsDetails.addClass('dateLabelComplete');
        noteNotificationsLabel.addClass('dateLabelComplete');
    } else if (hasNotifications) {
        // state Expired
        const local_timestamp = Math.floor(Date.now() / 1000);
        const localTimeOffset = parseInt($("#refProjectId").attr('data-local-time-offset'));
        if (notifications_alert_timestamp < local_timestamp + localTimeOffset) {
            noteNotificationsDetails.addClass('dateLabelExpired');
            noteNotificationsLabel.addClass('dateLabelExpired');
        }
    }
}

//------------------------------------------------
// all notes refresh notifications vis state
static #RefreshAllNotesNotificationsState() {
    // console.log('_TodoNotes_.RefreshAllNotesNotificationsState');
    $("[id^=noteNotificationsLabel-P]").each(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.RefreshNoteNotificationsState(project_id, id)
    });
}

//------------------------------------------------
// request browser notifications permission
static RequestBrowserNotificationsPermission() {
    // console.log('_TodoNotes_.RequestBrowserNotificationsPermission');

    const applicationServerKey = 'BNdqXy7oyszI1EJUA_0sWsEm9hFHeGJ4EVY0lsb7qAJ5Yi1ZcsQs1JKtuSvV0JjLhilR5QUlaoLUktMB40tFOMc';

    if (!('serviceWorker' in navigator)) {
        console.warn('Service workers are not supported by this browser!');
        return;
    }

    if (!('PushManager' in window)) {
        console.warn('Push notifications are not supported by this browser!');
        return;
    }

    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.warn('Notifications are not supported by this browser!');
        return;
    }

    if (!Notification) {
        console.warn('Notifications are not available in this browser!');
        return;
    }

    const user_id = $("#refProjectId").attr('data-user');

    Notification.requestPermission();

    navigator.serviceWorker.register(location.origin + '/plugins/TodoNotes/Template/notifications/service_worker.php', { scope: '/' })
    .then(function (sw) {
        console.info('[SW] Service worker has been registered');
        _TodoNotes_.#swRegistration = sw;

        navigator.serviceWorker.ready
            .then(function(sw) {
                console.info('[SW] Service worker is ready');
                sw.active.postMessage('heartbeat');
                return sw.pushManager.getSubscription();
            })
            .then(subscriptionExisting => {
                if (!subscriptionExisting) {
                    sw.pushManager.subscribe({
                             userVisibleOnly: true, //Always show notification when received
                             applicationServerKey: applicationServerKey,
                    })
                    .then(subscriptionNew => {
                        console.info('[SW] Successfully subscribed to push notifications');
                        // console.log(subscriptionNew);
                        _TodoNotes_Requests_.UpdateWebPNSubscription(user_id, subscriptionNew);
                        console.info('[SW] New subscription sent to server');
                        sessionStorage.setItem('todonotes_webpn_latest_subscription_endpoint', subscriptionNew.endpoint);
                    });
                } else {
                    console.info('[SW] Existing push notifications subscription');
                    // console.log(subscriptionExisting);
                    const latest_endpoint = sessionStorage.getItem('todonotes_webpn_latest_subscription_endpoint');
                    // console.log(latest_endpoint);
                    // console.log(subscriptionExisting.endpoint);
                    // reduce updating the WebPN subscription on each page load by using a session cache variable
                    if (!latest_endpoint || latest_endpoint !== subscriptionExisting.endpoint) {
                        _TodoNotes_Requests_.UpdateWebPNSubscription(user_id, subscriptionExisting);
                        console.info('[SW] Existing subscription sent to server');
                        sessionStorage.setItem('todonotes_webpn_latest_subscription_endpoint', subscriptionExisting.endpoint);
                    }
                }
            })
            .catch(error => {
                console.error('[SW] Impossible to subscribe to push notifications', error);
            });
    },
    function(e) {
      console.error('[SW] Service worker registration failed', e);
    });
}

//------------------------------------------------
// trigger a notification from the browser
static ShowBrowserNotification(title, content, link, timestamp_ms) {
    // console.log('_TodoNotes_.ShowBrowserNotification');
    if (!_TodoNotes_.#swRegistration) return;

    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    } else {
        const options = {
            body: content,
            icon: location.origin + '/plugins/TodoNotes/Assets/img/icon.png',
            badge: location.origin + '/plugins/TodoNotes/Assets/img/badge.png',
            data: { url: link },
            timestamp: timestamp_ms,
            vibrate: [200, 100, 200, 100, 200, 100, 200],
        };

        _TodoNotes_.#swRegistration.showNotification(title, options);
    }
}

//------------------------------------------------
// AUTO-Refresh routines
//------------------------------------------------

//------------------------------------------------
static #ShowRefreshIcon() {
    $("#refreshIcon").removeClass( 'hideMe' );
}

//------------------------------------------------
static #HideRefreshIcon( ) {
    $("#refreshIcon").addClass( 'hideMe' );
}

//------------------------------------------------
// schedule check for modifications every 15 sec
static ScheduleCheckModifications() {
    // console.log('_TodoNotes_.ScheduleCheckModifications()')
    setTimeout(function() {
        _TodoNotes_.#ShowRefreshIcon();
        _TodoNotes_.#RefreshAllNotesNotificationsState();

        const project_id = $("#refProjectId").attr('data-project');
        const user_id = $("#refProjectId").attr('data-user');

        const is_project = ($(".liNewNote").length === 1);
        const has_new_note = (is_project && project_id !== '0' && !_TodoNotes_Settings_.ArchiveView);
        const title = has_new_note ? $("#inputNewNote").val().trim() : '';
        const description = has_new_note ? $('[name="editorMarkdownDetailsNewNote"]').val() : '';

        // skip SQL query if page not visible, or if new note has pending changes
        if (!KB.utils.isVisible() || title !== '' || description !== '') {
            _TodoNotes_.ScheduleCheckModifications();
            return;
        }

        _TodoNotes_Requests_.GetLastTimestamp(project_id, user_id);
    }, 15 * 1000); // 15 sec
}

//------------------------------------------------
// check if page refresh is necessary
static CheckAndTriggerRefresh(lastModifiedTimestamp) {
    // console.log('_TodoNotes_.CheckAndTriggerRefresh');

    const project_id = $("#refProjectId").attr('data-project');
    const user_id = $("#refProjectId").attr('data-user');
    const lastRefreshedTimestamp = $("#refProjectId").attr('data-timestamp');

    const is_project = ($(".liNewNote").length === 1);
    if (is_project && lastRefreshedTimestamp < lastModifiedTimestamp.notes) {
        _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
    }
    if (lastRefreshedTimestamp < lastModifiedTimestamp.projects) {
        _TodoNotes_Requests_.RefreshTabs(user_id);
    }
    if (lastRefreshedTimestamp < lastModifiedTimestamp.max) {
        $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp.max);
    }

    _TodoNotes_.ScheduleCheckModifications();
    _TodoNotes_.#HideRefreshIcon();
}

//------------------------------------------------
// check if page refresh is necessary
static InitializeLocalTimeOffset() {
    const local_timestamp = Math.floor(Date.now() / 1000);
    const last_timestamp = $("#refProjectId").attr('data-timestamp');
    const localTimeOffset = local_timestamp - last_timestamp;
    $("#refProjectId").attr('data-local-time-offset', localTimeOffset);
}

//------------------------------------------------
// Global routines
//------------------------------------------------

//------------------------------------------------
static AttachAllHandlers() {
    // console.log('_TodoNotes_.AttachAllHandlers');

    _TodoNotes_.#NoteDetailsDblclickHandlersDisable();

    _TodoNotes_.#NoteDetailsHandlers();
    _TodoNotes_.#NoteStatusHandlers();
    _TodoNotes_.#NoteActionHandlers();
    _TodoNotes_.#SettingsHandlers();
}

//------------------------------------------------

} // class _TodoNotes_

//////////////////////////////////////////////////
$(function() {
    _TodoNotes_.InitializeLocalTimeOffset();
    // start the recursive check sequence on load page
    _TodoNotes_.ScheduleCheckModifications();

    _TodoNotes_.RequestBrowserNotificationsPermission();
});

//////////////////////////////////////////////////
