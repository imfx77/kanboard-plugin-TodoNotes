/**
 * @author  Im[F(x)]
 */

class _BoardNotes_Stats_ {

//------------------------------------------------
static prepareDocument() {
    var t = $("#chart").data("metrics");
    if (!t) return;

    var e = [];
    var a = 0;
    for (t; a < t.length; a++) {
        e.push([t[a].column_title, t[a].nb_tasks]);
    }

    c3.generate({
        data: {
            columns: e,
            type: "donut"
        },
        color: {
            pattern: ['#CD650A', '#2CA02C', '#1F77B4']
        }
    });
}

//------------------------------------------------

} // class _BoardNotes_Stats_

//////////////////////////////////////////////////
$( document ).ready( _BoardNotes_Stats_.prepareDocument );