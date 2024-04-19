class _BoardNotes_Project_ {

//------------------------------------------------
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
static prepareDocument() {
    _BoardNotes_.optionShowCategoryColors = ($("#session_vars").attr('data-optionShowCategoryColors') == 'true') ? true : false;
    _BoardNotes_.optionSortByStatus = ($("#session_vars").attr('data-optionSortByStatus') == 'true') ? true : false;

    var project_id = $("#refProjectId").attr('data-project');
    var user_id = $("#refProjectId").attr('data-user');
    var isMobile = _BoardNotes_.isMobile();

    // notes reordering is disabled in Overview Mode (ALL projects tab)
    // or when explicitly sorted by Status
    if (!_BoardNotes_.optionSortByStatus) {
        $(".sortableRef").each(function() {
            var sortable_project_id = $(this).attr('data-project');
            var sortable_notes_number = $("#nrNotes-P" + sortable_project_id).attr('data-num');

            $("#sortableRef-P" + sortable_project_id).disableSelection();
            $("#sortableRef-P" + sortable_project_id).sortable({
                placeholder: "ui-state-highlight",
                items: 'li.liNote', // exclude the NewNote
                update: function() {
                    // handle notes reordering
                    var order = $(this).sortable('toArray');
                    order = order.join(",");
                    var regex = new RegExp('item-', 'g');
                    order = order.replace(regex, '');
                    order = order.split(',');
                    _BoardNotes_.sqlUpdatePosition(sortable_project_id, user_id, order, sortable_notes_number);
                }
            });

            if (isMobile) {
                // bind explicit reorder handles for mobile
                $("#sortableRef-P" + sortable_project_id).sortable({
                    handle: ".sortableHandle",
                });
            }
        });

        if (isMobile) {
            // show explicit reorder handles for mobile
            $(".sortableHandle").removeClass( 'hideMe' );
        }
    }

    if(isMobile) {
        // choose mobile view
        $("#mainholderP" + project_id).removeClass('mainholder').addClass('mainholderMobile');
    }

    _BoardNotes_Translations_.initialize();

    _BoardNotes_Project_.adjustAllNotesPlaceholders();

    _BoardNotes_.refreshCategoryColors();
    _BoardNotes_.refreshSortByStatus();

    // prepare method for dashboard view if embedded
    if (typeof _BoardNotes_Dashboard_ !== 'undefined') {
        _BoardNotes_Dashboard_.prepareDocument();
    }
}

//------------------------------------------------

} // class _BoardNotes_Project_

//////////////////////////////////////////////////
window.onresize = _BoardNotes_Project_.adjustAllNotesPlaceholders;
$( document ).ready( _BoardNotes_Project_.prepareDocument );
