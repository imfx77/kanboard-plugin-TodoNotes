<?php

/**
 * Class TodoNotesNotificationsModelTest
 * @package Kanboard\Plugin\TodoNotes\Test\Model
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Test\Model;

require_once __DIR__ . '/../../../../tests/units/Base.php';

use Kanboard\Core\Plugin\Loader;
use Kanboard\Plugin\TodoNotes\Model\TodoNotesNotificationsModel;

class TodoNotesNotificationsModelTest extends Base
{
    protected $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $plugin = new Loader($this->container);
        $plugin->scan();
    }

    public function testNotificationsOptionsBitflagsConversion()
    {
        $model = new TodoNotesNotificationsModel($this->container);
        $notification_options = array(
            'alert_mail' => false,
            'alert_webpn' => true,
            'alert_before1day' => true,
            'alert_before1hour' => false,
            'alert_after1day' => false,
            'alert_after1hour' => false,
            'postpone' => false,
            'postpone_type' => 3,
            'postpone_value' => 7,
        );

        $notification_options_bitflags = $model->NotificationsOptionsToBitflags($notification_options);
        $notification_options_converted = $model->NotificationsOptionsFromBitflags($notification_options_bitflags);
        $this->assertEquals($notification_options, $notification_options_converted);
    }
}
