/**
 * @author  Im[F(x)]
 */

class _BoardNotes_Statuses_ {

//------------------------------------------------
// Global vars for status class aliases
//------------------------------------------------
static #aliasStatusCasual = {
    Done : 'fa fa-check',
    Open : 'fa fa-circle-thin',
    InProgress : 'fa fa-spinner fa-pulse',
    Suspended : 'fa fa-spinner',
};
static #aliasStatusStandard = {
    Done : 'fa fa-check-square-o',
    Open : 'fa fa-square-o',
    InProgress : 'fa fa-cog fa-spin',
    Suspended : 'fa fa-cog',
};

//------------------------------------------------
// Expand status aliases
static expandStatusAliases() {
    const aliasStatus = _BoardNotes_Statuses_.#aliasStatusCasual;
    //const aliasStatus = _BoardNotes_Statuses_.#aliasStatusStandard;

    $(".statusDone").each(function() {
        $(this).removeClass();
        $(this).addClass(aliasStatus.Done);
    });

    $(".statusOpen").each(function() {
        $(this).removeClass();
        $(this).addClass(aliasStatus.Open);
    });

    $(".statusInProgress").each(function() {
        $(this).removeClass();
        $(this).addClass(aliasStatus.InProgress);
    });

    $(".statusSuspended").each(function() {
        $(this).removeClass();
        $(this).addClass(aliasStatus.Suspended);
    });
}

//------------------------------------------------
static _dummy_() {}

//------------------------------------------------

} // class _BoardNotes_Statuses_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Statuses_._dummy_ );