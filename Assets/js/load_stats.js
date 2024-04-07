let _BoardNotes_Stats_ = {}; // namespace

_BoardNotes_Stats_.prepareDocument = function() {
  var t = $("#chart").data("metrics");
  if (!t) return;

  for (t, e = [], a = 0; a < t.length; a++) e.push([t[a].column_title, t[a].nb_tasks]);
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

$( document ).ready( _BoardNotes_Stats_.prepareDocument );