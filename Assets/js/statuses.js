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
        static expandStatusAliases() {
            // console.log('_TodoNotes_Statuses_.expandStatusAliases');

            const aliasStatus = _TodoNotes_Statuses_.#aliasStatusCasual;
            //const aliasStatus = _TodoNotes_Statuses_.#aliasStatusStandard;

            $(".statusDone").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.Done);
            });

            $(".statusOpen").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.Open);
            });

            $(".statusInProgress").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.InProgress);
            });

            $(".statusSuspended").each(function () {
                $(this).removeClass();
                $(this).addClass(aliasStatus.Suspended);
            });
        }

        //------------------------------------------------

    } // class _TodoNotes_Statuses_

    //////////////////////////////////////////////////

} // !defined _TodoNotes_Statuses_
