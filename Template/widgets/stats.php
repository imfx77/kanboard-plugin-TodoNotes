<?php

    $stats_user_id = $this->user->getId();
    if (isset($_SESSION['_TodoNotes_Cache_']) && array_key_exists('user_id', $_SESSION['_TodoNotes_Cache_'])) {
        $stats_user_id = $_SESSION['_TodoNotes_Cache_']['user_id'];
    }

    $statsData = $this->model->todoNotesModel->GetProjectStatsForUser($stats_project_id, $stats_user_id);

    $statsWidget = '<span class="containerNoWrap" id="TodoNotes-StatsWidget-P' . $stats_project_id . '">';
    $statsWidget .= '<span class="statOpen" title="' . t('Open') . '"><i class="statusOpen" aria-hidden="true"></i><b>' . $statsData['statOpen'] . '</b></span>';
    $statsWidget .= '<span class="statDone" title="' . t('Done') . '"><i class="statusDone" aria-hidden="true"></i><b>' . $statsData['statDone'] . '</b></span>';
    $statusProgress = $statsData['statProgress'] > 0 ? 'statusInProgress' : 'statusSuspended';
    $statsWidget .= '<span class="statProgress" title="' . t('In progress') . '"><i class="' . $statusProgress . '" aria-hidden="true"></i><b>' . $statsData['statProgress'] . '</b></span>';
    $statsWidget .= '</span>';

    print $statsWidget;
