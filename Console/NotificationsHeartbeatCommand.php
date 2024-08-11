<?php

/**
 * Class NotificationsHeartbeatCommand
 * @package Kanboard\Plugin\TodoNotes\Console
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Console;

use Kanboard\Console\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationsHeartbeatCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('TodoNotes:NotificationsHeartbeat')
            ->setDescription('TodoNotes Notifications Heartbeat cronjob');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->todoNotesNotificationsController->HeartbeatForced();
        return 0;
    }
}
