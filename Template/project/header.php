<li <?= $this->app->checkMenuSelection('TodoNotesController') ?>>
    <?= $this->url->icon('wpforms', t('TodoNotes__PROJECT_TITLE'), 'TodoNotesController', 'ShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'TodoNotes',
        )) ?>
</li>