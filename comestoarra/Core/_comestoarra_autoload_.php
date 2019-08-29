<?php namespace Core\Comestoarra;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Core/_comestoarra_autoload_.php
 * @package     Bootstrap Codecanyon Products
 * @company     Comestoarra Labs <labs@comestoarra.com>
 * @programmer  Rizki Wisnuaji, drg., M.Kom. <rizkiwisnuaji@comestoarra.com>
 * @copyright   2016 Comestoarra Labs. All Rights Reserved.
 * @license     http://codecanyon.net/licenses
 * @version     Release: @1.1@
 * @framework   http://slimframework.com
 *
 *
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 * This file may not be redistributed in whole or significant part.
**/


use Helpers\Huge\Core\HugeSession as ComestoarraSessionInit;
use Slim\Slim as ComestoarraApp;
use Slim\Log as ComestoarraLog;
use Slim\Views\Twig as ComestoarraTwig;
use Slim\Views\TwigExtension as ComestoarraTwigExtension;
use Flynsarmy\SlimMonolog\Log\MonologWriter as ComestoarraMonologWriter;
use Monolog\Handler\StreamHandler as ComestoarraStreamHandler;

require_once realpath("comestoarra/Core/Constant.php");

/*
|--------------------------------------------------------------------------
| Init session, set logger path, and set config array
|--------------------------------------------------------------------------
|
| Descriptions
|
*/
ComestoarraSessionInit::init();

$comestoarra['config'] = array(
    'mode'                  => ENVIRONMENT,
    'routes.case_sensitive' => true,
    'templates.path'        => realpath('comestoarra/Views'),
    'cache.path'            => realpath('tmp/cache/templates/')
);

/*
|--------------------------------------------------------------------------
| Register auto load
|--------------------------------------------------------------------------
|
| Descriptions
|
*/
ComestoarraApp::registerAutoloader();

$app = new ComestoarraApp( $comestoarra['config'] );

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app)
{
    $loggerConfig = new ComestoarraMonologWriter(array(
        'handlers' => array(
            new ComestoarraStreamHandler( 'tmp/logs/'.date('Y-m-d').'.log' ),
        ),
    ));
    $app->config(array(
        'log.enable'    => true,
        'debug'         => false,
        'log.level'     => ComestoarraLog::WARN,
        'log.writer'    => $loggerConfig,
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app)
{
    $app->config(array(
        'log.enable'    => false,
        'debug'         => true
    ));
});

/*
|--------------------------------------------------------------------------
| Twig
|--------------------------------------------------------------------------
|
| Descriptions
|
*/
$app->view = new ComestoarraTwig();
$app->view->setTemplatesDirectory( $comestoarra['config']['templates.path'] );

$view = $app->view();
$view->parserOptions = ['debug' => true, 'cache' => $comestoarra['config']['cache.path']];
$view->parserExtensions = [new ComestoarraTwigExtension];

/*
|--------------------------------------------------------------------------
| Set required files
|--------------------------------------------------------------------------
|
| Descriptions
|
*/
require_once realpath("comestoarra/Core/Route.php");

/*
| ============================================================================================================ |
|   kkk      kkk      ooooo       dddddddd         eeeeeeeeee   kkk      kkk      ooooo            ooooo       |
|   kkk     kkk     ooooooooo     ddddddddddd      eeeeeeeeee   kkk     kkk     ooooooooo        ooooooooo     | 
|   kkk    kkk     ooo     ooo    ddd      ddd     eee          kkk    kkk     ooo     ooo      ooo     ooo    |
|   kkk   kkk     oooo     oooo   ddd       ddd    eee          kkk   kkk     oooo     oooo    oooo     oooo   |
|   kkk  kkk      oooo     oooo   ddd        ddd   eee          kkk  kkk      oooo     oooo    oooo     oooo   |
|   kkkkkkkk      oooo     oooo   ddd        ddd   eeeeeeeeee   kkkkkkkk      oooo     oooo    oooo     oooo   |
|   kkk  kkk      oooo     oooo   ddd        ddd   eee          kkk  kkk      oooo     oooo    oooo     oooo   |
|   kkk   kkk     oooo     oooo   ddd       ddd    eee          kkk   kkk     oooo     oooo    oooo     oooo   |
|   kkk    kkk     ooo     ooo    ddd      ddd     eee          kkk    kkk     ooo     ooo      ooo     ooo    |
|   kkk     kkk     ooooooooo     dddddddddd       eeeeeeeeee   kkk     kkk     ooooooooo        ooooooooo     |
|   kkk      kkk      ooooo       ddddddd          eeeeeeeeee   kkk      kkk      ooooo            ooooo       |
| ============================================================================================================ |
*/
