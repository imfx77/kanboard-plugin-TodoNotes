<?php

print $this->asset->css('plugins/BoardNotes/Assets/css/project.css');
print $this->asset->js('plugins/BoardNotes/Assets/js/load_dropdown.js');

// export translations to JS
print $this->render('BoardNotes:translations/export_to_js');

?>

<style>
.localTable {
    display: table;
    width: 100%;
}
.localTableCell {
    display: table-cell;
}
.BoardNotes_ProjectDropdown_StatsWidget {
    display: table-cell;
    width: 100%;
    text-align: right;
}
</style>

<li>
    <?php
        $linkTitle = '<div class="localTableCell">' . t('BoardNotes_PROJECT_TITLE') . '</div>';

        $statsWidget = '';
        $statsWidget .= '<div class="BoardNotes_ProjectDropdown_StatsWidget" data-project="';
        $statsWidget .= $project['id'];
        $statsWidget .= '">';
        $statsWidget .= $this->render('BoardNotes:widgets/stats', array('stats_project_id' => $project['id']));
        $statsWidget .= '</div>';
    ?>

    <?= $this->url->icon('wpforms', $linkTitle . $statsWidget, 'BoardNotesController', 'ShowProject', array(
        'project_id' => $project['id'],
        'use_cached' => '1',
        'plugin' => 'BoardNotes',
    ), false, 'localTable') ?>
</li>
