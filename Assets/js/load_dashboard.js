/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Dashboard_');
//////////////////////////////////////////////////
class _TodoNotes_Dashboard_ {

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
            _TodoNotes_Modals_.ReorderCustomNoteList(user_id, order);
        }
    });

    if (_TodoNotes_.isMobile()) {
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
    _TodoNotes_Dashboard_.#prepareDocument_ConfigureDashboardHandlers(false);
}
//------------------------------------------------
static prepareDocument_SkipDashboardHandlers() {
    _TodoNotes_Dashboard_.#prepareDocument_ConfigureDashboardHandlers(true);
}

//------------------------------------------------
static #prepareDocument_ConfigureDashboardHandlers(skipDashboardHandlers = false) {
    // console.log('_TodoNotes_Dashboard_.prepareDocument (skipDashboardHandlers : ' + skipDashboardHandlers + ')');

    _TodoNotes_.optionShowTabStats = $("#session_vars").attr('data-optionShowTabStats') === 'true';

    const isMobile = _TodoNotes_.isMobile();
    const isAdmin = $("#tabId").attr('data-admin');

    if(isMobile) {
        // choose mobile view
        $("#mainholderDashboard").removeClass( 'mainholderDashboard' ).addClass( 'mainholderMobileDashboard' );
        $(".sidebar").addClass( 'sidebarMobileDashboard' );
    }

    if (isAdmin === '1') {
        _TodoNotes_Dashboard_.initializeSortableGroup("Global");
    }
    _TodoNotes_Dashboard_.initializeSortableGroup("Private");

    _TodoNotes_Statuses_.ExpandStatusAliases();

    _TodoNotes_Tabs_.UpdateTabs();
    _TodoNotes_Tabs_.UpdateTabStats();

    if (!skipDashboardHandlers) {
        _TodoNotes_Tabs_.AttachAllHandlers();
    }
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Dashboard_

//////////////////////////////////////////////////
_TodoNotes_Dashboard_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
