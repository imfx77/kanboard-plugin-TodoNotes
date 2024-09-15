<?php

$_TodoNotes_OverviewSettings_ = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetSettings($user_id, 0);
// _TodoNotes_OverviewSettingsExportToJS__ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_OverviewSettingsExportToJS__">' . json_encode($_TodoNotes_OverviewSettings_) . '</div>';

$_TodoNotes_ProjectSettings_ = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetSettings($user_id, $project_id);
// _TodoNotes_ProjectSettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_ProjectSettingsExportToJS_">' . json_encode($_TodoNotes_ProjectSettings_) . '</div>';

$settings_showArchive = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 'archive', 'showArchive');
$settings_sortByStatus = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 'sort', 'sortByStatus');
$settings_showStatusDone = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 'filter', 'showStatusDone');
