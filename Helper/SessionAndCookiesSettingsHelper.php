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

    private const DEFAULT_SETTINGS = array(
        2 /*sort*/ => [0 /*Manual*/],
        3 /*view*/ => [0 /*CategoryColors*/],
    );

    private const URL_ENCODE_MAP = array(
        '{' => 'a',
        '}' => 'b',
        '[' => 'c',
        ']' => 'd',
        '|' => 'x',
        ':' => 'y',
        ',' => 'z',
        '"' => 'q',
    );
    private const URL_DECODE_MAP = array(
        'a' => '{',
        'b' => '}',
        'c' => '[',
        'd' => ']',
        'x' => '|',
        'y' => ':',
        'z' => ',',
        'q' => '"',
    );

    public function GetSettings($user_id, $project_id)
    {
        $settings_key = $user_id . '|' . $project_id;

        // obtain from cookie
        $cookie = (isset($_COOKIE[self::SETTINGS_KEY_NAME])) ? $_COOKIE[self::SETTINGS_KEY_NAME] : '';
        $cookie_value = json_decode(strtr($cookie, self::URL_DECODE_MAP), true);

        $settings = (is_array($cookie_value) && array_key_exists($settings_key, $cookie_value))
            ? $cookie_value[$settings_key]
            : null;

        // else obtain from session
        if (!isset($settings)) {
            $session_value = (isset($_SESSION[self::SETTINGS_KEY_NAME])) ? $_SESSION[self::SETTINGS_KEY_NAME] : [];
            $settings = (is_array($session_value) && array_key_exists($settings_key, $session_value))
                ? $session_value[$settings_key]
                : self::DEFAULT_SETTINGS;
        }

        return $settings;
    }

    private function SetSettings($user_id, $project_id, $settings)
    {
        //print_r($settings);
        $settings_key = $user_id . '|' . $project_id;

        // store to session
        $session_value = (isset($_SESSION[self::SETTINGS_KEY_NAME])) ? $_SESSION[self::SETTINGS_KEY_NAME] : [];
        $session_value[$settings_key] = $settings;
        $_SESSION[self::SETTINGS_KEY_NAME] = $session_value;
        //unset($_SESSION[self::SETTINGS_KEY_NAME]);

        // store to cookie
        $cookie = (isset($_COOKIE[self::SETTINGS_KEY_NAME])) ? $_COOKIE[self::SETTINGS_KEY_NAME] : '';
        $cookie_value = json_decode(strtr($cookie, self::URL_DECODE_MAP), true);
        $cookie_value[$settings_key] = $settings;

        $cookie_options = array (
            'expires' => time() + (60 * 60 * 24 * 30), // 30 days
            //'expires' => time() - 3600, // reset cookie
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        );
        setcookie(self::SETTINGS_KEY_NAME, strtr(json_encode($cookie_value), self::URL_ENCODE_MAP), $cookie_options);

        //echo json_encode($cookie_value);
    }

    private function GetGroupSettings($user_id, $project_id, $settings_group_key): array
    {
        $settings = $this->GetSettings($user_id, $project_id);

        return (array_key_exists($settings_group_key, $settings) && is_array($settings[$settings_group_key]))
            ? $settings[$settings_group_key]
            : [];
    }

    private function SetGroupSettings($user_id, $project_id, $settings_group_key, $settings_group)
    {
        $settings = $this->GetSettings($user_id, $project_id);

        if (count($settings_group) > 0) {
            $settings[$settings_group_key] = $settings_group;
        } else {
            unset($settings[$settings_group_key]);
        }

        $this->SetSettings($user_id, $project_id, $settings);
    }

    public function GetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key): bool
    {
        $settings_group = $this->GetGroupSettings($user_id, $project_id, $settings_group_key);

        $settings_value = in_array($settings_key, $settings_group) ? true : false;

        return $settings_value;
    }

    private function SetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key, $settings_value, $settings_exclusive)
    {
        $settings_group = $this->GetGroupSettings($user_id, $project_id, $settings_group_key);
        if ($settings_exclusive) {
            $settings_group = [];
        }

        $hasValue = in_array($settings_key, $settings_group);
        if ($settings_value && !$hasValue) {
            $settings_group[] = $settings_key;
        }
        if (!$settings_value && $hasValue) {
            array_splice($settings_group, array_search($settings_key, $settings_group), 1);
        }
        
        $this->SetGroupSettings($user_id, $project_id, $settings_group_key, $settings_group);
    }

    public function ToggleSettings($user_id, $project_id, $settings_group_key, $settings_key, $settings_exclusive)
    {
        $settings_value = $settings_exclusive ? false : $this->GetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key);
        $this->SetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key, !$settings_value, $settings_exclusive);
    }
}
