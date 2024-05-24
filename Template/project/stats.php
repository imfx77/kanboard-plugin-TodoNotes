<style>
.c3 svg{font:10px sans-serif;-webkit-tap-highlight-color:transparent}.c3 line,.c3 path{fill:none;stroke:#000000}.c3 text{-webkit-user-select:none;-moz-user-select:none;user-select:none}.c3-bars path,.c3-event-rect,.c3-legend-item-tile,.c3-xgrid-focus,.c3-ygrid{shape-rendering:crispEdges}.c3-chart-arc path{stroke:#FFFFFF}.c3-chart-arc text{fill:#FFFFFF;font-size:13px}.c3-grid line{stroke:#aaa}.c3-grid text{fill:#aaa}.c3-xgrid,.c3-ygrid{stroke-dasharray:3 3}.c3-text.c3-empty{fill:gray;font-size:2em}.c3-line{stroke-width:1px}.c3-circle._expanded_{stroke-width:1px;stroke:#FFFFFF}.c3-selected-circle{fill:#FFFFFF;stroke-width:2px}.c3-bar{stroke-width:0}.c3-bar._expanded_{fill-opacity:.75}.c3-target.c3-focused{opacity:1}.c3-target.c3-focused path.c3-line,.c3-target.c3-focused path.c3-step{stroke-width:2px}.c3-target.c3-defocused{opacity:.3!important}.c3-region{fill:#4682b4;fill-opacity:.1}.c3-brush .extent{fill-opacity:.1}.c3-legend-item{font-size:12px}.c3-legend-item-hidden{opacity:.15}.c3-legend-background{opacity:.75;fill:#FFFFFF;stroke:#d3d3d3;stroke-width:1}.c3-title{font:14px sans-serif}.c3-tooltip-container{z-index:10}.c3-tooltip{border-collapse:collapse;border-spacing:0;background-color:#FFFFFF;empty-cells:show;-webkit-box-shadow:7px 7px 12px -9px #777777;-moz-box-shadow:7px 7px 12px -9px #777777;box-shadow:7px 7px 12px -9px #777777;opacity:.9}.c3-tooltip tr{border:1px solid #CCCCCC}.c3-tooltip th{background-color:#aaa;font-size:14px;padding:2px 5px;text-align:left;color:#FFFFFF}.c3-tooltip td{font-size:13px;padding:3px 6px;background-color:#FFFFFF;border-left:1px dotted #999999}.c3-tooltip td>span{display:inline-block;width:10px;height:10px;margin-right:6px}.c3-tooltip td.value{text-align:right}.c3-area{stroke-width:0;opacity:.2}.c3-chart-arcs-title{dominant-baseline:middle;font-size:1.3em}.c3-chart-arcs .c3-chart-arcs-background{fill:#e0e0e0;stroke:none}.c3-chart-arcs .c3-chart-arcs-gauge-unit{fill:#000000;font-size:16px}.c3-chart-arcs .c3-chart-arcs-gauge-max,.c3-chart-arcs .c3-chart-arcs-gauge-min{fill:#777777}.c3-chart-arc .c3-gauge-value{fill:#000000}
</style>

<?php
print $this->asset->js('plugins/TodoNotes/Assets/js/load_stats.js');

$statDone = $statsData['statDone'];
$statOpen = $statsData['statOpen'];
$statProgress = $statsData['statProgress'];
$statTotal = $statsData['statTotal'];

$chart_metrics = '';
if ($statTotal > 0) {
    $statDone_Percent = (($statDone / $statTotal) * 100);
    $statDone_Percent = number_format((float) $statDone_Percent, 2, '.', '');
    $statOpen_Percent = (($statOpen / $statTotal) * 100);
    $statOpen_Percent = number_format((float) $statOpen_Percent, 2, '.', '');
    $statProgress_Percent = (($statProgress / $statTotal) * 100);
    $statProgress_Percent = number_format((float) $statProgress_Percent, 2, '.', '');

    $chart_metrics .= '[';
    $chart_metrics .= '{"column_title":"' . t('Open') . '","nb_tasks":' . $statOpen . ',"percentage":' . $statOpen_Percent . '},';
    $chart_metrics .= '{"column_title":"' . t('Done') . '","nb_tasks":' . $statDone . ',"percentage":' . $statDone_Percent . '},';
    $chart_metrics .= '{"column_title":"' . t('In progress') . '","nb_tasks":' . $statProgress . ',"percentage":' . $statProgress_Percent . '}';
    $chart_metrics .= ']';
}
?>

<p><strong><?= t('Open') ?>: <?php print $statOpen; ?></strong></p>
<p><strong><?= t('Done') ?>: <?php print $statDone; ?></strong></p>
<p><strong><?= t('In progress') ?>: <?php print $statProgress; ?></strong></p>

<?php

if ($statTotal > 0) {
    print '<section class="analytic-task-repartition">';
    print '<div id="chart" class="c3" data-metrics=\'' . $chart_metrics . '\'></div>';
    print '</section>';
}
