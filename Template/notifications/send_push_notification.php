<?php
require_once(__DIR__  . '/../../vendor/autoload.php');

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

// TODO : reimplement with dynamic subscriptions (e.g. in database)!
$subscription = Subscription::create(json_decode(file_get_contents(__DIR__ . '/../../vapid/vapid.subscription'), true));

$auth = array(
    'VAPID' => array(
        'subject' => 'https://github.com/imfx77/kanboard-plugin-TodoNotes/',
        'publicKey' => file_get_contents(__DIR__ . '/../../vapid/vapid.public.key'), // don't forget that your public key also lives in app.js
        'privateKey' => file_get_contents(__DIR__ . '/../../vapid/vapid.private.key'), // in the real world, this would be in a secret file
    ),
);

$webPush = new WebPush($auth);

$report = $webPush->sendOneNotification(
    $subscription,
    '{"title":"TodoNotes Alert","content":"test","link":"https:\/\/kanboard.imfx77.myds.me\/dashboard\/3\/todonotes\/7","timestamp":1719190560}',
    ['TTL' => 5000]
);

//print_r($report);

$endpoint = $report->getRequest()->getUri()->__toString();

if ($report->isSuccess()) {
    echo "[v] Message sent successfully for subscription {$endpoint}.";
} else {
    echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
}