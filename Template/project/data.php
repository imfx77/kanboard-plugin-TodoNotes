<?php

if (!$is_refresh) { // load CSS and JS and translations only once per project !!!
    print $this->asset->css('plugins/BoardNotes/Assets/css/project.css');
    print $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js');
    print $this->asset->js('plugins/BoardNotes/Assets/js/load_project.js');
    print $this->asset->js('plugins/BoardNotes/Assets/js/load_report.js');
    print $this->asset->js('plugins/BoardNotes/Assets/js/load_stats.js');

    // export translations to JS
    print $this->render('BoardNotes:translations/export_to_js');
}

//----------------------------------------

if (!$is_refresh && !$is_dashboard_view) {
    // show project header only when initially viewing notes from project
    print $this->projectHeader->render($project, 'BoardNotesController', 'boardNotesShowProject', false, 'BoardNotes');
}

//----------------------------------------

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

// evaluate optionShowCategoryColors option from session
if (!array_key_exists('boardnotesShowCategoryColors', $_SESSION)) {
    $_SESSION['boardnotesShowCategoryColors'] = false;
}
$optionShowCategoryColors = $_SESSION['boardnotesShowCategoryColors'];

// evaluate optionSortByStatus option from session
if (!array_key_exists('boardnotesSortByStatus', $_SESSION)) {
    $_SESSION['boardnotesSortByStatus'] = false;
}
$optionSortByStatus = $_SESSION['boardnotesSortByStatus'];

// evaluate optionShowAllDone option from session
if (!array_key_exists('boardnotesShowAllDone', $_SESSION)) {
    $_SESSION['boardnotesShowAllDone'] = false;
}
$optionShowAllDone = $_SESSION['boardnotesShowAllDone'];

// evaluate optionShowTabStats option from session
if (!array_key_exists('boardnotesShowTabStats', $_SESSION)) {
    $_SESSION['boardnotesShowTabStats'] = false;
}
$optionShowTabStats = $_SESSION['boardnotesShowTabStats'];

// session_vars (hidden reference for options)
print '<div class="hideMe" id="session_vars"';
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

// Toggle category colors
print '<button id="settingsCategoryColors" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_TOGGLE_COLORIZE_BY_CATEGORY') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' .  $user_id . '"';
print '>';
print '<i class="fa fa-paint-brush" aria-hidden="true"></i>';
print '</button>';

// Toggle show All Done
print '<button id="settingsShowAllDone" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_TOGGLE_SHOW_ALL_DONE') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-check-square" aria-hidden="true"></i>';
print '</button>';

// Toggle sort by status
print '<button id="settingsSortByStatus" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_TOGGLE_SORT_BY_STATUS') . '"';
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
print ' title="' . t('BoardNotes_PROJECT_EXPAND_ALL_NOTES') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-plus-square" aria-hidden="true"></i>';
print '</button>';

// Collapse all
print '<button id="settingsCollapseAll" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_COLLAPSE_ALL_NOTES') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-minus-square" aria-hidden="true"></i>';
print '</button>';

// exclude when in Overview Mode
if (!$readonlyNotes) {
    // add some space between button groups
    print '<button class="toolbarSeparator">&nbsp;</button>';

    // Open report
    print '<button id="settingsReport" class="toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_CREATE_REPORT') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-file-text" aria-hidden="true"></i>';
    print '</button>';

    // Settings stats
    print '<button id="settingsStats" class="toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTES_STATS') . '"';
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
    print ' title="' . t('BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES') . '"';
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
    print '<label class="labelNewNote">' . t('BoardNotes_PROJECT_OVERVIEW_MODE_TITLE') . '</label>';
    if ($optionSortByStatus) {
        print '<span class="textNewNote">' . t('BoardNotes_PROJECT_OVERVIEW_MODE_TEXT_REORDERING_DISABLED') . '</label>';
    } else {
        print '<span class="textNewNote">' . t('BoardNotes_PROJECT_OVERVIEW_MODE_TEXT') . '</label>';
    }
} else {
    print '<label class="labelNewNote">' . t('BoardNotes_PROJECT_NEW_NOTE_LABEL') . '</label>';
    if ($optionSortByStatus) {
        print '<span class="textNewNote">' . t('BoardNotes_PROJECT_NEW_NOTE_TEXT_REORDERING_DISABLED') . '</span>';
    } else {
        print '<span class="textNewNote"></span>';
    }
}
print '</div>'; // Title row

// here goes the space Placeholder
print '<div class="hideMe containerFloatClear" id="placeholderNewNote"></div>';

// exclude when in Overview Mode
if (!$readonlyNotes) {
    // Newline after heading and top settings
    print '<br>';

    // here goes the New Note Input row
    print '<div class="containerNoWrap containerFloatClear">';

    // Input line
    print '<input id="inputNewNote" class="inputNewNote disableEventsPropagation"';
    print ' type="text" placeholder="' . t('BoardNotes_PROJECT_NEW_NOTE_TITLE_PLACEHOLDER') . '"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TITLE_SAVE_HINT') . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';

    // add some space before button group
    print '<button class="toolbarSeparator">&nbsp;</button>';

    // Save button
    print '<button id="saveNewNote" class="hideMe saveNewNote toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_SAVE') . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
    print '</button>';

    // Show details button
    print '<button id="showDetailsNewNote" class="showDetailsNewNote toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TOGGLE_DETAILS') . '"';
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
    print '<div class="containerNoWrap containerAbsoluteLeft disableEventsPropagation">';

    // Edit details button
    print '<button id="editDetailsNewNote"';
    print ' class="editDetailsNewNote toolbarButton buttonBigger disableEventsPropagation"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_EDIT_DETAILS') . '"';
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
    print t('BoardNotes_PROJECT_NOTE_DETAILS_EDIT_HINT');
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
print '<ul class="ulNotes sortableList" id="sortableList-P' . $project_id . '"';
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
            'BoardNotesController',
            'boardNotesShowAll',
            array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $projectsTabsById[ $last_project_id ]['tab_id']),
        );
        // collapse/expand project button
        print '<div class="containerNoWrap containerFloatRight">';
        print '<button class="toolbarSeparator">&nbsp;</button>';
        print '<button id="toggleList-P' . $last_project_id . '"';
        print ' class="toolbarButton buttonHeader disableEventsPropagation toggleList"';
        print ' title="' . t('BoardNotes_PROJECT_TOGGLE_LIST') . '"';
        print ' data-project="' . $last_project_id . '"';
        print '>';
        print '<a><i class="fa fa-chevron-circle-up " aria-hidden="true"></i></a>';
        print '</button>';
        print '</div>';
        print '</h2>';

        // sortable list by project
        print '<ul class="ulNotes sortableList" id="sortableList-P' . $last_project_id . '"';
        print ' data-project="' . $last_project_id . '"';
        print '>';
    }

    //////////////////////////////////////////
    ////    PROJECT NOTE
    //////////////////////////////////////////

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

    // Category label (in simple view)
    print '<label id="noteCatLabel-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="catLabel catLabelClickable textNonSelectable disableEventsPropagation';
    if (!empty($u['category']) && array_key_exists($u['category'], $mapCategoryColorByName)) {
        $category_color = $mapCategoryColorByName[ $u['category'] ];
        if (!empty($category_color)) {
            print ' color-' . $category_color; // append category color class
        }
    }
    print '"';
    print ' title="' . t('Change category') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print $u['category'];
    print '</label>';

    // Refresh order button (shown on changed status in SortByStatus mode only)
    print '<button id="noteRefreshOrder-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="hideMe toolbarButton buttonToggled buttonBigger disableEventsPropagation noteRefreshOrder"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_REFRESH_ORDER') . '"';
    print ' data-id="' . $num  . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-refresh fa-spin" aria-hidden="true"></i>';
    print '</button>';

    // explicit reorder handle for mobile
    print '<button class="hideMe toolbarButton buttonBigger sortableListHandle">';
    print '<i class="fa fa-arrows-alt" aria-hidden="true"></i>';
    print '</button>';

    // hide all the utility buttons when viewing notes as readonly
    // just allow for note status change
    if (!$readonlyNotes) {
        // notes from custom lists obviously CANNOT create tasks from notes
        if (!$project['is_custom']) {
            // Add note to tasks table (in detailed view)
            print '<button id="noteCreateTask-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteCreateTask"';
            print ' title="' . t('BoardNotes_PROJECT_NOTE_CREATE_TASK') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-share-square-o" aria-hidden="true"></i>';
            print '</button>';
        }

        // Transfer button (in detailed view)
        print '<button id="noteTransfer-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="hideMe toolbarButton noteTransfer"';
        print ' title="' . t('BoardNotes_PROJECT_NOTE_MOVE_TO_PROJECT') . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-exchange" aria-hidden="true"></i>';
        print '</button>';

        // add some space between button groups
        print '<button id="toolbarSeparator-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="hideMe toolbarSeparator"';
        print '>&nbsp;</button>';

        // Delete button viewed (in detailed view)
        print '<button id="noteDelete-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="hideMe toolbarButton noteDelete"';
        print ' title="' . t('BoardNotes_PROJECT_NOTE_DELETE') . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-trash-o" aria-hidden="true"></i>';
        print '</button>';

        // Save button (in detailed view)
        print '<button id="noteSave-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="hideMe toolbarButton noteSave"';
        print ' title="' . t('BoardNotes_PROJECT_NOTE_SAVE')  . '"';
        print ' data-id="' . $num . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
        print '</button>';
    }

    // Show details button
    print '<button id="showDetails-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="showDetails toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TOGGLE_DETAILS') . '"';
    print ' data-id="' . $num  . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-angle-double-down" aria-hidden="true"></i>';
    print '</button>';

    print '</div>'; // Note Button Toolbar

    // here goes the Note Title row with Checkbox
    print '<div class="containerNoWrap containerFloatLeft disableEventsPropagation">';

    // Checkbox for Note Status
    print '<button class="buttonStatus" id="buttonStatus-P' . $u['project_id'] . '-' . $num . '"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_SWITCH_STATUS') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i id="noteCheckmark-P' . $u['project_id'] . '-' . $num . '"';
    print ' data-id="' . $u['is_active'] . '"';
    if ($u['is_active'] == "2") {
        print ' class="statusInProgress" aria-hidden="true"';
    }
    if ($u['is_active'] == "1") {
        print ' class="statusOpen" aria-hidden="true"';
    }
    if ($u['is_active'] == "0") {
        print ' class="statusDone" aria-hidden="true"';
    }
    print '></i>';
    print '</button>';

    // Note Input line
    print '<input class="hideMe noteTitle" id="noteTitleInput-P' . $u['project_id'] . '-' . $num . '"';
    print ' type="text" placeholder=""';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TITLE_SAVE_HINT') . '"';
    print ' value="' . $u['title'] . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    if ($readonlyNotes) {
        print ' disabled';
    }
    print '>';

    // Note Title label
    print '<label id="noteTitleLabel-P' . $u['project_id'] . '-' . $num . '"';
    if ($u['is_active'] == "0") {
        print ' class="noteTitleLabel noteTitle noteDoneText"';
    } else {
        print ' class="noteTitleLabel noteTitle"';
    }
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TITLE_EDIT_HINT') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    if ($readonlyNotes) {
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
    if ($readonlyNotes) {
        print ' disabled';
    }
    print '>';

    if ($readonlyNotes) {
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

    print '<br>';

    //-----------------------

    if (!$readonlyNotes) {
        // here goes the Note Edit Buttons
        print '<div class="containerNoWrap containerAbsoluteLeft disableEventsPropagation">';

        // Edit details button
        print '<button id="editDetails-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="editDetails toolbarButton buttonBigger"';
        print ' title="' . t('BoardNotes_PROJECT_NOTE_EDIT_DETAILS') . '"';
        print ' data-id="' . $num  . '"';
        print ' data-project="' . $u['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
        print '</button>';

        print '</div>'; // Note Edit Buttons
    }

    //-----------------------

    print '<div id="noteMarkdownDetails-P' . $u['project_id'] . '-' . $num . '_Preview"';
    print ' class="markdown noteDetailsMarkdown disableEventsPropagation';
    if ($u['is_active'] == "0") {
        print ' noteDoneMarkdown';
    }
    print '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print $this->render('BoardNotes:widgets/markdown_preview', array(
         'markdown_text' => $u['description'],
    ));
    print '</div>';

    if (!$readonlyNotes) {
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
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print ' data-timestamp="' . time() . '"';
print '></div>';

print '<span id="boardnotesBusyIcon" class="boardnotesBusyIcon hideMe">';
print '&nbsp;<i class="fa fa-refresh fa-spin" title="' . t('BoardNotes_PROJECT_NOTE_BUSY_ICON_HINT') . '"></i></span>';


//----------------------------------------
// ACTUAL CONTENT ENDS HERE !!!
// all sections below must NOT be appended again on refresh
//----------------------------------------

if (!$is_refresh) { // print only once per project !!!
    print '</div>'; // id='result'
}

//----------------------------------------

if (!$is_refresh) { // print only once per project !!!
    //---------------------------------------------

    print '<div class="hideMe" id="dialogDeleteNote" title="' . t('BoardNotes_PROJECT_NOTE_DELETE') . '">';
    print '<p style="white-space: pre-wrap;">';
    print t('BoardNotes_DIALOG_DELETE_NOTE_MSG');
    print '</p>';
    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogDeleteAllDone" title="' . t('BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES') . '">';
    print '<p style="white-space: pre-wrap;">';
    print t('BoardNotes_DIALOG_DELETE_ALL_DONE_MSG');
    print '</p>';
    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogStats" title="' . t('BoardNotes_PROJECT_NOTES_STATS') . '">';
    print '<div id="dialogStatsInside"></div>';
    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogCreateTask-P' . $project_id . '" title="' . t('BoardNotes_PROJECT_NOTE_CREATE_TASK') . '">';

    print '<div id="dialogCreateTaskParams">';

    print '<label for="listCatCreateTask-P' . $project_id . '">' . t('Category') . ' : &nbsp;</label>';
    print '<select id="listCatCreateTask-P' . $project_id . '">';
    // Only allow blank select if there's other selectable options
    if (!empty($listCategoriesById)) {
        print '<option></option>';
    }
    print $listCategoriesById;
    print '</select>';
    print '<br>';

    print '<label for="listColCreateTask-P' . $project_id . '">' . t('Column') . ' : &nbsp;</label>';
    print '<select id="listColCreateTask-P' . $project_id . '">';
    print $listColumnsById;
    print '</select>';
    print '<br>';

    print '<label for="listSwimCreateTask-P' . $project_id . '">' . t('Swimlane') . ' : &nbsp;</label>';
    print '<select id="listSwimCreateTask-P' . $project_id . '">';
    print $listSwimlanesById;
    print '</select>';
    print '<br>';

    print '<input type="checkbox" checked id="removeNote-P' . $project_id . '">';
    print '<label for="removeNote-P' . $project_id . '">&nbsp;&nbsp;' . t('BoardNotes_DIALOG_CREATE_TASK_CHECKBOX_REMOVE_NOTE') . '</label>';

    print '</div>';

    print '<div id="deadloading" class="hideMe"></div>';
    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogTransferNote-P' . $project_id . '" title="' . t('BoardNotes_PROJECT_NOTE_MOVE_TO_PROJECT') . '">';

    print '<label for="listNoteProject-P' . $project_id . '">' . t('BoardNotes_DIALOG_TRANSFER_NOTE_TARGET_PROJECT') . ' : &nbsp&nbsp;</label>';
    print '<select id="listNoteProject-P' . $project_id . '">';
    foreach ($projectsTabsById as $key => $projectTab) {
        if ($key != $project_id) {
            print '<option value="';
            print $key;
            print '">';
            print $projectTab['name'];
            print '</option>';
        }
    }
    print '</select>';
    print '<br><br>';
    print '<p style="white-space: pre-wrap;">';
    print t('BoardNotes_DIALOG_TRANSFER_NOTE_MSG');
    print '</p>';

    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogReport-P' . $project_id . '" title="' . t('BoardNotes_PROJECT_CREATE_REPORT') . '">';
    print '<div id="">';
    print '<label for="catReport-P' . $project_id . '">' . t('BoardNotes_DIALOG_REPORT_CATEGORY_FILTER') . ' :</label><br>';
    print '<select id="catReport-P' . $project_id . '">';

    print '<option></option>'; // add an empty category option
    if (!empty($listCategoriesById)) {
        print $listCategoriesById;
    }

    print '</select>';
    print '</div>';
    print '</div>';

    //---------------------------------------------

    print '</section>';
    print '</div>';
} // if (!$is_refresh)
