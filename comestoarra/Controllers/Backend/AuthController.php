<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/AuthController.php
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
use Helpers\Huge\Core\HugeRequest;
use Helpers\Huge\Core\HugeSession;
use Helpers\GlobalHelper;
use Models\Backend\LoginModel;
use Models\Backend\PasswordResetModel;
use Models\Backend\RegistrationModel;

class AuthController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function backendAuthorized()
    {

        if ( LoginModel::isUserLoggedIn() ) :

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        elseif ( ! HugeSession::userIsLoggedIn() AND HugeRequest::cookie('remember_me') ) :

            $this->app->response()->redirect( $this->app->urlFor( 'loginCookie' ) );

        else :

            $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

        endif;

    }

    public function backendLogin()
    {

        if ( LoginModel::isUserLoggedIn() ) :

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

        // $this->data['DUMP']                       = var_dump( $_SESSION );
        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['LOGIN_ACTION']               = $this->app->urlFor( 'loginAction' );
        $this->data['LOGIN_LINK']                 = $this->app->urlFor( 'login' );
        $this->data['SIGNUP_LINK']                = $this->app->urlFor( 'signup' );
        $this->data['FORGOT_LINK']                = $this->app->urlFor( 'forgot' );

        $this->app->render('Backend/Content/Auth/Login.twig', $this->data);

    }

    public function backendActionLogin()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            $comestoarraLoginCheck = LoginModel::login( GlobalHelper::valueSafe( $this->app->request()->post('user_name') ), $this->app->request()->post('user_password'), $this->app->request()->post('set_remember_me_cookie') );

            if ( $comestoarraLoginCheck ) :

                $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

            else :

                $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

            endif;

        endif;

    }

    public function backendLoginWithCookie()
    {
        // self::backendLogin();
    }

    public function backendActionPasswordReset()
    {
        
        $comestoarraSetNewPassword = PasswordResetModel::setNewPassword( $this->app->request()->post( 'captcha' ), $this->app->request()->post( 'user_name' ), $this->app->request()->post( 'user_password_reset_hash' ), $this->app->request()->post( 'user_password_new' ), $this->app->request()->post( 'user_password_repeat' ) );
        
        $this->app->response()->redirect( $this->app->urlFor( 'login' ) );
    }

    public function backendPasswordReset( $username, $hash )
    {

        if ( PasswordResetModel::verifyPasswordReset( $username, $hash ) ) :

            $this->data['LOGIN_LINK']                 = $this->app->urlFor( 'login' );
            $this->data['SIGNUP_LINK']                = $this->app->urlFor( 'signup' );
            $this->data['FORGOT_LINK']                = $this->app->urlFor( 'forgot' );
            $this->data['RESET_ACTION']               = $this->app->urlFor( 'resetPasswordAction' );
            $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
            $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
            $this->data['CAPTCHA']                    = $this->raincaptcha->getImage();
            $this->data['PUBLIC_ASSETS']              = PUBLIC_ASSETS;
            $this->data['FRONTEND_ASSETS_CSS']        = FRONTEND_ASSETS_CSS;
            $this->data['FRONTEND_ASSETS_JS']         = FRONTEND_ASSETS_JS;
            $this->data['FRONTEND_ASSETS_IMG']        = FRONTEND_ASSETS_IMG;
            $this->data['BACKEND_ASSETS_CSS']         = BACKEND_ASSETS_CSS;
            $this->data['BACKEND_ASSETS_JS']          = BACKEND_ASSETS_JS;
            $this->data['BACKEND_ASSETS_IMG']         = BACKEND_ASSETS_IMG;
            $this->data['GLOBAL_ASSETS_CSS']          = GLOBAL_ASSETS_CSS;
            $this->data['GLOBAL_ASSETS_JS']           = GLOBAL_ASSETS_JS;
            $this->data['GLOBAL_ASSETS_IMG']          = GLOBAL_ASSETS_IMG;
            $this->data['GLOBAL_COMPONENTS']          = GLOBAL_COMPONENTS;
            $this->data['BOOTSTRAP_COMPONENT_CSS']    = BOOTSTRAP_COMPONENT_CSS;
            $this->data['BOOTSTRAP_COMPONENT_JS']     = BOOTSTRAP_COMPONENT_JS;
            $this->data['JQUERY_COMPONENT']           = JQUERY_COMPONENT;
            $this->data['KNOCKOUT_COMPONENT']         = KNOCKOUT_COMPONENT;
            $this->data['USER_NAME']                  = $username;
            $this->data['HASH']                       = $hash;

            $this->app->render('Backend/Content/Auth/ResetPassword.twig', $this->data);

        else :

            $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

        endif;

    }

    public function backendActionSignup()
    {
        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :


            $registration_successful = RegistrationModel::registerNewUser();

            if ( $registration_successful ) :

                $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

            else :

                $this->app->response()->redirect( $this->app->urlFor( 'signup' ) );

            endif;

        endif;

    }

    public function backendActionSignupVerify( $id, $activation )
    {

        if ( isset ( $id ) && isset( $activation ) ) :

            RegistrationModel::verifyNewUser( $id, $activation );

            $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

        else :

            $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

        endif;

    }

    public function backendSignup()
    {

        if ( LoginModel::isUserLoggedIn() ) :

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

        $this->data['LOGIN_LINK']                 = $this->app->urlFor( 'login' );
        $this->data['SIGNUP_LINK']                = $this->app->urlFor( 'signup' );
        $this->data['FORGOT_LINK']                = $this->app->urlFor( 'forgot' );
        $this->data['SIGNUP_ACTION']              = $this->app->urlFor( 'signupAction' );
        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CAPTCHA']                    = $this->raincaptcha->getImage();

        $this->app->render('Backend/Content/Auth/Signup.twig', $this->data);
    }

    public function backendActionForgot()
    {
        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            $recover_successful = PasswordResetModel::requestPasswordReset( $this->app->request()->post( 'captcha' ), $this->app->request()->post( 'user_name_or_email' ) );

            if ( $recover_successful ) :

                $this->app->response()->redirect( $this->app->urlFor( 'login' ) );

            else :

                $this->app->response()->redirect( $this->app->urlFor( 'forgot' ) );

            endif;

        endif;

    }

    public function backendForgot()
    {

        if ( LoginModel::isUserLoggedIn() ) :

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

        $this->data['LOGIN_LINK']                 = $this->app->urlFor( 'login' );
        $this->data['SIGNUP_LINK']                = $this->app->urlFor( 'signup' );
        $this->data['FORGOT_LINK']                = $this->app->urlFor( 'forgot' );
        $this->data['FORGOT_ACTION']              = $this->app->urlFor( 'forgotAction' );
        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CAPTCHA']                    = $this->raincaptcha->getImage();

        $this->app->render('Backend/Content/Auth/Forgot.twig', $this->data);

    }

    public function backendLogout()
    {

        LoginModel::logout();

    }

}