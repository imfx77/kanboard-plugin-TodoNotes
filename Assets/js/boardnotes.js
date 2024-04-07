let _BoardNotes_ = {}; // namespace

//------------------------------------------------
// IsMobile check
//------------------------------------------------
_BoardNotes_.isMobile = function() {
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))){
      return true;
    }
    return false;
}

  //------------------------------------------------
  // Global vars for options
  //------------------------------------------------

  var optionShowCategoryColors = false;
  var optionSortByState = false;

  //------------------------------------------------
  // Translation Export to JS
  //------------------------------------------------

  var translationsExportToJS = null;

  function getTranslationExportToJS(textId) {
    // lazy init the translations ONCE
    if (translationsExportToJS == null) {
        translationsExportToJS = JSON.parse( $("#BoardNotes_TranslationsExportToJS").html() );
        $("#BoardNotes_TranslationsExportToJS").remove();
    }
    return translationsExportToJS[textId];
  }

  //------------------------------------------------
  // Note Details routines
  //------------------------------------------------

  // Adjust notePlaceholderDescription container
  function adjustNotePlaceholders(project_id, id) {
    var offsetCheck = $("#checkDone" + project_id + "-" + id).offset().top;
    var offsetDetails = $("#showDetails" + project_id + "-" + id).offset().top;
    if (offsetCheck == offsetDetails) {
      $("#notePlaceholderDescriptionP" + project_id + "-" + id).addClass( 'hideMe' );
    }
    else {
      $("#notePlaceholderDescriptionP" + project_id + "-" + id).removeClass( 'hideMe' );
    }
  }

  //------------------------------------------------

  // Show input or label visuals for titles of existing notes
  function showTitleInput(project_id, id, show_input) {
    $noteTitleLabel = $("#noteTitleLabelP" + project_id + "-" + id);
    $noteTitleInput = $("#noteTitleInputP" + project_id + "-" + id);
    $noteDescription = $('#noteDescriptionP' + project_id + "-" + id);
    $textareaDescription = $('#noteTextareaDescriptionP' + project_id + "-" + id);

    if (show_input) {
      $noteTitleLabel.addClass( 'hideMe' );
      $noteTitleInput.removeClass( 'hideMe' );
      $noteTitleInput.focus();
      $noteTitleInput[0].selectionStart = 0;
      $noteTitleInput[0].selectionEnd = 0;


      // get current width of the description textarea
      var inputWidth = $textareaDescription.width();
      if ( $noteDescription.hasClass( 'hideMe' ) ) {
        $noteDescription.toggleClass( 'hideMe' );
        inputWidth = $textareaDescription.width();
        $noteDescription.toggleClass( 'hideMe' );
      }

      $noteTitleInput.width( inputWidth );
    }
    else {
      $noteTitleInput.blur();
      $noteTitleInput.addClass( 'hideMe' );
      $noteTitleLabel.removeClass( 'hideMe' );
    }
    adjustNotePlaceholders(project_id, id);
  };

  // Show input or textarea visuals for descriptions of existing notes
  function showDescriptionInput(project_id, id, show_input) {
    $textareaDescription = $('#noteTextareaDescriptionP' + project_id + "-" + id);
    if (show_input) {
      $textareaDescription.addClass( "textareaDescriptionSelected" );
      $textareaDescription.removeClass( "textareaDescription" );
      $textareaDescription[0].selectionStart = 0;
      $textareaDescription[0].selectionEnd = 0;

    }
    else {
      $textareaDescription.addClass( "textareaDescription" );
      $textareaDescription.removeClass( "textareaDescriptionSelected" );
    }
  };

  //------------------------------------------------

  // Show details for existing notes (toggle class)
  function toggleDetails(project_id, id) {
    //alert(getTranslationExportToJS('BoardNotes_JS_TEST_STRING'));

    $("#noteDescriptionP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteDeleteP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteSaveP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteTransferP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#singleNoteToTaskP" + project_id + "-" + id).toggleClass( "hideMe" );
    $("#noteCatLabelP" + project_id + "-" + id).toggleClass( "hideMe" );

    $("#showDetails" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetails" + project_id + "-" + id).find('i').toggleClass( "fa-angle-double-up" );
    adjustNotePlaceholders(project_id, id);
  };

  // Show details menu for new note (toggle class)
  function toggleDetailsNew(project_id) {
    if ( !$('#noteDescriptionP' + project_id).hasClass( 'hideMe' ) ) {
        $("#inputNewNote" + project_id).width( $('#textareaNewNote' + project_id).width() );
    }

    $("#noteDescriptionP" + project_id).toggleClass( "hideMe" );
    $("#saveNewNote").toggleClass( "hideMe" );

    if ( !$('#noteDescriptionP' + project_id).hasClass( 'hideMe' ) ) {
        $("#inputNewNote" + project_id).width( $('#textareaNewNote' + project_id).width() );
    }

    $("#showDetailsNew").find('i').toggleClass( "fa-angle-double-down" );
    $("#showDetailsNew").find('i').toggleClass( "fa-angle-double-up" );

    setTimeout(function() { $( "#textareaNewNote" + project_id).focus(); }, 0);
  };

  // Blink note
  function blinkNote(project_id, id){
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    setTimeout(function() { $( "#item-" + note_id ).addClass( "blurMe" ); }, 0);
    setTimeout(function() { toggleDetails(project_id, id); }, 100);
    setTimeout(function() { toggleDetails(project_id, id); }, 200);
    setTimeout(function() { $( "#item-" + note_id ).removeClass( "blurMe" ); }, 300);
  };

  //------------------------------------------------
  // Note Details handlers
  //------------------------------------------------
  function NoteDetailsHandlers() {
    // Show details for note by dblclick the note
    $( ".liNote" ).dblclick(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      toggleDetails(project_id, id);
    });

    // BUT disable dblclick propagation for all marked sub-elements
    $('.disableDblClickPropagation').dblclick(function (event) {
      event.stopPropagation();
    });

    // Show details for note by menu button
    $( "button" + ".showDetails" ).click(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      toggleDetails(project_id, id);
    });

    //------------------------------------------------

    // Show details for new note by dblclick the new note
    $( ".liNewNote" ).dblclick(function() {
      var project_id = $(this).attr('data-project');
      toggleDetailsNew(project_id);
    });

    // Show details for new note by menu button
    $( "button" + ".showDetailsNew" ).click(function() {
      var project_id = $(this).attr('data-project');
      toggleDetailsNew(project_id);
    });

    // On TAB key open detailed view
    $('.inputNewNote').keydown(function(event) {
      if (event.keyCode == 9) {
        var project_id = $(this).attr('data-project');
        toggleDetailsNew(project_id);
      }
    });

    //------------------------------------------------

    // Switch visuals for description update note
    $('.textareaDescription').focus(function(event) {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      showDescriptionInput(project_id, id, true);
    });

    // Change from label to input on click
    $( "label" + ".noteTitle" ).click(function() {
      if ($(this).attr('data-disabled')) return;
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      showTitleInput(project_id, id, true);
    });

    // Click on category label to auto open details and change category
    $( "label" + ".catLabel" ).click(function() {
      var project_id = $(this).attr('data-project');
      var id = $(this).attr('data-id');
      toggleDetails(project_id, id);

      setTimeout(function() {
        $("#catP" + project_id + "-" + id + "-button").trigger('click');
      }, 100);
    });
  }

  //------------------------------------------------
  // Note State routines & handlers
  //------------------------------------------------

  // Switch note done state
  function switchNoteDoneState(project_id, id){
      $checkDone = $("#noteDoneCheckmarkP" + project_id + "-" + id);

      // cycle through states
      if ($checkDone.hasClass( "fa-circle-thin" )) {
        $checkDone.removeClass( "fa-circle-thin" );
        $checkDone.addClass( "fa-spinner fa-pulse" );
        return;
      }
      if ($checkDone.hasClass( "fa-spinner fa-pulse" )) {
        $checkDone.removeClass( "fa-spinner fa-pulse" );
        $checkDone.addClass( "fa-check" );
        return;
      }
      if ($checkDone.hasClass( "fa-check" )) {
        $checkDone.removeClass( "fa-check" );
        $checkDone.addClass( "fa-circle-thin" );
        return;
      }
  }

  // Update note done checkmark
  function updateNoteDoneCheckmark(project_id, id){
    $noteDoneCheckmark = $("#noteDoneCheckmarkP" + project_id + "-" + id);
    $noteTitleLabel = $("#noteTitleLabelP" + project_id + "-" + id);
    $noteDescription = $("#noteTextareaDescriptionP" + project_id + "-" + id);

    if( $noteDoneCheckmark.hasClass( "fa-check" ) ){
      $noteTitleLabel.addClass( "noteDoneDesignText" );
      $noteDescription.addClass( "noteDoneDesignTextarea" );
      $noteDoneCheckmark.attr('data-id', '0');
    }
    if( $noteDoneCheckmark.hasClass( "fa-circle-thin" ) ){
      $noteTitleLabel.removeClass( "noteDoneDesignText" );
      $noteDescription.removeClass( "noteDoneDesignTextarea" );
      $noteDoneCheckmark.attr('data-id', '1');
    }
    if( $noteDoneCheckmark.hasClass( "fa-spinner fa-pulse" ) ){
      $noteTitleLabel.removeClass( "noteDoneDesignText" );
      $noteDescription.removeClass( "noteDoneDesignTextarea" );
      $noteDoneCheckmark.attr('data-id', '2');
    }
  };

  function NoteStateHandlers() {
    //Checkmark done handler
    $( "button" + ".checkDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');

      switchNoteDoneState(project_id, id);
      updateNoteDoneCheckmark(project_id, id);
      showTitleInput(project_id, id, false);
      showDescriptionInput(project_id, id, false);
      sqlUpdateNote(project_id, user_id, id);
      blinkNote(project_id, id);
    });
  }

  //------------------------------------------------
  // Add/Update/Delete/Transfer/Export handlers
  //------------------------------------------------

  function NoteActionHandlers() {
    // POST UPDATE when enter on title
    $('.noteTitle').keydown(function(event) {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');
      if (event.keyCode == 13) {
        showTitleInput(project_id, id, false);
        showDescriptionInput(project_id, id, false);
        sqlUpdateNote(project_id, user_id, id);
        blinkNote(project_id, id);
      }
    });

    // On TAB key in description update note
    $('.textareaDescription').keydown(function(event) {
      if (event.keyCode == 9) {
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        var id = $(this).attr('data-id');
        showTitleInput(project_id, id, false);
        showDescriptionInput(project_id, id, false);
        sqlUpdateNote(project_id, user_id, id);
        blinkNote(project_id, id);
      }
    });

    // POST UPDATE on save button for existing notes
    $( "button" + ".singleNoteSave" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var id = $(this).attr('data-id');
      showTitleInput(project_id, id, false);
      showDescriptionInput(project_id, id, false);
      sqlUpdateNote(project_id, user_id, id);
      blinkNote(project_id, id);
    });

    //------------------------------------------------

    // POST ADD when enter on title
    $('.inputNewNote').keypress(function(event) {
      if (event.keyCode == 13) {
	      var project_id = $(this).attr('data-project');
	      var user_id = $(this).attr('data-user');
	      $('.inputNewNote').blur();
	      sqlAddNote(project_id, user_id);
	      sqlRefreshTabs(user_id);
	      sqlRefreshNotes(project_id, user_id);
      }
    });

    // POST ADD on save button for new notes
    $( "button" + ".saveNewNote" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      $('.inputNewNote').blur();
      sqlAddNote(project_id, user_id);
      sqlRefreshTabs(user_id);
      sqlRefreshNotes(project_id, user_id);
    });

    //------------------------------------------------

    // POST delete on delete button
    $( "button" + ".singleNoteDelete" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var note_id = $(this).attr('data-id');
      sqlDeleteNote(project_id, user_id, note_id);
      sqlRefreshTabs(user_id);
      sqlRefreshNotes(project_id, user_id);
    });

    // POST transfer note to list
    $( "button" + ".singleNoteTransfer" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var note_id = $(this).attr('data-note');
      modalTransferNote(project_id, user_id, note_id);
    });

    // POST export note as task
    $( "button" + ".singleNoteToTask" ).click(function() {
      var id = $(this).attr('data-id');
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      var note_id = $(this).attr('data-note');
      var title = $('#noteTitleLabelP' + project_id + "-" + id).val().trim();
      var description = $('#noteTextareaDescriptionP' + project_id + "-" + id).val();
      var category_id = $('#catP' + project_id + "-" + id + ' option:selected').val();
      var is_active = $('#noteDoneCheckmarkP' + project_id + "-" + id).attr('data-id');
      modalNoteToTask(project_id, user_id, note_id, is_active, title, description, category_id);
    });

    //------------------------------------------------

    // Selector category
    $( ".catSelector" ).selectmenu({
      change: function( event, data ) {
        var id = $(this).attr('data-id');
        if (id > 0) { // exclude handling the category drop down for new note
            var project_id = $(this).attr('data-project');
            var user_id = $(this).attr('data-user');
            var old_category = $("#noteCatLabelP" + project_id + "-" + id).html();
            var new_category = $('#catP' + project_id + "-" + id + ' option:selected').text();
            $("#noteCatLabelP" + project_id + "-" + id).html(new_category);

            updateCategoryColors(project_id, id, old_category, new_category);
            // avoid the ugly empty category label boxes
            if (new_category && optionShowCategoryColors) {
                $("#noteCatLabelP" + project_id + "-" + id).addClass( 'task-board-category' );
            }
            if (!new_category || !optionShowCategoryColors) {
                $("#noteCatLabelP" + project_id + "-" + id).removeClass( 'task-board-category' );
            }

            showTitleInput(project_id, id, false);
            showDescriptionInput(project_id, id, false);
            sqlUpdateNote(project_id, user_id, id);
            blinkNote(project_id, id);
        }
      }
    });
  }

  //------------------------------------------------
  // Settings handlers
  //------------------------------------------------

  function SettingsHandlers() {
    // POST delete all done
    $( "#settingsDeleteAllDone" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      modalDeleteAllDoneNotes(project_id, user_id);
    });

    // POST stats
    $( "#settingsStats" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      modalStats(project_id, user_id);
    });

    // Sort and filter for report
    $( "#settingsReport" ).click(function() {
      var project_id = $(this).attr('data-project');
      var user_id = $(this).attr('data-user');
      modalReport(project_id, user_id);
    });

    //------------------------------------------------

    $( "#settingsCollapseAll" ).click(function() {
        $('.showDetails').each(function() {
            if ($(this).find('i').hasClass( "fa-angle-double-up" ))
            {
                var project_id = $(this).attr('data-project');
                var id = $(this).attr('data-id');
                toggleDetails(project_id, id);
            }
        });
    });

    $( "#settingsExpandAll" ).click(function() {
        $('.showDetails').each(function() {
            if ($(this).find('i').hasClass( "fa-angle-double-down" ))
            {
                var project_id = $(this).attr('data-project');
                var id = $(this).attr('data-id');
                toggleDetails(project_id, id);
            }
        });
    });

    //------------------------------------------------

    $( "#settingsSortByState" ).click(function() {
        sqlToggleSessionOption('boardnotesSortByState');
        var project_id = $(this).attr('data-project');
        var user_id = $(this).attr('data-user');
        sqlRefreshNotes(project_id, user_id);
    });

    $( "#settingsCategoryColors" ).click(function() {
        optionShowCategoryColors = !optionShowCategoryColors;
        refreshCategoryColors();
        sqlToggleSessionOption('boardnotesShowCategoryColors');
    });

    //------------------------------------------------

    // Hide note in report view
    $( "button" + "#singleReportHide" ).click(function() {
      var id = $(this).attr('data-id');
      $( "#trReportNr" + id ).addClass( "hideMe" );
    });
  }

  //------------------------------------------------
  // Refresh sort/colorizing routines
  //------------------------------------------------

  // Refresh sort by state
  function refreshSortByState() {
    if (optionSortByState) {
        $( "#settingsSortByState" ).addClass( 'toolbarButtonToggled' );
    } else {
        $( "#settingsSortByState" ).removeClass( 'toolbarButtonToggled' );
    }
  }

  // Refresh category colors
  function refreshCategoryColors() {
    if (optionShowCategoryColors) {
        $( "#settingsCategoryColors" ).addClass( 'toolbarButtonToggled' );
        $( ".trReport" ).addClass( 'task-board' );
        $( ".liNote" ).addClass( 'task-board' );
        // avoid the ugly empty category label boxes
        $('.catLabel').each(function() {
            if ($(this).html())
                $(this).addClass( 'task-board-category' );
        });
    } else {
        $( "#settingsCategoryColors" ).removeClass( 'toolbarButtonToggled' );
        $( ".trReport" ).removeClass( 'task-board' );
        $( ".liNote" ).removeClass( 'task-board' );
        $( ".catLabel" ).removeClass( 'task-board-category' );
    }
  }

  // Update category colors
  function updateCategoryColors(project_id, id, old_category, new_category) {
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    $old_color = $( "#category-" + old_category ).attr('data-color');
    $new_color = $( "#category-" + new_category ).attr('data-color');

    $( "#trReportNr" + id ).removeClass( 'color-' + $old_color );
    $( "#item-" + note_id ).removeClass( 'color-' + $old_color );
    $("#noteCatLabelP" + project_id + "-" + id).removeClass( 'color-' + $old_color );

    $( "#trReportNr" + id ).addClass( 'color-' + $new_color );
    $( "#item-" + note_id ).addClass( 'color-' + $new_color );
    $("#noteCatLabelP" + project_id + "-" + id).addClass( 'color-' + $new_color);
  }

  //------------------------------------------------
  // Modal Dialogs routines
  //------------------------------------------------

  function modalTransferNote(project_id, user_id, note_id) {
    $("#dialogTransferP" + project_id).removeClass( 'hideMe' );
    $("#dialogTransferP" + project_id).dialog({
      buttons: {
        Move: function() {
          var target_project_id = $('#listNoteProjectP' + project_id + ' option:selected').val();
          sqlTransferNote(project_id, user_id, note_id, target_project_id);
          $( this ).dialog( "close" );
	      sqlRefreshTabs(user_id);
          sqlRefreshNotes(project_id, user_id);
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
    return false;
  };

  function modalNoteToTask(project_id, user_id, note_id, is_active, title, description, category_id) {
    $.ajaxSetup ({
      cache: false
    });
    $('#dialogToTaskParams').removeClass( 'hideMe' );
    $('#deadloading').addClass( 'hideMe' );
    $('#listCatToTaskP' + project_id).val(category_id).change();
    $("#dialogToTaskP" + project_id).removeClass( 'hideMe' );
    $("#dialogToTaskP" + project_id).dialog({
      title: 'Create task from note?',
      buttons: {
        Create: function() {
          var categoryToTask = $('#listCatToTaskP' + project_id + ' option:selected').val();
          var columnToTask = $('#listColToTaskP' + project_id + ' option:selected').val();
          var swimlaneToTask = $('#listSwimToTaskP' + project_id + ' option:selected').val();
          var removeNote = $('#removeNoteP' + project_id).is(":checked");

          var ajax_load = '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="loading..."></i>';
          var loadUrl = '/?controller=BoardNotesController&action=boardNotesToTask&plugin=BoardNotes'
                        + '&project_cus_id=' + project_id
                        + '&user_id=' + user_id
                        + '&task_title=' + encodeURIComponent(title)
                        + '&task_description=' + encodeURIComponent(description)
                        + '&category_id=' + categoryToTask
                        + '&column_id=' + columnToTask
                        + '&swimlane_id=' + swimlaneToTask;

          $("#dialogToTaskP" + project_id).dialog({
            title: 'Result ...',
            buttons: {
              Close: function() { $( this ).dialog( "close" ); }
            }
          });
          $('#dialogToTaskParams').addClass( 'hideMe' );
          $('#deadloading').removeClass( 'hideMe' );
          $('#deadloading').html(ajax_load).load(loadUrl);
          if (removeNote) {
            sqlDeleteNote(project_id, user_id, note_id);
	        sqlRefreshTabs(user_id);
            sqlRefreshNotes(project_id, user_id);
          }
        },
        Cancel: function() { $( this ).dialog( "close" ); }
      }
    });
    return false;
  };

  //------------------------------------------------

  function modalDeleteAllDoneNotes(project_id, user_id) {
 	$( "#dialogDeleteAllDone" ).removeClass( 'hideMe' );
    $( "#dialogDeleteAllDone" ).dialog({
      resizable: false,
      height: "auto",
      modal: true,
      buttons: {
        "Delete all done notes!": function() {
          sqlDeleteAllDoneNotes(project_id, user_id);
          $( this ).dialog( "close" );
	      sqlRefreshTabs(user_id);
          sqlRefreshNotes(project_id, user_id);
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  };

  function modalStats(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    var ajax_load = '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="loading..."></i>';
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesStats&plugin=BoardNotes'
                + '&project_cus_id=' + project_id
                + '&user_id=' + user_id;
    $('#dialogStatsInside').html(ajax_load).load(loadUrl,
        function(response, status, xhr) {
            _BoardNotes_Stats_.prepareDocument();
        });

    $( "#dialogStats" ).removeClass( 'hideMe' );
    $( "#dialogStats" ).dialog({
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  };

  function modalReport(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    $( "#dialogReportP" + project_id ).removeClass( 'hideMe' );
    $( "#dialogReportP" + project_id ).dialog({
      buttons: {
        Ok: function() {
          var category = $('#reportCatP' + project_id + ' option:selected').text();
          var ajax_load = '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="loading..."></i>';
          var loadUrl = "/?controller=BoardNotesController&action=boardNotesReport&plugin=BoardNotes"
                        + "&project_cus_id=" + project_id
                        + "&user_id=" + user_id
                        + "&category=" + encodeURIComponent(category);
          $("#result" + project_id).html(ajax_load).load(loadUrl,
            function(response, status, xhr) {
                attachAllHandlers();
                _BoardNotes_Report_.prepareDocument();
            });
          $( this ).dialog( "close" );
        }
      }
    });
    return true;
  };

  //------------------------------------------------
  // SQL routines
  //------------------------------------------------

  // SQL note transfer (to another project)
  function sqlTransferNote(project_id, user_id, note_id, target_project_id){
    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesTransferNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&target_project_id=' + target_project_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert('sqlTransferNote');
        alert(e);
      }
    });
    return false;
  }

  // SQL note update (title etc. and done)
  function sqlUpdateNote(project_id, user_id, id){
    var note_id = $('#note_idP' + project_id + "-" + id).attr('data-id');
    var title = $('#noteTitleInputP' + project_id + "-" + id).val().trim();
    var description = $('#noteTextareaDescriptionP' + project_id + "-" + id).val();
    var category = $('#catP' + project_id + "-" + id + ' option:selected').text();
    var is_active = $('#noteDoneCheckmarkP' + project_id + "-" + id).attr('data-id');

    if (!title){
        alert('Note title is empty !\nKeeping the current one !');
        title = $("#noteTitleLabelP" + project_id + "-" + id).html();
        $('#noteTitleInputP' + project_id + "-" + id).val(title);
    }
    $("#noteTitleLabelP" + project_id + "-" + id).html(title);

    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesUpdateNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
      success: function(response) {
        var lastModifiedTimestamp = parseInt(response);
        if (lastModifiedTimestamp > 0) {
            $('#refProjectId').attr('data-timestamp', lastModifiedTimestamp);
        } else {
            alert('The note you are trying to update is INVALID !\nThe page will forcefully refresh now !');
	        sqlRefreshTabs(user_id);
            sqlRefreshNotes(project_id, user_id);
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

  function sqlAddNote(project_id, user_id){
    var title = $('#inputNewNote' +  project_id).val().trim();
    var description = $('#textareaNewNote' + project_id).val();
    var category = $('#catP' + project_id + ' option:selected').text();
    var is_active = "1";

    if (!title){
        alert('Note title is empty !');
        return false;
    }

    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesAddNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&title=' + encodeURIComponent(title)
            + '&description=' + encodeURIComponent(description)
            + '&category=' + encodeURIComponent(category)
            + '&is_active=' + is_active,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert('sqlAddNote');
        alert(e);
      }
     });
    return false;
  }

  function sqlDeleteNote(project_id, user_id, note_id){
    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesDeleteNote&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id
            + '&note_id=' + note_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert('sqlDeleteNote');
        alert(e);
      }
    });
    return false;
  }

  function sqlDeleteAllDoneNotes(project_id, user_id){
    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesDeleteAllDoneNotes&plugin=BoardNotes'
            + '&project_cus_id=' + project_id
            + '&user_id=' + user_id,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert('sqlDeleteAllDoneNotes');
        alert(e);
      }
    });
    return false;
  }

  //------------------------------------------------

  function sqlRefreshTabs(user_id){
    // refresh ONLY if notes are viewed via dashboard and project tabs are present
    if ($("#tabs").length == 0) return;

    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
      cache: false
    });
    var ajax_load = '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="loading..."></i>';
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesRefreshTabs&plugin=BoardNotes'
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#tabs").html(ajax_load).load(loadUrl);
    }, 50);
  }

  function sqlRefreshNotes(project_id, user_id){
    // don't cache ajax or content won't be fresh
    $.ajaxSetup ({
      cache: false
    });
    var ajax_load = '<i class="fa fa-spinner fa-pulse" aria-hidden="true" alt="loading..."></i>';
    var loadUrl = '/?controller=BoardNotesController&action=boardNotesRefreshProject&plugin=BoardNotes'
                + '&project_cus_id=' + project_id
                + '&user_id=' + user_id;
    setTimeout(function() {
        $("#result" + project_id).html(ajax_load).load(loadUrl,
            function(response, status, xhr) {
                attachAllHandlers();
                _BoardNotes_Project_.prepareDocument();
            });
    }, 100);
  }

  function sqlToggleSessionOption(session_option){
    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesToggleSessionOption&plugin=BoardNotes'
            + '&session_option=' + session_option,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert('sqlToggleSessionOption');
        alert(e);
      }
    });
    return false;
  }

  // SQL update positions
  function sqlUpdatePosition(project_id, user_id, order, nrNotes){
    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesUpdatePosition&plugin=BoardNotes'
        + '&project_cus_id=' + project_id
        + '&user_id=' + user_id
        + '&order=' + order
        + '&nrNotes=' + nrNotes,
      success: function(response) {
      },
      error: function(xhr,textStatus,e) {
        alert('sqlUpdatePosition');
        alert(e);
      }
    });
    return false;
  }

  // SQL get last modified timestamp
  function sqlGetLastModifiedTimestamp(project_id, user_id){
    $.ajax({
      cache: false,
      type: "POST",
      url: '/?controller=BoardNotesController&action=boardNotesGetLastModifiedTimestamp&plugin=BoardNotes'
        + '&project_cus_id=' + project_id
        + '&user_id=' + user_id,
      success: function(response) {
        var lastModifiedTimestamp = parseInt(response);
        CheckAndTriggerRefresh(lastModifiedTimestamp);
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

  function ShowBusyIcon() {
    $("#boardnotesBusyIcon").removeClass('hideMe');
  };

  function HideBusyIcon() {
    $("#boardnotesBusyIcon").addClass('hideMe');
  };

  // start the recursive check sequence on load page
  $(function() {
    ScheduleCheckModifications();
  });

  // schedule check for modifications every 15 sec
  function ScheduleCheckModifications() {
    setTimeout(function() {
      ShowBusyIcon();

      var project_id = $('#refProjectId').attr('data-project');
      var user_id = $('#refProjectId').attr('data-user');
      var title = (project_id != 0) ? $('#inputNewNote' +  project_id).val().trim() : "";
      var description = (project_id != 0) ? $('#textareaNewNote' + project_id).val() : "";

      // skip SQL query if page not visible, or if new note has pending changes
      if (!KB.utils.isVisible() || title!="" || description!="") {
          ScheduleCheckModifications();
          return;
      }

      sqlGetLastModifiedTimestamp(project_id, user_id);
    }, 15 * 1000); // 15 sec
  }

  // check if page refresh is necessary
  function CheckAndTriggerRefresh(lastModifiedTimestamp) {
    var lastRefreshedTimestamp = $('#refProjectId').attr('data-timestamp');

    if (lastRefreshedTimestamp < lastModifiedTimestamp) {
        var project_id = $('#refProjectId').attr('data-project');
        var user_id = $('#refProjectId').attr('data-user');
        sqlRefreshTabs(user_id);
        sqlRefreshNotes(project_id, user_id);
    }

    ScheduleCheckModifications();
    HideBusyIcon();
  }

  //------------------------------------------------
  function attachAllHandlers() {
    NoteDetailsHandlers();
    NoteStateHandlers();
    NoteActionHandlers();
    SettingsHandlers();
  }

  attachAllHandlers()
 //------------------------------------------------
