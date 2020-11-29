<?php


namespace Adv\Migrations;


use Bitrix\Main\IO\File;
use Bitrix\Main\IO\FileNotFoundException;

class Includer
{
    public static function getMigrationUsers()
    {
        self::getFile('users.php');
    }

    public static function getFile($file)
    {

        if(!File::isFileExists(realpath(__DIR__ .'/../files/' . $file))) {
            throw new FileNotFoundException(realpath(__DIR__ .'/../files/' . $file));
        }

        require realpath(__DIR__ .'/../files/' . $file);
    }

    public static function getRunMigrationScript()
    {
        self::getFile('migration/run.php');
    }
}