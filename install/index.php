<?php

use Bitrix\Main\ModuleManager;

class adv_duplicates extends CModule
{
    public $MODULE_ID = 'adv.migrations';
    public $MODULE_NAME = 'Миграции AdLabs';
    public $MODULE_SORT = 1;
    public $PARTNER_NAME = 'Roman Gonyukov';

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallFiles()
    {
        return CopyDirFiles(__DIR__ . '/files/', $_SERVER['DOCUMENT_ROOT'], true);
    }

    public function UnInstallFiles()
    {
    }
}
