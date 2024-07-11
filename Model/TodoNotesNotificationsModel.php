<?php

/**
 * Class TodoNotesNotificationsModel
 * @package Kanboard\Plugin\TodoNotes\Model
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Model;

use Kanboard\Core\Base;

class TodoNotesNotificationsModel extends Base
{
    private const TABLE_NOTES_WEBPN_SUBSCRIPTIONS   = 'todonotes_webpn_subscriptions';

    public function GetWebPNSubscriptionsForUser($user_id)
    {
        $encoded_subscriptions = $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
            ->columns('subscription')
            ->eq('user_id', $user_id)
            ->findAll();

        $decoded_subscriptions = array();
        foreach ($encoded_subscriptions as $encoded_subscription) {
            $decoded_subscriptions[] = json_decode($encoded_subscription['subscription'], true);
        }

        return $decoded_subscriptions;
    }

    public function UpdateWebPNSubscription($user_id, $webpn_subscription)
    {
        // check existing subscription
        $existing = $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
            ->eq('endpoint', $webpn_subscription['endpoint'])
            ->findOne();

        if (!$existing) {
            // add new subscription
            $valuesNew = array(
                'endpoint' => $webpn_subscription['endpoint'],
                'user_id' => $user_id,
                'subscription' => json_encode($webpn_subscription),
            );
            $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
                ->insert($valuesNew);
        } else {
            // update existing subscription
            $valuesExisting = array(
                'user_id' => $user_id,
                'subscription' => json_encode($webpn_subscription),
            );
            $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
                ->eq('endpoint', $webpn_subscription['endpoint'])
                ->update($valuesExisting);
        }
    }

    public function RemoveWebPNSubscription($user_id, $webpn_subscription)
    {
        return $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
            ->eq('user_id', $user_id)
            ->eq('endpoint', $webpn_subscription['endpoint'])
            ->remove();
    }

}
