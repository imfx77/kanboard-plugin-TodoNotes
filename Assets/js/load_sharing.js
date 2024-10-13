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
        const user_id = $("#tabId").attr('data-user');
        const id = $("#tabId").attr('data-tab');
        const notesUrl = location.origin + '/dashboard/' + user_id + '/todonotes/' + id;
        location.replace(notesUrl);
    });

    $(".listPermission").change(function() {
        const user_id = $(this).attr('data-shared-user');
        $("#setSharing-U" + user_id).prop('disabled', false);
        $("#setSharing-U" + user_id).attr('data-shared-permission', $(this).attr('data-shared-permission'));
    });
}

} // class _TodoNotes_Sharing_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Sharing_.prepareDocument );

//////////////////////////////////////////////////
