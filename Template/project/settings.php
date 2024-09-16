<?php

$_TodoNotes_OverviewSettings_ = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetSettings($user_id, 0);
// _TodoNotes_OverviewSettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_OverviewSettingsExportToJS_">' . json_encode($_TodoNotes_OverviewSettings_) . '</div>';

$_TodoNotes_ProjectSettings_ = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetSettings($user_id, $project_id);
// _TodoNotes_ProjectSettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_ProjectSettingsExportToJS_">' . json_encode($_TodoNotes_ProjectSettings_) . '</div>';

$settings_showStatusDone = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 1 /*filter*/, 0 /*Done*/);
$settings_showStatusOpen = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 1 /*filter*/, 1 /*Open*/);
$settings_showStatusInProgress = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 1 /*filter*/, 2 /*InProgress*/);
$settings_showArchive = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 1 /*filter*/, 3 /*Archived*/);
$settings_sortByStatus = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 2 /*sort*/, 1 /*Status*/);