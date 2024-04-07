<?php

print $this->asset->css('plugins/BoardNotes/Assets/css/style.css');
print $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js');
print $this->asset->js('plugins/BoardNotes/Assets/js/load_report.js');

    // evaluate optionShowCategoryColors option from session
    if (!array_key_exists('boardnotesShowCategoryColors', $_SESSION)) $_SESSION['boardnotesShowCategoryColors'] = false;
    $optionShowCategoryColors = $_SESSION['boardnotesShowCategoryColors'];

    // session_vars (hidden reference for options)
    print '<div id="session_vars';
    print '" data-optionShowCategoryColors="';
    print $optionShowCategoryColors ? 'true' : 'false';
    print '" class="hideMe">';
    print '</div>';
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

foreach($data as $u){
    print '<tr class="trReport" id="trReportNr';
    print $num;
    print '">';

    print '<td class="tdReport tdReportNr">';
     // Hide button
    print '<button id="singleReportHide" class="singleReportHide" data-id="';
    print $num;
    print '"><i class="fa fa-minus-square-o" style="color:#CCC" aria-hidden="true"';
    print ' title="' . t('BoardNotes_REPORT_HIDE_ROW') . '"></i></button>';
    // Report #
    print '<span class="fa-stack fa-lg">';
    print '<i class="fa fa-circle-thin fa-stack-2x"></i>';
    print '<i class="fa fa-inverse fa-stack-1x">';
    print $num;
    print '</i>';
    print '</span>';
    print '</td>';

    // Report Info
    print '<td class="tdReport tdReportInfo">';

    // Category label
    print '<label class="catLabel" id="noteCatLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '">';
    print $u['category'];
    print '</label>';

    // Note title label
    print '<label id="reportTitleLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" name="reportTitleLabel';
    print $num;
    if($u['is_active'] == "0"){
        print '" class="reportTitleLabel reportTitle noteDoneDesignText" value="">';
    } else {
        print '" class="reportTitleLabel reportTitle" value="">';
    }
    print $u['title'];
    print '</label>';

    // Detailed view
    if(!empty($u['description'])) {
        print '<div id="noteDescriptionP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" data-id="';
        print $num;
        print '" data-project="';
        print $u['project_id'];
        print '" class="details reportDescriptionClass ui-corner-all">';

        print '<span id="noteTextareaDescriptionP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" data-id="';
        print $num;
        print '" data-project="';
        print $u['project_id'];
        if($u['is_active'] == "0"){
            print '" class="textareaReportDescription reportTitle noteDoneDesignTextarea">';
        } else {
            print '" class="textareaReportDescription reportTitle">';
        }
        print $u['description'];
        print '</span>';
    }

    print '</td>'; // report info

    print '<td class="tdReport tdReportStatus reportTitle">';
        if($u['is_active'] == "2"){
            print '<i class="fa fa-spinner fa-pulse" aria-hidden="true"></i>';
        }
        //if($u['is_active'] == "1"){
        //    print '<i class="fa fa-circle-thin" aria-hidden="true"></i>';
        //}
        if($u['is_active'] == "0"){
            print '<i class="fa fa-check" aria-hidden="true"></i>';
        }
    print '</td>';

    print '</tr>';

    // #
    $num++;
}
?>
</tbody>
</table>
