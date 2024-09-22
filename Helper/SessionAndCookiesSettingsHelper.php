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

    public const SETTINGS_GROUP_TABS = 0;
    public const SETTINGS_GROUP_FILTER = 1;
    public const SETTINGS_GROUP_SORT = 2;
    public const SETTINGS_GROUP_VIEW = 3;

    public const SETTINGS_TABS_STATS = 0;
    public const SETTINGS_TABS_GLOBAL = 1;
    public const SETTINGS_TABS_PRIVATE = 2;
    public const SETTINGS_TABS_REGULAR = 3;

    public const SETTINGS_FILTER_DONE = 0;
    public const SETTINGS_FILTER_OPEN = 1;
    public const SETTINGS_FILTER_IN_PROGRESS = 2;
    public const SETTINGS_FILTER_ARCHIVED = 3;

    public const SETTINGS_SORT_MANUAL = 0;
    public const SETTINGS_SORT_STATUS = 1;
    public const SETTINGS_SORT_DATE_CREATED = 2;
    public const SETTINGS_SORT_DATE_MODIFIED = 3;
    public const SETTINGS_SORT_DATE_NOTIFIED = 4;
    public const SETTINGS_SORT_DATE_LAST_NOTIFIED = 5;
    public const SETTINGS_SORT_DATE_ARCHIVED = 6;
    public const SETTINGS_SORT_DATE_RESTORED = 7;

    public const SETTINGS_VIEW_CATEGORY_COLORS = 0;
    public const SETTINGS_VIEW_STANDARD_STATUS_MARKS = 1;

    private const DEFAULT_SETTINGS = array(
        self::SETTINGS_GROUP_SORT => [self::SETTINGS_SORT_MANUAL],
        self::SETTINGS_GROUP_VIEW => [self::SETTINGS_VIEW_CATEGORY_COLORS],
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

    public function GetSettings($user_id, $project_id, $from_session = false)
    {
        $settings_key = $user_id . '|' . $project_id;
        $settings = null;

        // obtain from cookie
        if (!$from_session) {
            $cookie = (isset($_COOKIE[self::SETTINGS_KEY_NAME])) ? $_COOKIE[self::SETTINGS_KEY_NAME] : '';
            $cookie_value = json_decode(strtr($cookie, self::URL_DECODE_MAP), true);

            if (is_array($cookie_value) && array_key_exists($settings_key, $cookie_value)) {
                $settings = $cookie_value[$settings_key];
            }
        }

        // else obtain from session
        if (!isset($settings)) {
            $session_value = (isset($_SESSION[self::SETTINGS_KEY_NAME])) ? $_SESSION[self::SETTINGS_KEY_NAME] : [];
            $settings = (is_array($session_value) && array_key_exists($settings_key, $session_value))
                ? $session_value[$settings_key]
                : self::DEFAULT_SETTINGS;
        }

        return $settings;
    }

    private function SetSettings($user_id, $project_id, $settings, $to_session_only = false)
    {
        //print_r($settings);
        $settings_key = $user_id . '|' . $project_id;

        // store to session
        $session_value = (isset($_SESSION[self::SETTINGS_KEY_NAME])) ? $_SESSION[self::SETTINGS_KEY_NAME] : [];
        $session_value[$settings_key] = $settings;
        $_SESSION[self::SETTINGS_KEY_NAME] = $session_value;
        //unset($_SESSION[self::SETTINGS_KEY_NAME]);

        // store to cookie
        if (!$to_session_only) {
            $cookie = (isset($_COOKIE[self::SETTINGS_KEY_NAME])) ? $_COOKIE[self::SETTINGS_KEY_NAME] : '';
            $cookie_value = json_decode(strtr($cookie, self::URL_DECODE_MAP), true);
            $cookie_value[$settings_key] = $settings;

            $cookie_options = array(
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
    }

    public function GetGroupSettings($user_id, $project_id, $settings_group_key, $from_session = false): array
    {
        $settings = $this->GetSettings($user_id, $project_id, $from_session);

        return (array_key_exists($settings_group_key, $settings) && is_array($settings[$settings_group_key]))
            ? $settings[$settings_group_key]
            : [];
    }

    private function SetGroupSettings($user_id, $project_id, $settings_group_key, $settings_group, $to_session_only = false)
    {
        $settings = $this->GetSettings($user_id, $project_id);

        if (count($settings_group) > 0) {
            $settings[$settings_group_key] = $settings_group;
        } else {
            unset($settings[$settings_group_key]);
        }

        $this->SetSettings($user_id, $project_id, $settings, $to_session_only);
    }

    public function GetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key, $from_session = false): bool
    {
        $settings_group = $this->GetGroupSettings($user_id, $project_id, $settings_group_key, $from_session);

       return in_array($settings_key, $settings_group);
    }

    private function SetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key, $settings_value, $settings_exclusive, $to_session_only = false)
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
        
        $this->SetGroupSettings($user_id, $project_id, $settings_group_key, $settings_group, $to_session_only);
    }

    public function ToggleSettings($user_id, $project_id, $settings_group_key, $settings_key, $settings_exclusive, $to_session_only = false)
    {
        $settings_value = !$settings_exclusive && $this->GetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key);
        $this->SetToggleableSettings($user_id, $project_id, $settings_group_key, $settings_key, !$settings_value, $settings_exclusive, $to_session_only);
    }
}
