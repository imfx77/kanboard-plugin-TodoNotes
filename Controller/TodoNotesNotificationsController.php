<?php

/**
 * Class TodoNotesNotificationsController
 * @package Kanboard\Plugin\TodoNotes\Controller
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Controller;

require_once(__DIR__  . '/../vendor/autoload.php');

use Kanboard\Controller\BaseController;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class TodoNotesNotificationsController extends BaseController
{
    const HEARTBEAT_INTERNAL_INTERVAL = 5 * 60; // 5 min

    public function UpdateWebPNSubscription()
    {
        $user_id = $this->request->getStringParam('user_id');
        $webpn_subscription = json_decode($this->request->getStringParam('webpn_subscription'), true);
        $this->todoNotesNotificationsModel->UpdateWebPNSubscription($user_id, $webpn_subscription);
    }

    private function SendEMailNotification($user_id, $note, $note_project_name, $notification_link)
    {
        $user = $this->userModel->getById($user_id);

        if (!$user || empty($user['email'])) {
            return;
        }

        //---------------------------------------------------
        // workaround for WysiwygMDEditor plugin markdown rendering (which is in JS)
        $isWysiwygMDEditorRendering = ($this->configModel->get('WysiwygMDEditor_enable_easymde_rendering', '0') == '1');
        $descriptionPrefix = $isWysiwygMDEditorRendering ? '__{WysiwygMDEditor_FORCE_FALLBACK_IMPL}__' : '';

        $notification_title = t('TodoNotes__NOTIFICATIONS_EMAIL_TITLE');
        $notification_content = e(
            'TodoNotes__NOTIFICATIONS_EMAIL_CONTENT',
            $notification_link,
            $note['title'],
            $note['date_notified'],
            $note_project_name,
            $note['category'] ?: '(' . t('None') . ')',
            $note['description'] ? $this->helper->text->markdown($descriptionPrefix . $note['description']) : '(' . t('None') . ')'
        );

        $this->emailClient->send(
            $user['email'],
            $user['name'] ?: $user['username'],
            $notification_title,
            $notification_content
        );

        echo 'EMail sent.';
    }

    private function SendWebPushNotification($user_id, $note, $note_project_name, $notification_link)
    {
        $notification = json_encode(array(
            'title' => $note_project_name,
            'content' => $note['title'],
            'link' => $notification_link,
            'timestamp' => $note['notifications_alert_timestamp'],
        ));

        $auth = array(
            'VAPID' => array(
                'subject' => 'https://github.com/imfx77/kanboard-plugin-TodoNotes/',
                'publicKey' => file_get_contents(__DIR__ . '/../vapid/vapid.public.key'),
                'privateKey' => file_get_contents(__DIR__ . '/../vapid/vapid.private.key'),
            ),
        );
        $webPush = new WebPush($auth);

        $subscriptions = $this->todoNotesNotificationsModel->GetWebPNSubscriptionsForUser($user_id);
        foreach($subscriptions as $subscription) {

            $webPushSubscription = Subscription::create($subscription);
            $report = $webPush->sendOneNotification($webPushSubscription, $notification, ['TTL' => 5000]);

            if (!$report->isSuccess() && $report->getResponse()->getStatusCode() == 410) { // GONE
                $this->todoNotesNotificationsModel->RemoveWebPNSubscription($user_id, $subscription);
                echo 'Removed WebPN subscription for endpoint : ' . PHP_EOL . $subscription['endpoint'] . PHP_EOL;
            }
        }

        echo 'WebPNs sent.';
    }

    public function TestNoteNotifications()
    {
        $user_id = $this->request->getStringParam('user_id');
        $project_id = $this->request->getStringParam('project_custom_id');
        $note_id = $this->request->getStringParam('note_id');

        $this->TestNoteNotificationsWithParams($user_id, $project_id, $note_id);
    }

    public function TestNoteNotificationsWithParams($user_id, $project_id, $note_id)
    {
//        $functionName = '\Kanboard\Plugin\\' . Plugin::NAME . '\Schema\version_1';
//        if (function_exists($functionName)) {
//            $this->db->startTransaction();
//            $this->db->getDriver()->disableForeignKeys();
//
//            call_user_func($functionName, $this->db->getConnection());
//
//            $this->db->getDriver()->enableForeignKeys();
//            $this->db->closeTransaction();
//        }

        $note = $this->todoNotesModel->GetProjectNoteForUser($note_id, $project_id, $user_id);
        $note_project_name = $this->todoNotesModel->GetProjectNameForUser($user_id, $note['project_id']);
        $note_project_tab = $this->todoNotesModel->GetTabForProject($user_id, $note['project_id']);
        $notification_link = $this->helper->url->base() . 'dashboard/' . $user_id . '/todonotes/' . $note_project_tab;

        //---------------------------------------------------
        // email notification
        //$this->SendEMailNotification($user_id, $note, $note_project_name, $notification_link);

        //---------------------------------------------------
        // web push notifications (to all registered endpoints)
        $this->SendWebPushNotification($user_id, $note, $note_project_name, $notification_link);
    }

    public function BroadcastNotesNotifications()
    {
        //echo 'TodoNotesNotificationsController::BroadcastNotesNotifications()' . PHP_EOL;
        //$this->TestNoteNotificationsWithParams(3, -6, 80);
    }

    public function Heartbeat()
    {
        $this->HeartbeatInternal(false);
    }
    public function HeartbeatForced()
    {
        $this->HeartbeatInternal(true);
    }

    private function HeartbeatInternal($force)
    {
        //echo 'TodoNotesNotificationsController::HeartbeatInternal()' . PHP_EOL;
        //echo 'force = ' . ($force ? 'true' : 'false') . PHP_EOL;

        $timestamp = time();
        //echo 'time = ' . date($this->dateParser->getUserDateTimeFormat(), $timestamp) . PHP_EOL;

        $heartbeat = file_get_contents(__DIR__ . '/../.cache/heartbeat');
        //echo 'heartbeat = ' . date($this->dateParser->getUserDateTimeFormat(), $heartbeat) . PHP_EOL;

        if (!$force && !empty($heartbeat) && $timestamp < intval($heartbeat) + self::HEARTBEAT_INTERNAL_INTERVAL) {
            echo 'SKIP heartbeat execution ...' . PHP_EOL;
            return;
        }

        //echo 'DO execute some actual stuff here ...' . PHP_EOL;
        $this->BroadcastNotesNotifications();
        file_put_contents(__DIR__ . '/../.cache/heartbeat', $timestamp);
    }

}
