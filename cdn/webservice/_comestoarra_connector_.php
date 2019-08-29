<?php

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	cdn/webservice/_comestoarra_connector_.php
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
 

/*
|--------------------------------------------------------------------------
| Optional security
|--------------------------------------------------------------------------
|
| if set to true only those will access RF whose url contains the access key(akey) like:
| <input type="button" href="../filemanager/dialog.php?field_id=imgField&lang=en_EN&akey=myPrivateKey" value="Files">
| in tinymce a new parameter added: filemanager_access_key:"myPrivateKey"
| example tinymce config:
|
| tiny init ...
| external_filemanager_path:"../filemanager/",
| filemanager_title:"Filemanager" ,
| filemanager_access_key:"myPrivateKey" ,
| ...
|
*/

define('USE_ACCESS_KEYS', true); // TRUE or FALSE
 
 /*
 *---------------------------------------------------------------
 * APP NAME REFFERENCE FOR FILE MANAGER OUTSIDE COMESTOARRA
 *---------------------------------------------------------------
 */
define( 'APP_ROOT_FOLDER', '' );

/*
 *---------------------------------------------------------------
 * SECRET KEY FOR FILE MANAGER
 *---------------------------------------------------------------
 */
define( 'CDN_SECRET_KEY', '213123hgljknuy1o_.9900-nNnjhkOPasdjjgd708hd32de0' );