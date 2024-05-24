<?php

/**
 * Mysql schema
 * @package Kanboard\Plugin\TodoNotes\Schema
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Schema;

use PDO;

const VERSION = 1;


//////////////////////////////////////////////////
//  VERSION = 1
//////////////////////////////////////////////////

//------------------------------------------------
function version_1(PDO $pdo)
{
    // create+insert custom projects
    $pdo->exec('CREATE TABLE IF NOT EXISTS `todonotes_custom_projects` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `owner_id` INT NOT NULL DEFAULT 0,
                    `position` INT,
                    `project_name` TEXT,
                    PRIMARY KEY(id)
                ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('INSERT INTO `todonotes_custom_projects`
                    (`owner_id`, `position`, `project_name`)
                    VALUES (0, 1, "Global Notes")
                ');
    $pdo->exec('INSERT INTO `todonotes_custom_projects`
                    (`owner_id`, `position`, `project_name`)
                    VALUES (0, 2, "Global TODO")
                ');

    // create+insert entries
    $pdo->exec('CREATE TABLE IF NOT EXISTS `todonotes_entries` (
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
    $pdo->exec('INSERT INTO `todonotes_entries`
                    (`project_id`, `user_id`, `position`, `is_active`, `date_created`, `date_modified`)
                    VALUES (0, 0, 0, -1, 0, 0)
                ');
}

//------------------------------------------------
function reindexNotesAndLists_1(PDO $pdo)
{
    // add+update old_project_id
    $pdo->exec('ALTER TABLE `todonotes_custom_projects` ADD `old_project_id` INTEGER');
    $pdo->exec('UPDATE `todonotes_custom_projects` SET `old_project_id` = `id`');
    $pdo->exec('ALTER TABLE `todonotes_entries` ADD `old_project_id` INTEGER');
    $pdo->exec('UPDATE `todonotes_entries` SET `old_project_id` = `project_id`');

    // create+insert new shrunk custom projects
    $pdo->exec('CREATE TABLE IF NOT EXISTS `todonotes_custom_projects` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `owner_id` INT NOT NULL DEFAULT 0,
                    `position` INT,
                    `project_name` TEXT,
                    `old_project_id` INTEGER,
                    PRIMARY KEY(id)
                ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('INSERT INTO `todonotes_custom_projects_NEW`
				    (`owner_id`, `position`, `project_name`, `old_project_id`)
                    SELECT `owner_id`, `position`, `project_name`, `old_project_id`
				    FROM `todonotes_custom_projects`
				');

    // create+insert new shrunk entries
    $pdo->exec('CREATE TABLE `todonotes_entries_NEW` (
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
                    `old_project_id` INTEGER,
                    PRIMARY KEY(id)
                ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('INSERT INTO `todonotes_entries_NEW`
                    (`project_id`, `user_id`, `position`, `is_active`, `date_created`, `date_modified`, `old_project_id`)
                    VALUES (0, 0, 0, -1, 0, 0, 0)
                ');
    $pdo->exec('INSERT INTO `todonotes_entries_NEW`
                    (`project_id`, `user_id`, `position`, `is_active`, `title`, `category`, `description`, `date_created`, `date_modified`, `old_project_id`)
                    SELECT `project_id`, `user_id`, `position`, `is_active`, `title`, `category`, `description`, `date_created`, `date_modified`, `old_project_id`
                    FROM `todonotes_entries`
                    WHERE `project_id` <> 0 AND `user_id` > 0 AND `position` > 0 AND `is_active` >= 0
                ');

    // cross update the reindexed project ids
    $pdo->exec('UPDATE `todonotes_entries_NEW` AS `tEntries`, `todonotes_custom_projects_NEW` AS `tProjects`
                    SET `tEntries`.`project_id` = -`tProjects`.`id`
                    WHERE `tEntries`.`old_project_id` = -`tProjects`.`old_project_id`
                ');

    // drop old_project_id from new tables
    $pdo->exec('ALTER TABLE todonotes_custom_projects_NEW DROP old_project_id');
    $pdo->exec('ALTER TABLE todonotes_entries_NEW DROP old_project_id');

    // drop old tables
    $pdo->exec('DROP TABLE todonotes_custom_projects');
    $pdo->exec('DROP TABLE todonotes_entries');

    // rename new tables
    $pdo->exec('ALTER TABLE todonotes_custom_projects_NEW RENAME TO todonotes_custom_projects');
    $pdo->exec('ALTER TABLE todonotes_entries_NEW RENAME TO todonotes_entries');
}

//////////////////////////////////////////////////
