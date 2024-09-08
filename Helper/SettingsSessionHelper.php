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
    public function GetToggleableSessionSettings($settings_group_name, $settings_name): bool
    {
        $settings = (isset($_SESSION['_TodoNotes_Settings_'])) ? $_SESSION['_TodoNotes_Settings_'] : array();

        // settings groups are expected to be arrays
        $settings_group = (array_key_exists($settings_group_name, $settings) && is_array($settings[$settings_group_name]))
            ? $settings[$settings_group_name]
            : array();

        // toggle settings are expected to be boolean i.e. to only have values of 'true' of 'false'
        $settings_value = (array_key_exists($settings_name, $settings_group) && is_bool($settings_group[$settings_name]))
            ? $settings_group[$settings_name]
            : false;

        return $settings_value;
    }

    public function SetToggleableSessionSettings($settings_group_name, $settings_name, $settings_value)
    {
        $settings = (isset($_SESSION['_TodoNotes_Settings_'])) ? $_SESSION['_TodoNotes_Settings_'] : array();

        $settings_group = (array_key_exists($settings_group_name, $settings) && is_array($settings[$settings_group_name]))
            ? $settings[$settings_group_name]
            : array();

        $settings_group[$settings_name] = $settings_value;
        $settings[$settings_group_name] = $settings_group;
        $_SESSION['_TodoNotes_Settings_'] = $settings;

        //print_r($settings);
    }

    public function ToggleSessionSettings($settings_group_name, $settings_name)
    {
        $settings_value = $this->GetToggleableSessionSettings($settings_group_name, $settings_name);
        $this->SetToggleableSessionSettings($settings_group_name, $settings_name, !$settings_value);
    }
}
