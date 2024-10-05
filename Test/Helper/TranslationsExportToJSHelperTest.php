<?php

/**
 * Class TranslationsExportToJSHelperTest
 * @package Kanboard\Plugin\TodoNotes\Test\Helper
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Test\Helper;

require_once __DIR__ . '/../../../../tests/units/Base.php';

use Kanboard\Core\Plugin\Loader;
use Kanboard\Plugin\TodoNotes\Helper\TranslationsExportToJSHelper;
use Kanboard\Plugin\TodoNotes\Plugin;

class TranslationsExportToJSHelperTest extends \Base
{
    protected $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $loader = new Loader($this->container);
        $loader->scan();

        // ensure translator initialization
        $loader->getPlugins()[Plugin::NAME]->onStartup();
    }

    public function testExport()
    {
        $helper = new TranslationsExportToJSHelper($this->container);

        $translationTextIds = array(
            'TodoNotes__GENERIC_NO_OWNER_PRIVILEGES',
            'TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES',
            'TodoNotes__DASHBOARD_MY_NOTES',
            'TodoNotes__DASHBOARD_ALL_TAB',
            'TodoNotes__JS_LOADING_MSG',
            'TodoNotes__JS_REINDEXING_MSG',
        );

        $result = $helper->export($translationTextIds);

        $this->assertEquals($result, '{"TodoNotes__GENERIC_NO_OWNER_PRIVILEGES":"\u26a0\ufe0f Current user has no Owner privileges!","TodoNotes__GENERIC_NO_ADMIN_PRIVILEGES":"\u26a0\ufe0f Current user has no Admin privileges!","TodoNotes__DASHBOARD_MY_NOTES":"My notes","TodoNotes__DASHBOARD_ALL_TAB":"All Lists","TodoNotes__JS_LOADING_MSG":"Loading ...","TodoNotes__JS_REINDEXING_MSG":"Reindexing ..."}');
    }
}
