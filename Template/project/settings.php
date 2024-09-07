<?php

$_TodoNotes_Settings_ = (isset($_SESSION['_TodoNotes_Settings_'])) ? $_SESSION['_TodoNotes_Settings_'] : array();
// _TodoNotes_SettingsExportToJS_ (hidden reference for session settings)
print '<div class="hideMe" id="_TodoNotes_SettingsExportToJS_">' . json_encode($_TodoNotes_Settings_) . '</div>';

$settings_ArchiveView = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings('todonotesSettings_ArchiveView');
$settings_SortByStatus = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings('todonotesSettings_SortByStatus');
$settings_ShowAllDone = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings('todonotesSettings_ShowAllDone');
