<?php

if (!$is_refresh) { // load CSS and JS and translations only once per project !!!
    print $this->asset->css('plugins/BoardNotes/Assets/css/style.css');
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
            print '<div id="category-';
            print $cat['name'];
            print '" data-color="';
            print $cat['color_id'];
            print '" class="hideMe">';
            print '</div>';
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
    print '<section class="mainholder" id="mainholderP';
    print $project_id;
    print '">';

    print '<div align="left" id="result';
    print $project_id;
    print '">';
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

// session_vars (hidden reference for options)
print '<div id="session_vars';
print '" data-optionShowCategoryColors="';
print $optionShowCategoryColors ? 'true' : 'false';
print '" data-optionSortByStatus="';
print $optionSortByStatus ? 'true' : 'false';
print '" class="hideMe">';
print '</div>';

//----------------------------------------

print '<ul class="sortableRef" id="sortableRef-P' . $project_id . '"';
print ' data-project="' . $project_id . '"';
print '>';

//----------------------------------------

//////////////////////////////////////////
////    NEW NOTE / OVERVIEW MODE TITLE
//////////////////////////////////////////

print '<li id="item-0" class="ui-state-default liNewNote" data-id="0" data-project="' . $project_id . '">';

// here goes the Button Toolbar
print '<div class="containerNoWrap containerFloatRight disableEventsPropagation">';

// exclude when in Overview Mode
if (!$readonlyNotes) {
    // Settings delete all done
    print '<button id="settingsDeleteAllDone" class="toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-trash-o" aria-hidden="true"></i>';
    print'</button>';

    // add some space between button groups
    print '<div class="toolbarSeparator">&nbsp;</div>';

    // Settings stats
    print '<button id="settingsStats" class="toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTES_STATS') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-bar-chart" aria-hidden="true"></i>';
    print '</button>';

    // Open report
    print '<button id="settingsReport" class="toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_CREATE_REPORT') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
    print '</button>';

    // add some space between button groups
    print '<div class="toolbarSeparator">&nbsp;</div>';
} // end exclude

// Collapse all
print '<button id="settingsCollapseAll" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_COLLAPSE_ALL_NOTES') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-minus-square" aria-hidden="true"></i>';
print '</button>';

// Expand all
print '<button id="settingsExpandAll" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_EXPAND_ALL_NOTES') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-plus-square" aria-hidden="true"></i>';
print '</button>';

// add some space between button groups
print '<div class="toolbarSeparator">&nbsp;</div>';

// Toggle sort by status
print '<button id="settingsSortByStatus" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_TOGGLE_SORT_BY_STATUS') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-sort" aria-hidden="true"></i>';
print '</button>';

// Toggle category colors
print '<button id="settingsCategoryColors" class="toolbarButton"';
print ' title="' . t('BoardNotes_PROJECT_TOGGLE_COLORIZE_BY_CATEGORY') . '"';
print ' data-id="0"';
print ' data-project="' . $project_id . '"';
print ' data-user="' .  $user_id . '"';
print '>';
print '<i class="fa fa-paint-brush" aria-hidden="true"></i>';
print '</button>';

print '</div>';

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
print '</div>';

// here goes the space Placeholder
print '<div class="hideMe containerFloatClear" id="placeholderNewNote"></div>';

// exclude when in Overview Mode
if (!$readonlyNotes) {
    // Newline after heading and top settings
    print '<br>';

    // here goes the Input line
    print '<div class="containerNoWrap containerFloatClear">';

    // Show details button
    print '<button id="showDetailsNewNote" class="showDetailsNewNote toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TOGGLE_DETAILS') . '"';
    print ' data-id="0"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-angle-double-down" aria-hidden="true"></i>';
    print '</button>';

    // Save button
    print '<button id="saveNewNote" class="hideMe saveNewNote toolbarButton"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_SAVE') . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
    print '</button>';

    // Input line
    print '<input class="inputNewNote disableEventsPropagation" id="inputNewNote"';
    print ' type="text" placeholder="' . t('BoardNotes_PROJECT_NEW_NOTE_TITLE_PLACEHOLDER') . '"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_TITLE_SAVE_HINT') . '"';
    print ' data-project="' . $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '>';

    print '</div>';

    // here goes the Detailed View
    print '<div id="detailsNewNote" class="hideMe details containerFloatClear noteDetails ui-corner-all"';
    print ' data-id="0"';
    print '>';

    //-----------------------

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
    print '</div>';
    print '<br>';

    //-----------------------

    print '<textarea id="textareaNewNote" class="textareaNewNote disableDblClickPropagation"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_DESCR_SAVE_HINT') . '"';
    print ' data-project="' .  $project_id . '"';
    print ' data-user="' . $user_id . '"';
    print '"></textarea>';

    //-----------------------

    print '</div>'; // Detailed View
} // end exclude

print '</li>';

//----------------------------------------

$num = 1;
$last_project_id = $project_id;
$last_num_notes = 0;
foreach ($data as $u) {
    if (!empty($project_id) && $u['project_id'] != $project_id) {
        continue;
    }

    // show project name links in Overview Mode
    if ($readonlyNotes && $last_project_id != $u['project_id']) {
        print '</ul>';

        // hidden reference for number of notes by project
        print '<div class="hideMe" id="nrNotes-P' . $last_project_id . '"';
        print ' data-num="' . $last_num_notes . '"';
        print '></div>';

        // reset project and number of notes
        $last_project_id = $u['project_id'];
        $last_num_notes = 0;

        // project name link
        print '<h2>';
        print $this->url->link(
            $projectsTabsById[ $last_project_id ]['name'],
            'BoardNotesController',
            'boardNotesShowAll',
            array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $projectsTabsById[ $last_project_id ]['tab_id']),
        );
        print '</h2>';

        // sortable list by project
        print '<ul class="sortableRef" id="sortableRef-P' . $last_project_id . '"';
        print ' data-project="' . $last_project_id . '"';
        print '>';
    }

    //////////////////////////////////////////
    ////    PROJECT NOTE
    //////////////////////////////////////////

    print '<li id="item' . '-' . $u['id'] . '"';
    print ' class="ui-state-default liNote';
    if (!empty($u['category']) && array_key_exists($u['category'], $mapCategoryColorByName)) {
        $category_color = $mapCategoryColorByName[ $u['category'] ];
        if (!empty($category_color)) {
            print ' color-' . $category_color; // append category color class
        }
    }
    print '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print '>';

    // explicit reorder handle for mobile
    print '<div class="hideMe sortableHandle containerNoWrap containerFloatRight"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>';

    // here goes the Button Toolbar
    print '<div class="containerNoWrap containerFloatRight disableEventsPropagation">';

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

    // hide all the utility buttons when viewing notes as readonly
    // just allow for check/uncheck note
    if (!$readonlyNotes) {
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

        // add some space between button groups
        print '<div class="toolbarSeparator">&nbsp;</div>';

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

        // notes from custom lists obviously CANNOT create tasks from notes
        if (!$project['is_custom']) {
            // Add note to tasks table (in detailed view)
            print '<button id="noteToTask-P' . $u['project_id'] . '-' . $num . '"';
            print ' class="hideMe toolbarButton noteToTask"';
            print ' title="' . t('BoardNotes_PROJECT_NOTE_CREATE_TASK') . '"';
            print ' data-id="' . $num . '"';
            print ' data-project="' . $u['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-share-square-o" aria-hidden="true"></i>';
            print '</button>';
        }
    }

    // Category label (in simple view)
    print '<label id="noteCatLabel-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="catLabel catLabelClickable';
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

    // add some space between button groups
    print '<div class="toolbarSeparator">&nbsp;</div>';

    // Refresh order button (shown on changed status in SortByStatus mode only)
    print '<button id="noteRefreshOrder-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="hideMe toolbarButton toolbarButtonToggled noteRefreshOrder"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_REFRESH_ORDER') . '"';
    print ' data-id="' . $num  . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i class="fa fa-refresh fa-spin fa-spin-reverse" aria-hidden="true" style="font-size: 1.5em"></i>';
    print '</button>';

    print '</div>';

    // here goes the Title row with Checkbox
    print '<div class="containerNoWrap containerFloatLeft disableEventsPropagation">';

    // Checkbox for done note
    print '<button class="checkDone" id="checkDone-P' . $u['project_id'] . '-' . $num . '"';
    print ' title="' . t('BoardNotes_PROJECT_NOTE_SWITCH_STATUS') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';
    print '<i id="noteDoneCheckmark-P' . $u['project_id'] . '-' . $num . '"';
    print ' data-id="' . $u['is_active'] . '"';
    if ($u['is_active'] == "2") {
        print ' class="fa fa-spinner fa-pulse" aria-hidden="true"';
    }
    if ($u['is_active'] == "1") {
        print ' class="fa fa-circle-thin" aria-hidden="true"';
    }
    if ($u['is_active'] == "0") {
        print ' class="fa fa-check" aria-hidden="true"';
    }
    print '></i>';
    print '</button>';

    // Note title input - typing. Changes after submit to label below.
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

    // Note title label - visual. Changes on click to input
    print '<label id="noteTitleLabel-P' . $u['project_id'] . '-' . $num . '"';
    if ($u['is_active'] == "0") {
        print ' class="noteTitleLabel noteTitle noteDoneText"';
    } else {
        print ' class="noteTitleLabel noteTitle"';
    }
    print ' title="' . t('BoardNotes_PROJECT_NOTE_EDIT_HINT') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    if ($readonlyNotes) {
        print ' data-disabled="true"';
    }
    print '>';
    print $u['title'];
    print '</label>';

    print '</div>'; // Title row with Checkbox

    // here goes the space Placeholder
    print '<div class="hideMe containerFloatClear" id="notePlaceholder-P' . $u['project_id'] . '-' . $num . '"></div>';

    // here goes the Detailed View
    print '<div id="noteDetails-P' . $u['project_id'] . '-' . $num . '"';
    print ' class="hideMe details containerFloatClear noteDetails ui-corner-all"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    print '>';

    //-----------------------

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
    print '</div>';
    print '<br>';

    //-----------------------

    print '<textarea id="noteTextareaDescription-P' . $u['project_id'] . '-' . $num . '"';
    if ($u['is_active'] == "0") {
        print ' class="textareaDescription noteDoneTextarea disableEventsPropagation"';
    } else {
        print ' class="textareaDescription disableEventsPropagation"';
    }
    print' title="' . t('BoardNotes_PROJECT_NOTE_DESCR_SAVE_HINT') . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print ' data-user="' . $user_id . '"';
    if ($readonlyNotes) {
        print ' disabled';
    }
    print '>';
    print $u['description'];
    print '</textarea>';

    //-----------------------

    print '</div>'; // Detailed View

    // noteId (hidden reference for each note)
    print '<div class="hideMe" id="noteId-P' .  $u['project_id'] . '-' . $num . '"';
    print ' data-note="' . $u['id'] . '"';
    print ' ></div>';

    print '</li>';

    $last_num_notes++;

    // Id
    $num++;
}

print '</ul>';

// hidden reference for number of notes by project
print '<div class="hideMe" id="nrNotes-P' . $last_project_id . '"';
print ' data-num="' . $last_num_notes . '"';
print '></div>';

//----------------------------------------

// hidden reference for total number of notes
print '<div class="hideMe" id="nrNotes"';
print ' data-id="' . ($num - 1) . '"';
print '></div>';

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
    print '<div class="hideMe" id="dialogDeleteAllDone" title="' . t('BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES') . '">';
    print '<p style="white-space: pre-wrap;">';
    print t('BoardNotes_DELETEALLDONE_DIALOG_MSG');
    print '</p>';
    print '</div>';

    print '<div class="hideMe" id="dialogStats" title="' . t('BoardNotes_PROJECT_NOTES_STATS') . '">';
    print '<div id="dialogStatsInside"></div>';
    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogToTask-P' . $project_id . '" title="' . t('BoardNotes_PROJECT_NOTE_CREATE_TASK') . '">';

    print '<div id="dialogToTaskParams">';

    print '<label for="listCatToTask-P' . $project_id . '">' . t('Category') . ' : &nbsp;</label>';
    print '<select id="listCatToTask-P' . $project_id . '">';
    // Only allow blank select if there's other selectable options
    if (!empty($listCategoriesById)) {
        print '<option></option>';
    }
    print $listCategoriesById;
    print '</select>';
    print '<br>';

    print '<label for="listColToTask-P' . $project_id . '">' . t('Column') . ' : &nbsp;</label>';
    print '<select id="listColToTask-P' . $project_id . '">';
    print $listColumnsById;
    print '</select>';
    print '<br>';

    print '<label for="listSwimToTask-P' . $project_id . '">' . t('Swimlane') . ' : &nbsp;</label>';
    print '<select id="listSwimToTask-P' . $project_id . '">';
    print $listSwimlanesById;
    print '</select>';
    print '<br>';

    print '<input type="checkbox" checked id="removeNote-P' . $project_id . '">';
    print '<label for="removeNote-P' . $project_id . '">&nbsp;&nbsp;' . t('BoardNotes_CREATETASK_CHECKBOX_REMOVE_TASK') . '</label>';

    print '</div>';

    print '<div id="deadloading" class="hideMe"></div>';
    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogTransfer-P' . $project_id . '" title="' . t('BoardNotes_PROJECT_NOTE_MOVE_TO_PROJECT') . '">';

    print '<label for="listNoteProject-P' . $project_id . '">' . t('BoardNotes_TRANSFERNOTE_DIALOG_TARGET_PROJECT') . ' : &nbsp&nbsp;</label>';
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
    print t('BoardNotes_TRANSFERNOTE_DIALOG_MSG');
    print '</p>';

    print '</div>';

    //---------------------------------------------

    print '<div class="hideMe" id="dialogReport-P' . $project_id . '" title="' . t('BoardNotes_PROJECT_CREATE_REPORT') . '">';
    print '<div id="">';
    print '<label for="catReport-P' . $project_id . '">' . t('BoardNotes_REPOSR_DIALOG_CATEGORY_FILTER') . ' :</label><br>';
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
