class _BoardNotes_Dashboard_ {

//------------------------------------------------
static initializeSortableGroup(group) {
    // private custom lists
    $("#group" + group).sortable({
        placeholder: "ui-state-highlight",
        items: 'li.singleTab',
        cancel: '.disableEventsPropagation',
        update: function() {
            // handle lists reordering
            let order = $(this).sortable('toArray');
            order = order.join(",");
            const regex = new RegExp('singleTab-P', 'g');
            order = order.replace(regex, '');
            order = order.split(',');

            const user_id = $("#refProjectId").attr('data-user');
            _BoardNotes_Tabs_.modalReorderCustomNoteList(user_id, order);
        }
    });

    if (_BoardNotes_.isMobile()) {
        // bind explicit reorder handles for mobile
        $("#group" + group).sortable({
            handle: ".sortableGroupHandle",
        });
        // show explicit reorder handles for mobile
        $(".sortableGroupHandle").removeClass( 'hideMe' );
    }
}

//------------------------------------------------
static prepareDocument() {
    _BoardNotes_.optionShowTabStats = ($("#session_vars").attr('data-optionShowTabStats') == 'true') ? true : false;

    var isMobile = _BoardNotes_.isMobile();
    var isAdmin = $("#tabId").attr('data-admin');

    if(isMobile) {
        // choose mobile view
        $("#mainholderDashboard").removeClass( 'mainholderDashboard' ).addClass( 'mainholderMobileDashboard' );
        $(".sidebar").addClass( 'sidebarMobileDashboard' );
    }

    if (isAdmin == "1") {
        _BoardNotes_Dashboard_.initializeSortableGroup("Global");
    }
    _BoardNotes_Dashboard_.initializeSortableGroup("Private");

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
