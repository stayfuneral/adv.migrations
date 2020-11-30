<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\File;

class adv_migrations extends CModule
{
    public $MODULE_ID = 'adv.migrations';
    public $MODULE_NAME = 'Миграции AdLabs';
    public $MODULE_SORT = 1;
    public $PARTNER_NAME = 'Roman Gonyukov';

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallFiles();
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallFiles()
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/adv_migration/run.php';
        $content = '<?php' . PHP_EOL . PHP_EOL;
        $content .= '$_SERVER[\'DOCUMENT_ROOT\'] = \''.$_SERVER['DOCUMENT_ROOT'].'\';' . PHP_EOL . PHP_EOL;
        $content .= 'require $_SERVER[\'DOCUMENT_ROOT\'] . \'/local/modules/adv.migrations/files/migration/run.php\';';

        return File::putFileContents($file, $content, 1);
    }

    public function UnInstallFiles()
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/adv_migration/run.php';

        if(File::isFileExists($file)) {
            File::deleteFile($file);
        }

    }
}
