<li <?= $this->app->checkMenuSelection('BoardNotesController', 'ShowDashboard') ?>>
    <?= $this->url->link(t('BoardNotes_DASHBOARD_MY_NOTES'), 'BoardNotesController', 'ShowDashboard', array(
        'user_id' => $user['id'],
        'plugin' => 'BoardNotes',
        )) ?>
</li>
