/**
 * @author  Im[F(x)]
 */

class _BoardNotes_Project_ {

//------------------------------------------------
static prepareDocument() {
    _BoardNotes_.optionShowCategoryColors = $("#session_vars").attr('data-optionShowCategoryColors') === 'true';
    _BoardNotes_.optionSortByStatus = $("#session_vars").attr('data-optionSortByStatus') === 'true';
    _BoardNotes_.optionShowAllDone = $("#session_vars").attr('data-optionShowAllDone') === 'true';

    const project_id = $("#refProjectId").attr('data-project');
    const user_id = $("#refProjectId").attr('data-user');
    const isMobile = _BoardNotes_.isMobile();
    const readonlyNotes = (project_id === '0'); // Overview Mode

    // notes reordering is disabled when explicitly sorted by Status
    if (!_BoardNotes_.optionSortByStatus) {
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
                    _BoardNotes_.sqlUpdateNotesPositions(sortable_project_id, user_id, order);
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

    _BoardNotes_Translations_.initialize();

    _BoardNotes_Project_.resizeDocument();

    _BoardNotes_.expandStatusAliases();
    _BoardNotes_.refreshCategoryColors();
    _BoardNotes_.refreshSortByStatus();
    _BoardNotes_.refreshShowAllDone();

    // prepare method for dashboard view if embedded
    if (typeof(_BoardNotes_Dashboard_) !== 'undefined') {
        _BoardNotes_Dashboard_.prepareDocument();
    }

    // force render all KB elements
    KB.render();

    setTimeout(function() {
        _BoardNotes_.showTitleInputNewNote();
    }, 100);
}

//------------------------------------------------
static resizeDocument() {
    _BoardNotes_.adjustAllNotesPlaceholders();
    _BoardNotes_.adjustAllNotesTitleInputs();
    setTimeout(function() {
        _BoardNotes_.adjustScrollableContent();
    }, 100);
}

//------------------------------------------------

} // class _BoardNotes_Project_

//////////////////////////////////////////////////
window.onresize = _BoardNotes_Project_.resizeDocument;
$( document ).ready( _BoardNotes_Project_.prepareDocument );
