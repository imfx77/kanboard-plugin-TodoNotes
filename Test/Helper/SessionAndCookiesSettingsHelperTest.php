<?php

/**
 * Class SessionAndCookiesSettingsHelperTest
 * @package Kanboard\Plugin\TodoNotes\Test\Helper
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Test\Helper;

require_once __DIR__ . '/../../../../tests/units/Base.php';

use Kanboard\Core\Plugin\Loader;
use Kanboard\Plugin\TodoNotes\Helper\SessionAndCookiesSettingsHelper;
use Kanboard\Plugin\TodoNotes\Plugin;

class SessionAndCookiesSettingsHelperTest extends \Base
{
    protected $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $loader = new Loader($this->container);
        $loader->scan();
    }

    public function testToggleableSettings()
    {
        $helper = new SessionAndCookiesSettingsHelper($this->container);

        $this->assertEquals(false, $helper->GetToggleableSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_FILTER,
            $helper::SETTINGS_FILTER_ARCHIVED
        ));
        $helper->ToggleSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_FILTER,
            $helper::SETTINGS_FILTER_ARCHIVED,
            false,
            true
        );
        $this->assertEquals(true, $helper->GetToggleableSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_FILTER,
            $helper::SETTINGS_FILTER_ARCHIVED,
            true
        ));

        $this->assertEquals(true, $helper->GetToggleableSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_SORT,
            $helper::SETTINGS_SORT_MANUAL
        ));
        $helper->ToggleSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_SORT,
            $helper::SETTINGS_SORT_STATUS,
            true,
            true
        );
        $this->assertEquals(false, $helper->GetToggleableSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_SORT,
            $helper::SETTINGS_SORT_MANUAL,
            true
        ));
        $this->assertEquals(true, $helper->GetToggleableSettings(
            1,
            0,
            $helper::SETTINGS_GROUP_SORT,
            $helper::SETTINGS_SORT_STATUS,
            true
        ));
    }
}
