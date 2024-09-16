/**
 * @author  Im[F(x)]
 */

var _TodoNotes_Statuses_;

if (typeof(_TodoNotes_Statuses_) === 'undefined') {

    // console.log('define _TodoNotes_Statuses_');
    //////////////////////////////////////////////////
    _TodoNotes_Statuses_ = class {

        //------------------------------------------------
        // Global vars for status class aliases
        //------------------------------------------------
        static #aliasStatusCasual = {
            Done: 'fa fa-check',
            Open: 'fa fa-circle-thin',
            InProgress: 'fa fa-spinner fa-pulse',
            Suspended: 'fa fa-spinner',
        };
        static #aliasStatusStandard = {
            Done: 'fa fa-check-square-o',
            Open: 'fa fa-square-o',
            InProgress: 'fa fa-cog fa-spin',
            Suspended: 'fa fa-cog',
        };

        //------------------------------------------------
        // Expand status aliases
        static ExpandStatusAliases() {
            // console.log('_TodoNotes_Statuses_.ExpandStatusAliases');

            const aliasStatus = _TodoNotes_Settings_.showStandardStatusMarks
                ? _TodoNotes_Statuses_.#aliasStatusStandard
                : _TodoNotes_Statuses_.#aliasStatusCasual;

            $(".statusDone").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.Done);
                $(this).addClass('statusDone');
            });

            $(".statusOpen").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.Open);
                $(this).addClass('statusOpen');
            });

            $(".statusInProgress").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.InProgress);
                $(this).addClass('statusInProgress');
            });

            $(".statusSuspended").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.Suspended);
                $(this).addClass('statusSuspended');
            });
        }

        //------------------------------------------------

    } // class _TodoNotes_Statuses_

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Statuses_
