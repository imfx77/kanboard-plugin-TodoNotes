function prepareDocumentForStats() {
  for (var t = $("#chart").data("metrics"), e = [], a = 0; a < t.length; a++) e.push([t[a].column_title, t[a].nb_tasks]);
    c3.generate({
        data: {
            columns: e,
            type: "donut"
        },
        color: {
            pattern: ['#cd650a', '#2ca02c', '#1f77b4']
        }
    });
}

$( document ).ready( prepareDocumentForStats );