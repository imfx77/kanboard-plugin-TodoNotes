<li <?= $this->app->checkMenuSelection('BoardNotesController', 'boardNotesShowAll') ?>>
    <?= $this->url->link(t('BoardNotes_DASHBOARD_MY_NOTES'), 'BoardNotesController', 'boardNotesShowAll', array(
        'user_id' => $user['id'],
        'plugin' => 'BoardNotes',
        )) ?>
</li>
