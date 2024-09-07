/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Report_');
//////////////////////////////////////////////////
class _TodoNotes_Report_ {

//------------------------------------------------
static prepareDocument() {
    // console.log('_TodoNotes_Report_.prepareDocument');

    $(".noteTitleInput").hide();

    _TodoNotes_.settingsShowCategoryColors = $("#session_vars").attr('data-settingsShowCategoryColors') === 'true';
    _TodoNotes_.settingsShowAllDone = $("#session_vars").attr('data-settingsShowAllDone') === 'true';

    // category colors
    $(".catLabel").each(function() {
        const id = $(this).attr('data-id');
        const project_id = $(this).attr('data-project');
        const category = $(this).html();
        _TodoNotes_.UpdateCategoryColors(project_id, id, category, category)
    });

    _TodoNotes_Statuses_.ExpandStatusAliases();

    _TodoNotes_.RefreshCategoryColors();

    _TodoNotes_.AttachAllHandlers();

    setTimeout(function() {
        // resize the report table to fit in screen height so to scroll its contents
        const scrollableTable = $(".tableReport");
        if (!scrollableTable.length) return; // missing scrollableTable when NOT in report screen
        scrollableTable.height(0);

        let maxHeight;
        if ( _TodoNotes_.IsMobile() ) {
            // adjust scrollableTable height
            maxHeight = 0.7 * $(window).height();
            scrollableTable.height( Math.min(maxHeight, scrollableTable.prop('scrollHeight')) );
        } else {
            // adjust scrollableTable height
            maxHeight = 0.9 * ( $(window).height() - scrollableTable.offset().top );
            scrollableTable.height( Math.min(maxHeight, scrollableTable.prop('scrollHeight')) );
        }
    }, 300);
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Report_

//////////////////////////////////////////////////
_TodoNotes_Report_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
