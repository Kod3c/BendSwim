<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/CalendarController.php
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
use Models\Backend\AppointmentModel;
use Models\Backend\InstructorModel;

class CalendarController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

        HugeAuth::checkAuthentication();

    }

    public function backendCalendarManager()
    {

        $this->data['CONTENT']                    = "Success Login !";
        $this->data['CALENDAR_ACTIVE']            = "active";
        $this->data['STICKY_NAV_LOWER']           = TRUE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['ALL_Instructor']                = InstructorModel::getAllInstructor();

        $this->data['ALL_APPOINTMENT']            = AppointmentModel::getAllAppointment();

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/Calendar/Index.twig', $this->data);

    }

}
