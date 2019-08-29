<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/UserController.php
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
use Helpers\Huge\Core\HugeAvatar;
use Helpers\Huge\Core\HugeSession;
use Helpers\GlobalHelper;
use Helpers\LanguageHelper;
use Models\Backend\AppointmentModel;
use Models\Backend\AuditModel;
use Models\Backend\InstructorModel;
use Models\Backend\LoginModel;
use Models\Backend\SwimmerModel;
use Models\Backend\RegistrationModel;
use Models\Backend\UserModel;
use Models\Entity\Huge\User;

class UserController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

        HugeAuth::checkAuthentication();

        $this->permission    = UserModel::checkUserRolePermission( 'user_managers' );

    }

    public function backendAllUser()
    {

        if ( isset ( $this->permission ) ) :

            $this->data['PERMISSION']             = $this->permission;

        endif;

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['USER_ACTIVE']                = "active";
        $this->data['CONTENT']                    = "Welcome !";
        $this->data['STICKY_NAV_LOWER']           = TRUE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['ALL_USERS']                  = UserModel::getPublicProfilesOfAllUsers();
        $this->data['ALL_ROLES']                  = UserModel::getAllUserRoles();
        $this->data['ALL_ADMIN_ROLES']            = UserModel::getAllUserAdminRoles();

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/Setting/AllUser.twig', $this->data);

    }

    public function backendUserDatatable()
    {

        if ( $this->app->request()->isAjax() ) :

            UserModel::getUserDatatable();

        endif;

    }

    public function backendUserProfile( $id )
    {

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['USER_ACTIVE']                = "active";
        $this->data['STICKY_NAV_LOWER']           = FALSE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['USER_PROFILE']               = UserModel::getPublicProfileOfUser( $id );

        $this->data['USER_AUDIT']                 = AuditModel::getUserAudit( $id );

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/User/UserProfile.twig', $this->data);
    }

    public function backendActionAddUser()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            RegistrationModel::registerNewAdmin();
            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendActionAddRole()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::registerNewRole();
            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendProfile()
    {
        if ( isset ( $this->permission ) ) :

            $this->data['PERMISSION']             = $this->permission;

        endif;

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['STICKY_NAV_LOWER']           = TRUE;
        $this->data['CONTENT_FLUID']              = TRUE;
        $this->data['MYPROFILE_ACTIVE']           = "active";
        $this->data['HUGE_AVATAR_MAX_SIZE']       = HUGE_AVATAR_MAX_SIZE;
        $this->data['PUBLIC_DIR']                 = PUBLIC_DIR;
        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        if ( $this->data['PROVIDER'] == 'DEFAULT' ) :

            $this->data['USER_PROFILE']               = UserModel::getPublicProfileOfUser( $this->data['ID'] );

            $this->data['USER_AUDIT']                 = AuditModel::getUserProfileAudit( $this->data['ID'] );

            $this->app->render('Backend/Content/User/Profile.twig', $this->data);

        elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :

            $this->data['Instructor_PROFILE']            = InstructorModel::getMyProfileOfInstructor( $this->data['USERNAME'] );

            $this->data['Swimmer_APPOINTMENT']        = InstructorModel::getMyAppointmentDataOfSwimmer( $this->data['USERNAME'] );

            $this->data['ALL_APPOINTMENT_Instructor']    = AppointmentModel::getAllInstructorMyAppointment( $this->data['USERNAME'] );

            $this->data['USER_AUDIT']                 = AuditModel::getUserProfileAudit( $this->data['ID'] );

            $this->app->render('Backend/Content/Instructor/Profile.twig', $this->data);

        elseif ( $this->data['PROVIDER'] == 'BSSSwimmer Registration' ) :

            $this->data['Swimmer_PROFILE']            = SwimmerModel::getMyProfileOfSwimmer( $this->data['USERNAME'] );

            $this->data['Swimmer_APPOINTMENT']        = AppointmentModel::getMyAppointmentDataOfSwimmer( $this->data['USERNAME'] );

            $this->data['ALL_Instructor']                = InstructorModel::getAllInstructor();

            $this->data['USER_AUDIT']                 = AuditModel::getUserProfileAudit( $this->data['ID'] );

            $this->app->render('Backend/Content/Swimmer/Profile.twig', $this->data);

        endif;

    }

    public function backendActionUpdateProfile()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::updateProfileDetailsOfUser(
                $this->app->request()->post( 'user_id' ), $this->app->request()->post( 'first_name' ), $this->app->request()->post( 'last_name' ), $this->app->request()->post( 'birth_date' ), $this->app->request()->post( 'phone' ), $this->app->request()->post( 'bio' ), $this->app->request()->post( 'address' )
            );

            AuditModel::saveAuditTrails( $this->data['ID'], ucfirst( $this->data['USERNAME'] ) . ' ' . LanguageHelper::get( 'AUDIT_SUCCESS_UPDATE_PROFILE' ) . ' ' . LanguageHelper::get( 'GLOBAL_FROM' ) . ' : ' . $this->ip );

            $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );

        endif;

    }

    public function backendActionChangePassword()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::setNewPassword(
                $this->app->request()->post( 'user_name' ), $this->app->request()->post( 'user_password_new' ), $this->app->request()->post( 'user_password_repeat' )
            );

            AuditModel::saveAuditTrails( $this->data['ID'], ucfirst( $this->data['USERNAME'] ) . ' ' . LanguageHelper::get( 'AUDIT_SUCCESS_CHANGE_PASSWORD' ) . ' ' . LanguageHelper::get( 'GLOBAL_FROM' ) . ' : ' . $this->ip );

            $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );

        endif;

    }

    public function backendActionChangeEmail()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::editUserEmail( $this->app->request()->post( 'user_email' ) );

            AuditModel::saveAuditTrails( $this->data['ID'], ucfirst( $this->data['USERNAME'] ) . ' ' . LanguageHelper::get( 'AUDIT_SUCCESS_CHANGE_EMAIL' ) . ' ' . LanguageHelper::get( 'GLOBAL_FROM' ) . ' : ' . $this->ip );

            $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );

        endif;

    }

    public function backendActionChangeUsername()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::editUserName( $this->app->request()->post( 'user_name' ) );

            AuditModel::saveAuditTrails( $this->data['ID'], ucfirst( $this->data['USERNAME'] ) . ' ' . LanguageHelper::get( 'AUDIT_SUCCESS_CHANGE_USERNAME' ) . ' ' . LanguageHelper::get( 'GLOBAL_FROM' ) . ' : ' . $this->ip );

            $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );

        endif;

    }

    public function backendActionUploadAvatar()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() AND $this->app->request()->isAjax() ) :

            $saveAvatar = HugeAvatar::createAvatar();

            if ($saveAvatar) {
                AuditModel::saveAuditTrails($this->data['ID'], ucfirst($this->data['USERNAME']) . ' ' . LanguageHelper::get('AUDIT_SUCCESS_CHANGE_AVATAR') . ' ' . LanguageHelper::get('GLOBAL_FROM') . ' : ' . $this->ip);
            }

            $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );

        endif;


    }

    public function backendActionSuspendUser()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData()) :

            UserModel::setAccountSuspensionStatus(
                $this->app->request()->post( 'suspension' ), $this->app->request()->post( 'user_id' )
            );

            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendActionBanUser()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::setAccountBanStatus(
                $this->app->request()->post( 'softDelete' ), $this->app->request()->post( 'user_id' )
            );

            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendActionRootUser()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::setAccountRootStatus(
                $this->app->request()->post( 'rooting' ), $this->app->request()->post( 'user_id' )
            );

            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendActionRoleUser()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::setAccountRole(
                $this->app->request()->post( 'role_id' ), $this->app->request()->post( 'user_id' )
            );

            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendUpdateRole()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::updateRole( $this->app->request()->post( 'role_id' ) );

            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

    public function backendDeleteRole()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            UserModel::deleteRole( $this->app->request()->post( 'role_id' ) );

            $this->app->response()->redirect( $this->app->urlFor( 'users' ) );

        endif;

    }

}