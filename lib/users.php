<?php


namespace Adv\Migrations;


class Users
{
    public static function get()
    {
        $usersFile = __DIR__ . '/../files/users.php';
        return require $usersFile;
    }
}