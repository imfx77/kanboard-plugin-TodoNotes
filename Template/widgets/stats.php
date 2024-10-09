<?php

$statsData = $this->model->todoNotesModel->GetProjectStatsForUser($stats_project_id, $stats_user_id);
$isShared = !empty($stats_is_shared) && $stats_is_shared;

$statsWidget = '<span class="containerNoWrap" id="TodoNotes-StatsWidget-P' . $stats_project_id . '">';
$statsWidget .= '<span class="statOpen' . ($isShared ? ' statShared' : '') . '" title="' . t('Open') . '">';
$statsWidget .= '<i class="statusOpen" aria-hidden="true"></i><b>' . $statsData['statOpen'] . '</b></span>';
$statsWidget .= '<span class="statDone' . ($isShared ? ' statShared' : '') . '" title="' . t('Done') . '">';
$statsWidget .= '<i class="statusDone" aria-hidden="true"></i><b>' . $statsData['statDone'] . '</b></span>';
$statusProgress = $statsData['statProgress'] > 0 ? 'statusInProgress' : 'statusSuspended';
$statsWidget .= '<span class="statProgress' . ($isShared ? ' statShared' : '') . '" title="' . t('In progress') . '">';
$statsWidget .= '<i class="' . $statusProgress . '" aria-hidden="true"></i><b>' . $statsData['statProgress'] . '</b></span>';
$statsWidget .= '</span>';

print $statsWidget;
