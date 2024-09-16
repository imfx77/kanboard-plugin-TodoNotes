<?php

/**
 * Class SessionAndCookiesSettingsHelper
 * @package Kanboard\Plugin\TodoNotes\Helper
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Helper;

use Kanboard\Core\Base;

class SessionAndCookiesSettingsHelper extends Base
{
    private const SETTINGS_KEY_NAME = '_KB_plugin_TodoNotes_user_Settings_';

    public function GetSettings($user_id, $project_id)
    {
        // obtain from cookie
        $cookie = (isset($_COOKIE[self::SETTINGS_KEY_NAME])) ? $_COOKIE[self::SETTINGS_KEY_NAME] : '';
        $cookie_value = json_decode($cookie, true);
        $cookie_settings_key = 'user=' . $user_id . ';project=' . $project_id . ';';

        $settings = (is_array($cookie_value) && array_key_exists($cookie_settings_key, $cookie_value))
            ? $cookie_value[$cookie_settings_key]
            : null;

        // else obtain from session
        if (!isset($settings)) {
            $settings = (isset($_SESSION[self::SETTINGS_KEY_NAME])) ? $_SESSION[self::SETTINGS_KEY_NAME] : array();
        }

        return $settings;
    }

    private function SetSettings($user_id, $project_id, $settings)
    {
        //print_r($settings);

        // store to session
        $_SESSION[self::SETTINGS_KEY_NAME] = $settings;

        // store to cookie
        $cookie = (isset($_COOKIE[self::SETTINGS_KEY_NAME])) ? $_COOKIE[self::SETTINGS_KEY_NAME] : '';
        $cookie_value = json_decode($cookie, true);
        $cookie_settings_key = 'user=' . $user_id . ';project=' . $project_id . ';';
        $cookie_value[$cookie_settings_key] = $settings;

        $cookie_options = array (
            'expires' => time() + (60 * 60 * 24 * 30), // 30 days
            //'expires' => time() - 3600, // reset cookie
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        );
        setcookie(self::SETTINGS_KEY_NAME, json_encode($cookie_value), $cookie_options);
    }

    public function GetToggleableSettings($user_id, $project_id, $settings_group_name, $settings_name): bool
    {
        $settings = $this->GetSettings($user_id, $project_id);

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

    private function SetToggleableSettings($user_id, $project_id, $settings_group_name, $settings_name, $settings_value)
    {
        $settings = $this->GetSettings($user_id, $project_id);

        $settings_group = (array_key_exists($settings_group_name, $settings) && is_array($settings[$settings_group_name]))
            ? $settings[$settings_group_name]
            : array();

        $settings_group[$settings_name] = $settings_value;
        $settings[$settings_group_name] = $settings_group;

        $this->SetSettings($user_id, $project_id, $settings);
    }

    public function ToggleSettings($user_id, $project_id, $settings_group_name, $settings_name)
    {
        $settings_value = $this->GetToggleableSettings($user_id, $project_id, $settings_group_name, $settings_name);
        $this->SetToggleableSettings($user_id, $project_id, $settings_group_name, $settings_name, !$settings_value);
    }
}
