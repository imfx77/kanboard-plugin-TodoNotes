<?php

namespace Kanboard\Plugin\BoardNotes;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        //HOOKS
        $this->template->hook->attach('template:dashboard:sidebar', 'BoardNotes:dashboard/sidebar');
        $this->template->hook->attach('template:project:dropdown', 'BoardNotes:project/dropdown');
        $this->template->hook->attach('template:project-header:view-switcher', 'BoardNotes:project/header');

        // ROUTES
        $this->route->addRoute('boardnotes/:project_id', 'BoardNotesController', 'boardNotesShowProject', 'BoardNotes');
        $this->route->addRoute('boardnotes/:project_id/:use_cached', 'BoardNotesController', 'boardNotesShowProject', 'BoardNotes');
        $this->route->addRoute('boardnotes/:project_id/user/:user_id', 'BoardNotesController', 'boardNotesShowProject', 'BoardNotes');
        $this->route->addRoute('dashboard/:user_id/boardnotes', 'BoardNotesController', 'boardNotesShowAll', 'BoardNotes');
        $this->route->addRoute('dashboard/:user_id/boardnotes/:tab_id', 'BoardNotesController', 'boardNotesShowAll', 'BoardNotes');
    }

    public function getClasses()
    {
        return array(
            'Plugin\BoardNotes\Model' => array(
                'BoardNotesModel'
             )
         );
    }

    public function getPluginName()
    {
        return 'BoardNotes';
    }
    public function getPluginAuthor()
    {
        return 'TTJ';
    }
    public function getPluginVersion()
    {
        return '0.0.6';
    }
    public function getPluginDescription()
    {
        return 'Keep notes on every single projects. Notes which is not suitable for creating board tasks.';
    }
    public function getPluginHomepage()
    {
        return '';
    }
}
