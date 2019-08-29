<?php

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Core/Database/Config/Credentials.php
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

// DATABASE TYPE : mysql, sqlite, pgsql, sqlsrv
define( 'DB_TYPE', 'mysql' );

// HOST NAME, DEFAULT: localhost
define( 'DB_HOST', 'localhost' );

// DATABASE PREFIX
define( 'DB_PREFIX',  'cbn_' );

// DATABASE NAME
define( 'DB_NAME', 'dcas' );

// DATABASE USER
define( 'DB_USER', 'root' );

// DATABASE PASSWORD
define( 'DB_PASS', '' );

// DATABASE PORT
define( 'DB_PORT', '3306' );

// DATABASE CHARSET
define( 'DB_CHARSET', 'utf8' );

// DATABASE COLLATION, DEFAULT: utf8_unicode_ci
define( 'DB_COLLATION', 'utf8_unicode_ci' );