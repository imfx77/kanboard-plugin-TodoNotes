<ul>
<?php
    $num = 0;

    // Add a default tab that denotes none project and all notes
    print '<li class="singleTab" id="singleTab';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="0"><a href="';
    print '/?controller=BoardNotesController&action=boardNotesShowAll&plugin=BoardNotes';
    print '&user_id='.$user_id.'&tab_id='.$num;
    print '">All</a>';

    print '<div class="toolbarSeparator">&nbsp;</div>';
    $stats_project_id = 0;
    require(__DIR__.'/../widgets/stats.php');
    print $statsWidget;

//     print '<div class="toolbarSeparator">&nbsp;</div>';
//     print '<button title="New custom project" id="customProjectNewP';
//     print $num;
//     print '" class="toolbarButton customProjectNew" data-user="';
//     print $user_id;
//     print '"><a><i class="fa fa-file-o" aria-hidden="true"></i></a></button>';

    print '</li>';
    $num++;

    $separatorPlacedCustom = false;
    $separatorPlacedNormal = false;
    // Loop through all projects
    foreach($projectsAccess as $o){

        // separator for custom projects
        if (!$separatorPlacedCustom && $o['is_custom']) {
            print '<hr class="hrTabs">';
            $separatorPlacedCustom = true;
        }

        // separator for normal projects
        if (!$separatorPlacedNormal && !$o['is_custom']) {
            // separator for normal projects
            print '<hr class="hrTabs">';
            $separatorPlacedNormal = true;
        }

        print '<li class="singleTab" id="singleTab';
        print $num;
        print '" data-id="';
        print $num;
        print '" data-project="';
        print $o['project_id'];
        print '"><a href="';
        print '/?controller=BoardNotesController&action=boardNotesShowAll&plugin=BoardNotes';
        print '&user_id='.$user_id.'&tab_id='.$num;
        print '">';
        print $o['project_name'];
        print '</a>';

        print '<div class="toolbarSeparator">&nbsp;</div>';
        $stats_project_id = $o['project_id'];
        require(__DIR__.'/../widgets/stats.php');
        print $statsWidget;

//         // edit buttons for custom projects ONLY
//         print '<div class="toolbarSeparator">&nbsp;</div>';
//         if ($o['is_custom']) {
//             print '<button title="Delete custom project" id="customProjectDeleteP';
//             print $o['project_id'];
//             print '" class="toolbarButton customProjectDelete" data-project="';
//             print $o['project_id'];
//             print '" data-user="';
//             print $user_id;
//             print '"><a><i class="fa fa-trash-o" aria-hidden="true"></i></a></button>';
//
//             print '<button title="Rename custom project" id="customProjectRenameP';
//             print $o['project_id'];
//             print '" class="toolbarButton customProjectRename" data-project="';
//             print $o['project_id'];
//             print '" data-user="';
//             print $user_id;
//             print '"><a><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></button>';
//         }

        print'</li>';
        $num++;
    }
?>
</ul>