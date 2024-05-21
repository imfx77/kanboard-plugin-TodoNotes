/**
 * @author  Im[F(x)]
 */

class _TodoNotes_Stats_ {

//------------------------------------------------
static prepareDocument() {
    const t = $("#chart").data("metrics");
    if (!t) return;

    let e = [];
    let a = 0;
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

} // class _TodoNotes_Stats_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Stats_.prepareDocument );