<?php


namespace Adv\Duplicates;


use Bitrix\Main\IO\File;
use Bitrix\Main\IO\FileNotFoundException;

class Includer
{
    public static function getMigrationUsers()
    {
        return self::getFile('users.php');
    }

    public static function getFile($file)
    {
        $path = __DIR__ . '/../files/' . $file;
        if(!File::isFileExists($file)) {
            throw new FileNotFoundException($path);
        }

        return require $path;
    }

    public static function getRunMigrationScript()
    {
        return self::getFile('migration/run.php');
    }
}