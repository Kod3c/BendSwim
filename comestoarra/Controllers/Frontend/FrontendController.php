<?php namespace Controllers\Frontend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Frontend/FrontendController.php
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
use Models\Entity\Huge\User;
use Helpers\GlobalHelper;
use Helpers\SessionHelper;

class FrontendController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

    }
    
    public function frontendHome()
    {

        $this->data['NAME']                       = $this->siteName;
        $this->data['TITLE']                      = $this->siteTitle;
        $this->data['TIME']                       = $this->timezone->now( TIMEZONE );
        
        $this->data['SCRIPT']                         = <<<SCRIPT


SCRIPT;
        
    	$this->app->render('Frontend/'. self::FRONTEND_TEMPLATE .'/Content/Homepage.twig', $this->data);


    }
    
}