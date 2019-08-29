<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/DashboardController.php
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
use Helpers\CsrfHelper;
use Helpers\Huge\Core\HugeAuth;
use Helpers\Huge\Core\HugeSession;
use Models\Backend\AppointmentModel;
use Models\Backend\DashboardModel;
use Models\Backend\InstructorModel;
use Models\Backend\SwimmerModel;
use Models\Backend\SettingModel;
use Models\Backend\UserModel;
use Helpers\GlobalHelper;

class DashboardController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

        HugeAuth::checkAuthentication();

        /*
        |--------------------------------------------------------------------------
        | Comestoarra Role Permission Check
        |--------------------------------------------------------------------------
        |
        | Check role based access permission, fetching data from DB, decode
        | it and check boolean object value
        |
        */
        $this->permission = UserModel::checkUserRolePermission( 'dashboard' );

    }

    public function backendDashboard()
    {
        if ( isset ( $this->permission ) ) :

            $this->data['PERMISSION']             = $this->permission;

        endif;

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['DASHBOARD_ACTIVE']           = "active";
        $this->data['STICKY_NAV_LOWER']           = FALSE;
        $this->data['CONTENT_FLUID']              = TRUE;
        $this->data['TIME']                       = $this->timezone->now( TIMEZONE );

        $this->data['Swimmer_COUNT']              = DashboardModel::getSwimmerCount();
        $this->data['Instructor_COUNT']              = DashboardModel::getInstructorCount();
        $this->data['USER_COUNT']                 = DashboardModel::getUserCount();
        $this->data['FINISHED_APPOINTMENT_COUNT'] = DashboardModel::getFinishedAppointmentCount();
        $this->data['CANCELLED_APPOINTMENT_COUNT']= DashboardModel::getCancelledAppointmentCount();
        $this->data['TODAY_APPOINTMENT_COUNT']    = DashboardModel::getTodayAppointmentCount();
        $this->data['ALL_TODAY_APPOINTMENT']      = DashboardModel::getAllTodayAppointment();
        $this->data['ALL_Swimmer']                = SwimmerModel::getAllSwimmer();
        $this->data['ALL_Instructor']                = InstructorModel::getAllInstructor();
        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        if ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :

            $this->data['Instructor_TODAY_APPOINTMENT_COUNT']    = DashboardModel::getInstructorTodayAppointmentCount( $this->data['USERNAME'] );

            $this->data['PROFILE_Instructor']                    = InstructorModel::getMyProfileOfInstructor( $this->data['USERNAME'] );

        elseif ( $this->data['PROVIDER'] == 'BSSSwimmer Registration' ) :

            $this->data['Swimmer_TODAY_APPOINTMENT_COUNT']    = DashboardModel::getSwimmerTodayAppointmentCount( $this->data['USERNAME'] );

            $this->data['ALL_Swimmer_APPOINTMENT']            = DashboardModel::getAllSwimmerAppointment( $this->data['USERNAME'] );

            $this->data['PROFILE_Swimmer']                    = SwimmerModel::getMyProfileOfSwimmer( $this->data['USERNAME'] );

        endif;

//        $this->data['VARDUMP']                    = print_r( $_SESSION );
        

        $this->app->render('Backend/Content/Dashboard/Index.twig', $this->data);

    }

}