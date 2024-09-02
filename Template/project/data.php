<?php

if (!$is_refresh) { // load CSS and JS and translations only once per project !!!
    // export translations to JS
    print $this->render('TodoNotes:translations/export_to_js');
    // load all necessary CSS and JS
    print $this->asset->css('plugins/TodoNotes/Assets/css/project.css');
    print $this->asset->js('plugins/TodoNotes/Assets/js/statuses.js');
    print $this->asset->js('plugins/TodoNotes/Assets/js/modals.js');
    print $this->asset->js('plugins/TodoNotes/Assets/js/requests.js');
    print $this->asset->js('plugins/TodoNotes/Assets/js/notes.js');
    print $this->asset->js('plugins/TodoNotes/Assets/js/load_project.js');
    print $this->asset->js('plugins/TodoNotes/Assets/js/load_report.js');
    print $this->asset->js('plugins/TodoNotes/Assets/js/load_stats.js');
}

//----------------------------------------

if (!$is_refresh && !$is_dashboard_view) {
    // show project header only when initially viewing notes from project
    print $this->projectHeader->render($project, 'TodoNotesController', 'ShowProject', false, 'TodoNotes');
}

//----------------------------------------

$current_time = time();

$readonlyNotes = ($project_id == 0); // Overview Mode

$tab_id = 1;
$projectsTabsById = array();
foreach ($projectsAccess as $projectAccess) {
    $projectsTabsById[ $projectAccess['project_id'] ] = array('tab_id' => $tab_id, 'name' => $projectAccess['project_name']);
    $tab_id++;
}
//----------------------------------------

$listCategoriesById = '';
$mapCategoryColorByName = array();
if (!empty($categories)) {
    foreach ($categories as $cat) {
        // list by id
        $listCategoriesById .= '<option value="';
        $listCategoriesById .= $cat['id'];
        $listCategoriesById .= '">';
        $listCategoriesById .= $cat['name'];
        $listCategoriesById .= '</option>';
        // map color by name
        $mapCategoryColorByName[ $cat['name'] ] = $cat['color_id'];
        // category color hidden reference
        if (!$is_refresh) { // generate only once per project !!!
            print '<div class="hideMe" id="category-' . $cat['name'] . '"';
            print ' data-color="' . $cat['color_id'] . '"';
            print '></div>';
        }
    }
}

$listColumnsById = '';
if (!empty($columns)) {
    foreach ($columns as $col) {
        $listColumnsById .= '<option value="';
        $listColumnsById .= $col['id'];
        $listColumnsById .= '">';
        $listColumnsById .= $col['title'];
        $listColumnsById .= '</option>';
    }
}

$listSwimlanesById = '';
if (!empty($swimlanes)) {
    foreach ($swimlanes as $swim) {
        $listSwimlanesById .= '<option value="';
        $listSwimlanesById .= $swim['id'];
        $listSwimlanesById .= '">';
        $listSwimlanesById .= $swim['name'];
        $listSwimlanesById .= '</option>';
    }
}

//----------------------------------------

if (!$is_refresh) { // print only once per project !!!
    print '<div align="center">';
    print '<section class="mainholder" id="mainholderP' . $project_id . '">';
    print '<div align="left" id="result' . $project_id . '">';
}

//----------------------------------------
// ACTUAL CONTENT BEGINS HERE !!!
// it shall be regenerated both on initial page load and on every refresh
//----------------------------------------

// evaluate optionArchiveView option from session
if (!array_key_exists('todonotesOption_ArchiveView', $_SESSION)) {
    $_SESSION['todonotesOption_ArchiveView'] = false;
}
$optionArchiveView = $_SESSION['todonotesOption_ArchiveView'];

// evaluate optionShowCategoryColors option from session
if (!array_key_exists('todonotesOption_ShowCategoryColors', $_SESSION)) {
    $_SESSION['todonotesOption_ShowCategoryColors'] = false;
}
$optionShowCategoryColors = $_SESSION['todonotesOption_ShowCategoryColors'];

// evaluate optionSortByStatus option from session
if (!array_key_exists('todonotesOption_SortByStatus', $_SESSION)) {
    $_SESSION['todonotesOption_SortByStatus'] = false;
}
$optionSortByStatus = $_SESSION['todonotesOption_SortByStatus'];

// evaluate optionShowAllDone option from session
if (!array_key_exists('todonotesOption_ShowAllDone', $_SESSION)) {
    $_SESSION['todonotesOption_ShowAllDone'] = false;
}
$optionShowAllDone = $_SESSION['todonotesOption_ShowAllDone'];

// evaluate optionShowTabStats option from session
if (!array_key_exists('todonotesOption_ShowTabStats', $_SESSION)) {
    $_SESSION['todonotesOption_ShowTabStats'] = false;
}
$optionShowTabStats = $_SESSION['todonotesOption_ShowTabStats'];

// session_vars (hidden reference for options)
print '<div class="hideMe" id="session_vars"';
print ' data-optionArchiveView="' . ($optionArchiveView ? 'true' : 'false') . '"';
print ' data-optionShowCategoryColors="' . ($optionShowCategoryColors ? 'true' : 'false') . '"';
print ' data-optionSortByStatus="' . ($optionSortByStatus ? 'true' : 'false') . '"';
print ' data-optionShowAllDone="' . ($optionShowAllDone ? 'true' : 'false') . '"';
print ' data-optionShowTabStats="' . ($optionShowTabStats ? 'true' : 'false') . '"';
print '></div>';

//----------------------------------------

//////////////////////////////////////////
////    NEW NOTE / OVERVIEW MODE TITLE
//////////////////////////////////////////

print '<ul class="ulNotes"><li id="item-0" class="liNewNote" data-id="0" data-project="' . $project_id . '">';
print '<div class="liNewNoteBkgr"></div>';

// here goes the Settings Button Toolbar
print '<div class="toolbarSettingsButtons containerNoWrap containerFloatRight disableEventsPropagation">';

// Toggle Archive View
//print '<div class="dropdown">';
//print '<button id="settingsArchiveView" class="toolbarButton dropdown-menu"';
print '<button id="settingsArchiveView" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_TOGGLE_ARCHIVE_VIEW') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' .  $user_id . '"';
print '>';
print '<i class="fa fa-archive" aria-hidden="true"></i>';
print '</button>';
//print '<ul><li class="toolbarButton">123</li><li class="toolbarButton">456</li><hr class="toolbarDivider"><li class="toolbarButton">789</li></ul>';
//print '</div>';

// add some space between button groups
print '<button class="toolbarSeparator">&nbsp;</button>';

// Toggle category colors
print '<button id="settingsCategoryColors" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_TOGGLE_COLORIZE_BY_CATEGORY') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' .  $user_id . '"';
print '>';
print '<i class="fa fa-paint-brush" aria-hidden="true"></i>';
print '</button>';

// Toggle show All Done
print '<button id="settingsShowAllDone" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_TOGGLE_SHOW_ALL_DONE') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-check-square" aria-hidden="true"></i>';
print '</button>';

// Toggle sort by status
print '<button id="settingsSortByStatus" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_TOGGLE_SORT_BY_STATUS') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-sort" aria-hidden="true"></i>';
print '</button>';

// add some space between button groups
print '<button class="toolbarSeparator">&nbsp;</button>';

// Expand all
print '<button id="settingsExpandAll" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_EXPAND_ALL_NOTES') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-plus-square" aria-hidden="true"></i>';
print '</button>';

// Collapse all
print '<button id="settingsCollapseAll" class="toolbarButton"';
print ' title="' . t('TodoNotes__PROJECT_COLLAPSE_ALL_NOTES') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-minus-square" aria-hidden="true"></i>';
print '</button>';

// exclude when in Overview Mode or in Archive View
if (!$readonlyNotes && !$optionArchiveView) {
    // add some space between button groups
    print '<button class="toolbarSeparator">&nbsp;</button>';

    // Open report
    print '<button id="settingsReport" class="toolbarButton"';
    print ' title="' . t('TodoNotes__PROJECT_CREATE_REPORT') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-file-text" aria-hidden="true"></i>';
    print '</button>';

    // Settings stats
    print '<button id="settingsStats" class="toolbarButton"';
    print ' title="' . t('TodoNotes__PROJECT_NOTES_STATS') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-pie-chart" aria-hidden="true"></i>';
    print '</button>';

    // add some space between button groups
    print '<button class="toolbarSeparator">&nbsp;</button>';

    // Settings delete all done
    print '<button id="settingsDeleteAllDone" class="toolbarButton buttonToggled"';
    print ' title="' . t('TodoNotes__PROJECT_DELETE_ALL_DONE_NOTES') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-trash" aria-hidden="true"></i>';
    print'</button>';
} // end exclude in Overview Mode

print '</div>'; // Settings Button Toolbar

// here goes the Title row
print '<div class="containerNoWrap containerFloatLeft disableEventsPropagation">';
if ($readonlyNotes) {
    if ($optionArchiveView) {
        print '<label class="labelNewNote">' . t('TodoNotes__PROJECT_ARCHIVE_OVERVIEW_MODE_TITLE') . '</label>';
        print '<span class="textNewNote">' . t('TodoNotes__PROJECT_ARCHIVE_OVERVIEW_MODE_TEXT') . '</label>';
    } else {
        print '<label class="labelNewNote">' . t('TodoNotes__PROJECT_OVERVIEW_MODE_TITLE') . '</label>';
        if ($optionSortByStatus) {
            print '<span class="textNewNote">' . t('TodoNotes__PROJECT_OVERVIEW_MODE_TEXT_REORDERING_DISABLED') . '</label>';
        } else {
            print '<span class="textNewNote">' . t('TodoNotes__PROJECT_OVERVIEW_MODE_TEXT') . '</label>';
        }
    }
} else {
    if ($optionArchiveView) {
        print '<label class="labelNewNote">' . t('TodoNotes__PROJECT_ARCHIVE_TITLE') . '</label>';
        print '<span class="textNewNote">' . t('TodoNotes__PROJECT_ARCHIVE_TEXT') . '</label>';
    } else {
        print '<label class="labelNewNote">' . t('TodoNotes__PROJECT_NEW_NOTE_LABEL') . '</label>';
        if ($optionSortByStatus) {
            print '<span class="textNewNote">' . t('TodoNotes__PROJECT_NEW_NOTE_TEXT_REORDERING_DISABLED') . '</span>';
        } else {
            print '<span class="textNewNote"></span>';
        }
    }
}
print '</div>'; // Title row

// here goes the space Placeholder
print '<div class="hideMe containerFloatClear" id="placeholderNewNote"></div>';

// exclude when in Overview Mode or in Archive View
if (!$readonlyNotes && !$optionArchiveView) {
    // Newline after heading and top settings
    print '<br>';

    // here goes the New Note Input row
    print '<div class="containerNoWrap containerFloatClear">';

    // Input line
    print '<input id="inputNewNote" class="inputNewNote disableEventsPropagation"';
    print ' type="text" placeholder="' . t('TodoNotes__PROJECT_NEW_NOTE_TITLE_PLACEHOLDER') . '"';
    print ' title="' . t('TodoNotes__PROJECT_NOTE_TITLE_SAVE_HINT') . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';

    // add some space before button group
    print '<button class="toolbarSeparator">&nbsp;</button>';

    // Save button
    print '<button id="saveNewNote" class="hideMe saveNewNote toolbarButton"';
    print ' title="' . t('TodoNotes__PROJECT_NOTE_SAVE') . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
    print '</button>';

    // Show details button
    print '<button id="showDetailsNewNote" class="showDetailsNewNote toolbarButton"';
    print ' title="' . t('TodoNotes__PROJECT_NOTE_TOGGLE_DETAILS') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-angle-double-down" aria-hidden="true"></i>';
    print '</button>';

    print '</div>'; // New Note Input row

    // here goes the New Note Detailed View
    print '<div id="detailsNewNote" class="hideMe details containerFloatClear noteDetails ui-corner-all"';
    print ' data-id="0"';
    print '>';

    // Category dropdown
    print '<div class="categories disableEventsPropagation">';
    print '<label for="catNewNote">' . t('Category') . '</label> : &nbsp;&nbsp;';
    print '<select id="catNewNote"';
    print ' class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<option selected="selected"></option>'; // Insert empty line for keeping non category by default
    print $listCategoriesById;
    print '</select>';
    print '</div>'; // Category dropdown

    print '<br>';

    // here goes the New Note Edit Button
    print '<div class="containerNoWrap buttonEditMarkdown disableEventsPropagation">';

    // Edit details button
    print '<button id="editDetailsNewNote"';
    print ' class="editDetailsNewNote toolbarButton buttonBigger disableEventsPropagation"';
    print ' title="' . t('TodoNotes__PROJECT_NOTE_EDIT_DETAILS') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
    print '</button>';

    print '</div>'; // New Note Edit Button

    //-----------------------

    print '<div id="noteMarkdownDetailsNewNote_Preview"';
    print ' class="noteDetailsMarkdown disableEventsPropagation"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print t('TodoNotes__PROJECT_NOTE_DETAILS_EDIT_HINT');
    print '</div>';

    print '<div id="noteMarkdownDetailsNewNote_Editor"';
    print ' class="hideMe noteDetailsMarkdown noteEditorMarkdownNewNote disableEventsPropagation"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print $this->form->textEditor('editorMarkdownDetailsNewNote');
    print '</div>';

    //-----------------------

    print '</div>'; // New Note Detailed View
} // end exclude

print '</li></ul>';

//----------------------------------------

//////////////////////////////////////////
////    PROJECT LIST(S)
//////////////////////////////////////////

print '<div id="scrollableContent" class="scrollableContent">';
print '<ul class="ulNotes sortableList accordionShow" id="sortableList-P' . $project_id . '"';
print ' data-project="' . $project_id . '"';
print '>';

//----------------------------------------

$num = 1;
$last_project_id = $project_id;
foreach ($data as $u) {
    if (!empty($project_id) && $u['project_id'] != $project_id) {
        continue;
    }

    // show project name links in Overview Mode
    if ($readonlyNotes && $last_project_id != $u['project_id']) {
        print '</ul>';

        // reset project and number of notes
        $last_project_id = $u['project_id'];

        // project name link
        print '<h2 class="textNonSelectable disableEventsPropagation headerList" id="headerList-P' . $last_project_id . '">';
        print $this->url->link(
            $projectsTabsById[ $last_project_id ]['name'],
            'TodoNotesController',
            'ShowDashboard',
            array('plugin' => 'TodoNotes', 'user_id' => $user_id, 'tab_id' => $projectsTabsById[ $last_project_id ]['tab_id']),
        );
        // collapse/expand project button
        print '<div class="containerNoWrap containerFloatRight">';
        print '<button class="toolbarSeparator">&nbsp;</button>';
        print '<button id="toggleList-P' . $last_project_id . '"';
        print ' class="toolbarButton buttonHeader disableEventsPropagation toggleList"';
        print ' title="' . t('TodoNotes__PROJECT_TOGGLE_LIST') . '"';
        print ' data-project="' . $last_project_id . '"';
        print '>';
        print '<a><i class="fa fa-chevron-circle-up " aria-hidden="true"></i></a>';
        print '</button>';
        print '</div>';
        print '</h2>';

        // sortable list by project
        print '<ul class="ulNotes sortableList accordionShow" id="sortableList-P' . $last_project_id . '"';
        print ' data-project="' . $last_project_id . '"';
        print '>';
    }

    //////////////////////////////////////////
    ////    PROJECT NOTE
    //////////////////////////////////////////

    $isNoteActive = (!$optionArchiveView) ? intval($u['is_active']) : -1;

    print '<li id="item' . '-' . $u['id'] . '" class="liNote" data-id="' . $num . '" data-project="' . $u['project_id'] . '">';
    print '<div class="liNoteBkgr';
    if (!empty($u['category']) && array_key_exists($u['category'], $mapCategoryColorByName)) {
        $category_color = $mapCategoryColorByName[ $u['category'] ];
        if (!empty($category_color)) {
            print ' color-' . $category_color; // append category color class
        }
    }
    print '"></div>';

    // here goes the Note Button Toolbar
    print '<div class="toolbarNoteButtons containerNoWrap containerFloatRight">';

    // and inside it, a Note Label Toolbar container, for easier show/hide of all labels
    print '<span id="toolbarNoteLabels-P' . $u['project_id'] . '-' . $num . '" class="toolbarNoteLabels">';

    // Category label (in simple view)
    print '<label id="noteCatLabel-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="catLabel catLabelClickable textNonSelectable disableEventsPropagation';
    if (!empty($u['category']) && array_key_exists($u['category'], $mapCategoryColorByName)) {
        $category_color = $mapCategoryColorByName[ $u['category'] ];
        if (!empty($category_color)) {
            print ' color-' . $category_color; // append category color class
        }
    } else {
        print ' hideMe'; // hide the label
    }
    print '"';
    print ' title="' . t('Change category') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print $u['category'];
    print '</label>';

    // Date details
    print '<label id="noteDatesDetails-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="dateLabel dateLabelClickable disableEventsPropagation noteDatesDetails"';
    if ($optionArchiveView) {
        print ' title="' . t('TodoNotes__NOTE_DATE_ARCHIVED') . $u['date_archived'] . '"';
    } else {
        print ' title="' . t('TodoNotes__NOTE_DATE_MODIFIED') . $u['date_modified'] . '"';
    }
    print ' data-id="' . $num  . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-calendar-check-o" aria-hidden="true"></i>';
    print '</label>';

    // hide notifications in Archive View
    if ((!$optionArchiveView)) {
        // Notifications details
        $hasNotifications = ($u['notifications_alert_timestamp'] > 0);
        $noteNotificationsStyleExtra = '';
        // Expired notifications
        if ($hasNotifications && ($u['notifications_alert_timestamp'] < $current_time)) {
            $noteNotificationsStyleExtra = ' dateLabelExpired';
        }
        // Complete notifications
        if ($isNoteActive == 0) {
            $noteNotificationsStyleExtra = ' dateLabelComplete';
        }

        print '<label id="noteNotificationsDetails-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="dateLabel dateLabelClickable' . $noteNotificationsStyleExtra . ' disableEventsPropagation noteNotificationsDetails"';
        print ' title="' . t('TodoNotes__NOTE_DATE_NOTIFIED') . ($hasNotifications ? $u['date_notified'] : '🔕') . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-bell' . ($hasNotifications ? '-o' : '-slash-o') . '" aria-hidden="true"></i>';
        print '</label>';
    }

    print '</span>'; // Note Label Toolbar

    // disable reorder related functionality in Archive View
    if ((!$optionArchiveView)) {
        // Refresh order button (shown on changed status in SortByStatus mode only)
        print '<button id="noteRefreshOrder-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="hideMe toolbarButton buttonToggled buttonBigger disableEventsPropagation noteRefreshOrder"';
        print ' title="' . t('TodoNotes__PROJECT_NOTE_REFRESH_ORDER') . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $project_id . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-refresh fa-spin" aria-hidden="true"></i>';
        print '</button>';

        // explicit reorder handle for mobile
        print '<button class="hideMe toolbarButton buttonBigger sortableListHandle">';
        print '<i class="fa fa-arrows-alt" aria-hidden="true"></i>';
        print '</button>';
    }

    // hide all the utility buttons when viewing notes as readonly
    // just allow for note status change
    if (!$readonlyNotes) {
        // hide some utility buttons in Archive View
        if (!$optionArchiveView) {
            // Link button (in detailed view)
            print '<button id="noteLink-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteLink"';
            print ' title="' . t('TodoNotes__PROJECT_NOTE_GET_LINK') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-link" aria-hidden="true"></i>';
            print '</button>';

            // Transfer button (in detailed view)
            print '<button id="noteTransfer-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteTransfer"';
            print ' title="' . t('TodoNotes__PROJECT_NOTE_MOVE_TO_PROJECT') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-exchange" aria-hidden="true"></i>';
            print '</button>';

            // notes from custom lists obviously CANNOT create tasks from notes
            if (!$project['is_custom']) {
                // Add note to tasks table (in detailed view)
                print '<button id="noteCreateTask-P' . $u['project_id'] . '-' . $num . '"';
                print ' class="hideMe toolbarButton noteCreateTask"';
                print ' title="' . t('TodoNotes__PROJECT_NOTE_CREATE_TASK') . '"';
                print ' data-id="' . $num . '"';
                print ' data-project="' . $u['project_id'] . '"';
                print ' data-user="' . $user_id . '"';
                print '>';
                print '<i class="fa fa-share-square-o" aria-hidden="true"></i>';
                print '</button>';
            }

            // add some space between button groups
            print '<button id="toolbarSeparator-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarSeparator"';
            print '>&nbsp;</button>';
        }

        // Delete button viewed (in detailed view)
        if ($optionArchiveView) {
            print '<button id="noteDeleteFromArchive-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteDeleteFromArchive"';
        } else {
            print '<button id="noteDelete-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteDelete"';
        }
        print ' title="' . t('TodoNotes__PROJECT_NOTE_DELETE') . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-trash-o" aria-hidden="true"></i>';
        print '</button>';

        // this button is relevant ONLY for Archive View
        if ($optionArchiveView) {
            // Restore button (in detailed view)
            print '<button id="noteRestoreFromArchive-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteRestoreFromArchive"';
            print ' title="' . t('TodoNotes__PROJECT_NOTE_RESTORE') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-undo" aria-hidden="true"></i>';
            print '</button>';
        }

        // hide some utility buttons in Archive View
        if (!$optionArchiveView) {
            // Archive button (in detailed view)
            print '<button id="noteMoveToArchive-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteMoveToArchive"';
            print ' title="' . t('TodoNotes__PROJECT_NOTE_ARCHIVE') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
            print '</button>';

            // Save button (in detailed view)
            print '<button id="noteSave-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteSave"';
            print ' title="' . t('TodoNotes__PROJECT_NOTE_SAVE') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
            print '</button>';
        }
    }

    // Show details button
    print '<button id="showDetails-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="showDetails toolbarButton"';
    print ' title="' . t('TodoNotes__PROJECT_NOTE_TOGGLE_DETAILS') . '"';
    print ' data-id="' . $num  . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-angle-double-down" aria-hidden="true"></i>';
    print '</button>';

    print '</div>'; // Note Button Toolbar

    // here goes the Note Title row with Checkbox
    print '<div class="containerNoWrap containerFloatLeft disableEventsPropagation">';

    // hide some note elements in Archive View
    if (!$optionArchiveView) {
        // Checkbox for Note Status
        print '<button class="buttonStatus" id="buttonStatus-P' . $u['project_id'] . '-' . $num . '"';
        print ' title="' . t('TodoNotes__PROJECT_NOTE_SWITCH_STATUS') . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i id="noteCheckmark-P' . $u['project_id'] . '-' . $num . '"';
        print ' data-id="' . $isNoteActive . '"';
        if ($isNoteActive == 2) {
            print ' class="statusInProgress" aria-hidden="true"';
        }
        if ($isNoteActive == 1) {
            print ' class="statusOpen" aria-hidden="true"';
        }
        if ($isNoteActive == 0) {
            print ' class="statusDone" aria-hidden="true"';
        }
        print '></i>';
        print '</button>';

        // Note Input line
        print '<input class="hideMe noteTitle" id="noteTitleInput-P' . $u['project_id'] . '-' . $num . '"';
        print ' type="text" placeholder=""';
        print ' title="' . t('TodoNotes__PROJECT_NOTE_TITLE_SAVE_HINT') . '"';
        print ' value="' . htmlspecialchars($u['title']) . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        if ($readonlyNotes) {
            print ' disabled';
        }
        print '>';
    }

    // Note Title label
    print '<label id="noteTitleLabel-P' . $u['project_id'] . '-' . $num . '"';
    if ($isNoteActive == 0) {
        print ' class="noteTitleLabel noteTitle noteDoneText"';
    } else {
        print ' class="noteTitleLabel noteTitle"';
    }
    print ' title="' . t('TodoNotes__PROJECT_NOTE_TITLE_EDIT_HINT') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    if ($readonlyNotes || $optionArchiveView) {
        print ' data-disabled="true"';
    }
    print '>';
    print $u['title'];
    print '</label>';

    print '</div>'; // Note Title row with Checkbox

    // here goes the space Placeholder
    print '<div class="hideMe containerFloatClear" id="notePlaceholder-P' . $u['project_id'] . '-' . $num . '"></div>';

    // here goes the Note Detailed View
    print '<div id="noteDetails-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="hideMe details containerFloatClear noteDetails ui-corner-all"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';

    // Category dropdown
    print '<div class="categories disableEventsPropagation">';
    print '<label for="cat-P' . $u['project_id'] . '-' . $num . '">' . t('Category') . '</label> : &nbsp;&nbsp;';
    print '<select id="cat-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    if ($readonlyNotes || $optionArchiveView) {
        print ' disabled';
    }
    print '>';

    if ($readonlyNotes || $optionArchiveView) {
        // just preserve the existing category data from the note
        print '<option selected="selected">' . $u['category'] . '</option>';
    } else {
        $emptyCatList = empty($listCategoriesById);
        $emptyCat = empty($u['category']);

        if ($emptyCatList || $emptyCat) { // If no categories available or none selected
            print '<option selected="selected"></option>'; // None category selected
        }
        if (!$emptyCat && !$emptyCatList) {
            print '<option></option>'; // add an empty category option
            foreach ($categories as $cat) { // detect the selected category
                if ($cat['name'] == $u['category']) {
                    print '<option value="' . $cat['id'] . '" selected="selected">';
                } else {
                    print '<option value="' . $cat['id'] . '">';
                }
                print $cat['name'];
                print '</option>';
            }
        }
        if ($emptyCat && !$emptyCatList) {
            print $listCategoriesById;
        }
    }

    print '</select>';
    print '</div>'; // Category dropdown

    // Dates and Notifications panel
    print '<div class="containerFloatRight disableEventsPropagation" style="text-align: right">';
    print '<label  id="noteModifiedLabel-P' . $u['project_id'] . '-' . $num . '" class="dateLabel">';
    print '<i class="fa fa-calendar-check-o" aria-hidden="true"> ' . t('TodoNotes__NOTE_DATE_MODIFIED') . $u['date_modified'] . '</i></label><br>';
    print '<label  id="noteCreatedLabel-P' . $u['project_id'] . '-' . $num . '" class="dateLabel">';
    print '<i class="fa fa-calendar-o" aria-hidden="true"> ' . t('TodoNotes__NOTE_DATE_CREATED') . $u['date_created'] . '</i></label><br>';

    if (!empty($u['date_archived'])) {
        print '<label  id="noteArchivedLabel-P' . $u['project_id'] . '-' . $num . '" class="dateLabel">';
        print '<i class="fa fa-file-archive-o" aria-hidden="true"> ' . t('TodoNotes__NOTE_DATE_ARCHIVED') . $u['date_archived'] . '</i></label><br>';
    }

    if (!empty($u['date_restored'])) {
        print '<label  id="noteRestoredLabel-P' . $u['project_id'] . '-' . $num . '" class="dateLabel">';
        print '<i class="fa fa-undo" aria-hidden="true"> ' . t('TodoNotes__NOTE_DATE_RESTORED') . $u['date_restored'] . '</i></label><br>';
    }

    if (!empty($u['last_notified'])) {
        print '<label  id="noteLastNotifiedLabel-P' . $u['project_id'] . '-' . $num . '" class="dateLabel">';
        print '<i class="fa fa-bell" aria-hidden="true"> ' . t('TodoNotes__NOTE_DATE_LAST_NOTIFIED') . $u['last_notified'] . '</i></label><br>';
    }

    if (!$optionArchiveView) {
        print '<label  id="noteNotificationsLabel-P' . $u['project_id'] . '-' . $num . '"';
        print 'class="dateLabel dateLabelClickable' . $noteNotificationsStyleExtra . ' noteNotificationsSetup"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print ' data-notifications-alert-timestamp="' . $u['notifications_alert_timestamp'] . '"';
        print ' data-notifications-alert-timestring="' . $u['date_notified'] . '"';
        print ' data-notifications-options-bitflags="' . $u['flags_notified'] . '"';
        print '>';
        print '<i class="fa fa-bell-o" aria-hidden="true"> ' . t('TodoNotes__NOTE_DATE_NOTIFIED') . ($hasNotifications ? $u['date_notified'] : '🔕') . '</i></label><br>';
    }
    print '</div>'; // Dates and Notifications panel

    //-----------------------

    // Markdown Details
    print '<div class="containerFloatClear">';

    if (!$readonlyNotes && !$optionArchiveView) {
        // here goes the Note Edit Button
        print '<div class="containerNoWrap buttonEditMarkdown disableEventsPropagation">';

        // Edit details button
        print '<button id="editDetails-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="editDetails toolbarButton buttonBigger"';
        print ' title="' . t('TodoNotes__PROJECT_NOTE_EDIT_DETAILS') . '"';
        print ' data-id="' . $num  . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
        print '</button>';

        print '</div>'; // Note Edit Button
    }

    // Markdown Preview
    if ((!$readonlyNotes && !$optionArchiveView) || !empty($u['description'])) {
        print '<div id="noteMarkdownDetails-P' . $u['project_id'] . '-' . $num . '_Preview"';
        print ' class="markdown noteDetailsMarkdown disableEventsPropagation';
        if (!$optionArchiveView && $u['is_active'] == 0) {
            print ' noteDoneMarkdown';
        }
        print '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->render('TodoNotes:widgets/markdown_preview', array(
            'markdown_text' => $u['description'],
        ));
        print '</div>';
    }

    // Markdown Editor
    if (!$readonlyNotes && !$optionArchiveView) {
        print '<div id="noteMarkdownDetails-P' . $u['project_id'] . '-' . $num . '_Editor"';
        print ' class="hideMe noteDetailsMarkdown noteEditorMarkdown disableEventsPropagation"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $project_id . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        $textEditorName = 'editorMarkdownDetails-P' . $u['project_id'] . '-' . $num;
        print $this->form->textEditor($textEditorName, array($textEditorName => $u['description']));
        print '</div>';
    }

    print '</div>'; // Markdown Details

    //-----------------------

    print '</div>'; // Note Detailed View

    // noteId (hidden reference for each note)
    print '<div class="hideMe" id="noteId-P' .  $u['project_id'] . '-' . $num . '"';
    print ' data-note="' . $u['id'] . '"';
    print ' ></div>';

    print '</li>';

    // Id
    $num++;
}

print '</ul>';
print '</div>'; // scrollableContent

//----------------------------------------

// hidden reference for project_id and user_id of the currently active page
print '<div class="hideMe" id="refProjectId"';
print ' data-user="' . $user_id . '"';
print ' data-project="' . $project_id . '"';
print ' data-note="' . (isset($note_id) ? $note_id : 0) . '"';
print ' data-timestamp="' . $current_time . '"';
print '></div>';

print '<span id="refreshIcon" class="refreshIcon hideMe">';
print '&nbsp;<i class="fa fa-refresh fa-spin" title="' . t('TodoNotes__PROJECT_NOTE_BUSY_ICON_HINT') . '"></i></span>';


//----------------------------------------
// ACTUAL CONTENT ENDS HERE !!!
// all sections below must appear ONCE ONLY and NOT be refreshed
//----------------------------------------

if (!$is_refresh) { // print only once per project !!!
    print '</div>'; // id='result'

    //---------------------------------------------
    // include modal dialogs
    print $this->render('TodoNotes:widgets/modal_dialogs', array(
        'project_id' => $project_id,
        'user_datetime_format' => $user_datetime_format,
        'listCategoriesById' => $listCategoriesById,
        'listColumnsById' => $listColumnsById,
        'listSwimlanesById' => $listSwimlanesById,
        'projectsTabsById' => $projectsTabsById,
    ));
    //---------------------------------------------

    print '</section>';
    print '</div>';
} // if (!$is_refresh)
