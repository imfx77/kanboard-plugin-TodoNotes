let _BoardNotes_Report_ = {}; // namespace

_BoardNotes_Report_.prepareDocument = function() {
    $(".noteTitleInput").hide();

    _BoardNotes_.optionShowCategoryColors = ($("#session_vars").attr('data-optionShowCategoryColors') == 'true') ? true : false;

    // category colors
    $(".catLabel").each(function() {
        var id = $(this).attr('data-id');
        var project_id = $(this).attr('data-project');
        var category = $(this).html();
        _BoardNotes_.updateCategoryColors(project_id, id, category, category)
    });

    _BoardNotes_.refreshCategoryColors();
}

$( document ).ready( _BoardNotes_Report_.prepareDocument );
