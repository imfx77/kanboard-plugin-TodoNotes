/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Stats_');
//////////////////////////////////////////////////
class _TodoNotes_Stats_ {

//------------------------------------------------
static prepareDocument() {
    // console.log('_TodoNotes_Stats_.prepareDocument');

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
static _dummy_() {}

//------------------------------------------------

} // class _TodoNotes_Stats_

//////////////////////////////////////////////////
_TodoNotes_Stats_._dummy_(); // linter error workaround

//////////////////////////////////////////////////
