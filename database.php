<?php

$db = new SQLite3('llubot.db') or die('Unable to create database');
$pdo = new PDO('sqlite:llubot.db');

// Members count awards
$query = <<<EOD
  CREATE TABLE IF NOT EXISTS MEMBERS_COUNT (
    number INTEGER PRIMARY KEY,
    winner TEXT,
    comment TEXT,
    delivered INTEGER)
EOD;
$db->exec($query) or die('Create MEMBERS_COUNT table failed');

// Recommendations
$query = <<<EOD
  CREATE TABLE IF NOT EXISTS RECOMMENDATIONS (
    id INTEGER PRIMARY KEY,
    name TEXT,
    category TEXT,
    URI TEXT,
    comment TEXT,
    likes INTEGER,
    dislikes INTEGER)
EOD;
$db->exec($query) or die('Create RECOMMENDATIONS table failed');
