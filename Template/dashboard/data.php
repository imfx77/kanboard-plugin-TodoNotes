<?= $this->asset->css('plugins/TodoNotes/Assets/css/dashboard.css') ?>
<?= $this->asset->js('plugins/TodoNotes/Assets/js/tabs.js') ?>
<?= $this->asset->js('plugins/TodoNotes/Assets/js/load_dashboard.js') ?>

<div id="myNotesHeader" class="page-header"><h2>
<?= t('TodoNotes__DASHBOARD_MY_NOTES')?> > <?= t('TodoNotes__DASHBOARD_ALL_TAB') ?>
</h2></div>

<!--
//----------------------------------------
// ACTUAL CONTENT BEGINS HERE !!!
// it shall be regenerated dynamically by reloading the "tabs" and "content" containers from within the page
//----------------------------------------
-->

<section class="mainholderDashboard sidebar-container" id="mainholderDashboard">

<div id="tabs" class="sidebar tabs">

    <?= $this->render('TodoNotes:dashboard/tabs', array(
         'projectsAccess' => $projectsAccess,
         'user_id' => $user_id,
    )) ?>

</div>

<div id="content" class="sidebar-content">

<?php

$project = array('id' => 0, 'name' => t('TodoNotes__DASHBOARD_ALL_TAB'));
if ($tab_id > 0) {
    $projectAccess = $projectsAccess[$tab_id - 1];
    $project = array('id' => $projectAccess['project_id'],
                     'name' => $projectAccess['project_name'],
                     'is_custom' => $projectAccess['is_custom']);
}

?>

<?= $this->render('TodoNotes:project/data', array(
    'projectsAccess' => $projectsAccess,
    'project' => $project,
    'project_id' => $project['id'],
    'user' => $user,
    'user_id' => $user_id,
    'user_datetime_format' => $user_datetime_format,

    'is_refresh' => false,
    'is_dashboard_view' => 1,

    'categories' => $categories,
    'columns' => $columns,
    'swimlanes' => $swimlanes,
    'data' => $data,
)) ?>

</div>

</section>

<!--
//----------------------------------------
// ACTUAL CONTENT ENDS HERE !!!
// all sections below must appear ONCE ONLY and NOT be refreshed
//----------------------------------------
-->

<?php

$isAdmin = $this->user->isAdmin();

// tabId (hidden reference for tabs)
print '<div class="hideMe" id="tabId"';
print ' data-tab="' . $tab_id  . '"';
print ' data-project="' . $project['id'] . '"';
print ' data-admin="' . ($isAdmin ? '1' : '0') . '"';
print '></div>';
