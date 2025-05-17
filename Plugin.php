<?php

namespace Kanboard\Plugin\TodoNotes;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\TodoNotes\Console\NotificationsHeartbeatCommand;

class Plugin extends Base
{
    public function initialize()
    {
        //HELPER
        $this->helper->register('todonotesTranslationsExportToJSHelper', '\Kanboard\Plugin\TodoNotes\Helper\TranslationsExportToJSHelper');
        $this->helper->register('todonotesSessionAndCookiesSettingsHelper', '\Kanboard\Plugin\TodoNotes\Helper\SessionAndCookiesSettingsHelper');

        //HOOKS
        $this->template->hook->attach('template:dashboard:sidebar', 'TodoNotes:dashboard/sidebar');
        $this->template->hook->attach('template:project:dropdown', 'TodoNotes:project/dropdown');
        $this->template->hook->attach('template:project-header:view-switcher', 'TodoNotes:project/header');

        // ROUTES
        $this->route->addRoute('todonotes/:project_id', 'TodoNotesController', 'ShowProject', 'TodoNotes');
        $this->route->addRoute('todonotes/:project_id/:use_cached', 'TodoNotesController', 'ShowProject', 'TodoNotes');
        $this->route->addRoute('todonotes/:project_id/user/:user_id', 'TodoNotesController', 'ShowProject', 'TodoNotes');
        $this->route->addRoute('dashboard/:user_id/todonotes', 'TodoNotesController', 'ShowDashboard', 'TodoNotes');
        $this->route->addRoute('dashboard/:user_id/todonotes/:tab_id', 'TodoNotesController', 'ShowDashboard', 'TodoNotes');
        $this->route->addRoute('dashboard/:user_id/todonotes/:tab_id/:note_id', 'TodoNotesController', 'ShowDashboard', 'TodoNotes');
        $this->route->addRoute('dashboard/:user_id/todonotesSharing', 'TodoNotesController', 'ShowDashboardSharing', 'TodoNotes');
        $this->route->addRoute('dashboard/:user_id/todonotesSharing/:tab_id', 'TodoNotesController', 'ShowDashboardSharing', 'TodoNotes');

        // COMMANDS [ ./cli TodoNotes:NotificationsHeartbeat ]
        $this->cli->add(new NotificationsHeartbeatCommand($this->container));
    }

    public function onStartup()
    {
        // initialize translator, default locale en_US
        $path = __DIR__ . '/Locale';
        $language = $this->languageModel->getCurrentLanguage();
        $filename = implode(DIRECTORY_SEPARATOR, array($path, $language, 'translations.php'));

        if (file_exists($filename)) {
            Translator::load($language, $path);
        } else {
            Translator::load('en_US', $path);
        }
    }

    public function getClasses()
    {
        return array(
            'Plugin\TodoNotes\Model' => array(
                'TodoNotesModel',
                'TodoNotesNotificationsModel'
            ),
            'Plugin\TodoNotes\Controller' => array(
                'TodoNotesController',
                'TodoNotesNotificationsController'
            )
        );
    }

    public const NAME = 'TodoNotes';

    public function getPluginName()
    {
        return 'TodoNotes';
    }

    public function getPluginDescription()
    {
        return t('TodoNotes__PLUGIN_DESCRIPTION');
    }

    public function getPluginAuthor()
    {
        return 'Im[F(x)]';
    }

    public function getPluginVersion()
    {
        return '1.0.2';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.33';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/imfx77/kanboard-plugin-TodoNotes';
    }
}
