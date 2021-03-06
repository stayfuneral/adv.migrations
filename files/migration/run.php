<?php

define('NOT_CHECK_PERMISSIONS', true);
$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/ext_www/portal.advgroup.fbweb.ru';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$USER = new CUser;
$USER->Authorize(1);

use Bitrix\Main\Loader;
use Adv\Migrations\Migration;

if(Loader::includeModule('adv.migrations')) {
    $migration = new Migration;
    $migration->run();
}



require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
