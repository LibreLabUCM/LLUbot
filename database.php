<?php

$db = new SQLite3('llubot.db') or die('Unable to create database');
$pdo = new PDO('sqlite:llubot.db');
$query = <<<EOD
  CREATE TABLE IF NOT EXISTS MEMBERS_COUNT (
    number INTEGER PRIMARY KEY,
    winner STRING,
    comment STRING,
    delivered INTEGER)
EOD;
$db->exec($query) or die('Create MEMBERS_COUNT table failed');
