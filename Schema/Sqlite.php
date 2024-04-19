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
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_cus (
        id INTEGER PRIMARY KEY,
        project_id INTEGER NOT NULL,
        project_name TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        id INTEGER PRIMARY KEY,
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

    $pdo->exec('INSERT INTO boardnotes_cus (project_id, project_name) VALUES (9998, "General")');

    $pdo->exec('INSERT INTO boardnotes_cus (project_id, project_name) VALUES (9997, "Todo")');
}

//------------------------------------------------
function reindexNotesAndLists_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_NOPK (
        id INTEGER,
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

    $pdo->exec('INSERT INTO boardnotes_NOPK SELECT * FROM boardnotes WHERE is_active >= 0');

    $pdo->exec('DROP TABLE boardnotes');
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        id INTEGER PRIMARY KEY,
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

    $pdo->exec('INSERT INTO boardnotes
                (project_id, user_id, position, is_active, title, category, description, date_created, date_modified)
                SELECT project_id, user_id, position, is_active, title, category, description, date_created, date_modified
                FROM boardnotes_NOPK');

    $pdo->exec('DROP TABLE boardnotes_NOPK');
}

//////////////////////////////////////////////////
