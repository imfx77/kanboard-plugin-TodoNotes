class _BoardNotes_Dashboard_ {

//------------------------------------------------
static prepareDocument() {
    _BoardNotes_.optionShowTabStats = ($("#session_vars").attr('data-optionShowTabStats') == 'true') ? true : false;

    var isMobile = _BoardNotes_.isMobile();

    if(isMobile) {
        // choose mobile view
        $("#mainholderDashboard").removeClass( 'mainholderDashboard' ).addClass( 'mainholderMobileDashboard' );
        $(".sidebar").addClass( 'sidebarMobileDashboard' );
    }

    _BoardNotes_Translations_.initialize();

    _BoardNotes_Tabs_.updateTabs();
    _BoardNotes_Tabs_.updateTabStats();

    _BoardNotes_Tabs_.attachAllHandlers();
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _BoardNotes_Dashboard_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Dashboard_._dummy_ );
