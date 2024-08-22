<?php

/**
 * Postgres schema
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
    // create+insert+index custom projects
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_custom_projects (
                    id SERIAL PRIMARY KEY,
                    owner_id INTEGER NOT NULL DEFAULT 0,
                    position INTEGER,
                    project_name TEXT
                )');
    $pdo->exec('INSERT INTO todonotes_custom_projects
                    (owner_id, position, project_name)
                    VALUES (0, 1, \'Global Notes\')
                ');
    $pdo->exec('INSERT INTO todonotes_custom_projects
                    (owner_id, position, project_name)
                    VALUES (0, 2, \'Global TODO\')
                ');
    $pdo->exec('CREATE INDEX todonotes_custom_projects_owner_ix ON todonotes_custom_projects(owner_id)');
    $pdo->exec('CREATE INDEX todonotes_custom_projects_position_ix ON todonotes_custom_projects(position)');

    // create+insert+index entries
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_entries (
                    id SERIAL PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    position INTEGER,
                    is_active INTEGER,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_notified INTEGER,
                    last_notified INTEGER,
                    flags_notified INTEGER,
                    date_restored INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_entries
                    (project_id, user_id, position, is_active, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored)
                    VALUES (0, 0, 0, -1, 0, 0, 0, 0, 0, 0)
                ');
    $pdo->exec('CREATE INDEX todonotes_entries_project_ix ON todonotes_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_user_ix ON todonotes_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_position_ix ON todonotes_entries(position)');
    $pdo->exec('CREATE INDEX todonotes_entries_active_ix ON todonotes_entries(is_active)');
    $pdo->exec('CREATE INDEX todonotes_entries_created_ix ON todonotes_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_entries_modified_ix ON todonotes_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_entries_notified_ix ON todonotes_entries(date_notified)');
    $pdo->exec('CREATE INDEX todonotes_entries_last_notified_ix ON todonotes_entries(last_notified)');

    // create+index archive entries
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_archive_entries (
                    id SERIAL PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_archived INTEGER
                )');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_project_ix ON todonotes_archive_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_user_ix ON todonotes_archive_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_created_ix ON todonotes_archive_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_modified_ix ON todonotes_archive_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_archived_ix ON todonotes_archive_entries(date_archived)');

    // create+index webpn subscriptions
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_webpn_subscriptions (
                    endpoint TEXT NOT NULL PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    subscription TEXT NOT NULL
                )');
    $pdo->exec("CREATE INDEX todonotes_webpn_subscriptions_user_ix ON todonotes_webpn_subscriptions(user_id)");
}

//------------------------------------------------
function reindexNotesAndLists_1(PDO $pdo)
{
    // add+update old_project_id
    $pdo->exec('ALTER TABLE todonotes_custom_projects ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_custom_projects SET old_project_id = id');
    $pdo->exec('ALTER TABLE todonotes_entries ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_entries SET old_project_id = project_id');
    $pdo->exec('ALTER TABLE todonotes_archive_entries ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_archive_entries SET old_project_id = project_id');

    // create+insert new shrunk custom projects
    $pdo->exec('CREATE TABLE todonotes_custom_projects_NEW (
                    id SERIAL PRIMARY KEY,
                    owner_id INTEGER NOT NULL DEFAULT 0,
                    position INTEGER,
                    project_name TEXT,
                    old_project_id INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_custom_projects_NEW
				    (owner_id, position, project_name, old_project_id)
                    SELECT owner_id, position, project_name, old_project_id
				    FROM todonotes_custom_projects
				');

    // create+insert new shrunk entries
    $pdo->exec('CREATE TABLE todonotes_entries_NEW (
                    id SERIAL PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    position INTEGER,
                    is_active INTEGER,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_notified INTEGER,
                    last_notified INTEGER,
                    flags_notified INTEGER,
                    date_restored INTEGER,
                    old_project_id INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_entries_NEW
                    (project_id, user_id, position, is_active, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored, old_project_id)
                    VALUES (0, 0, 0, -1, 0, 0, 0, 0, 0, 0, 0)
                ');
    $pdo->exec('INSERT INTO todonotes_entries_NEW
                    (project_id, user_id, position, is_active, title, category, description, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored, old_project_id)
                    SELECT project_id, user_id, position, is_active, title, category, description, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored, old_project_id
                    FROM todonotes_entries
                    WHERE project_id <> 0 AND user_id > 0 AND position > 0 AND is_active >= 0
                ');

    // create+insert new shrunk archive entries
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_archive_entries_NEW (
                    id SERIAL PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_archived INTEGER,
                    old_project_id INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_archive_entries_NEW
                    (project_id, user_id, title, category, description, date_created, date_modified, date_archived, old_project_id)
                    SELECT project_id, user_id, title, category, description, date_created, date_modified, date_archived, old_project_id
                    FROM todonotes_archive_entries
                    WHERE project_id <> 0 AND user_id > 0 AND date_archived > 0
                ');

    // cross update the reindexed project ids
    $pdo->exec('UPDATE todonotes_entries_NEW AS tEntries
                    SET project_id = -tProjects.id
                    FROM todonotes_custom_projects_NEW AS tProjects
                    WHERE tEntries.old_project_id = -tProjects.old_project_id
                ');
    $pdo->exec('UPDATE todonotes_archive_entries_NEW AS tArchiveEntries
                    SET project_id = -tProjects.id
                    FROM todonotes_custom_projects_NEW AS tProjects
                    WHERE tArchiveEntries.old_project_id = -tProjects.old_project_id
                ');

    // drop old_project_id from new tables
    $pdo->exec('ALTER TABLE todonotes_custom_projects_NEW DROP old_project_id');
    $pdo->exec('ALTER TABLE todonotes_entries_NEW DROP old_project_id');
    $pdo->exec('ALTER TABLE todonotes_archive_entries_NEW DROP old_project_id');

    // drop old tables
    $pdo->exec('DROP TABLE todonotes_custom_projects');
    $pdo->exec('DROP TABLE todonotes_entries');
    $pdo->exec('DROP TABLE todonotes_archive_entries');

    // rename new tables
    $pdo->exec('ALTER TABLE todonotes_custom_projects_NEW RENAME TO todonotes_custom_projects');
    $pdo->exec('ALTER TABLE todonotes_entries_NEW RENAME TO todonotes_entries');
    $pdo->exec('ALTER TABLE todonotes_archive_entries_NEW RENAME TO todonotes_archive_entries');

    // re-create indices for todonotes_custom_projects
    $pdo->exec('CREATE INDEX todonotes_custom_projects_owner_ix ON todonotes_custom_projects(owner_id)');
    $pdo->exec('CREATE INDEX todonotes_custom_projects_position_ix ON todonotes_custom_projects(position)');
    // re-create indices for todonotes_entries
    $pdo->exec('CREATE INDEX todonotes_entries_project_ix ON todonotes_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_user_ix ON todonotes_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_position_ix ON todonotes_entries(position)');
    $pdo->exec('CREATE INDEX todonotes_entries_active_ix ON todonotes_entries(is_active)');
    $pdo->exec('CREATE INDEX todonotes_entries_created_ix ON todonotes_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_entries_modified_ix ON todonotes_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_entries_notified_ix ON todonotes_entries(date_notified)');
    $pdo->exec('CREATE INDEX todonotes_entries_last_notified_ix ON todonotes_entries(last_notified)');
    // re-create indices for todonotes_archive_entries
    $pdo->exec('CREATE INDEX todonotes_archive_entries_project_ix ON todonotes_archive_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_user_ix ON todonotes_archive_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_created_ix ON todonotes_archive_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_modified_ix ON todonotes_archive_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_archived_ix ON todonotes_archive_entries(date_archived)');
}

//////////////////////////////////////////////////
