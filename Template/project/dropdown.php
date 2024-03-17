<li class="">
    <?php
        $stats_project_id = $project['id'];
        require(__DIR__.'/../widgets/stats.php');
    ?>

    <?= $this->url->icon('wpforms', t('Notes') . $statsWidget, 'BoardNotesController', 'boardNotesShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'BoardNotes',
    )) ?>
</li>
