<?php


namespace Adv\Duplicates;


class Users
{
    public static function get()
    {
        $usersFile = __DIR__ . '/../files/users.php';
        return require $usersFile;
    }
}