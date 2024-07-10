<?php

/**
 * Class TodoNotesNotificationsController
 * @package Kanboard\Plugin\TodoNotes\Controller
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Plugin\TodoNotes\Plugin;

class TodoNotesNotificationsController extends BaseController
{

    public function TestNoteNotificationAlerts()
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

        $project_id = $this->request->getStringParam('project_custom_id');
        $user_id = $this->request->getStringParam('user_id');
        $note_id = $this->request->getStringParam('note_id');
        $note = $this->todoNotesModel->GetProjectNoteForUser($note_id, $project_id, $user_id);
        $note_project_name = $this->todoNotesModel->GetProjectNameForUser($user_id, $note['project_id']);
        $note_project_tab = $this->todoNotesModel->GetTabForProject($user_id, $note['project_id']);

        //---------------------------------------------------
        // workaround for WysiwygMDEditor plugin rendering (which is in JS)
        $isWysiwygMDEditorRendering = ($this->configModel->get('WysiwygMDEditor_enable_easymde_rendering', '0') == '1');
        $descriptionPrefix = $isWysiwygMDEditorRendering ? '__{WysiwygMDEditor_FORCE_FALLBACK_IMPL}__' : '';

        $notification_link = $this->helper->url->base() . 'dashboard/' . $user_id . '/todonotes/' . $note_project_tab;
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

//        //---------------------------------------------------
//        // email notification
//        if (!empty($user['email'])) {
//            $this->emailClient->send(
//                $user['email'],
//                $user['name'] ?: $user['username'],
//                $notification_title,
//                $notification_content
//            );
//        }

        //---------------------------------------------------
        // response to JS for web notification
        print(json_encode(array('notification_title' => $note_project_name,
                                'notification_content' => $note['title'],
                                'notification_link' => $notification_link,
                                'notification_timestamp' => $note['notifications_alert_timestamp'],
        )));
    }

}
