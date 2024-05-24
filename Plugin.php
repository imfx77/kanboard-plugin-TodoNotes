<?php

namespace Kanboard\Plugin\BoardNotes;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    const NAME = 'BoardNotes';

    public function initialize()
    {
        //HELPER
        $this->helper->register('translationsExportToJSHelper', '\Kanboard\Plugin\BoardNotes\Helper\TranslationsExportToJSHelper');

        //HOOKS
        $this->template->hook->attach('template:dashboard:sidebar', 'BoardNotes:dashboard/sidebar');
        $this->template->hook->attach('template:project:dropdown', 'BoardNotes:project/dropdown');
        $this->template->hook->attach('template:project-header:view-switcher', 'BoardNotes:project/header');

        // ROUTES
        $this->route->addRoute('todonotes/:project_id', 'TodoNotesController', 'ShowProject', 'BoardNotes');
        $this->route->addRoute('todonotes/:project_id/:use_cached', 'TodoNotesController', 'ShowProject', 'BoardNotes');
        $this->route->addRoute('todonotes/:project_id/user/:user_id', 'TodoNotesController', 'ShowProject', 'BoardNotes');
        $this->route->addRoute('dashboard/:user_id/todonotes', 'TodoNotesController', 'ShowDashboard', 'BoardNotes');
        $this->route->addRoute('dashboard/:user_id/todonotes/:tab_id', 'TodoNotesController', 'ShowDashboard', 'BoardNotes');
    }

    public function onStartup()
    {
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
            'Plugin\BoardNotes\Model' => array(
                'TodoNotesModel'
            )
        );
    }

    public function getPluginName()
    {
        return self::NAME;
    }

    public function getPluginAuthor()
    {
        return 'Im[F(x)]';
    }

    public function getPluginVersion()
    {
        return '0.0.6';
    }

    public function getPluginDescription()
    {
        return t('TodoNotes__PLUGIN_DESCRIPTION');
    }

    public function getPluginHomepage()
    {
        return '';
    }
}
