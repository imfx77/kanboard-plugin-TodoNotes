<?php

require_once('settings.php');

?>

<table class="tableReport">
<thead class="theadReport">
<tr>
<th class="thReport thReportNr">#</th>
<th class="thReport"><?= t('Information') ?></th>
<th class="thReport thReportStatus"><?= t('Status') ?></th>
</tr>
</thead>
<tbody>

<?php

$num = "1";

foreach ($data as $u) {
    $isNoteActive = intval($u['is_active']);
    if ($isNoteActive == 0 && $settings_hideStatusDone) {
        continue;
    }
    if ($isNoteActive == 1 && $settings_hideStatusOpen) {
        continue;
    }
    if ($isNoteActive == 2 && $settings_hideStatusInProgress) {
        continue;
    }

    // ------------------------------------------
    // BEGIN NOTE
    // ------------------------------------------

    print '<tr class="trReport" id="trReportNr' . $num . '">';

    print '<td class="tdReport tdReportNr">';
    print '<div class="reportBkgr"></div>';

    // Hide button
    print '<button id="reportHide" class="reportHide"';
    print ' data-id="' . $num . '"';
    print '>';
    print '<i class="fa fa-minus-square-o" style="color:#CCCCCC" aria-hidden="true"';
    print ' title="' . t('TodoNotes__REPORT_HIDE_ROW') . '">';
    print '</i>';
    print '</button>';
    // Report #
    print '<span class="fa-stack fa-lg">';
    print '<i class="fa fa-circle-thin fa-stack-2x"></i>';
    print '<i class="fa fa-inverse fa-stack-1x">' . $num . '</i>';
    print '</span>';
    print '</td>';

    // Report Info
    print '<td class="tdReport tdReportInfo">';
    print '<div class="reportBkgr"></div>';

    // Note title label
    print '<label id="reportTitleLabel-P' . $u['project_id'] . '-' . $num . '"';
    if ($isNoteActive == 0) {
        print ' class="reportTitleLabel reportTitle noteDoneText">';
    } else {
        print ' class="reportTitleLabel reportTitle">';
    }
    print $u['title'];
    print '</label>';

    // Category label
    print '<label class="catLabel containerFloatRight"';
    print ' id="noteCatLabel-P' . $u['project_id'] . '-' . $num . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print '>';
    print $u['category'];
    print '</label>';

    // Note details
    if (!empty($u['description'])) {
        print '<div id="noteDetails-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="details reportDetails ui-corner-all">';

        print '<span id="noteMarkdownDetails-P' . $u['project_id'] . '-' . $num . '"';
        if ($isNoteActive == 0) {
            print ' class="markdown markdownReportDetails reportTitle noteDoneMarkdown"';
        } else {
            print ' class="markdown markdownReportDetails reportTitle"';
        }
        print '>';
        print $this->helper->text->markdown($u['description']);
        print '</span>';

        print '</div>';
    }

    print '</td>'; // report info

    print '<td class="tdReport tdReportStatus reportTitle">';
    print '<div class="reportBkgr"></div>';

    if ($isNoteActive == 2) {
        print '<i class="statusInProgress" aria-hidden="true"></i>';
    }
    //if ($isNoteActive == 1) {
    //    print '<i class="statusOpen" aria-hidden="true"></i>';
    //}
    if ($isNoteActive == 0) {
        print '<i class="statusDone" aria-hidden="true"></i>';
    }

    print '</td>';

    print '</tr>';

    // #
    $num++;

    // ------------------------------------------
    // END NOTE
    // ------------------------------------------
}

?>

</tbody>
</table>

<?php

//----------------------------------------

// hidden reference for project_id and user_id of the currently active page
print '<div class="hideMe" id="refProjectId"';
print ' data-project="' . $project_id . '"';
print ' data-user="' . $user_id . '"';
print ' data-timestamp="' . time() . '"';
print '></div>';

print '<span id="refreshIcon" class="refreshIcon hideMe">';
print '&nbsp;<i class="fa fa-refresh fa-spin" title="' . t('TodoNotes__PROJECT_NOTE_BUSY_ICON_HINT') . '"></i></span>';
