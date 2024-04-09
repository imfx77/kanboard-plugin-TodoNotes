<style>
table {
    width: initial !important;
}
</style>

<?php

if ($task_id > 0) {
    print '<strong>' . t('BoardNotes_POST_DIALOG_SUCCESS_TITLE') . '</strong>';
    print '<br>';
    print t('BoardNotes_POST_DIALOG_SUCCESS_TEXT') . ' : ';
    print $this->url->icon(
        'external-link',
        '<strong>#' . $task_id . '</strong>',
        'TaskViewController',
        'show',
        array('task_id' => $task_id),
        false,
        '',
        t('Opens in a new window') . ' ⇗',
        true
    );
} else {
    print '<strong>' . t('BoardNotes_POST_DIALOG_FAILURE_TITLE') . '</strong>';
    print '<br>';
    print t('BoardNotes_POST_DIALOG_FAILURE_TEXT');
}

    print '<br>';
    print '<br>';
    print '<table>';

    print '<tr><td align="right"><strong>[project] : </strong></td><td>' . $project_name . '</td></tr>';
if ($task_id > 0) {
    print '<tr><td align="right"><strong>[creator] : </strong></td><td>' . $user_name . '</td></tr>';
    print '<tr><td align="right"><strong>[owner] : </strong></td><td>' . $user_name . '</td></tr>';
} else {
    print '<tr><td align="right"><strong>[user] : </strong></td><td>' . $user_name . '</td></tr>';
}
    print '<tr><td align="right"><strong>[title] : </strong></td><td>' . $task_title . '</td></tr>';
    print '<tr><td align="right"><strong>[description] : </strong></td><td>' . $task_description . '</td></tr>';
    print '<tr><td align="right"><strong>[category] : </strong></td><td>' . $category . '</td></tr>';
    print '<tr><td align="right"><strong>[column] : </strong></td><td>' . $column . '</td></tr>';
    print '<tr><td align="right"><strong>[swimlane] : </strong></td><td>' . $swimlane . '</td></tr>';

    print '</table>';
?>
