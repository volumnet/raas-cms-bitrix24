<?php
namespace RAAS\CMS;

use RAAS\Application;

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'on';
require __DIR__ . '/../vendor/autoload.php';
$GLOBALS['bitrix24'] = array(
    'domain' => '',
    'login' => '',
    'password' => '',
    'webhook' => '',
);
$f = __DIR__ . '/../../../../bitrix24.config.php';
if (is_file($f)) {
    $GLOBALS['bitrix24'] = require $f;
}
require __DIR__ . '/../../../../cron/cron.php';
$newSQL = file_get_contents(__DIR__ . '/resources/test.sql');
Application::i()->SQL->query($newSQL);
