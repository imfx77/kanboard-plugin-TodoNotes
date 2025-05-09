/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Sharing_');
//////////////////////////////////////////////////
class _TodoNotes_Sharing_ {

//------------------------------------------------
static prepareDocument() {
    // console.log('_TodoNotes_Report_.prepareDocument');

    _TodoNotes_Settings_.Initialize();

    // prepare method for dashboard view if embedded
    if (typeof(_TodoNotes_Dashboard_) !== 'undefined') {
        // if (skipDashboardHandlers) {
        //     _TodoNotes_Dashboard_.prepareDocument_SkipDashboardHandlers();
        // } else {
            _TodoNotes_Dashboard_.prepareDocument();
        // }
    }

    const isMobile = _TodoNotes_.IsMobile();
    if(isMobile) {
        // show mobile GitHub buttons
        $("#containerGithubButtons").remove();
        $("#containerGithubButtonsMobile").removeClass( 'hideMe' );
    } else {
        // show desktop GitHub buttons
        $("#containerGithubButtonsMobile").remove();
        $("#containerGithubButtons").removeClass( 'hideMe' );
    }

    _TodoNotes_Sharing_.AttachAllHandlers();

    setTimeout(function() {
        // resize the sharing table to fit in screen height so to scroll its contents
        const scrollableTable = $(".tableSharing");
        if (!scrollableTable.length) return; // missing scrollableTable when NOT in report screen
        scrollableTable.height(0);

        let maxHeight;
        if (isMobile) {
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
static AttachAllHandlers() {
    // console.log('_TodoNotes_Sharing_.AttachAllHandlers');

    // close btn
    $("#closeSharing").click(function() {
        location.replace($(this).attr('data-url'));
    });

    $(".listPermission").change(function() {
        const shared_user_id = $(this).attr('data-shared-user');
        $("#setSharingPermission-U" + shared_user_id).prop('disabled', false);
        $("#setSharingPermission-U" + shared_user_id).attr('data-shared-permission', $(this).attr('data-shared-permission'));
    });

    $(".setSharingPermission").click(function() {
        const project_id = $(this).attr('data-project');
        const user_id = $(this).attr('data-user');
        const shared_user_id = $(this).attr('data-shared-user');
        const shared_permission = $(this).attr('data-shared-permission');

        _TodoNotes_Requests_.SetSharingPermission(project_id, user_id, shared_user_id, shared_permission);

        $(this).prop('disabled', true);
    });
}

} // class _TodoNotes_Sharing_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Sharing_.prepareDocument );

//////////////////////////////////////////////////
