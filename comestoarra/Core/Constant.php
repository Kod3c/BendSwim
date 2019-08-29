<?php

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Core/Constant.php
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

require_once "Database/Config/Credentials.php";

if ( file_exists ( realpath("cdn/webservice/_comestoarra_connector_.php") ) ) :

    include_once realpath("cdn/webservice/_comestoarra_connector_.php");

endif;

/*
 *---------------------------------------------------------------
 * DEFAULT LANGUAGE
 *---------------------------------------------------------------
 */
define( 'DEFAULT_LANGUAGE', 'EN' ); // 'EN, ID, ...'

/*
 *---------------------------------------------------------------
 * ALL ASSETS AND COMPONENTS DIR
 *---------------------------------------------------------------
 */
define( 'BASE_DIR', 'http' . ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 's' : '') . '://'.$_SERVER['HTTP_HOST'].str_replace( '//','/',dirname( $_SERVER['SCRIPT_NAME'] ).'/') );

define( 'PUBLIC_DIR', BASE_DIR . 'public/' );

define( 'PUBLIC_ASSETS', BASE_DIR . 'public/assets/' );

define( 'FRONTEND_ASSETS_CSS', BASE_DIR . 'public/assets/css/frontend/' );

define( 'FRONTEND_ASSETS_JS', BASE_DIR . 'public/assets/js/frontend/' );

define( 'FRONTEND_ASSETS_IMG', BASE_DIR . 'public/assets/img/frontend/' );

define( 'BACKEND_ASSETS_CSS', BASE_DIR . 'public/assets/css/backend/' );

define( 'BACKEND_ASSETS_JS', BASE_DIR . 'public/assets/js/backend/' );

define( 'BACKEND_ASSETS_IMG', BASE_DIR . 'public/assets/img/backend/' );

define( 'GLOBAL_ASSETS_CSS', BASE_DIR . 'public/assets/css/global/' );

define( 'GLOBAL_ASSETS_JS', BASE_DIR . 'public/assets/js/global/' );

define( 'GLOBAL_ASSETS_IMG', BASE_DIR . 'public/assets/img/global/' );

define( 'GLOBAL_COMPONENTS', BASE_DIR . 'public/components/' );

define( 'BOOTSTRAP_COMPONENT_CSS', BASE_DIR . 'public/components/bootstrap/dist/css/bootstrap.min.css' );

define( 'BOOTSTRAP_COMPONENT_JS', BASE_DIR . 'public/components/bootstrap/dist/js/bootstrap.min.js' );

define( 'JQUERY_COMPONENT', BASE_DIR . 'public/components/jquery/dist/jquery.min.js' );

define( 'KNOCKOUT_COMPONENT', BASE_DIR . 'public/components/knockout/dist/knockout.js' );

define( 'FULLCALENDAR_CSS', BASE_DIR . 'public/components/fullcalendar/dist/fullcalendar.css' );

define( 'FULLCALENDAR_PRINT_CSS', BASE_DIR . 'public/components/fullcalendar/dist/fullcalendar.print.css' );

define( 'FULLCALENDAR_JS', BASE_DIR . 'public/components/fullcalendar/dist/fullcalendar.min.js' );

define( 'FULLCALENDAR_MOMENT_JS', BASE_DIR . 'public/components/moment/min/moment.min.js' );

/*
 *---------------------------------------------------------------
 * DEVELOPMENT DEBUGGER
 *---------------------------------------------------------------
 */
define( 'COMESTOARRA_DEBUGGER', 'whoops' ); // 'tracy OR whoops'

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     debug
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
define( 'ENVIRONMENT', 'production' );

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if ( defined( 'ENVIRONMENT' ) ) :

    switch ( ENVIRONMENT ) :

        case 'development':

            error_reporting( E_ALL & ~E_NOTICE );

            ini_set( 'display_errors', TRUE );

            ini_set('display_startup_errors', TRUE);

            break;

        case 'debug':

            error_reporting( E_ALL | E_STRICT );

            ini_set( 'display_errors', TRUE );

            ini_set('display_startup_errors', TRUE);

            break;

        case 'production':

            error_reporting( 0 );

            ini_set( 'display_errors', FALSE );

            break;

        default:

            exit( 'The environment is not set correctly. ENVIRONMENT = '.ENVIRONMENT.'.' );

    endswitch;

endif;

/*
|--------------------------------------------------------------------------
| TIMEZONE
|--------------------------------------------------------------------------
|
| set timezone for timestamps etc
|
*/
define( 'TIMEZONE', 'Asia/Jakarta' );

date_default_timezone_set( TIMEZONE ); // !

if ( ini_get('date.timezone') == '' ) :

    date_default_timezone_set( 'UTC' );

endif;

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

define('MAX_UPLOAD_SIZE', '1M');

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| Huge Config
|--------------------------------------------------------------------------
|
| Huge configurations
|
*/
define('HUGE_PATH_AVATARS',         BASE_DIR . 'public/avatars/');
define('HUGE_PATH_AVATARS_PUBLIC',  BASE_DIR . 'public/avatars/');

define('DIR_AVATARS',               realpath( dirname(__FILE__) . '/../../' ) . '/public/avatars/' );
define('DIR_AVATARS_PUBLIC',        realpath( dirname(__FILE__) . '/../../' ) . '/public/avatars/' );



/**
 * Configuration for: Captcha size
 * The currently used Captcha generator (https://github.com/Gregwar/Captcha) also runs without giving a size,
 * so feel free to use ->build(); inside CaptchaModel.
 */
define('HUGE_CAPTCHA_WIDTH',         359);
define('HUGE_CAPTCHA_HEIGHT',        100);
/**
 * Configuration for: Cookies
 * 1209600 seconds = 2 weeks
 * COOKIE_PATH is the path the cookie is valid on, usually "/" to make it valid on the whole domain.
 * @see http://stackoverflow.com/q/9618217/1114320
 * @see php.net/manual/en/function.setcookie.php
 */
define('HUGE_COOKIE_RUNTIME',                      1209600);
define('HUGE_COOKIE_PATH',                             '/');
define('HUGE_COOKIE_DOMAIN',                            "");
define('HUGE_COOKIE_SECURE',                         false);
define('HUGE_COOKIE_HTTP',                            true);
define('HUGE_SESSION_RUNTIME',                      604800);
define('HUGE_ENCRYPTION_KEY',        '6#x0gÊìf^25cL1f$08&');
define('HUGE_HMAC_SALT',           '8qk9c^4L6d#15tM8z7n0%');
/**
 * Configuration for: Avatars/Gravatar support
 * Set to true if you want to use "Gravatar(s)", a service that automatically gets avatars pictures via using email
 * addresses of users by requesting images from the gravatar.com API. Set to false to use own locally saved avatars.
 * AVATAR_SIZE set the pixel size of avatars/gravatars (will be 44x44 by default). Avatars are always squares.
 * AVATAR_DEFAULT_IMAGE is the default image in public/avatars/
 */
define('HUGE_USE_GRAVATAR',                    false);
define('HUGE_GRAVATAR_DEFAULT_IMAGESET',        'mm');
define('HUGE_GRAVATAR_RATING',                  'pg');
define('HUGE_AVATAR_SIZE',                        300);
define('HUGE_AVATAR_JPEG_QUALITY',                100);
define('HUGE_AVATAR_DEFAULT_IMAGE',    'default.jpg');
define('HUGE_AVATAR_MAX_SIZE',               5000000);
/**
 * Configuration for: Email server credentials
 *
 * Here you can define how you want to send emails.
 * If you have successfully set up a mail server on your linux server and you know
 * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
 * and fill in your SMTP provider account data.
 *
 * EMAIL_USED_MAILER: Check Mail class for alternatives
 * EMAIL_USE_SMTP: Use SMTP or not
 * EMAIL_SMTP_AUTH: leave this true unless your SMTP service does not need authentication
 */
define('HUGE_EMAIL_USED_MAILER',          'phpmailer');
define('HUGE_EMAIL_USE_SMTP',                   false);
define('HUGE_EMAIL_SMTP_HOST',             'yourhost');
define('HUGE_EMAIL_SMTP_AUTH',                   true);
define('HUGE_EMAIL_SMTP_USERNAME',      'yourusername');
define('HUGE_EMAIL_SMTP_PASSWORD',      'yourpassword');
define('HUGE_EMAIL_SMTP_PORT',                     465);
define('HUGE_EMAIL_SMTP_ENCRYPTION',             'ssl');
/**
 * Configuration for: Email content data
 */
define('HUGE_EMAIL_PASSWORD_RESET_URL',             'manage/login/verifypasswordreset');
define('HUGE_EMAIL_PASSWORD_RESET_FROM_EMAIL',                  'no-reply@comestoarra.com');
define('HUGE_EMAIL_PASSWORD_RESET_FROM_NAME',                             'My Account');
define('HUGE_EMAIL_PASSWORD_RESET_SUBJECT',            'Password reset');
define('HUGE_EMAIL_PASSWORD_RESET_CONTENT',      'Please click on this link to reset your password: ');
define('HUGE_EMAIL_VERIFICATION_URL',                            'manage/login/verify');
define('HUGE_EMAIL_VERIFICATION_FROM_EMAIL',                    'no-reply@comestoarra.com');
define('HUGE_EMAIL_VERIFICATION_FROM_NAME',                               'My Account');
define('HUGE_EMAIL_VERIFICATION_SUBJECT',          'Account activation');
define('HUGE_EMAIL_VERIFICATION_CONTENT',       'Please click on this link to activate your account: ');