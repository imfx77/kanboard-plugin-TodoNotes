<?php

require_once(__DIR__  . '/../../vendor/autoload.php');

use Minishlink\WebPush\VAPID;

print_r(VAPID::createVapidKeys());
