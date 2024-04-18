<ul>
<?php

$num = 0;

// Add a default tab that denotes none project and all notes
//----------------------------------------
print '<li class="singleTab" id="singleTab' .  $num . '"';
print ' data-id="' . $num . '"';
print ' data-project="0"';
print '>';
print $this->url->link(
    t('BoardNotes_DASHBOARD_ALL_TAB'),
    'BoardNotesController',
    'boardNotesShowAll',
    array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $num)
);
print '<div class="toolbarSeparator">&nbsp;</div>';
print $this->render('BoardNotes:widgets/stats', array(
     'stats_project_id' => 0,
));

// buttons for ALL tab
//----------------------------------------
print '<div class="toolbarSeparator">&nbsp;</div>';
print '<button title="New custom list" id="customListNew-P' . $num . '"';
print ' class="toolbarButton customListNew"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-fw fa-wpforms" aria-hidden="true"></i></a>';
print '</button>';

print '<div class="toolbarSeparator">&nbsp;</div>';
print '<button title="Reindex Notes and Lists" id="reindexNotesAndLists-P' . $num . '"';
print ' class="toolbarButton reindexNotesAndLists"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-fw fa-recycle" aria-hidden="true"></i></a>';
print '</button>';
//----------------------------------------

print '</li>';
$num++;

$separatorPlacedCustom = false;
$separatorPlacedNormal = false;

// Loop through all projects
//----------------------------------------
foreach ($projectsAccess as $o) {
    // separator for custom lists
    if (!$separatorPlacedCustom && $o['is_custom']) {
        print '<hr class="hrTabs">';
        $separatorPlacedCustom = true;
    }

    // separator for regular projects
    if (!$separatorPlacedNormal && !$o['is_custom']) {
        print '<hr class="hrTabs">';
        $separatorPlacedNormal = true;
    }

    //----------------------------------------
    print '<li class="singleTab" id="singleTab';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $o['project_id'];
    print '">';
    print $this->url->link(
        $o['project_name'],
        'BoardNotesController',
        'boardNotesShowAll',
        array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $num)
    );

    print '<div class="toolbarSeparator">&nbsp;</div>';
    print $this->render('BoardNotes:widgets/stats', array(
         'stats_project_id' => $o['project_id'],
    ));

    print '<div class="toolbarSeparator">&nbsp;</div>';
    if ($o['is_custom']) {
        // edit buttons for custom lists ONLY
        //----------------------------------------
        print '<button title="Delete custom list" id="customListDelete-P' . $o['project_id'] . '"';
        print ' class="toolbarButton customListDelete"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<a><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
        print '</button>';

        print '<button title="Rename custom list" id="customListRenameP' . $o['project_id'] . '"';
        print ' class="toolbarButton customListRename"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print '<a><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
        print '</button>';
        //----------------------------------------
    } else {
        // shortcut buttons for regular projects ONLY
        //----------------------------------------
        print '<button id="gotoProjectTasks-P' . $o['project_id'] . '"';
        print ' class="toolbarButton gotoProjectTasks"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('list', '', 'TaskListController', 'show', array('project_id' => $o['project_id']), false, 'view-listing', t('List') . ' ⇗');
        print '</button>';

        print '<button id="gotoProjectBoard-P' . $o['project_id'] . '"';
        print ' class="toolbarButton gotoProjectBoard"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('th', '', 'BoardViewController', 'show', array('project_id' => $o['project_id']), false, 'view-board', t('Board') . ' ⇗');
        print '</button>';
        //----------------------------------------
    }

    print'</li>';
    $num++;
}

?>
</ul>
