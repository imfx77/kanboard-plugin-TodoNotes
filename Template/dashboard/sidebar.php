<li <?= $this->app->checkMenuSelection('TodoNotesController', 'ShowDashboard') ?>>
    <?= $this->url->link(t('TodoNotes__DASHBOARD_MY_NOTES'), 'TodoNotesController', 'ShowDashboard', array(
        'user_id' => $user['id'],
        'plugin' => 'BoardNotes',
        )) ?>
</li>
