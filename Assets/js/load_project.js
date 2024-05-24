/**
 * @author  Im[F(x)]
 */

class _TodoNotes_Project_ {

//------------------------------------------------
static prepareDocument() {
    _TodoNotes_.optionShowCategoryColors = $("#session_vars").attr('data-optionShowCategoryColors') === 'true';
    _TodoNotes_.optionSortByStatus = $("#session_vars").attr('data-optionSortByStatus') === 'true';
    _TodoNotes_.optionShowAllDone = $("#session_vars").attr('data-optionShowAllDone') === 'true';

    const project_id = $("#refProjectId").attr('data-project');
    const user_id = $("#refProjectId").attr('data-user');
    const isMobile = _TodoNotes_.isMobile();
    const readonlyNotes = (project_id === '0'); // Overview Mode

    // notes reordering is disabled when explicitly sorted by Status
    if (!_TodoNotes_.optionSortByStatus) {
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
                    _TodoNotes_.sqlUpdateNotesPositions(sortable_project_id, user_id, order);
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

    _TodoNotes_Statuses_.expandStatusAliases();

    _TodoNotes_.refreshCategoryColors();
    _TodoNotes_.refreshSortByStatus();
    _TodoNotes_.refreshShowAllDone();

    _TodoNotes_.attachAllHandlers();

    // prepare method for dashboard view if embedded
    if (typeof(_TodoNotes_Dashboard_) !== 'undefined') {
        _TodoNotes_Dashboard_.prepareDocument();
    }

    // force render all KB elements
    KB.render();

    setTimeout(function() {
        _TodoNotes_.showTitleInputNewNote();
    }, 100);
}

//------------------------------------------------
static resizeDocument() {
    _TodoNotes_.adjustAllNotesPlaceholders();
    _TodoNotes_.adjustAllNotesTitleInputs();
    setTimeout(function() {
        _TodoNotes_.adjustScrollableContent();
    }, 100);
}

//------------------------------------------------

} // class _TodoNotes_Project_

//////////////////////////////////////////////////
window.onresize = _TodoNotes_Project_.resizeDocument;
$( document ).ready( _TodoNotes_Project_.prepareDocument );

//////////////////////////////////////////////////
