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

static isMobile() {

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
// Global vars for options
//------------------------------------------------

static optionShowCategoryColors = false;
static optionSortByStatus = false;
static optionShowAllDone = false;
static optionShowTabStats = false;

//------------------------------------------------
// Note Details routines
//------------------------------------------------

//------------------------------------------------
// Adjust scrollableContent container
static adjustScrollableContent() {
    const scrollableContent = $("#scrollableContent");
    if (!scrollableContent.length) return; // missing scrollableContent when NOT in project screen
    scrollableContent.height(0);

    let maxHeight;
    if ( _TodoNotes_.isMobile() ) {
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
static adjustNotePlaceholders(project_id, id) {
    const isTitle = (project_id === '0' && id === '0');
    if (isTitle) {
        if (!$(".liNewNote").length) return; // missing NewNote when NOT in project screen
        const offsetTitle = $(".labelNewNote").offset().top;
        let offsetButtons = $("#settingsShowAllDone").offset().top;
        offsetButtons += $("#settingsShowAllDone").outerHeight();
        if (offsetTitle > offsetButtons) {
            $("#placeholderNewNote").removeClass( 'hideMe' );
        } else {
            $("#placeholderNewNote").addClass( 'hideMe' );
        }
    } else {
        const offsetStatus = $("#buttonStatus-P" + project_id + "-" + id).offset().top;
        const offsetDetails = $("#showDetails-P" + project_id + "-" + id).offset().top;
        if (offsetStatus === offsetDetails) {
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
        _TodoNotes_.adjustNotePlaceholders('0', '0');
        $("button" + ".buttonStatus").each(function() {
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.adjustNotePlaceholders(project_id, id);
        });
    }, 100);
}

//------------------------------------------------
// Adjust ALL notePlaceholder containers
static adjustAllNotesTitleInputs() {
    _TodoNotes_.showTitleInputNewNote();
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
    const noteTitleLabel = $("#noteTitleLabel-P" + project_id + "-" + id);
    const noteTitleInput = $("#noteTitleInput-P" + project_id + "-" + id);
    const noteDetails = $("#noteDetails-P" + project_id + "-" + id);
    const previewDetails = $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview");

    if (show_title_input) {
        noteTitleLabel.addClass( 'hideMe' );
        noteTitleInput.removeClass( 'hideMe' );
        noteTitleInput.focus();
        noteTitleInput[0].selectionStart = 0;
        noteTitleInput[0].selectionEnd = 0;

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

        if (_TodoNotes_.isMobile()) {
            noteTitleInput.width( inputWidth );
        } else {
            const toolbarButtons = noteDetails.parent().find(".toolbarNoteButtons");
            noteTitleInput.width( inputWidth - toolbarButtons.width());
        }
    } else {
        noteTitleInput.blur();
        noteTitleInput.addClass( 'hideMe' );
        noteTitleLabel.removeClass( 'hideMe' );
    }

    _TodoNotes_.adjustNotePlaceholders(project_id, id);
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
    if (!_TodoNotes_.isMobile()) {
        $("#noteSave-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    }
    $("#toolbarSeparator-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteTransfer-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteCreateTask-P" + project_id + "-" + id).toggleClass( 'hideMe' );
    $("#noteCatLabel-P" + project_id + "-" + id).toggleClass( 'hideMe' );

    _TodoNotes_.#showTitleInput(project_id, id, !$("#noteTitleInput-P" + project_id + "-" + id).hasClass( 'hideMe' ));

    $("#showDetails-P" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetails-P" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-up" );

    _TodoNotes_.adjustNotePlaceholders(project_id, id);
}

//------------------------------------------------
// Blink note
static #blinkNote(project_id, id) {
    const note_id = $("#noteId-P" + project_id + "-" + id).attr('data-note');
    setTimeout(function() { $("#item-" + note_id).addClass( 'blurMe' ); }, 0);
    setTimeout(function() { _TodoNotes_.#toggleDetails(project_id, id); }, 100);
    setTimeout(function() { _TodoNotes_.#toggleDetails(project_id, id); }, 200);
    setTimeout(function() { $("#item-" + note_id).removeClass( 'blurMe' ); }, 300);
}

//------------------------------------------------
// Show input visuals for title of NewNote
static showTitleInputNewNote() {
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
    editor.prop('title', _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__PROJECT_NOTE_DETAILS_SAVE_HINT'));
}

//------------------------------------------------
// Show details menu for NewNote (toggle class)
static #toggleDetailsNewNote() {
    $("#detailsNewNote").toggleClass( 'hideMe' );
    if (!_TodoNotes_.isMobile()) {
        $("#saveNewNote").toggleClass( 'hideMe' );
    }
    $("#showDetailsNewNote").find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetailsNewNote").find('i').toggleClass( "fa-angle-double-up" );

    _TodoNotes_.showTitleInputNewNote();
}

//------------------------------------------------
// Note Details handlers
//------------------------------------------------

//------------------------------------------------
static #noteDetailsDblclickHandlersInitialized = false;

static #noteDetailsDblclickHandlers() {
    //------------------------------------------------

    if (_TodoNotes_.#noteDetailsDblclickHandlersInitialized) return;

    // Show details for new note by dblclick the New Note
    $(".liNewNote").dblclick(function() {
        _TodoNotes_.#toggleDetailsNewNote();
    });

    // Show details for note by dblclick the Note
    $(".liNote").dblclick(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#toggleDetails(project_id, id);

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
        }, 100);
    });

    _TodoNotes_.#noteDetailsDblclickHandlersInitialized = true;

    //------------------------------------------------
}

static #noteDetailsDblclickHandlersDisable() {
    // console.log('_TodoNotes_.noteDetailsDblclickHandlersDisable');
    //------------------------------------------------

    $(".liNewNote").off('dblclick');
    $(".liNote").off('dblclick');

    _TodoNotes_.#noteDetailsDblclickHandlersInitialized = false;

    //------------------------------------------------
}

//------------------------------------------------
static #noteDetailsHandlers() {
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
    $(".disableEventsPropagation").click(function (event) {
        event.stopPropagation();

        _TodoNotes_.#noteDetailsDblclickHandlersDisable();

        setTimeout(function() {
            _TodoNotes_.#noteDetailsDblclickHandlers();
        }, 500);
    });

    $(".disableEventsPropagation").dblclick(function (event) {
        event.stopPropagation();
    });

    _TodoNotes_.#noteDetailsDblclickHandlers();

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
        _TodoNotes_.#toggleDetailsNewNote();
    });

    // On TAB key open detailed view for New Note
    $(".inputNewNote").keydown(function(event) {
        if (event.keyCode === 9) {
            $("#editDetailsNewNote").addClass( 'hideMe' );
            $("#noteMarkdownDetailsNewNote_Preview").addClass( 'hideMe' );
            $("#noteMarkdownDetailsNewNote_Editor").removeClass( 'hideMe' );
            $('[name="editorMarkdownDetailsNewNote"]').addClass( 'noteEditorTextarea' );

            if ($("#detailsNewNote").hasClass( 'hideMe' )) {
                _TodoNotes_.#toggleDetailsNewNote();
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
        _TodoNotes_.#toggleDetails(project_id, id);

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
        }, 100);
    });

    // On TAB key open detailed view for Note
    $(".noteTitle").keydown(function(event) {
        if (event.keyCode === 9) { // TAB
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');

            _TodoNotes_.#showDetailsInput(project_id, id, true);
            if ($("#noteDetails-P" + project_id + "-" + id).hasClass( 'hideMe' )) {
                _TodoNotes_.#toggleDetails(project_id, id);
            }

            setTimeout(function() {
                $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').focus();
                _TodoNotes_.adjustScrollableContent();
            }, 100);
        }
    });

    // On click Edit Details button for Note
    $(".editDetails").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');

        _TodoNotes_.#showDetailsInput(project_id, id, true);

        setTimeout(function() {
            $('[name="editorMarkdownDetails-P' + project_id + '-' + id + '"]').focus();
            _TodoNotes_.adjustScrollableContent();
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
            _TodoNotes_.adjustScrollableContent();
        }, 100);
    });

    // Click on category label to auto open details and change category
    $("label" + ".catLabelClickable").click(function() {
        const project_id = $(this).attr('data-project');
        const id = $(this).attr('data-id');
        _TodoNotes_.#toggleDetails(project_id, id);

        setTimeout(function() {
            $("#cat-P" + project_id + "-" + id + "-button").trigger('click');
            _TodoNotes_.adjustScrollableContent();
        }, 100);
    });

    //------------------------------------------------

    // Refresh notes order by explicit conditional button
    $("button" + ".noteRefreshOrder").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_.sqlRefreshNotes(project_id, user_id);
    });

    //------------------------------------------------
}

//------------------------------------------------
// Note Status routines & handlers
//------------------------------------------------

//------------------------------------------------
// Switch note status
static #switchNoteStatus(project_id, id) {
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
static #refreshNoteStatus(project_id, id) {
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

    _TodoNotes_Statuses_.expandStatusAliases();
}

//------------------------------------------------
static #noteStatusHandlers() {
    //------------------------------------------------

    //Status button handler
    $("button" + ".buttonStatus").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');

        const ref_project_id = $("#refProjectId").attr('data-project');
        const readonlyNotes = (ref_project_id === '0'); // Overview Mode

        _TodoNotes_.#switchNoteStatus(project_id, id);
        _TodoNotes_.#refreshNoteStatus(project_id, id);

        if (readonlyNotes) {
            _TodoNotes_.#sqlUpdateNoteStatus(project_id, user_id, id);
        } else {
            _TodoNotes_.#showTitleInput(project_id, id, false);
            _TodoNotes_.#showDetailsInput(project_id, id, false);
            _TodoNotes_.#sqlUpdateNote(project_id, user_id, id);
        }

        if (_TodoNotes_.optionSortByStatus) {
            $("#noteRefreshOrder-P" + project_id + "-" + id).removeClass( 'hideMe' );
        }

        _TodoNotes_.#blinkNote(project_id, id);
        _TodoNotes_.refreshShowAllDone();

        _TodoNotes_.adjustNotePlaceholders(project_id, id);

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
        }, 400); // waiting for blinkNote() to finish
    });

    //------------------------------------------------
}

//------------------------------------------------
// Add/Update/Delete/Transfer/CreateTask handlers
//------------------------------------------------

//------------------------------------------------
static #noteActionHandlers() {
    //------------------------------------------------

    // POST ADD when ENTER key on New Note title
    $(".inputNewNote").keypress(function(event) {
        if (event.keyCode === 13) { // ENTER
            const project_id = $(this).attr('data-project');
            const user_id = $(this).attr('data-user');
            $(".inputNewNote").blur();
            _TodoNotes_.#sqlAddNote(project_id, user_id);
        }
    });

    // POST ADD when TAB key on New Note description
    $(".noteEditorMarkdownNewNote").keydown(function(event) {
        if (event.keyCode === 9) {
            const project_id = $("#noteMarkdownDetailsNewNote_Editor").attr('data-project');
            const user_id = $("#noteMarkdownDetailsNewNote_Editor").attr('data-user');
            $(".inputNewNote").blur();
            _TodoNotes_.#sqlAddNote(project_id, user_id);
        }
    });

    // POST ADD on Save button for New Note
    $("button" + ".saveNewNote").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        $(".inputNewNote").blur();
        _TodoNotes_.#sqlAddNote(project_id, user_id);
    });

    //------------------------------------------------

    // POST UPDATE when ENTER on Note title
    $(".noteTitle").keydown(function(event) {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        if (event.keyCode === 13) { // ENTER
            _TodoNotes_.#showTitleInput(project_id, id, false);
            _TodoNotes_.#showDetailsInput(project_id, id, false);
            _TodoNotes_.#sqlUpdateNote(project_id, user_id, id);
            _TodoNotes_.#blinkNote(project_id, id);

            setTimeout(function() {
                _TodoNotes_.adjustScrollableContent();
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
            _TodoNotes_.#showDetailsInput(project_id, id, false);
            _TodoNotes_.#sqlUpdateNote(project_id, user_id, id);
            _TodoNotes_.#blinkNote(project_id, id);

            setTimeout(function() {
                _TodoNotes_.adjustScrollableContent();
            }, 400); // waiting for blinkNote() to finish
        }
    });

    // POST UPDATE on Save button for existing notes
    $("button" + ".noteSave").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_.#showTitleInput(project_id, id, false);
        _TodoNotes_.#showDetailsInput(project_id, id, false);
        _TodoNotes_.#sqlUpdateNote(project_id, user_id, id);
        _TodoNotes_.#blinkNote(project_id, id);

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
        }, 400); // waiting for blinkNote() to finish
    });

    //------------------------------------------------

    // POST on Delete Note button
    $("button" + ".noteDelete").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_.#modalDeleteNote(project_id, user_id, id);
    });

    // POST on Transfer Note button
    $("button" + ".noteTransfer").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const id = $(this).attr('data-id');
        _TodoNotes_.#modalTransferNote(project_id, user_id, id);
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
        _TodoNotes_.#modalCreateTaskFromNote(project_id, user_id, id, is_active, title, description, category_id);
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

                _TodoNotes_.updateCategoryColors(project_id, id, old_category, new_category);
                // avoid the ugly empty category label boxes
                if (new_category && _TodoNotes_.optionShowCategoryColors) {
                    $("#noteCatLabel-P" + project_id + "-" + id).addClass( 'task-board-category' );
                }
                if (!new_category || !_TodoNotes_.optionShowCategoryColors) {
                    $("#noteCatLabel-P" + project_id + "-" + id).removeClass( 'task-board-category' );
                }

                _TodoNotes_.#showTitleInput(project_id, id, false);
                _TodoNotes_.#showDetailsInput(project_id, id, false);
                _TodoNotes_.#sqlUpdateNote(project_id, user_id, id);
                _TodoNotes_.#blinkNote(project_id, id);

                setTimeout(function() {
                    _TodoNotes_.adjustScrollableContent();
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
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.#toggleDetails(project_id, id);
        }
    });

    setTimeout(function() {
        _TodoNotes_.adjustScrollableContent();
    }, 100);
}

static #settingsExpandAll() {
    $(".showDetails").each(function() {
        if ($(this).find('i').hasClass( 'fa-angle-double-down' ))
        {
            const project_id = $(this).attr('data-project');
            const id = $(this).attr('data-id');
            _TodoNotes_.#toggleDetails(project_id, id);
        }
    });

    setTimeout(function() {
        _TodoNotes_.adjustScrollableContent();
    }, 100);
}

static #toggleList(project_id) {
    $("#sortableList-P" + project_id).toggleClass( 'hideMe' );
    $("#toggleList-P" + project_id).find('i').toggleClass( "fa-chevron-circle-up" );
    $("#toggleList-P" + project_id).find('i').toggleClass( "fa-chevron-circle-down" );
}

//------------------------------------------------
static #settingsHandlers() {
    //------------------------------------------------

    // POST delete all done
    $("#settingsDeleteAllDone").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_.#modalDeleteAllDoneNotes(project_id, user_id);
    });

    // POST stats
    $("#settingsStats").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_.#modalStats(project_id, user_id);
    });

    // Sort and filter for report
    $("#settingsReport").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_.#modalReport(project_id, user_id);
    });

    //------------------------------------------------

    $("#settingsCollapseAll").click(function() {
        _TodoNotes_.#settingsCollapseAll();
    });

    $(document).keydown(function(event) {
        if (event.keyCode !== 109) return; // [-] key
        _TodoNotes_.#settingsCollapseAll();
    });

    $("#settingsExpandAll").click(function() {
        _TodoNotes_.#settingsExpandAll();
    });

    $(document).keydown(function(event) {
        if (event.keyCode !== 107) return; // [+] key
        _TodoNotes_.#settingsExpandAll();
    });

    //------------------------------------------------

    $("#settingsSortByStatus").click(function() {
        _TodoNotes_.sqlToggleSessionOption('todonotesOption_SortByStatus');

        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        _TodoNotes_.sqlRefreshNotes(project_id, user_id);
    });

    $("#settingsShowAllDone").click(function() {
        _TodoNotes_.sqlToggleSessionOption('todonotesOption_ShowAllDone');

        _TodoNotes_.optionShowAllDone = !_TodoNotes_.optionShowAllDone;
        _TodoNotes_.refreshShowAllDone();

        _TodoNotes_.adjustAllNotesPlaceholders();

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
        }, 100);
    });

    $("#settingsCategoryColors").click(function() {
        _TodoNotes_.sqlToggleSessionOption('todonotesOption_ShowCategoryColors');

        _TodoNotes_.optionShowCategoryColors = !_TodoNotes_.optionShowCategoryColors;
        _TodoNotes_.refreshCategoryColors();
    });

    //------------------------------------------------

    // Toogle lists in OverviewMode
    $(".headerList").dblclick(function() {
        const project_id = $(this).find("button" + ".toggleList").attr('data-project');
        _TodoNotes_.#toggleList(project_id);

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
        }, 100);
    });

    $("button" + ".toggleList").click(function() {
        const project_id = $(this).attr('data-project');
        _TodoNotes_.#toggleList(project_id);

        setTimeout(function() {
            _TodoNotes_.adjustScrollableContent();
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
static refreshShowAllDone() {
    if (_TodoNotes_.optionShowAllDone) {
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
// Refresh sort by Status
static refreshSortByStatus() {
    if (_TodoNotes_.optionSortByStatus) {
        $("#settingsSortByStatus").addClass( 'buttonToggled' );
    } else {
        $("#settingsSortByStatus").removeClass( 'buttonToggled' );
    }
}

//------------------------------------------------
// Refresh category colors
static refreshCategoryColors() {
    if (_TodoNotes_.optionShowCategoryColors) {
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
static updateCategoryColors(project_id, id, old_category, new_category) {
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
// Modal Dialogs routines
//------------------------------------------------

//------------------------------------------------
static #modalDeleteNote(project_id, user_id, id) {
    $("#dialogDeleteNote").removeClass( 'hideMe' );
    $("#dialogDeleteNote").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click: function() {
                    _TodoNotes_.#sqlDeleteNote(project_id, user_id, id);
                    $( this ).dialog( "close" );
                    _TodoNotes_.sqlRefreshNotes(project_id, user_id);
                    _TodoNotes_.sqlRefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
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
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click: function() {
                    _TodoNotes_.#sqlDeleteAllDoneNotes(project_id, user_id);
                    $( this ).dialog( "close" );
                    _TodoNotes_.sqlRefreshNotes(project_id, user_id);
                    _TodoNotes_.sqlRefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static #modalTransferNote(project_id, user_id, id) {
    $("#dialogTransferNote-P" + project_id).removeClass( 'hideMe' );
    $("#dialogTransferNote-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_MOVE_BTN'),
                click : function() {
                    const target_project_id = $("#listNoteProject-P" + project_id + " option:selected").val();
                    _TodoNotes_.#sqlTransferNote(project_id, user_id, id, target_project_id);
                    $( this ).dialog( "close" );
                    _TodoNotes_.sqlRefreshNotes(project_id, user_id);
                    _TodoNotes_.sqlRefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static #modalCreateTaskFromNote(project_id, user_id, id, is_active, title, description, category_id) {
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
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
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
                        title: _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_RESULT_TITLE'),
                        buttons: [
                            {
                                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CLOSE_BTN'),
                                click: function() { $( this ).dialog( "close" ); }
                            },
                        ]
                    });
                    $("#dialogCreateTaskParams").addClass( 'hideMe' );
                    $("#deadloading").removeClass( 'hideMe' );
                    $("#deadloading").html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl);
                    if (removeNote) {
                        _TodoNotes_.#sqlDeleteNote(project_id, user_id, id);
                        _TodoNotes_.sqlRefreshNotes(project_id, user_id);
                        _TodoNotes_.sqlRefreshTabs(user_id);
                    }
                },
            },
            {
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static #modalReport(project_id, user_id) {
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
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
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
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return true;
}

//------------------------------------------------
static #modalStats(project_id, user_id) {
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
                text : _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_DIALOG_CLOSE_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
// SQL routines
//------------------------------------------------

//------------------------------------------------
static #sqlAddNote(project_id, user_id) {
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
            _TodoNotes_.sqlRefreshNotes(project_id, user_id);
            _TodoNotes_.sqlRefreshTabs(user_id);
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
        url: '/?controller=TodoNotesController&action=DeleteAllDoneNotes&plugin=TodoNotes'
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
// SQL note update (title, description, category and status)
static #sqlUpdateNote(project_id, user_id, id) {
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
            const lastModifiedTimestamp = parseInt(response);
            if (lastModifiedTimestamp > 0) {
                $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp);
                // refresh and render the details markdown preview
                $("#noteMarkdownDetails-P" + project_id + "-" + id + "_Preview").html(_TodoNotes_Translations_.msgLoadingSpinner).load(
                    '/?controller=TodoNotesController&action=RefreshMarkdownPreviewWidget&plugin=TodoNotes'
                        + '&markdown_text=' + encodeURIComponent(description),
                ).css('height', 'auto');
            } else {
                alert( _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_INVALID_MSG') );
                _TodoNotes_.sqlRefreshNotes(project_id, user_id);
                _TodoNotes_.sqlRefreshTabs(user_id);
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
            const lastModifiedTimestamp = parseInt(response);
            if (lastModifiedTimestamp > 0) {
                $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp);
            } else {
                alert( _TodoNotes_Translations_.getTranslationExportToJS('TodoNotes__JS_NOTE_UPDATE_INVALID_MSG') );
                _TodoNotes_.sqlRefreshNotes(project_id, user_id);
                _TodoNotes_.sqlRefreshTabs(user_id);
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
// SQL update notes positions
static sqlUpdateNotesPositions(project_id, user_id, order) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=UpdateNotesPositions&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id
            + '&order=' + order,
        success: function(response) {
            const lastModifiedTimestamp = parseInt(response);
            if (lastModifiedTimestamp > 0) {
                $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp);
            } else {
                _TodoNotes_.sqlRefreshNotes(project_id, user_id);
            }
        },
        error: function(xhr,textStatus,e) {
            alert('sqlUpdateNotesPositions');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
// SQL note transfer (to another project)
static #sqlTransferNote(project_id, user_id, id, target_project_id) {
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
            alert('sqlTransferNote');
            alert(e);
        }
    });
    return false;
}

//------------------------------------------------
static sqlRefreshNotes(project_id, user_id) {
    // console.log('_TodoNotes_.sqlRefreshNotes(');

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
static sqlRefreshTabs(user_id) {
    // console.log('_TodoNotes_.sqlRefreshTabs(');

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
static sqlToggleSessionOption(session_option) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=ToggleSessionOption&plugin=TodoNotes'
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
// SQL get last modified timestamp
static #sqlGetLastModifiedTimestamp(project_id, user_id) {
    $.ajax({
        cache: false,
        type: "POST",
        url: '/?controller=TodoNotesController&action=GetLastModifiedTimestamp&plugin=TodoNotes'
            + '&project_custom_id=' + project_id
            + '&user_id=' + user_id,
        success: function(response) {
            _TodoNotes_.#checkAndTriggerRefresh(JSON.parse(response));
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
static #showRefreshIcon() {
    $("#refreshIcon").removeClass( 'hideMe' );
}

//------------------------------------------------
static #hideRefreshIcon( ) {
    $("#refreshIcon").addClass( 'hideMe' );
}

//------------------------------------------------
// schedule check for modifications every 15 sec
static scheduleCheckModifications() {
    setTimeout(function() {
        _TodoNotes_.#showRefreshIcon();

        const project_id = $("#refProjectId").attr('data-project');
        const user_id = $("#refProjectId").attr('data-user');

        const is_project = ($(".liNewNote").length === 1);
        const title = (is_project && project_id !== '0') ? $("#inputNewNote").val().trim() : '';
        const description = (is_project && project_id !== '0') ? $('[name="editorMarkdownDetailsNewNote"]').val() : '';

        // skip SQL query if page not visible, or if new note has pending changes
        if (!KB.utils.isVisible() || title !== '' || description !== '') {
            _TodoNotes_.scheduleCheckModifications();
            return;
        }

        _TodoNotes_.#sqlGetLastModifiedTimestamp(project_id, user_id);
    }, 15 * 1000); // 15 sec
}

//------------------------------------------------
// check if page refresh is necessary
static #checkAndTriggerRefresh(lastModifiedTimestamp) {
    // console.log('_TodoNotes_.checkAndTriggerRefresh');

    const project_id = $("#refProjectId").attr('data-project');
    const user_id = $("#refProjectId").attr('data-user');
    const lastRefreshedTimestamp = $("#refProjectId").attr('data-timestamp');

    const is_project = ($(".liNewNote").length === 1);
    if (is_project && lastRefreshedTimestamp < lastModifiedTimestamp.notes) {
        _TodoNotes_.sqlRefreshNotes(project_id, user_id);
    }
    if (lastRefreshedTimestamp < lastModifiedTimestamp.projects) {
        _TodoNotes_.sqlRefreshTabs(user_id);
    }
    if (lastRefreshedTimestamp < lastModifiedTimestamp.max) {
        $("#refProjectId").attr('data-timestamp', lastModifiedTimestamp.max);
    }

    _TodoNotes_.scheduleCheckModifications();
    _TodoNotes_.#hideRefreshIcon();
}

//------------------------------------------------
// Global routines
//------------------------------------------------

//------------------------------------------------
static attachAllHandlers() {
    // console.log('_TodoNotes_.attachAllHandlers');

    _TodoNotes_.#noteDetailsHandlers();
    _TodoNotes_.#noteStatusHandlers();
    _TodoNotes_.#noteActionHandlers();
    _TodoNotes_.#settingsHandlers();
}

//------------------------------------------------

} // class _TodoNotes_

//////////////////////////////////////////////////
$(function() {
    // start the recursive check sequence on load page
    _TodoNotes_.scheduleCheckModifications();
});

//////////////////////////////////////////////////
