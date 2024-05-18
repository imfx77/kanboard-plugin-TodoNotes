<?php

/**
 * Mysql schema
 * @package Kanboard\Plugin\BoardNotes\Schema
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 1;


//////////////////////////////////////////////////
//  VERSION = 1
//////////////////////////////////////////////////

//------------------------------------------------
function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS `boardnotes_custom_projects` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `owner_id` INT NOT NULL DEFAULT 0,
        `position` INT,
        `project_name` TEXT,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS `boardnotes_entries` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `project_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `position` INT,
        `is_active` INT,
        `title` TEXT,
        `category` TEXT,
        `description` TEXT,
        `date_created` INT,
        `date_modified` INT,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo->exec('INSERT INTO `boardnotes_custom_projects`(`owner_id`, `position`, `project_name`) VALUES (0, 1, "Global Notes")');
    $pdo->exec('INSERT INTO `boardnotes_custom_projects`(`owner_id`, `position`, `project_name`) VALUES (0, 2, "Global TODO")');

    $pdo->exec('INSERT INTO `boardnotes_entries` (`project_id`, `user_id`, `position`, `is_active`, `date_created`, `date_modified`) VALUES (0, 0, 0, -1, 0, 0)');
}

//------------------------------------------------
function reindexNotesAndLists_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS `boardnotes_entries_NOPK` (
        `id` INT NOT NULL,
        `project_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `position` INT,
        `is_active` INT,
        `title` TEXT,
        `category` TEXT,
        `description` TEXT,
        `date_created` INT,
        `date_modified` INT
    ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo->exec('INSERT INTO `boardnotes_entries_NOPK` (`project_id`, `user_id`, `position`, `is_active`, `date_created`, `date_modified`) VALUES (0, 0, 0, -1, 0, 0)');
    $pdo->exec('INSERT INTO `boardnotes_entries_NOPK`
                SELECT * FROM `boardnotes_entries`
                WHERE `project_id` <> 0 AND `user_id` > 0 AND `position` > 0 AND `is_active` >= 0');

    $pdo->exec('DROP TABLE `boardnotes_entries`');
    $pdo->exec('CREATE TABLE IF NOT EXISTS `boardnotes_entries` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `project_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `position` INT,
        `is_active` INT,
        `title` TEXT,
        `category` TEXT,
        `description` TEXT,
        `date_created` INT,
        `date_modified` INT,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo->exec('INSERT INTO `boardnotes_entries`
                (`project_id`, `user_id`, `position`, `is_active`, `title`, `category`, `description`, `date_created`, `date_modified`)
                SELECT `project_id`, `user_id`, `position`, `is_active`, `title`, `category`, `description`, `date_created`, `date_modified`
                FROM `boardnotes_entries_NOPK`');
    $pdo->exec('DROP TABLE `boardnotes_entries_NOPK`');
}

//////////////////////////////////////////////////
