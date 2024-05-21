<li <?= $this->app->checkMenuSelection('BoardNotesController') ?>>
    <?= $this->url->icon('wpforms', t('BoardNotes_PROJECT_TITLE'), 'BoardNotesController', 'ShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'BoardNotes',
        )) ?>
</li>