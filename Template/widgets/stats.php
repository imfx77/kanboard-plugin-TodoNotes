<?php

    // TODO: improve obtaining the user_id !!!
    $stats_user_id = isset($_SESSION['cached_user_id'])
            ? $_SESSION['cached_user_id']
            : $this->user->getId();

    $statsData = $this->model->boardNotesModel->boardNotesStats($stats_project_id, $stats_user_id);

    $statsWidget = '<span id="BoardNotes-StatsWidget-P' . $stats_project_id . '">';
    $statsWidget .= '<span class="statProgress" title="' . t('In progress') . '"><i class="fa fa-fw fa-spinner ';
    $statsWidget .= ($statsData['statProgress'] > 0) ? 'fa-pulse' : '';
    $statsWidget .= '" aria-hidden="true"></i><b>' . $statsData['statProgress'] . '</b></span>';
    $statsWidget .= '<span class="statDone" title="' . t('Done') . '"><i class="fa fa-fw fa-check" aria-hidden="true"></i><b>' . $statsData['statDone'] . '</b></span>';
    $statsWidget .= '<span class="statOpen" title="' . t('Open') . '"><i class="fa fa-fw fa-circle-thin" aria-hidden="true"></i><b>' . $statsData['statOpen'] . '</b></span>';
    $statsWidget .= '</span>';

    print $statsWidget;
