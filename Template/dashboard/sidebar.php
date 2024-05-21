<li <?= $this->app->checkMenuSelection('BoardNotesController', 'ShowDashboard') ?>>
    <?= $this->url->link(t('TodoNotes__DASHBOARD_MY_NOTES'), 'BoardNotesController', 'ShowDashboard', array(
        'user_id' => $user['id'],
        'plugin' => 'BoardNotes',
        )) ?>
</li>
