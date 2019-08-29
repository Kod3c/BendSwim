<?php namespace Models;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Models/GlobalModel.php
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

use Illuminate\Database\Eloquent\Model as ComestoarraEloquent;
use Illuminate\Database\Capsule\Manager as ComestoarraCapsule;

abstract class GlobalModel extends ComestoarraEloquent
{

    const DB_USERS      = "users";
    const DB_PROFILE    = "user_profile";
    const DB_MAILBOX    = "mailbox";
    const DB_ROLE       = "roles";
    const DB_B_STATS    = "browser_statistics";
    const DB_AUDIT      = "audit";
    const DB_SETTING    = "settings";
    const DB_Swimmer    = "Swimmers";
    const DB_Instructor    = "Instructors";
    const DB_APPOINTMENT    = "appointment";

    public function __construct()
    {
        parent::__construct();

        $comestoarraCapsule = new ComestoarraCapsule;
        $comestoarraCapsule->addConnection(
            array(
                'driver'    => DB_TYPE,
                'host'      => DB_HOST,
                'database'  => DB_NAME,
                'port'      => DB_PORT,
                'username'  => DB_USER,
                'password'  => DB_PASS,
                'charset'   => DB_CHARSET,
                'collation' => DB_COLLATION,
                'prefix'    => DB_PREFIX,
            )
        );
        $comestoarraCapsule->setAsGlobal();
        $comestoarraCapsule->bootEloquent();

    }

}