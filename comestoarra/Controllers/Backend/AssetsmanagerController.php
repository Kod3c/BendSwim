<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/AssetsmanagerController.php
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

use Controllers\GlobalController;
use Helpers\Huge\Core\HugeAuth;
use Helpers\Huge\Core\HugeSession;

class AssetsmanagerController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

        HugeAuth::checkAuthentication();

    }

    public function backendAssetManager()
    {

        $this->data['CONTENT']                    = "Success Login !";
        $this->data['ASSETSMANAGER_ACTIVE']         = "active";
        $this->data['STICKY_NAV_LOWER']           = TRUE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );
        
        $this->app->render('Backend/Content/Assetsmanager/Index.twig', $this->data);

    }
    
}
