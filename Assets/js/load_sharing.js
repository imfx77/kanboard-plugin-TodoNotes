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

    $("#closeSharing").click(function() {
        const user_id = $("#tabId").attr('data-user');
        const id = $("#tabId").attr('data-tab');
        const notesUrl = location.origin + '/dashboard/' + user_id + '/todonotes/' + id;
        location.replace(notesUrl);
    });

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
}

} // class _TodoNotes_Sharing_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Sharing_.prepareDocument );

//////////////////////////////////////////////////
