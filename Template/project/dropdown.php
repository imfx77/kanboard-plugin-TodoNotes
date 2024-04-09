<?= $this->render('BoardNotes:translations/export_to_js') ?>

<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/translations.js') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/load_dropdown.js') ?>

<li class="">
    <?php
        $statsWidget = '';
        $statsWidget .= '<span class="BoardNotes_ProjectDropdown_StatsWidget" data-project="';
        $statsWidget .= $project['id'];
        $statsWidget .= '">';
        $statsWidget .= $this->render('BoardNotes:widgets/stats', array('stats_project_id' => $project['id']));
        $statsWidget .= '</span>';
    ?>

    <?= $this->url->icon('wpforms', t('BoardNotes_PROJECT_TITLE') . $statsWidget, 'BoardNotesController', 'boardNotesShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'BoardNotes',
    )) ?>
</li>
