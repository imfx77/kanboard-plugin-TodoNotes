class _BoardNotes_ {

//------------------------------------------------
// IsMobile check
//------------------------------------------------
static #isMobileValue = null;

static isMobile() {

    // initialize ONCE
    if (_BoardNotes_.#isMobileValue === null) {
        _BoardNotes_.#isMobileValue = false;
        // device detection
        if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
            || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw-(n|u)|c55\/|capi|ccwa|cdm-|cell|chtm|cldc|cmd-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc-s|devi|dica|dmob|do(c|p)o|ds(12|-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(-|_)|g1 u|g560|gene|gf-5|g-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd-(m|p|t)|hei-|hi(pt|ta)|hp( i|ip)|hs-c|ht(c(-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i-(20|go|ma)|i230|iac( |-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|-[a-w])|libw|lynx|m1-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|-([1-8]|c))|phil|pire|pl(ay|uc)|pn-2|po(ck|rt|se)|prox|psio|pt-g|qa-a|qc(07|12|21|32|60|-[2-7]|i-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h-|oo|p-)|sdk\/|se(c(-|0|1)|47|mc|nd|ri)|sgh-|shar|sie(-|m)|sk-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h-|v-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl-|tdg-|tel(i|m)|tim-|t-mo|to(pl|sh)|ts(70|m-|m3|m5)|tx-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas-|your|zeto|zte-/i.test(navigator.userAgent.substr(0,4))) {
          _BoardNotes_.#isMobileValue = true;
        }
    }

    return _BoardNotes_.#isMobileValue;
}

//------------------------------------------------
// Global vars for options
//------------------------------------------------

static optionShowCategoryColors = false;
static optionSortByStatus = false;
static optionShowAllDone = false;

//------------------------------------------------
// Note Details routines
//------------------------------------------------

//------------------------------------------------
// Adjust scrollableContent container
static adjustScrollableContent() {
    var scrollableContent = $("#scrollableContent");
    if ( _BoardNotes_.isMobile() ) {
        // adjust scrollableContent height
        scrollableContent.height( 0.7 * $(window).height() );
    } else {
        // adjust scrollableContent height
        var maxHeight = 0.9 * ( $(window).height() - scrollableContent.offset().top );
        scrollableContent.height(0);
        scrollableContent.height( Math.min(maxHeight, scrollableContent.prop('scrollHeight')) );
        // adjust scrollableContent margins regarding scrollbar width
        const scrollbarWidth = (scrollableContent.outerWidth() - scrollableContent.prop('scrollWidth'));
        $(".liNewNote").css('margin-right', scrollbarWidth + 3); // 3px margin from CSS ".ulNotes li"
    }
}

//------------------------------------------------
// Adjust notePlaceholder container
static adjustNotePlaceholders(project_id, id) {
    var isTitle = (project_id == 0 && id == 0);
    if (isTitle) {
        var offsetTitle = $(".labelNewNote").offset().top;
        var offsetButtons = $("#settingsShowAllDone").offset().top;
        offsetButtons += $("#settingsShowAllDone").outerHeight();
        if (offsetTitle > offsetButtons) {
            $("#placeholderNewNote").removeClass( 'hideMe' );
        } else {
            $("#placeholderNewNote").addClass( 'hideMe' );
        }
    } else {
        var offsetCheck = $("#checkDone-P" + project_id + "-" + id).offset().top;
        var offsetDetails = $("#showDetails-P" + project_id + "-" + id).offset().top;
        if (offsetCheck == offsetDetails) {
            $("#notePlaceholder-P" + project_id + "-" + id).addClass( 'hideMe' );
        } else {
            $("#notePlaceholder-P" + project_id + "-" + id).removeClass( 'hideMe' );
        }
    }
}

//------------------------------------------------
// Adjust ALL notePlaceholder containers
static adjustAllNotesPlaceholders() {
    setTimeout(function() {
        // adjust notePlaceholder containers where not needed
        _BoardNotes_.adjustNotePlaceholders(0, 0);
        $("button" + ".checkDone").each(function() {
            var project_id = $(this).attr('data-project');
            var id = $(this).attr('data-id');
            _BoardNotes_.adjustNotePlaceholders(project_id, id);
        })
    }, 100);
}

//------------------------------------------------
// Show input or label visuals for titles of existing notes
static #showTitleInput(project_id, id, show_title_input) {
    var noteTitleLabel = $("#noteTitleLabel-P" + project_id + "-" + id);
    var noteTitleInput = $("#noteTitleInput-P" + project_id + "-" + id);
    var noteDetails = $("#noteDetails-P" + project_id + "-" + id);
    var previewDetails = $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview");

    if (show_title_input) {
        noteTitleLabel.addClass( 'hideMe' );
        noteTitleInput.removeClass( 'hideMe' );
        noteTitleInput.focus();
        noteTitleInput[0].selectionStart = 0;
        noteTitleInput[0].selectionEnd = 0;

        var inputWidth = previewDetails.width();
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

        if (_BoardNotes_.isMobile()) {
            noteTitleInput.width( inputWidth );
        } else {
            var toolbarHeader = noteDetails.parent().find(".toolbarNoteHeader");
            noteTitleInput.width( inputWidth - toolbarHeader.width());
        }
    } else {
        noteTitleInput.blur();
        noteTitleInput.addClass( 'hideMe' );
        noteTitleLabel.removeClass( 'hideMe' );
    }

    _BoardNotes_.adjustNotePlaceholders(project_id, id);
}

//------------------------------------------------
// Show editor or markdown visuals for details of existing notes
static #showDetailsInput(project_id, id, show_details_input) {
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
static #toggleDetails(project_id, id) {
    $("#noteDetails-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteDelete-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    if (!_BoardNotes_.isMobile()) {
        $("#noteSave-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    }
    $("#toolbarSeparator-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteTransfer-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteCreateTask-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteCatLabel-P" + project_id + "-" + id).toggleClass( 'hideMe' );

    _BoardNotes_.#showTitleInput(project_id, id, !$("#noteTitleInput-P" + project_id + "-" + id).hasClass( 'hideMe' ));

    $("#showDetails-P" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetails-P" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-up" );

    _BoardNotes_.adjustNotePlaceholders(project_id, id);
}

//------------------------------------------------
// Show input or label visuals for title of NewNote
static showTitleInputNewNote() {
    var noteDetails = $("#detailsNewNote");
    var previewDetails = $("#noteMarkdownDetailsNewNote_Preview");
    var editor = $('[name="editorMarkdownDetailsNewNote"]');

    if (!noteDetails[0]) return;

    var inputWidth = previewDetails.width();
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
    editor.prop('title', _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_PROJECT_NOTE_DETAILS_SAVE_HINT'));
}

//------------------------------------------------
// Show details menu for NewNote (toggle class)
static #toggleDetailsNewNote() {
    $("#detailsNewNote").toggleClass( 'hideMe' );
    if (!_BoardNotes_.isMobile()) {
        $("#saveNewNote").toggleClass( 'hideMe' );
    }
    $("#showDetailsNewNote").find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetailsNewNote").find('i').toggleClass( "fa-angle-double-up" );

    _BoardNotes_.showTitleInputNewNote();
}

//------------------------------------------------
// Blink note
static #blinkNote(project_id, id) {
    var note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    setTimeout(function() { $("#item-" + note_id).addClass( 'blurMe' ); }, 0);
    setTimeout(function() { _BoardNotes_.#toggleDetails(project_id, id); }, 100);
    setTimeout(function() { _BoardNotes_.#toggleDetails(project_id, id); }, 200);
    setTimeout(function() { $("#item-" + note_id).removeClass( 'blurMe' ); }, 300);
}

//------------------------------------------------
// Note Details handlers
//------------------------------------------------

//------------------------------------------------
static #noteDetailsDblclickHandlersInitialized = false;

static #noteDetailsDblclickHandlers() {
    //------------------------------------------------

    if (_BoardNotes_.#noteDetailsDblclickHandlersInitialized) return;

    // Show details for new note by dblclick the New Note
    $(".liNewNote").dblclick(function() {
        _BoardNotes_.#toggleDetailsNewNote();
    });

    // Show details for note by dblclick the Note
    $(".liNote").dblclick(function() {
        var project_id = $(this).attr('data-project');
        var id = $(this).attr('data-id');
        _BoardNotes_.#toggleDetails(project_id, id);

        setTimeout(function() {
            _BoardNotes_.adjustScrollableContent();
        }, 100);
    });

    _BoardNotes_.#noteDetailsDblclickHandlersInitialized = true;

    //------------------------------------------------
}

static #noteDetailsDblclickHandlersDisable() {
    //------------------------------------------------

    $(".liNewNote").off('dblclick');
    $(".liNote").off('dblclick');

    _BoardNotes_.#noteDetailsDblclickHandlersInitialized = false;

    //------------------------------------------------
}

//------------------------------------------------
static #noteDetailsHandlers() {
    //------------------------------------------------

    // disable click & dblclick propagation for all marked sub-elements
    $(".disableEventsPropagation").click(function (event) {
        event.stopPropagation();

        _BoardNotes_.#noteDetailsDblclickHandlersDisable();

        setTimeout(function() {
            _BoardNotes_.#noteDetailsDblclickHandlers();
        }, 500);
    });

    $(".disableEventsPropagation").dblclick(function (event) {
        event.stopPropagation();
    });

    _BoardNotes_.#noteDetailsDblclickHandlers();

    //------------------------------------------------

    // Show details for New Note by menu button
    $("button" + ".showDetailsNewNote").click(function() {
        _BoardNotes_.#toggleDetailsNewNote();
    });

    // On TAB key open detailed view for New Note
    $(".inputNewNote").keydown(function(event) {
        if (event.keyCode == 9) {
            $("#editDetailsNewNote").addClass( 'hideMe' );
            $("#noteMarkdownDetailsNewNote_Preview").addClass( 'hideMe' );
            $("#noteMarkdownDetailsNewNote_Editor").removeClass( 'hideMe' );
            $('[name="editorMarkdownDetailsNewNote"]').addClass( 'noteEditorTextarea' );

            if ($("#detailsNewNote").hasClass( 'hideMe' )) {
                _BoardNotes_.#toggleDetailsNewNote();
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
        var project_id = $(this).attr('data-project');
        var id = $(this).attr('data-id');
        _BoardNotes_.#toggleDetails(project_id, id);

        setTimeout(function() {
            _BoardNotes_.adjustScrollableContent();
        }, 100);
    });

    // On TAB key open detailed view for Note
    $(".noteTitle").keydown(function(event) {
        if (event.keyCode == 9) { // TAB
            var project_id = $(this).attr('data-project');
            var id = $(this).attr('data-id');

            _BoardNotes_.#showDetailsInput(project_id, id, true);
            if ($("#noteDetails-P" + project_id + "-" + id).hasClass( 'hideMe' )) {
                _BoardNotes_.#toggleDetails(project_id, id);
            }

            setTimeout(function() {
                $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').focus();
                _BoardNotes_.adjustScrollableContent();
            }, 100);
        }
    });

    // On click Edit Details button for Note
    $(".editDetails").click(function() {
        var project_id = $(this).attr('data-project');
        var id = $(this).attr('data-id');

        _BoardNotes_.#showDetailsInput(project_id, id, true);

        setTimeout(function() {
            $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').focus();
            _BoardNotes_.adjustScrollableContent();
        }, 100);
    });

    //------------------------------------------------

    // Change from label to input on click
    $("label" + ".noteTitle").click(function() {
        if ($(this).attr('data-disabled')) return;
        var project_id = $(this).attr('data-project');
        var id = $(this).attr('data-id');
        _BoardNotes_.#showTitleInput(project_id, id, true);

        setTimeout(function() {
            _BoardNotes_.adjustScrollableContent();
        }, 100);
    });

    // Click on category label to auto open details and change category
    $("label" + ".catLabelClickable").click(function() {
        var project_id = $(this).attr('data-project');
        var id = $(this).attr('data-id');
        _BoardNotes_.#toggleDetails(project_id, id);

        setTimeout(function() {
            $("#cat-P" + project_id + "-" + id + "-button").trigger('click');
            _BoardNotes_.adjustScrollableContent();
        }, 100);
    });

    //------------------------------------------------

    // Refresh notes order by explicit conditional button
    $("button" + ".noteRefreshOrder").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
    });

    //------------------------------------------------
}

//------------------------------------------------
// Note Status routines & handlers
//------------------------------------------------

//------------------------------------------------
// Switch note done status
static #switchNoteDoneStatus(project_id, id) {
    var checkDone = $("#noteDoneCheckmark-P" + project_id + "-" + id);

    // cycle through statuses
    if (checkDone.hasClass( 'fa-circle-thin' )) {
        checkDone.removeClass( 'fa-circle-thin' );
        checkDone.addClass( 'fa-spinner fa-pulse' );
        return;
    }
    if (checkDone.hasClass( 'fa-spinner fa-pulse' )) {
        checkDone.removeClass( 'fa-spinner fa-pulse' );
        checkDone.addClass( 'fa-check' );
        return;
    }
    if (checkDone.hasClass( 'fa-check' )) {
        checkDone.removeClass( 'fa-check' );
        checkDone.addClass( 'fa-circle-thin' );
        return;
    }
}

//------------------------------------------------
// Update note done checkmark
static #updateNoteDoneCheckmark(project_id, id) {
    var noteDoneCheckmark = $("#noteDoneCheckmark-P" + project_id + "-" + id);
    var noteTitleLabel = $("#noteTitleLabel-P" + project_id + "-" + id);
    var noteMarkdownDetailsPreview = $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview");
    var noteMarkdownDetailsEditor = $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Editor");

    if( noteDoneCheckmark.hasClass( 'fa-check' ) ) {
        noteTitleLabel.addClass( 'noteDoneText' );
        noteMarkdownDetailsPreview.addClass( 'noteDoneMarkdown' );
        noteMarkdownDetailsEditor.addClass( 'noteDoneMarkdown' );
        noteDoneCheckmark.attr('data-id', '0');
    }
    if( noteDoneCheckmark.hasClass( 'fa-circle-thin' ) ) {
        noteTitleLabel.removeClass( 'noteDoneText' );
        noteMarkdownDetailsPreview.removeClass( 'noteDoneMarkdown' );
        noteMarkdownDetailsEditor.removeClass( 'noteDoneMarkdown' );
        noteDoneCheckmark.attr('data-id', '1');
    }
    if( noteDoneCheckmark.hasClass( 'fa-spinner fa-pulse' ) ) {
        noteTitleLabel.removeClass( 'noteDoneText' );
        noteMarkdownDetailsPreview.removeClass( 'noteDoneMarkdown' );
        noteMarkdownDetailsEditor.removeClass( 'noteDoneMarkdown' );
        noteDoneCheckmark.attr('data-id', '2');
    }
}

//------------------------------------------------
static #noteStatusHandlers() {
    //------------------------------------------------

    //Checkmark done handler
    $("button" + ".checkDone").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');

        var ref_project_id = $("#refProjectId").attr('data-project');
        var readonlyNotes = (ref_project_id == 0); // Overview Mode

        _BoardNotes_.#switchNoteDoneStatus(project_id, id);
        _BoardNotes_.#updateNoteDoneCheckmark(project_id, id);

        if (readonlyNotes) {
            _BoardNotes_.#sqlUpdateNoteStatus(project_id, user_id, id);
        } else {
            _BoardNotes_.#showTitleInput(project_id, id, false);
            _BoardNotes_.#showDetailsInput(project_id, id, false);
            _BoardNotes_.#sqlUpdateNote(project_id, user_id, id);
        }

        if (_BoardNotes_.optionSortByStatus) {
            $("#noteRefreshOrder-P" + project_id + "-" + id).removeClass( 'hideMe' );
        }

        _BoardNotes_.#blinkNote(project_id, id);
        _BoardNotes_.refreshShowAllDone();

        _BoardNotes_.adjustNotePlaceholders(project_id, id);

        setTimeout(function() {
            _BoardNotes_.adjustScrollableContent();
        }, 400); // waiting for blinkNote() to finish
    });

    //------------------------------------------------
}

//------------------------------------------------
// Add/Update/Delete/Transfer/Export handlers
//------------------------------------------------

//------------------------------------------------
static #noteActionHandlers() {
    //------------------------------------------------

    // POST ADD when ENTER key on New Note title
    $(".inputNewNote").keypress(function(event) {
        if (event.keyCode == 13) { // ENTER
            var project_id = $(this).attr('data-project');
            var user_id = $(this).attr('data-user');
            $(".inputNewNote").blur();
            _BoardNotes_.#sqlAddNote(project_id, user_id);
            _BoardNotes_.#sqlRefreshTabs(user_id);
            _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
        }
    });

    // POST ADD when TAB key on New Note description
    $(".noteEditorMarkdownNewNote").keydown(function(event) {
        if (event.keyCode == 9) {
            var project_id = $("#noteMarkdownDetailsNewNote_Editor").attr('data-project');
            var user_id = $("#noteMarkdownDetailsNewNote_Editor").attr('data-user');
            $(".inputNewNote").blur();
            _BoardNotes_.#sqlAddNote(project_id, user_id);
            _BoardNotes_.#sqlRefreshTabs(user_id);
            _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
        }
    });

    // POST ADD on Save button for New Note
    $("button" + ".saveNewNote").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        $(".inputNewNote").blur();
        _BoardNotes_.#sqlAddNote(project_id, user_id);
        _BoardNotes_.#sqlRefreshTabs(user_id);
        _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
    });

    //------------------------------------------------

    // POST UPDATE when ENTER on Note title
    $(".noteTitle").keydown(function(event) {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        if (event.keyCode == 13) { // ENTER
            _BoardNotes_.#showTitleInput(project_id, id, false);
            _BoardNotes_.#showDetailsInput(project_id, id, false);
            _BoardNotes_.#sqlUpdateNote(project_id, user_id, id);
            _BoardNotes_.#blinkNote(project_id, id);

            setTimeout(function() {
                _BoardNotes_.adjustScrollableContent();
            }, 400); // waiting for blinkNote() to finish
        }
    });

    // POST UPDATE when TAB key on Note description
    $(".noteEditorMarkdown").keydown(function(event) {
        if (event.keyCode == 9) {
            var project_id = $(this).attr('data-project');
            var user_id = $(this).attr('data-user');
            var id = $(this).attr('data-id');
            _BoardNotes_.#showTitleInput(project_id, id, false);
            _BoardNotes_.#showDetailsInput(project_id, id, false);
            _BoardNotes_.#sqlUpdateNote(project_id, user_id, id);
            _BoardNotes_.#blinkNote(project_id, id);

            setTimeout(function() {
                _BoardNotes_.adjustScrollableContent();
            }, 400); // waiting for blinkNote() to finish
        }
    });

    // POST UPDATE on Save button for existing notes
    $("button" + ".noteSave").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        _BoardNotes_.#showTitleInput(project_id, id, false);
        _BoardNotes_.#showDetailsInput(project_id, id, false);
        _BoardNotes_.#sqlUpdateNote(project_id, user_id, id);
        _BoardNotes_.#blinkNote(project_id, id);

        setTimeout(function() {
            _BoardNotes_.adjustScrollableContent();
        }, 400); // waiting for blinkNote() to finish
    });

    //------------------------------------------------

    // POST on Delete Note button
    $("button" + ".noteDelete").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        _BoardNotes_.#sqlDeleteNote(project_id, user_id, id);
        _BoardNotes_.#sqlRefreshTabs(user_id);
        _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
    });

    // POST on Transfer Note button
    $("button" + ".noteTransfer").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        _BoardNotes_.#modalTransferNote(project_id, user_id, id);
    });

    // POST on Export Note button
    $("button" + ".noteCreateTask").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        var title = $("#noteTitleLabel-P" + project_id + "-" + id).html();
        var description = $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').val();
        var category_id = $("#cat-P" + project_id + "-" + id + " option:selected").val();
        var is_active = $("#noteDoneCheckmark-P" + project_id + "-" + id).attr('data-id');
        _BoardNotes_.#modalCreateTask(project_id, user_id, id, is_active, title, description, category_id);
    });

    //------------------------------------------------

    // Selector for Category
    $(".catSelector").selectmenu({
        change: function() {
            var id = $(this).attr('data-id');
            if (id > 0) { // exclude handling the category drop down for new note
                var project_id = $(this).attr('data-project');
                var user_id = $(this).attr('data-user');
                var old_category = $("#noteCatLabel-P" + project_id + "-" + id).html();
                var new_category = $("#cat-P" + project_id + "-" + id + " option:selected").text();
                $("#noteCatLabel-P" + project_id + "-" + id).html(new_category);

                _BoardNotes_.updateCategoryColors(project_id, id, old_category, new_category);
                // avoid the ugly empty category label boxes
                if (new_category && _BoardNotes_.optionShowCategoryColors) {
                    $("#noteCatLabel-P" + project_id + "-" + id).addClass( 'task-board-category' );
                }
                if (!new_category || !_BoardNotes_.optionShowCategoryColors) {
                    $("#noteCatLabel-P" + project_id + "-" + id).removeClass( 'task-board-category' );
                }

                _BoardNotes_.#showTitleInput(project_id, id, false);
                _BoardNotes_.#showDetailsInput(project_id, id, false);
                _BoardNotes_.#sqlUpdateNote(project_id, user_id, id);
                _BoardNotes_.#blinkNote(project_id, id);

                setTimeout(function() {
                    _BoardNotes_.adjustScrollableContent();
                    }, 400); // waiting for blinkNote() to finish
            }
        }
    });

    //------------------------------------------------
}

//------------------------------------------------
// Settings handlers
//------------------------------------------------

static #settingsCollapseAll() {
    $(".showDetails").each(function() {
        if ($(this).find('i').hasClass( 'fa-angle-double-up' ))
        {
            var project_id = $(this).attr('data-project');
            var id = $(this).attr('data-id');
            _BoardNotes_.#toggleDetails(project_id, id);
        }
    });

    setTimeout(function() {
        _BoardNotes_.adjustScrollableContent();
    }, 100);
}

static #settingsExpandAll() {
    $(".showDetails").each(function() {
        if ($(this).find('i').hasClass( 'fa-angle-double-down' ))
        {
            var project_id = $(this).attr('data-project');
            var id = $(this).attr('data-id');
            _BoardNotes_.#toggleDetails(project_id, id);
        }
    });

    setTimeout(function() {
        _BoardNotes_.adjustScrollableContent();
    }, 100);
}

//------------------------------------------------
static #settingsHandlers() {
    //------------------------------------------------

    // POST delete all done
    $("#settingsDeleteAllDone").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        _BoardNotes_.#modalDeleteAllDoneNotes(project_id, user_id);
    });

    // POST stats
    $("#settingsStats").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        _BoardNotes_.#modalStats(project_id, user_id);
    });

    // Sort and filter for report
    $("#settingsReport").click(function() {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        _BoardNotes_.#modalReport(project_id, user_id);
    });

    //------------------------------------------------

    $("#settingsCollapseAll").click(function() {
        _BoardNotes_.#settingsCollapseAll();
    });

    $(document).keydown(function(event) {
        if (event.keyCode != 109) return; // [-] key
        _BoardNotes_.#settingsCollapseAll();
    });

    $("#settingsExpandAll").click(function() {
        _BoardNotes_.#settingsExpandAll();
    });

    $(document).keydown(function(event) {
        if (event.keyCode != 107) return; // [+] key
        _BoardNotes_.#settingsExpandAll();
    });

    //------------------------------------------------

    $("#settingsShowAllDone").click(function() {
        _BoardNotes_.#sqlToggleSessionOption('boardnotesShowAllDone');

        _BoardNotes_.optionShowAllDone = !_BoardNotes_.optionShowAllDone;
        _BoardNotes_.refreshShowAllDone();

        _BoardNotes_.adjustAllNotesPlaceholders();

        setTimeout(function() {
            _BoardNotes_.adjustScrollableContent();
        }, 100);
    });

    $("#settingsSortByStatus").click(function() {
        _BoardNotes_.#sqlToggleSessionOption('boardnotesSortByStatus');

        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
    });

    $("#settingsCategoryColors").click(function() {
        _BoardNotes_.#sqlToggleSessionOption('boardnotesShowCategoryColors');

        _BoardNotes_.optionShowCategoryColors = !_BoardNotes_.optionShowCategoryColors;
        _BoardNotes_.refreshCategoryColors();
    });

    //------------------------------------------------

    // Hide note in report view
    $("button" + "#reportHide").click(function() {
        var id = $(this).attr('data-id');
        $("#trReportNr" + id).addClass( 'hideMe' );
    });

    //------------------------------------------------
}

//------------------------------------------------
// Refresh hide/sort/colorizing routines
//------------------------------------------------

//------------------------------------------------
// Refresh hide All Done
static refreshShowAllDone() {
    if (_BoardNotes_.optionShowAllDone) {
        $("#settingsShowAllDone").addClass( 'toolbarButtonToggled' );
        $(".liNote").each(function() {
            if ($(this).find(".checkDone").children().hasClass( 'fa-check' )) {
                $(this).removeClass( 'hideMe' );
            }
        });
    } else {
        $("#settingsShowAllDone").removeClass( 'toolbarButtonToggled' );
        $(".liNote").each(function() {
            if ($(this).find(".checkDone").children().hasClass( 'fa-check' )) {
                $(this).addClass( 'hideMe' );
            }
        });
    }
}

//------------------------------------------------
// Refresh sort by Status
static refreshSortByStatus() {
    if (_BoardNotes_.optionSortByStatus) {
        $("#settingsSortByStatus").addClass( 'toolbarButtonToggled' );
    } else {
        $("#settingsSortByStatus").removeClass( 'toolbarButtonToggled' );
    }
}

//------------------------------------------------
// Refresh category colors
static refreshCategoryColors() {
    if (_BoardNotes_.optionShowCategoryColors) {
        $("#settingsCategoryColors").addClass( 'toolbarButtonToggled' );
        $(".tdReport .reportBkgr").addClass( 'task-board' );
        $(".liNote .liNoteBkgr").addClass( 'task-board' );
        // avoid the ugly empty category label boxes
        $(".catLabel").each(function() {
            if ($(this).html()) {
                $(this).addClass( 'task-board-category' );
            }
        });
    } else {
        $("#settingsCategoryColors").removeClass( 'toolbarButtonToggled' );
        $(".tdReport .reportBkgr").removeClass( 'task-board' );
        $(".liNote .liNoteBkgr").removeClass( 'task-board' );
        $(".catLabel").removeClass( 'task-board-category' );
    }
}

// Update category colors
static updateCategoryColors(project_id, id, old_category, new_category) {
    var note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    var old_color = $("#category-" + old_category).attr('data-color');
    var new_color = $("#category-" + new_category).attr('data-color');

    $("#trReportNr" + id + " .reportBkgr").removeClass( 'color-' + old_color );
    $("#item-" + note_id + " .liNoteBkgr").removeClass( 'color-' + old_color );
    $("#noteCatLabel-P" + project_id + "-" + id).removeClass( 'color-' + old_color );

    $("#trReportNr" + id + " .reportBkgr").addClass( 'color-' + new_color );
    $("#item-" + note_id + " .liNoteBkgr").addClass( 'color-' + new_color );
    $("#noteCatLabel-P" + project_id + "-" + id).addClass( 'color-' + new_color);
}

//------------------------------------------------
// Modal Dialogs routines
//------------------------------------------------

//------------------------------------------------
static #modalTransferNote(project_id, user_id, id) {
    $("#dialogTransferNote-P" + project_id).removeClass( 'hideMe' );
    $("#dialogTransferNote-P" + project_id).dialog({
        buttons: [
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_MOVE_BTN'),
                click : function() {
                    var target_project_id = $("#listNoteProject-P" + project_id + " option:selected").val();
                    _BoardNotes_.#sqlTransferNote(project_id, user_id, id, target_project_id);
                    $( this ).dialog( "close" );
                    _BoardNotes_.#sqlRefreshTabs(user_id);
                    _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
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
static #modalCreateTask(project_id, user_id, id, is_active, title, description, category_id) {
    $.ajaxSetup ({
        cache: false
    });
    $("#dialogCreateTaskParams").removeClass( 'hideMe' );
    $("#deadloading").addClass( 'hideMe' );
    $("#listCatCreateTask-P" + project_id).val(category_id).change();
    $("#dialogCreateTask-P" + project_id).removeClass( 'hideMe' );
    $("#dialogCreateTask-P" + project_id).dialog({
        buttons: [
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CREATE_BTN'),
                click: function() {
                    var categoryCreateTask = $("#listCatCreateTask-P" + project_id + " option:selected").val();
                    var columnCreateTask = $("#listColCreateTask-P" + project_id + " option:selected").val();
                    var swimlaneCreateTask = $("#listSwimCreateTask-P" + project_id + " option:selected").val();
                    var removeNote = $("#removeNote-P" + project_id).is(":checked");

                    var loadUrl = '/?controller=BoardNotesController&action=boardNotesCreateTask&plugin=BoardNotes'
                                + '&project_custom_id=' + project_id
                                + '&user_id=' + user_id
                                + '&task_title=' + encodeURIComponent(title)
                                + '&task_description=' + encodeURIComponent(description)
                                + '&category_id=' + categoryCreateTask
                                + '&column_id=' + columnCreateTask
                                + '&swimlane_id=' + swimlaneCreateTask;

                    $("#dialogCreateTask-P" + project_id).dialog({
                        title: _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_RESULT_TITLE'),
                        buttons: [
                            {
                                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CLOSE_BTN'),
                                click: function() { $( this ).dialog( "close" ); }
                            },
                        ]
                    });
                    $("#dialogCreateTaskParams").addClass( 'hideMe' );
                    $("#deadloading").removeClass( 'hideMe' );
                    $("#deadloading").html(_BoardNotes_Translations_.msgLoadingSpinner).load(loadUrl);
                    if (removeNote) {
                        _BoardNotes_.#sqlDeleteNote(project_id, user_id, id);
                        _BoardNotes_.#sqlRefreshTabs(user_id);
                        _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
                    }
                },
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static #modalDeleteAllDoneNotes(project_id, user_id) {
    $("#dialogDeleteAllDone").removeClass( 'hideMe' );
    $("#dialogDeleteAllDone").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_DELETE_BTN'),
                click: function() {
                    _BoardNotes_.#sqlDeleteAllDoneNotes(project_id, user_id);
                    $( this ).dialog( "close" );
                    _BoardNotes_.#sqlRefreshTabs(user_id);
                    _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
                },
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static #modalStats(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesStats&plugin=BoardNotes'
                + '&project_custom_id=' + project_id
                + '&user_id=' + user_id;
    $("#dialogStatsInside").html(_BoardNotes_Translations_.msgLoadingSpinner).load(loadUrl,
        function() {
            _BoardNotes_Stats_.prepareDocument();
        });

    $("#dialogStats").removeClass( 'hideMe' );
    $("#dialogStats").dialog({
        buttons: [
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CLOSE_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static #modalReport(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    $("#dialogReport-P" + project_id).removeClass( 'hideMe' );
    $("#dialogReport-P" + project_id).dialog({
        buttons: [
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CREATE_BTN'),
                click: function() {
                    var category = $("#catReport-P" + project_id + " option:selected").text();
                    var loadUrl = "/?controller=BoardNotesController&action=boardNotesReport&plugin=BoardNotes"
                                + "&project_custom_id=" + project_id
                                + "&user_id=" + user_id
                                + "&category=" + encodeURIComponent(category);
                    $("#result" + project_id).html(_BoardNotes_Translations_.msgLoadingSpinner).load(loadUrl,
                        function() {
                            _BoardNotes_Report_.prepareDocument();
                            _BoardNotes_.attachAllHandlers();
                        });
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return true;
}

//------------------------------------------------
// SQL routines
//------------------------------------------------

//------------------------------------------------
// SQL note transfer (to another project)
static #sqlTransferNote(project_id, user_id, id, target_project_id) {
    var note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesTransferNote&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&target_project_id=' + target_project_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('sqlTransferNote');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// SQL note update (title, description, category and status)
static #sqlUpdateNote(project_id, user_id, id) {
    var note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    var title = $("#noteTitleInput-P" + project_id + "-" + id).val().trim();
    var description = $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').val();
    var category = $("#cat-P" + project_id + "-" + id + " option:selected").text();
    var is_active = $("#noteDoneCheckmark-P" + project_id + "-" + id).attr('data-id');

    if (!title) {
        alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_NOTE_UPDATE_TITLE_EMPTY_MSG') );
        title = $("#noteTitleLabel-P" + project_id + "-" + id).html();
        $("#noteTitleInput-P" + project_id + "-" + id).val(title);
    }
    $("#noteTitleLabel-P" + project_id + "-" + id).html(title);

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesUpdateNote&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
        success: function(response) {
            var lastModifiedTimestamp = parseInt(response);
            if (lastModifiedTimestamp > 0) {
                $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp);
                $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").html(_BoardNotes_Translations_.msgLoadingSpinner).load(
                    '/?controller=BoardNotesController&action=boardNotesRefreshMarkdownPreviewWidget&plugin=BoardNotes'
                        + '&markdown_text=' + encodeURIComponent(description),
                ).css('height', 'auto');
            } else {
                alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_NOTE_UPDATE_INVALID_MSG') );
                _BoardNotes_.#sqlRefreshTabs(user_id);
                _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('sqlUpdateNote');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// SQL note update Status only!
static #sqlUpdateNoteStatus(project_id, user_id, id) {
    var note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    var is_active = $("#noteDoneCheckmark-P" + project_id + "-" + id).attr('data-id');

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesUpdateNoteStatus&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&is_active=' + is_active,
        success: function(response) {
            var lastModifiedTimestamp = parseInt(response);
            if (lastModifiedTimestamp > 0) {
                $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp);
            } else {
                alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_NOTE_UPDATE_INVALID_MSG') );
                _BoardNotes_.#sqlRefreshTabs(user_id);
                _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('sqlUpdateNoteStatus');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
static #sqlAddNote(project_id, user_id) {
    var title = $("#inputNewNote").val().trim();
    var description = $('[name="editorMarkdownDetailsNewNote"]').val();
    var category = $("#catNewNote" + " option:selected").text();
    var is_active = "1";

    if (!title) {
        alert( _BoardNotes_Translations_.getTranslationExportToJS('BoardNotes_JS_NOTE_ADD_TITLE_EMPTY_MSG') );
        return false;
    }

    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesAddNote&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('sqlAddNote');
            alert(e);
        }
     });
    return false;
}

//------------------------------------------------
static #sqlDeleteNote(project_id, user_id, id) {
    var note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesDeleteNote&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('sqlDeleteNote');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
static #sqlDeleteAllDoneNotes(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesDeleteAllDoneNotes&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('sqlDeleteAllDoneNotes');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
static #sqlRefreshTabs(user_id) {
    // refresh ONLY if notes are viewed via dashboard and project tabs are present
    if ($("#tabs").length == 0) return;

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesRefreshTabs&plugin=BoardNotes'
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#tabs").html(_BoardNotes_Translations_.msgLoadingSpinner).load(loadUrl);
    }, 50);
}

//------------------------------------------------
static #sqlRefreshNotes(project_id, user_id) {
    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
        cache: false
    });
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesRefreshProject&plugin=BoardNotes'
                + '&project_custom_id=' + project_id
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#result" + project_id).html(_BoardNotes_Translations_.msgLoadingSpinner).load(loadUrl,
            function() {
                _BoardNotes_Project_.prepareDocument();
                _BoardNotes_.#noteDetailsDblclickHandlersDisable();
                _BoardNotes_.attachAllHandlers();
            });
    }, 100);
}

//------------------------------------------------
static #sqlToggleSessionOption(session_option) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesToggleSessionOption&plugin=BoardNotes'
            + '&session_option=' + session_option,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('sqlToggleSessionOption');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// SQL update positions
static sqlUpdatePosition(project_id, user_id, order, nrNotes) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesUpdatePosition&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&order=' + order
            + '&nrNotes=' + nrNotes,
        success: function() {
        },
        error: function(xhr,textStatus,e) {
            alert('sqlUpdatePosition');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// SQL get last modified timestamp
static #sqlGetLastModifiedTimestamp(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=BoardNotesController&action=boardNotesGetLastModifiedTimestamp&plugin=BoardNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function(response) {
            var lastModifiedTimestamp = parseInt(response);
            _BoardNotes_.#checkAndTriggerRefresh(lastModifiedTimestamp);
        },
        error: function(xhr,textStatus,e) {
            alert('sqlGetLastModifiedTimestamp');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// AUTO-Refresh routines
//------------------------------------------------

//------------------------------------------------
static #showBusyIcon() {
    $("#boardnotesBusyIcon").removeClass( 'hideMe' );
}

//------------------------------------------------
static #hideBusyIcon( ) {
    $("#boardnotesBusyIcon").addClass( 'hideMe' );
}

//------------------------------------------------
// schedule check for modifications every 15 sec
static scheduleCheckModifications() {
    setTimeout(function() {
        if ($(".liNewNote").length == 0) {
            // this means the page no longer displays notes list(s)
            // most probably we are showing the report page
            // the scheduled check is no longer relevant => so abort it
            return;
        }

        _BoardNotes_.#showBusyIcon();

        var project_id = $("#refProjectId").attr('data-project');
        var user_id = $("#refProjectId").attr('data-user');
        var title = (project_id != 0) ? $("#inputNewNote").val().trim() : "";
        var description = (project_id != 0) ? $('[name="editorMarkdownDetailsNewNote"]').val() : "";

        // skip SQL query if page not visible, or if new note has pending changes
        if (!KB.utils.isVisible() || title!="" || description!="") {
            _BoardNotes_.scheduleCheckModifications();
            return;
        }

        _BoardNotes_.#sqlGetLastModifiedTimestamp(project_id, user_id);
    }, 15 * 1000); // 15 sec
}

//------------------------------------------------
// check if page refresh is necessary
static #checkAndTriggerRefresh(lastModifiedTimestamp) {
    var lastRefreshedTimestamp = $("#refProjectId").attr('data-timestamp');

    if (lastRefreshedTimestamp < lastModifiedTimestamp) {
        var project_id = $("#refProjectId").attr('data-project');
        var user_id = $("#refProjectId").attr('data-user');
        _BoardNotes_.#sqlRefreshTabs(user_id);
        _BoardNotes_.#sqlRefreshNotes(project_id, user_id);
    }

    _BoardNotes_.scheduleCheckModifications();
    _BoardNotes_.#hideBusyIcon();
}

//------------------------------------------------
// Global routines
//------------------------------------------------

//------------------------------------------------
static attachAllHandlers() {
    _BoardNotes_.#noteDetailsHandlers();
    _BoardNotes_.#noteStatusHandlers();
    _BoardNotes_.#noteActionHandlers();
    _BoardNotes_.#settingsHandlers();
}

//------------------------------------------------

} // class _BoardNotes_

//////////////////////////////////////////////////
$(function() {
    // attach all handlers on load page
    _BoardNotes_.attachAllHandlers();

    // start the recursive check sequence on load page
    _BoardNotes_.scheduleCheckModifications();
});
