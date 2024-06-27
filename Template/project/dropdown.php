<?php

// export translations to JS
print $this->render('TodoNotes:translations/export_to_js');
// load all necessary CSS and JS
print $this->asset->css('plugins/TodoNotes/Assets/css/project.css');
print $this->asset->js('plugins/TodoNotes/Assets/js/statuses.js');
print $this->asset->js('plugins/TodoNotes/Assets/js/load_dropdown.js');

?>

<style>
.localTable {
    display: table;
    width: 100%;
}
.localTableCell {
    display: table-cell;
    white-space: nowrap;
}
._TodoNotes_ProjectDropdown_StatsWidget {
    display: table-cell;
    text-align: right;
}
li._TodoNotes_ProjectDropdown_Icon i.fa-wpforms {
    display: table-cell;
}
li._TodoNotes_ProjectDropdown_Icon i.fa-wpforms::before {
    content: url('<?= $this->helper->url->base() ?>plugins/TodoNotes/Assets/img/button.svg');
    display: inline-block;
    vertical-align: middle;
}
</style>

<li class="_TodoNotes_ProjectDropdown_Icon">
    <?php
        $linkTitle = '<div class="localTableCell">' . t('TodoNotes__PROJECT_TITLE') . '</div>';

        $statsWidget = '';
        $statsWidget .= '<div class="_TodoNotes_ProjectDropdown_StatsWidget" data-project="';
        $statsWidget .= $project['id'];
        $statsWidget .= '">';
        $statsWidget .= $this->render('TodoNotes:widgets/stats', array('stats_project_id' => $project['id']));
        $statsWidget .= '</div>';
    ?>

    <?= $this->url->icon('wpforms', $linkTitle . $statsWidget, 'TodoNotesController', 'ShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'TodoNotes',
    ), false, 'localTable') ?>
</li>
