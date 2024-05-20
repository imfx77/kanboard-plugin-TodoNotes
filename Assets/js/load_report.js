/**
 * @author  Im[F(x)]
 */

class _BoardNotes_Report_ {

//------------------------------------------------
static prepareDocument() {
    $(".noteTitleInput").hide();

    _BoardNotes_.optionShowCategoryColors = $("#session_vars").attr('data-optionShowCategoryColors') === 'true';
    _BoardNotes_.optionShowAllDone = $("#session_vars").attr('data-optionShowAllDone') === 'true';

    // category colors
    $(".catLabel").each(function() {
        const id = $(this).attr('data-id');
        const project_id = $(this).attr('data-project');
        const category = $(this).html();
        _BoardNotes_.updateCategoryColors(project_id, id, category, category)
    });

    _BoardNotes_.expandStatusAliases();
    _BoardNotes_.refreshCategoryColors();

    setTimeout(function() {
        // resize the report table to fit in screen height so to scroll its contents
        const scrollableTable = $(".tableReport");
        if (!scrollableTable.eq(0).length) return; // missing table when NOT in report screen
        scrollableTable.height(0);

        let maxHeight;
        if ( _BoardNotes_.isMobile() ) {
            // adjust scrollableTable height
            maxHeight = 0.7 * $(window).height();
            scrollableTable.height( Math.min(maxHeight, scrollableTable.prop('scrollHeight')) );
        } else {
            // adjust scrollableTable height
            maxHeight = 0.9 * ( $(window).height() - scrollableTable.eq(0).offset().top );
            scrollableTable.height( Math.min(maxHeight, scrollableTable.prop('scrollHeight')) );
        }
    }, 100);
}

//------------------------------------------------

} // class _BoardNotes_Report_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Report_.prepareDocument );
