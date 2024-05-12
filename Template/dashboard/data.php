<style>

.mainholderDashboard {
    width: 350px;
    font: 16px 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #4D4D4D;
    -webkit-font-smoothing: antialiased;
    position: relative;
    font-weight: 300;
}

.mainholderMobileDashboard {
    width: auto;
    font: 14px 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #4D4D4D;
    -webkit-font-smoothing: antialiased;
    position: relative;
    font-weight: 300;
}

.sidebarMobileDashboard {
    min-width: auto;
}

.tabs {
    max-width: 100%;
    min-width: auto;
}

.hrTabs {
    border: 1px solid;
    width: 100%;
}

.localTable {
    display: table;
    width: 100%;
}

.localTableCell {
    display: table-cell;
    white-space: nowrap;
}

.localTableCellExpandRight {
    display: table-cell;
    width: 100%;
    white-space: nowrap;
    text-align: right;
}

/* desktop browser styles override */
@media only screen and (max-width: 768px) {
    .mainholderDashboard {
        width: auto;
        font: 14px "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #4D4D4D;
        -webkit-font-smoothing: antialiased;
        position: relative;
        font-weight: 300;
    }

    .sidebar {
        max-width: 100%;
        min-width: auto;
    }
}

/* mobile browser portrait mode styles override */
@media only screen and (max-device-width: 768px) and (orientation:portrait) {
    .mainholderMobileDashboard {
        width: auto;
        font: 12px "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #4D4D4D;
        -webkit-font-smoothing: antialiased;
        position: relative;
        font-weight: 300;
    }

    .sidebarMobileDashboard {
        max-width: 100%;
    }
}

/* mobile browser landscape mode styles override */
@media only screen and (max-device-width: 768px) and (orientation:landscape) {
    .mainholderMobileDashboard {
        width: auto;
        font: 10px "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #4D4D4D;
        -webkit-font-smoothing: antialiased;
        position: relative;
        font-weight: 300;
    }
}

</style>

<?= $this->asset->js('plugins/BoardNotes/Assets/js/tabs.js') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/load_dashboard.js') ?>

<div id="myNotesHeader" class="page-header"><h2>
<?= t('BoardNotes_DASHBOARD_MY_NOTES')?> > <?= t('BoardNotes_DASHBOARD_ALL_TAB') ?>
</h2></div>

<!--
//----------------------------------------
// ACTUAL CONTENT BEGINS HERE !!!
// it shall be regenerated dynamically by reloading the "tabs" and "content" containers from within the page
//----------------------------------------
-->

<section class="mainholderDashboard sidebar-container" id="mainholderDashboard">

<div id="tabs" class="sidebar tabs">

    <?= $this->render('BoardNotes:dashboard/tabs', array(
         'projectsAccess' => $projectsAccess,
         'user_id' => $user_id,
    )) ?>

</div>

<div id="content" class="sidebar-content">

<?php

$project = array('id' => 0, 'name' => t('BoardNotes_DASHBOARD_ALL_TAB'));
if ($tab_id > 0) {
    $projectAccess = $projectsAccess[$tab_id - 1];
    $project = array('id' => $projectAccess['project_id'],
                     'name' => $projectAccess['project_name'],
                     'is_custom' => $projectAccess['is_custom']);
}

?>

<?= $this->render('BoardNotes:project/data', array(
    'projectsAccess' => $projectsAccess,
    'project' => $project,
    'project_id' => $project['id'],
    'user' => $user,
    'user_id' => $user_id,
    'is_refresh' => false,
    'is_dashboard_view' => 1,
    'data' => $data,
    'categories' => $categories,
    'columns' => $columns,
    'swimlanes' => $swimlanes,
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
print '></div>';

//----------------------------------------

print '<div class="hideMe" id="dialogCreateCustomNoteList" title="' . t('BoardNotes_DASHBOARD_CREATE_CUSTOM_NOTE_LIST') . '">';

print '<input type="text" id="nameCreateCustomNoteList" placeholder="' . t('BoardNotes_DIALOG_CREATE_CUSTOM_NOTE_LIST_NAME_PLACEHOLDER') . '">';
print '<br>';
if ($isAdmin) {
    print '<input type="checkbox" id="globalCreateCustomNoteList">';
    print '<label for="globalCreateCustomNoteList">&nbsp;&nbsp;' . t('BoardNotes_DIALOG_CREATE_CUSTOM_NOTE_LIST_GLOBAL_CHECKBOX') . '</label>';
} else {
    print '<input type="checkbox" disabled id="globalCreateCustomNoteList">';
    print '<label for="globalCreateCustomNoteList">&nbsp;&nbsp;' . t('BoardNotes_DIALOG_CREATE_CUSTOM_NOTE_LIST_GLOBAL_CHECKBOX') . ' ' . t('BoardNotes_DASHBOARD_ADMIN_ONLY') . '</label>';
}
print '<br><br>';
print '<p style="white-space: pre-wrap;">';
print t('BoardNotes_DIALOG_CREATE_CUSTOM_NOTE_LIST_MSG');
print '</p>';

print '</div>';

//---------------------------------------------
