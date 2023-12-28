<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/load_report.js') ?>

<table class="tableReport">
<thead class="theadReport">
<tr>
<th class="thReport thReportNr">#</th>
<th class="thReport">Info</th>
<th class="thReport thReportResponsible">Responsible</th>
<th class="thReport thReportStatus">Status</th>
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
    print '"><i class="fa fa-minus-square-o" style="color:#CCC" aria-hidden="true" title="Hide"></i></button>';
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
        $description = str_ireplace("<br >", "\r\n", $u['description']);
        print $description;
        print '</span>';
    }

    // Project_id (hidden, for reference)
    print '<div id="project_id';
    print $num;
    print '" data-id="';
    print $u['project_id'];
    print '" class="hideMe">';
    print '</div>';

    // Note_id (hidden, for reference)
    print '<div id="note_idP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $u['id'];
    print '" class="hideMe">';
    print '</div>';

    print '</td>';

    print '<td class="tdReport tdReportResponsible reportTitle">';
    print '</td>';
    print '<td class="tdReport tdReportStatus reportTitle">';
        if($u['is_active'] == "2"){
            print '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';
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
