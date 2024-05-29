<?php

$num = 0;

$isAdmin = $this->user->isAdmin();

print '<ul id="groupAll" class="ulLists">';

// Add a default tab that denotes none project and all notes
//----------------------------------------
print '<li class="singleTab" id="singleTab-P0"';
print ' data-id="' . $num . '"';
print ' data-project="0"';
print '>';

// ALL tab title row container
//----------------------------------------
print '<div class="localTable">';

// ALL tab title
print '<div class="localTableCell textNonSelectable">';
print $this->url->link(
    t('TodoNotes__DASHBOARD_ALL_TAB'),
    'TodoNotesController',
    'ShowDashboard',
    array('plugin' => 'TodoNotes', 'user_id' => $user_id, 'tab_id' => $num)
);
print '</div>'; // ALL tab title

// buttons for ALL tab
//----------------------------------------
print '<div class="localTableCellExpandRight">';

// button space
print '<button class="toolbarSeparator">&nbsp;</button>';

// New list button
print '<button id="customNoteListCreate"';
print ' class="toolbarButton customNoteListCreate"';
print ' title="' . t('TodoNotes__DASHBOARD_CREATE_CUSTOM_NOTE_LIST') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-fw fa-wpforms" aria-hidden="true"></i></a>';
print '</button>';

// Show tabStatsWidget button
print '<button id="settingsTabStats"';
print ' class="toolbarButton settingsTabStats"';
print ' title="' . t('TodoNotes__PROJECT_TOGGLE_TAB_STAT') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-pie-chart" aria-hidden="true"></i></a>';
print '</button>';

// ReIndex button - available to Admins ONLY!
print '<button id="reindexNotesAndLists"';
print $isAdmin
    ? ' class="toolbarButton buttonToggled reindexNotesAndLists"'
    : ' class="toolbarButton buttonDisabled reindexNotesAndLists"';
print $isAdmin
    ? ' title="' . t('TodoNotes__DASHBOARD_REINDEX') . '"'
    : ' title="' . t('TodoNotes__DASHBOARD_REINDEX') . ' ' . t('TodoNotes__DASHBOARD_ADMIN_ONLY') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-fw fa-recycle" aria-hidden="true"></i>';
print '</button>';

print '</div>'; // buttons for ALL tab

// stats widget for ALL tab
//----------------------------------------
print '<div class="hideMe localTableCell tabStatsWidget">';
print $this->render('TodoNotes:widgets/stats', array(
     'stats_project_id' => 0,
));
print '</div>'; // stats widget for ALL tab

//----------------------------------------
print '</div>'; // ALL tab title row container

//----------------------------------------
print '</li>';
$num++;

// Loop through all projects for single tabs
//----------------------------------------
$separatorPlacedGlobal = false;
$separatorPlacedPrivate = false;
$separatorPlacedRegular = false;

//----------------------------------------
foreach ($projectsAccess as $o) {
    // separator header for custom GLOBAL lists
    if (!$separatorPlacedGlobal && $o['is_custom'] && $o['is_global']) {
        print '</ul>';
        print '<hr class="hrTabs">';
        print '<h4 id="headerGroupGlobal" class="tabGroupHeader localTable textNonSelectable disableTabsEventsPropagation">';
        print '<div class="localTableCell">' . t('TodoNotes__DASHBOARD_LIST_GROUP_GLOBAL') . '</div>';
        // collapse/expand lists group button
        print '<div class="localTableCellExpandRight">';
        print '<button class="toolbarSeparator">&nbsp;</button>';
        print '<button id="toggleGroupGlobal" class="toolbarButton buttonHeader disableTabsEventsPropagation"';
        print ' title="' . t('TodoNotes__DASHBOARD_TOGGLE_LIST_GROUP') . '">';
        print '<a><i class="fa fa-chevron-circle-up " aria-hidden="true"></i></a>';
        print '</button></div></h4>';
        $separatorPlacedGlobal = true;
        print '<ul id="groupGlobal" class="ulLists">';
    }

    // separator header for custom PRIVATE lists
    if (!$separatorPlacedPrivate && $o['is_custom'] && !$o['is_global']) {
        print '</ul>';
        print '<hr class="hrTabs">';
        print '<h4 id="headerGroupPrivate" class="tabGroupHeader localTable textNonSelectable disableTabsEventsPropagation">';
        print '<div class="localTableCell">' . t('TodoNotes__DASHBOARD_LIST_GROUP_PRIVATE') . '</div>';
        // collapse/expand lists group button
        print '<div class="localTableCellExpandRight">';
        print '<button class="toolbarSeparator">&nbsp;</button>';
        print '<button id="toggleGroupPrivate" class="toolbarButton buttonHeader disableTabsEventsPropagation"';
        print ' title="' . t('TodoNotes__DASHBOARD_TOGGLE_LIST_GROUP') . '">';
        print '<a><i class="fa fa-chevron-circle-up " aria-hidden="true"></i></a>';
        print '</button></div></h4>';
        $separatorPlacedPrivate = true;
        print '<ul id="groupPrivate" class="ulLists">';
    }

    // separator header for regular projects
    if (!$separatorPlacedRegular && !$o['is_custom']) {
        print '</ul>';
        print '<hr class="hrTabs">';
        print '<h4 id="headerGroupRegular" class="tabGroupHeader localTable textNonSelectable disableTabsEventsPropagation">';
        print '<div class="localTableCell">' . t('TodoNotes__DASHBOARD_LIST_GROUP_REGULAR') . '</div>';
        // collapse/expand lists group button
        print '<div class="localTableCellExpandRight">';
        print '<button class="toolbarSeparator">&nbsp;</button>';
        print '<button id="toggleGroupRegular" class="toolbarButton buttonHeader disableTabsEventsPropagation"';
        print ' title="' . t('TodoNotes__DASHBOARD_TOGGLE_LIST_GROUP') . '">';
        print '<a><i class="fa fa-chevron-circle-up " aria-hidden="true"></i></a>';
        print '</button></div></h4>';
        $separatorPlacedRegular = true;
        print '<ul id="groupRegular" class="ulLists">';
    }

    //----------------------------------------
    print '<li class="singleTab" id="singleTab-P' . $o['project_id'] . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $o['project_id'] . '"';
    print '>';

    // single tab title row container
    //----------------------------------------
    print '<div class="localTable">';

    // single tab title
    print '<div class="localTableCell textNonSelectable">';
    print $this->url->link(
        $o['project_name'],
        'TodoNotesController',
        'ShowDashboard',
        array('plugin' => 'TodoNotes', 'user_id' => $user_id, 'tab_id' => $num)
    );
    print '</div>'; // single tab title

    // buttons for single tabs
    //----------------------------------------
    print '<div class="localTableCellExpandRight">';

    if ($o['is_custom']) {
        if ($o['is_global']) {
            // managing custom GLOBAL lists is available to Admins ONLY!
            //----------------------------------------

            // button space
            print '<button class="toolbarSeparator">&nbsp;</button>';
            // explicit reorder handle for mobile
            print $isAdmin
                ? '<button class="hideMe toolbarButton buttonBigger buttonToggled sortableGroupHandle">'
                : '<button class="hideMe toolbarButton buttonBigger buttonDisabled sortableGroupHandle">';
            print '<i class="fa fa-arrows-alt" aria-hidden="true"></i>';
            print '</button>';
            // button space
            print '<button class="toolbarSeparator">&nbsp;</button>';

            // Rename button
            print '<button id="customNoteListRenameGlobal-P' . $o['project_id'] . '"';
            print $isAdmin
                ? ' class="toolbarButton buttonToggled customNoteListRenameGlobal"'
                : ' class="toolbarButton buttonDisabled customNoteListRenameGlobal"';
            print $isAdmin
                ? ' title="' . t('TodoNotes__DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST') . '"'
                : ' title="' . t('TodoNotes__DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST') . ' ' . t('TodoNotes__DASHBOARD_ADMIN_ONLY') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
            print '</button>';

            // Delete button
            print '<button id="customNoteListDeleteGlobal-P' . $o['project_id'] . '"';
            print $isAdmin
                ? ' class="toolbarButton buttonToggled customNoteListDeleteGlobal"'
                : ' class="toolbarButton buttonDisabled customNoteListDeleteGlobal"';
            print $isAdmin
                ? ' title="' . t('TodoNotes__DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST') . '"'
                : ' title="' . t('TodoNotes__DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST') . ' ' . t('TodoNotes__DASHBOARD_ADMIN_ONLY') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-trash-o" aria-hidden="true"></i>';
            print '</button>';
            //----------------------------------------
        } else {
            // managing custom PRIVATE lists is available to each user for their owned lists
            //----------------------------------------

            // button space
            print '<button class="toolbarSeparator">&nbsp;</button>';
            // explicit reorder handle for mobile
            print '<button class="hideMe toolbarButton buttonBigger sortableGroupHandle">';
            print '<a><i class="fa fa-arrows-alt" aria-hidden="true"></i></a>';
            print '</button>';
            // button space
            print '<button class="toolbarSeparator">&nbsp;</button>';

            // Rename button
            print '<button id="customNoteListRenamePrivate-P' . $o['project_id'] . '"';
            print ' class="toolbarButton customNoteListRenamePrivate"';
            print ' title="' . t('TodoNotes__DASHBOARD_RENAME_CUSTOM_PRIVATE_LIST') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<a><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            print '</button>';

            // Delete button
            print '<button id="customNoteListDeletePrivate-P' . $o['project_id'] . '"';
            print ' class="toolbarButton customNoteListDeletePrivate"';
            print ' title="' . t('TodoNotes__DASHBOARD_DELETE_CUSTOM_PRIVATE_LIST') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<a><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
            print '</button>';
            //----------------------------------------
        }
    } else {
        // shortcut buttons for regular projects ONLY
        //----------------------------------------

        // button space
        print '<button class="toolbarSeparator">&nbsp;</button>';

        // goto Board button
        print '<button id="gotoProjectBoard-P' . $o['project_id'] . '"';
        print ' class="toolbarButton gotoProjectBoard"';
        print ' title="' . t('Board') . ' ⇗' . '"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('th', '', 'BoardViewController', 'show', array('project_id' => $o['project_id']), false, 'view-board', t('Board') . ' ⇗');
        print '</button>';

        // goto Tasks button
        print '<button id="gotoProjectTasks-P' . $o['project_id'] . '"';
        print ' class="toolbarButton gotoProjectTasks"';
        print ' title="' . t('List') . ' ⇗' . '"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('list', '', 'TaskListController', 'show', array('project_id' => $o['project_id']), false, 'view-listing', t('List') . ' ⇗');
        print '</button>';
        //----------------------------------------
    }

    print '</div>'; // buttons for single tabs

    // stats widget for single tabs
    //----------------------------------------
    print '<div class="hideMe localTableCell tabStatsWidget">';
    print $this->render('TodoNotes:widgets/stats', array(
         'stats_project_id' => $o['project_id'],
    ));
    print '</div>'; // stats widget for single tabs

    //----------------------------------------
    print '</div>'; // single tab title row container

    //----------------------------------------
    print'</li>';
    $num++;
}

print '</ul>';
print '<hr class="hrTabs">';
