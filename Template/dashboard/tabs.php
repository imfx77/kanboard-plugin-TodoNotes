<?php

$todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;

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
print '<button id="customNoteListCreate" class="toolbarButton"';
print ' title="' . t('TodoNotes__DASHBOARD_CREATE_CUSTOM_NOTE_LIST') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-fw fa-wpforms" aria-hidden="true"></i></a>';
print '</button>';

// Show tabStatsWidget button
print '<button id="settingsShowTabsStats" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_TOGGLE_TAB_STAT') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-pie-chart" aria-hidden="true"></i></a>';
print '</button>';

// ReIndex button - available to Admins ONLY!
print '<button id="reindexNotesAndLists"';
print $isAdmin
    ? ' class="toolbarButton buttonToggled"'
    : ' class="toolbarButton buttonDisabled"';
print $isAdmin
    ? ' title="' . t('TodoNotes__DASHBOARD_REINDEX') . '"'
    : ' title="' . t('TodoNotes__DASHBOARD_REINDEX') . ' ' . t('TodoNotes__GENERIC_ADMIN_ONLY') . '"';
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
    'stats_user_id' => $user_id,
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
$separatorPlacedShared = false;
$separatorPlacedRegular = false;

//----------------------------------------
foreach ($projectsAccess as $o) {
    $curr_project_id = $o['project_id'];

    $selectedUserGroup = $todonotesSettingsHelper->GetGroupSettings(
        $user_id,
        $curr_project_id,
        $todonotesSettingsHelper::SETTINGS_GROUP_USER,
        true /*from_session*/
    );
    $selectedUser = (count($selectedUserGroup) == 1) ? $selectedUserGroup[0] : $user_id;

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
        print '<ul id="groupGlobal" class="ulLists accordionShow">';
    }

    // separator header for custom PRIVATE lists
    if (!$separatorPlacedPrivate && $o['is_custom'] && !$o['is_global'] && $o['is_owner']) {
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
        print '<ul id="groupPrivate" class="ulLists accordionShow">';
    }

    // separator header for custom SHARED lists
    if (!$separatorPlacedShared && $o['is_custom'] && !$o['is_global'] && !$o['is_owner']) {
        print '</ul>';
        print '<hr class="hrTabs">';
        print '<h4 id="headerGroupShared" class="tabGroupHeader localTable textNonSelectable disableTabsEventsPropagation">';
        print '<div class="localTableCell">' . t('TodoNotes__DASHBOARD_LIST_GROUP_SHARED') . '</div>';
        // collapse/expand lists group button
        print '<div class="localTableCellExpandRight">';
        print '<button class="toolbarSeparator">&nbsp;</button>';
        print '<button id="toggleGroupShared" class="toolbarButton buttonHeader disableTabsEventsPropagation"';
        print ' title="' . t('TodoNotes__DASHBOARD_TOGGLE_LIST_GROUP') . '">';
        print '<a><i class="fa fa-chevron-circle-up " aria-hidden="true"></i></a>';
        print '</button></div></h4>';
        $separatorPlacedShared = true;
        print '<ul id="groupShared" class="ulLists accordionShow">';
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
        print '<ul id="groupRegular" class="ulLists accordionShow">';
    }

    //----------------------------------------
    print '<li class="singleTab" id="singleTab-P' . $curr_project_id . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $curr_project_id . '"';
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
    $hasGrantedSharingPermissions = (count($this->model->todoNotesModel->GetGrantedSharingPermissions($curr_project_id, $user_id)) > 0);

    $isMoveableTab = $o['is_custom'] && ($isAdmin || $o['is_owner']);
    $tabSeparatorStyle = 'toolbarSeparator' . ($isMoveableTab ? ' tabMoveable' : '');

    print '<div class="localTableCellExpandRight' . ($isMoveableTab ? ' tabMoveable' : '') . '"></div>';
    print '<div class="localTableCellExpandRight">';

    if ($o['is_custom']) {
        if ($o['is_global']) {
            // managing custom GLOBAL lists is available to Admins ONLY!
            //----------------------------------------

            // button space
            print '<button class="' . $tabSeparatorStyle . '">&nbsp;</button>';
            // explicit reorder handle for mobile
            print $isAdmin
                ? '<button class="hideMe toolbarButton buttonBigger buttonToggled sortableGroupHandle">'
                : '<button class="hideMe toolbarButton buttonBigger buttonDisabled sortableGroupHandle">';
            print '<i class="fa fa-arrows" aria-hidden="true"></i>';
            print '</button>';
            // button space
            print '<button class="' . $tabSeparatorStyle . '">&nbsp;</button>';

            // Rename button
            print '<button id="customNoteListRenameGlobal-P' . $curr_project_id . '"';
            print $isAdmin
                ? ' class="toolbarButton buttonToggled customNoteListRenameGlobal"'
                : ' class="toolbarButton buttonDisabled customNoteListRenameGlobal"';
            print $isAdmin
                ? ' title="' . t('TodoNotes__DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST') . '"'
                : ' title="' . t('TodoNotes__DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST') . ' ' . t('TodoNotes__GENERIC_ADMIN_ONLY') . '"';
            print ' data-project="' . $curr_project_id . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
            print '</button>';

            // Delete button
            print '<button id="customNoteListDeleteGlobal-P' . $curr_project_id . '"';
            print $isAdmin
                ? ' class="toolbarButton buttonToggled customNoteListDeleteGlobal"'
                : ' class="toolbarButton buttonDisabled customNoteListDeleteGlobal"';
            print $isAdmin
                ? ' title="' . t('TodoNotes__DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST') . '"'
                : ' title="' . t('TodoNotes__DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST') . ' ' . t('TodoNotes__GENERIC_ADMIN_ONLY') . '"';
            print ' data-project="' . $curr_project_id . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-trash-o" aria-hidden="true"></i>';
            print '</button>';

            // Share button
            print '<button id="customNoteListShare-P' . $curr_project_id . '"';
            print ' class="toolbarButton customNoteListShare"';
            print ' title="' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS') . '"';
            print ' data-project="' . $curr_project_id . '"';
            print ' data-user="' . $user_id . '"';
            print ' data-id="' . $num . '"';
            print '>';
            print '<a><i class="fa fa-share-alt' . ($hasGrantedSharingPermissions ? ' buttonHighlighted' : '') . '" aria-hidden="true"></i></a>';
            print '</button>';
            //----------------------------------------
        } elseif ($o['is_owner']) {
            // managing custom PRIVATE lists is available to each user for their owned lists
            //----------------------------------------

            // button space
            print '<button class="' . $tabSeparatorStyle . '">&nbsp;</button>';
            // explicit reorder handle for mobile
            print '<button class="hideMe toolbarButton buttonBigger sortableGroupHandle">';
            print '<a><i class="fa fa-arrows" aria-hidden="true"></i></a>';
            print '</button>';
            // button space
            print '<button class="' . $tabSeparatorStyle . '">&nbsp;</button>';

            // Rename button
            print '<button id="customNoteListRenamePrivate-P' . $curr_project_id . '"';
            print ' class="toolbarButton customNoteListRenamePrivate"';
            print ' title="' . t('TodoNotes__DASHBOARD_RENAME_CUSTOM_PRIVATE_LIST') . '"';
            print ' data-project="' . $curr_project_id . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<a><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            print '</button>';

            // Delete button
            print '<button id="customNoteListDeletePrivate-P' . $curr_project_id . '"';
            print ' class="toolbarButton customNoteListDeletePrivate"';
            print ' title="' . t('TodoNotes__DASHBOARD_DELETE_CUSTOM_PRIVATE_LIST') . '"';
            print ' data-project="' . $curr_project_id . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<a><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
            print '</button>';

            // Share button
            print '<button id="customNoteListShare-P' . $curr_project_id . '"';
            print ' class="toolbarButton customNoteListShare"';
            print ' title="' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS') . '"';
            print ' data-project="' . $curr_project_id . '"';
            print ' data-user="' . $user_id . '"';
            print ' data-id="' . $num . '"';
            print '>';
            print '<a><i class="fa fa-share-alt' . ($hasGrantedSharingPermissions ? ' buttonHighlighted' : '') . '" aria-hidden="true"></i></a>';
            print '</button>';
            //----------------------------------------
        } elseif (!$o['is_owner']) {
            // managing custom SHARED lists is available to each user ONLY for PRIVATE custom lists owned and shared by other users
            //----------------------------------------

            // button space
            print '<button class="toolbarSeparator">&nbsp;</button>';

            // Permissions icon
            print '<button class="toolbarButton buttonNonClickable">';
            switch ($o['permissions']) {
                case $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_VIEW:
                    print '<i class="fa fa-eye" aria-hidden="true" title="' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS_VIEW') . '"></i>';
                    break;
                case $this->model->todoNotesModel::PROJECT_SHARING_PERMISSION_EDIT:
                    print '<i class="fa fa-pencil" aria-hidden="true" title="' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS_EDIT') . '"></i>';
                    break;
                default:
                    print '<i class="fa fa-question" aria-hidden="true" title="' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS_UNKNOWN') . '"></i>';
                    break;
            }
            print '</button>';

            // Owner avatar
            print '<button class="toolbarButton buttonDisabled">';
            $owner_details = $this->model->userModel->getById($o['owner_id']);
            print $this->avatar->small(
                $owner_details['id'],
                $owner_details['username'],
                $owner_details['name'],
                $owner_details['email'],
                $owner_details['avatar_path'],
                'avatar-inline'
            );
            print '</button>';
            //----------------------------------------
        }
    } else {
        // shortcut buttons for regular projects ONLY
        //----------------------------------------

        // button space
        print '<button class="toolbarSeparator">&nbsp;</button>';

        // goto Board button
        print '<button id="gotoProjectBoard-P' . $curr_project_id . '"';
        print ' class="toolbarButton gotoProjectBoard"';
        print ' title="' . t('Board') . ' ⇗' . '"';
        print ' data-project="' . $curr_project_id . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('th', '', 'BoardViewController', 'show', array('project_id' => $curr_project_id), false, 'view-board', t('Board') . ' ⇗', true);
        print '</button>';

        // goto Tasks button
        print '<button id="gotoProjectTasks-P' . $curr_project_id . '"';
        print ' class="toolbarButton gotoProjectTasks"';
        print ' title="' . t('List') . ' ⇗' . '"';
        print ' data-project="' . $curr_project_id . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('list', '', 'TaskListController', 'show', array('project_id' => $curr_project_id), false, 'view-listing', t('List') . ' ⇗', true);
        print '</button>';

        // Share button
        print '<button id="customNoteListShare-P' . $curr_project_id . '"';
        print ' class="toolbarButton customNoteListShare"';
        print ' title="' . t('TodoNotes__PROJECT_SHARING_PERMISSIONS') . '"';
        print ' data-project="' . $curr_project_id . '"';
        print ' data-user="' . $user_id . '"';
        print ' data-id="' . $num . '"';
        print '>';
        print '<a><i class="fa fa-share-alt' . ($hasGrantedSharingPermissions ? ' buttonHighlighted' : '') . '" aria-hidden="true"></i></a>';
        print '</button>';
        //----------------------------------------
    }

    print '</div>'; // buttons for single tabs

    // stats widget for single tabs
    //----------------------------------------
    print '<div class="hideMe localTableCell tabStatsWidget">';
    print $this->render('TodoNotes:widgets/stats', array(
        'stats_project_id' => $curr_project_id,
        'stats_user_id' => $selectedUser,
        'stats_is_shared' => ($selectedUser != $user_id),
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
