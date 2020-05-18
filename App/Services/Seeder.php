<?php

namespace App\Services;


use App\Helpers\ClanRoles;

class Seeder
{
    function run()
    {
        $db = Db::getPdo();

        $dbUsers = $db->query('SELECT * FROM `users`');
        if (empty($dbUsers)) {
            $users = 'CREATE TABLE `users` (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(30) UNIQUE NOT NULL,
                session_key VARCHAR(50) NOT NULL
                )';
            $db->query($users);

            $insert = 'INSERT INTO `users` (username, session_key) VALUES (?, ?)';
            for ($i = 1; $i <= 10; $i++) {
                $db->prepare($insert)->execute(['user_' . $i, 'key_' . $i]);
            }
        }

        $dbClans = $db->query('SELECT * FROM `clans`');
        if (empty($dbClans)) {
            $clan = 'CREATE TABLE `clans` (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(12) UNIQUE NOT NULL,
                description VARCHAR(30) NOT NULL,
                user_id INT(11),
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
                )';
            $db->query($clan);
        }

        $dbClanMember = $db->query('SELECT * FROM `clan_members`');
        if (empty($dbClanMember)) {
            $clanMembers = 'CREATE TABLE `clan_members` (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                user_id INT(11),
                clan_id INT(11),
                role VARCHAR(50),
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                FOREIGN KEY (clan_id) REFERENCES clans (id) ON DELETE CASCADE
                )';
            $db->query($clanMembers);
        }

        die('Tables and seeds created successfully');
    }
}