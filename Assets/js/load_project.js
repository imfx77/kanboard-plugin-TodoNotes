/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Project_');
//////////////////////////////////////////////////
class _TodoNotes_Project_ {

//------------------------------------------------
static prepareDocument() {
    _TodoNotes_Project_.#prepareDocument_ConfigureDashboardHandlers(false);
}
//------------------------------------------------
static prepareDocument_SkipDashboardHandlers() {
    _TodoNotes_Project_.#prepareDocument_ConfigureDashboardHandlers(true);
}

//------------------------------------------------
static #prepareDocument_ConfigureDashboardHandlers(skipDashboardHandlers = false) {
    // console.log('_TodoNotes_Project_.prepareDocument (skipDashboardHandlers : ' + skipDashboardHandlers + ')');

    _TodoNotes_Settings_.Initialize();

    const project_id = $("#refProjectId").attr('data-project');
    const user_id = $("#refProjectId").attr('data-user');
    const isMobile = _TodoNotes_.IsMobile();
    const readonlyNotes = (project_id === '0'); // Overview Mode

    // hide the KB filters toolbar
    $('.input-addon-field').addClass( 'hideMe' );
    $('.input-addon-item').addClass( 'hideMe' );

    // notes reordering is disabled when in Archive View or explicitly sorted by Status
    if (!_TodoNotes_Settings_.ArchiveView && !_TodoNotes_Settings_.SortByStatus) {
        $(".sortableList").each(function() {
            const sortable_project_id = $(this).attr('data-project');

            $("#sortableList-P" + sortable_project_id).sortable({
                placeholder: "ui-state-highlight",
                items: 'li.liNote', // exclude the NewNote
                cancel: '.disableEventsPropagation',
                update: function() {
                    // handle notes reordering
                    let order = $(this).sortable('toArray');
                    order = order.join(",");
                    const regex = new RegExp('item-', 'g');
                    order = order.replace(regex, '');
                    order = order.split(',');
                    _TodoNotes_Requests_.UpdateNotesPositions(sortable_project_id, user_id, order);
                }
            });

            if (isMobile) {
                // bind explicit reorder handles for mobile
                $("#sortableList-P" + sortable_project_id).sortable({
                    handle: ".sortableListHandle",
                });
            }
        });

        if (isMobile) {
            // show explicit reorder handles for mobile
            $(".sortableListHandle").removeClass( 'hideMe' );
        }
    }

    if(isMobile) {
        // choose mobile view
        $("#mainholderP" + project_id).removeClass( 'mainholder' ).addClass( 'mainholderMobile' );

        // show all Save buttons
        if (!readonlyNotes ) { // if NOT in Overview Mode
            $(".saveNewNote").removeClass( 'hideMe' );
            $(".noteSave").removeClass( 'hideMe' );
        }
    }

    _TodoNotes_Project_.resizeDocument();

    _TodoNotes_Statuses_.ExpandStatusAliases();

    _TodoNotes_.RefreshArchiveView();
    _TodoNotes_.RefreshCategoryColors();
    _TodoNotes_.RefreshSortByStatus();
    _TodoNotes_.RefreshShowAllDone();

    _TodoNotes_.AttachAllHandlers();

    // prepare method for dashboard view if embedded
    if (typeof(_TodoNotes_Dashboard_) !== 'undefined') {
        if (skipDashboardHandlers) {
            _TodoNotes_Dashboard_.prepareDocument_SkipDashboardHandlers();
        } else {
            _TodoNotes_Dashboard_.prepareDocument();
        }
    }

    // force render all KB elements
    KB.render();

    setTimeout(function() {
        _TodoNotes_.ShowTitleInputNewNote();
    }, 100);
}

//------------------------------------------------
static resizeDocument() {
    _TodoNotes_Translations_.Initialize();

    _TodoNotes_.AdjustAllNotesPlaceholders();
    _TodoNotes_.AdjustAllNotesTitleInputs();

    setTimeout(function() {
        _TodoNotes_.AdjustScrollableContent();
        const note_id = $("#refProjectId").attr('data-note');
        _TodoNotes_.FocusNote(note_id);
    }, 100);
}

//------------------------------------------------

} // class _TodoNotes_Project_

//////////////////////////////////////////////////
window.onresize = _TodoNotes_Project_.resizeDocument;
$( document ).ready( _TodoNotes_Project_.prepareDocument );

//////////////////////////////////////////////////
