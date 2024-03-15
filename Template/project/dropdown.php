<style>

.statOpen {
    float: right;
    color: #f7f7f7;
    background: #ff7f0e;
    border: 2px solid #1f1f1f;
    margin-left: 5px;
    padding-right: 5px;
    padding-left: 5px;
    padding-top: 2px;
    padding-bottom: 2px;
}

.statProgress {
    float: right;
    color: #f7f7f7;
    background: #1f77b4;
    border: 2px solid #1f1f1f;
    margin-left: 5px;
    padding-right: 5px;
    padding-left: 5px;
    padding-top: 2px;
    padding-bottom: 2px;
}

.statDone {
    float: right;
    color: #f7f7f7;
    background: #2ca02c;
    border: 2px solid #1f1f1f;
    margin-left: 5px;
    padding-right: 5px;
    padding-left: 5px;
    padding-top: 2px;
    padding-bottom: 2px;
}

</style>

<li class="">
    <?php
        $user_id = isset($_SESSION['cached_user_id'])
                ? $_SESSION['cached_user_id']
                : $user_id = $this->user->getId();

        $statsData = $this->model->boardNotesModel->boardNotesStats($project['id'], $user_id);

        $statsText = '';
        $statsText .= '<span class="statDone" title="'.t('Done').'"><i style="color:#f7f7f7" class="fa fa-check" aria-hidden="true"></i>' . $statsData['statDone'] . '</span>';
        $statsText .= '<span class="statProgress" title="'.t('Progress').'"><i style="color:#f7f7f7" class="fa fa-spinner fa-pulse" aria-hidden="true"></i>' . $statsData['statProgress'] . '</span>';
        $statsText .= '<span class="statOpen" title="'.t('Open').'"><i style="color:#f7f7f7" class="fa fa-circle-thin" aria-hidden="true"></i>' . $statsData['statOpen'] . '</span>';
    ?>

    <?= $this->url->icon('wpforms', t('Notes') . $statsText, 'BoardNotesController', 'boardNotesShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'BoardNotes',
        )) ?>
</li>
