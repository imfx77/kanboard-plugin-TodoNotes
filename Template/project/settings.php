<?php

$todonotesSettingsHelper = $this->helper->todonotesSessionAndCookiesSettingsHelper;

$_TodoNotes_OverviewSettings_ = $todonotesSettingsHelper->GetSettings($user_id, 0);
// _TodoNotes_OverviewSettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_OverviewSettingsExportToJS_">' . json_encode($_TodoNotes_OverviewSettings_) . '</div>';

$_TodoNotes_ProjectSettings_ = $todonotesSettingsHelper->GetSettings($user_id, $project_id);
// _TodoNotes_ProjectSettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_ProjectSettingsExportToJS_">' . json_encode($_TodoNotes_ProjectSettings_) . '</div>';

$settings_hideStatusDone = $todonotesSettingsHelper->GetToggleableSettings(
    $user_id,
    $project_id,
    $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
    $todonotesSettingsHelper::SETTINGS_FILTER_DONE
);
$settings_hideStatusOpen = $todonotesSettingsHelper->GetToggleableSettings(
    $user_id,
    $project_id,
    $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
    $todonotesSettingsHelper::SETTINGS_FILTER_OPEN
);
$settings_hideStatusInProgress = $todonotesSettingsHelper->GetToggleableSettings(
    $user_id,
    $project_id,
    $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
    $todonotesSettingsHelper::SETTINGS_FILTER_IN_PROGRESS
);
$settings_showArchive = $todonotesSettingsHelper->GetToggleableSettings(
    $user_id,
    $project_id,
    $todonotesSettingsHelper::SETTINGS_GROUP_FILTER,
    $todonotesSettingsHelper::SETTINGS_FILTER_ARCHIVED
);
$settings_sortExplicit = !$todonotesSettingsHelper->GetToggleableSettings(
    $user_id,
    $project_id,
    $todonotesSettingsHelper::SETTINGS_GROUP_SORT,
    $todonotesSettingsHelper::SETTINGS_SORT_MANUAL
);
