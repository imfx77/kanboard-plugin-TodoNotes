<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 1;


//////////////////////////////////////////////////
//  VERSION = 1
//////////////////////////////////////////////////

//------------------------------------------------
function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_custom_projects (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        project_name TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_entries (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        position INTEGER,
        is_active INTEGER,
        title TEXT,
        category TEXT,
        description TEXT,
        date_created INTEGER,
        date_modified INTEGER
    )');

    $pdo->exec('INSERT INTO boardnotes_custom_projects (project_id, project_name) VALUES (9998, "General")');

    $pdo->exec('INSERT INTO boardnotes_custom_projects (project_id, project_name) VALUES (9997, "Todo")');
}

//------------------------------------------------
function reindexNotesAndLists_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_entries_NOPK (
        id SERIAL,
        project_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        position INTEGER,
        is_active INTEGER,
        title TEXT,
        category TEXT,
        description TEXT,
        date_created INTEGER,
        date_modified INTEGER
    )');

    $pdo->exec('INSERT INTO boardnotes_entries_NOPK
                SELECT * FROM boardnotes_entries
                WHERE project_id > 0 AND user_id > 0 AND position > 0 AND is_active >= 0');

    $pdo->exec('DROP TABLE boardnotes_entries');
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_entries (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        position INTEGER,
        is_active INTEGER,
        title TEXT,
        category TEXT,
        description TEXT,
        date_created INTEGER,
        date_modified INTEGER
    )');

    $pdo->exec('INSERT INTO boardnotes_entries
                (project_id, user_id, position, is_active, title, category, description, date_created, date_modified)
                SELECT project_id, user_id, position, is_active, title, category, description, date_created, date_modified
                FROM boardnotes_entries_NOPK');

    $pdo->exec('DROP TABLE boardnotes_entries_NOPK');
}

//////////////////////////////////////////////////
