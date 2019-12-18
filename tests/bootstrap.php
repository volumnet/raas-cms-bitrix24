<?php
namespace RAAS\CMS;

use RAAS\Application;

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'on';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/resources/Controller_Cron.php';
$GLOBALS['bitrix24'] = array(
    'domain' => '',
    'login' => '',
    'password' => '',
    'webhook' => '',
);
Application::i()->run('cron');
$newSQL = file_get_contents(__DIR__ . '/resources/test.sql');
Application::i()->SQL->query($newSQL);
echo "Data loaded\n";
