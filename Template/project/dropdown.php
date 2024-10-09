<?php

$user_id = $this->user->getId();
if (isset($_SESSION['_TodoNotes_Cache_']) && array_key_exists('user_id', $_SESSION['_TodoNotes_Cache_'])) {
    $user_id = $_SESSION['_TodoNotes_Cache_']['user_id'];
}
$project_id = $project['id'];

// dropdown needs to initialize translations and settings on its own
// ONLY if opened alongside any page that in NOT a TodoNotes view !!!
if (!str_starts_with($_SERVER['REQUEST_URI'], '/todonotes/')) {
    // export translations to JS
    print $this->render('TodoNotes:translations/export_to_js');
    // export settings to JS
    require_once('settings.php');
    print $this->asset->js('plugins/TodoNotes/Assets/js/settings.js');
}

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
        $statsWidget .= '<div class="_TodoNotes_ProjectDropdown_StatsWidget"';
        $statsWidget .= ' data-project="' . $project_id . '"';
        $statsWidget .= ' data-user="' . $user_id . '"';
        $statsWidget .= '>';
        $statsWidget .= $this->render('TodoNotes:widgets/stats', array(
            'stats_project_id' => $project_id,
            'stats_user_id' => $user_id
        ));
        $statsWidget .= '</div>';
    ?>

    <?= $this->url->icon('wpforms', $linkTitle . $statsWidget, 'TodoNotesController', 'ShowProject', array(
        'project_id' => $project_id,
        'use_cached' => '1',
        'plugin' => 'TodoNotes',
    ), false, 'localTable') ?>
</li>
