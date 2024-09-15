<?php

$_TodoNotes_Settings_ = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetSettings($user_id, $project_id);
// _TodoNotes_SettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_SettingsExportToJS_">' . json_encode($_TodoNotes_Settings_) . '</div>';

$settings_showArchive = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 'archive', 'showArchive');
$settings_sortByStatus = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 'sort', 'sortByStatus');
$settings_showStatusDone = $this->helper->todonotesSessionAndCookiesSettingsHelper->GetToggleableSettings($user_id, $project_id, 'filter', 'showStatusDone');
