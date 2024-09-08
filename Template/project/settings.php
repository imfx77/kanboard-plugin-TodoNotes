<?php

$_TodoNotes_Settings_ = (isset($_SESSION['_TodoNotes_Settings_'])) ? $_SESSION['_TodoNotes_Settings_'] : array();
// _TodoNotes_SettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_SettingsExportToJS_">' . json_encode($_TodoNotes_Settings_) . '</div>';

$settings_showArchive = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings('archive', 'showArchive');
$settings_sortByStatus = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings('sort', 'sortByStatus');
$settings_showStatusDone = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings('filter', 'showStatusDone');
