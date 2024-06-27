<style>
li._TodoNotes_ProjectHeader_Icon i.fa-wpforms::before {
    content: url('<?= $this->helper->url->base() ?>plugins/TodoNotes/Assets/img/button.svg');
    display: inline-block;
    vertical-align: middle;
}
</style>

<li class="_TodoNotes_ProjectHeader_Icon" <?= $this->app->checkMenuSelection('TodoNotesController') ?>>
    <?= $this->url->icon('wpforms', t('TodoNotes__PROJECT_TITLE'), 'TodoNotesController', 'ShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'TodoNotes',
        )) ?>
</li>