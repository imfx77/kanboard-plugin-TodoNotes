<?php

/**
 * Class SettingsSessionHelper
 * @package Kanboard\Plugin\TodoNotes\Helper
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Helper;

use Kanboard\Core\Base;

class SettingsSessionHelper extends Base
{
    public function GetToggleableSessionSettings($settings_name): bool
    {
        $settings = (isset($_SESSION['_TodoNotes_Settings_'])) ? $_SESSION['_TodoNotes_Settings_'] : array();
        // toggle settings are expected to be boolean i.e. to only have values of 'true' of 'false'
        return (array_key_exists($settings_name, $settings) && is_bool($settings[$settings_name]))
            ? $settings[$settings_name]
            : false;
    }

    public function SetToggleableSessionSettings($settings_name, $settings_value)
    {
        $settings = (isset($_SESSION['_TodoNotes_Settings_'])) ? $_SESSION['_TodoNotes_Settings_'] : array();
        $settings[$settings_name] = $settings_value;
        $_SESSION['_TodoNotes_Settings_'] = $settings;
        print_r($settings);
    }

    public function ToggleSessionSettings($settings_name)
    {
        $settings_value = $this->helper->todonotesSettingsSessionHelper->GetToggleableSessionSettings($settings_name);
        $this->helper->todonotesSettingsSessionHelper->SetToggleableSessionSettings($settings_name, !$settings_value);
    }
}
