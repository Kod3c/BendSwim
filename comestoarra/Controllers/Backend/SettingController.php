<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/SettingController.php
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
use Helpers\GlobalHelper;
use Helpers\Huge\Core\HugeAuth;
use Helpers\Huge\Core\HugeSession;
use Helpers\LanguageHelper;
use Models\Backend\AuditModel;
use Models\Backend\SettingModel;
use Models\Backend\UserModel;

class SettingController extends GlobalController
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
        $this->global_settings  = UserModel::checkUserRolePermission( 'global_settings' );
        $this->audit_trails     = UserModel::checkUserRolePermission( 'audit_trails' );

    }

    public function backendAllAudit()
    {

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['PERMISSION']                 = $this->audit_trails;
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['AUDIT_ACTIVE']                = "active";
        $this->data['CONTENT']                    = "Welcome !";
        $this->data['STICKY_NAV_LOWER']           = TRUE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['ALL_AUDIT']                  = AuditModel::getAllAudit();

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/Setting/AllAuditTrails.twig', $this->data);

    }

    public function backendGlobalSetting()
    {
        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['PERMISSION']                 = $this->global_settings;
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['SETTING_ACTIVE']             = "active";
        $this->data['CONTENT']                    = "Welcome !";
        $this->data['STICKY_NAV_LOWER']           = TRUE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['BROWSER_STATS']              = SettingModel::getBrowserStatistics();

        $this->data['ALL_SETTING']                 = SettingModel::getAllSetting();
        $this->data['MAX_SIZE']                    = ini_get('post_max_size');

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/Setting/GlobalSetting.twig', $this->data);

    }

    public function backendActionUpdateGlobalSetting()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            if( empty ( $this->app->request()->post( 'site_url' ) ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_URL' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            elseif( empty ( $this->app->request()->post( 'site_name' ) ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_NAME' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            elseif( empty ( $this->app->request()->post( 'site_title' ) ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_TITLE' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            elseif( empty ( $this->app->request()->post( 'owner_name' ) ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_OWNER_NAME' ) );

            elseif( ! filter_var( $this->app->request()->post( 'owner_email' ), FILTER_VALIDATE_EMAIL ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_OWNER_EMAIL' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            elseif( empty ( $this->app->request()->post( 'upload_path' ) ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_UPLOAD_PATH' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            elseif( empty ( $this->app->request()->post( 'files_allowed' ) ) ) :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_FILES_ALLOWED' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            else :

                if ( substr ( $this->app->request()->post( 'site_url' ), -1 ) != '/' ) :

                    $site_url = $this->app->request()->post( 'site_url' ).'/';

                else :

                    $site_url = $this->app->request()->post( 'site_url' );

                endif;

                if ( substr( $this->app->request()->post( 'upload_path' ), -1 ) != '/' ) :

                    $upload_path = $this->app->request()->post( 'upload_path' ).'/';

                else :

                    $upload_path = $this->app->request()->post( 'upload_path' );

                endif;

                if ( ! is_dir ( $upload_path ) ) :

                    //CREATE DIRECTORY
                    mkdir( $upload_path, 0777 );

                endif;

                $saveGlobalSetting = SettingModel::saveGlobalSetting( $this->app->request()->post( 'id' ), $site_url, $this->app->request()->post( 'site_name' ), $this->app->request()->post( 'site_title' ), $this->app->request()->post( 'meta_desc' ), $this->app->request()->post( 'meta_key' ), $this->app->request()->post( 'owner_name' ), $this->app->request()->post( 'owner_address' ), $this->app->request()->post( 'owner_email' ), $this->app->request()->post( 'owner_phone' ), $upload_path, $this->app->request()->post( 'files_allowed' ), HugeSession::get("user_id") );

                if ( $saveGlobalSetting ) :

                    $this->app->flash( 'success', LanguageHelper::get( 'SETTING_SUCCESS_POST_ACTION' ) );
                    $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

                else :

                    $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_POST_ACTION' ) );
                    $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

                endif;

            endif;

        endif;

    }

    public function backendActionUpdateMailSetting()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

                $saveMailSetting = SettingModel::saveMailSetting( $this->app->request()->post( 'id' ), $this->app->request()->post( 'mail_engine' ), $this->app->request()->post( 'mail_used_smtp' ), $this->app->request()->post( 'mail_smtp_host' ), $this->app->request()->post( 'mail_smtp_auth' ), $this->app->request()->post( 'mail_smtp_username' ), $this->app->request()->post( 'mail_smtp_password' ), $this->app->request()->post( 'mail_smtp_port' ), $this->app->request()->post( 'mail_smtp_encryption' ), $this->app->request()->post( 'mail_sendgrid_api' ), HugeSession::get("user_id") );

            if ( $saveMailSetting ) :

                $this->app->flash( 'success', LanguageHelper::get( 'SETTING_SUCCESS_POST_ACTION' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            else :

                $this->app->flash( 'error', LanguageHelper::get( 'SETTING_ERROR_POST_ACTION' ) );
                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            endif;

        endif;

    }

    public function backendActionUpdateGlobalLogo()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            //$storage = new \Upload\Storage\FileSystem('/path/to/directory');
            if ( substr( $this->uploadPath, -1 ) != '/' ) :

                $upload_path = $this->uploadPath.'/';

            else :

                $upload_path = $this->uploadPath;

            endif;

            if ( ! is_dir ( $upload_path ) ) :

                //CREATE DIRECTORY
                mkdir( $upload_path, DIR_WRITE_MODE );

            endif;

            $storage = new \Upload\Storage\FileSystem( $upload_path );
            $file = new \Upload\File( 'logo_file', $storage );

            // Optionally you can rename the file on upload
            $new_filename = uniqid();
            $file->setName( 'logo_' . $new_filename );

            // Validate file upload
            // MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
            $file->addValidations(array(

                new \Upload\Validation\Mimetype( explode( ",", $this->filesAllowed ) ),

                //You can also add multi mimetype validation
                //new \Upload\Validation\Mimetype(array('image/png', 'image/gif'))

                // Ensure file is no larger than 5M (use "B", "K", M", or "G")
                new \Upload\Validation\Size( MAX_UPLOAD_SIZE )
            ));

            // Access data about the file that has been uploaded
            $app_logo = array(
                'name'       => $file->getNameWithExtension(),
                'extension'  => $file->getExtension(),
                'mime'       => $file->getMimetype(),
                'size'       => $file->getSize(),
                'md5'        => $file->getMd5(),
                'dimensions' => $file->getDimensions()
            );

            // Try to upload file
            try {
                // Success!
                $file->upload();
            } catch ( \Exception $e ) {
                // Fail!
                $errors = $file->getErrors();
            }

            if ( ! empty ( $errors ) ) :

                foreach ( $errors as $key => $error ) :

                    $this->app->flash( 'error', var_export( $error, true ) );

                endforeach;

                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            else :

                SettingModel::saveLogoSetting( $this->app->request()->post( 'id' ), $app_logo['name'], HugeSession::get("user_id") );

                $this->app->flash( 'success', LanguageHelper::get( 'SETTING_SUCCESS_POST_ACTION' ) );

                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            endif;

        endif;
    }

    public function backendActionUpdateGlobalFavicon()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            //$storage = new \Upload\Storage\FileSystem('/path/to/directory');
            if ( substr( $this->uploadPath, -1 ) != '/' ) :

                $upload_path = $this->uploadPath.'/';

            else :

                $upload_path = $this->uploadPath;

            endif;

            if ( ! is_dir ( $upload_path ) ) :

                //CREATE DIRECTORY
                mkdir( $upload_path, DIR_WRITE_MODE );

            endif;

            $storage = new \Upload\Storage\FileSystem( $upload_path );
            $file = new \Upload\File( 'favicon_file', $storage );

            // Optionally you can rename the file on upload
            $new_filename = uniqid();
            $file->setName( 'favicon_' . $new_filename );

            // Validate file upload
            // MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
            $file->addValidations(array(

                new \Upload\Validation\Mimetype( explode( ",", $this->filesAllowed ) ),

                //You can also add multi mimetype validation
                //new \Upload\Validation\Mimetype(array('image/png', 'image/gif'))

                // Ensure file is no larger than 5M (use "B", "K", M", or "G")
                new \Upload\Validation\Size( MAX_UPLOAD_SIZE )
            ));

            // Access data about the file that has been uploaded
            $app_favicon = array(
                'name'       => $file->getNameWithExtension(),
                'extension'  => $file->getExtension(),
                'mime'       => $file->getMimetype(),
                'size'       => $file->getSize(),
                'md5'        => $file->getMd5(),
                'dimensions' => $file->getDimensions()
            );

            // Try to upload file
            try {
                // Success!
                $file->upload();
            } catch ( \Exception $e ) {
                // Fail!
                $errors = $file->getErrors();
            }

            if ( ! empty ( $errors ) ) :

                foreach ( $errors as $key => $error ) :

                    $this->app->flash( 'error', var_export( $error, true ) );

                endforeach;

                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            else :

                SettingModel::saveFaviconSetting( $this->app->request()->post( 'id' ), $app_favicon['name'], HugeSession::get("user_id") );

                $this->app->flash( 'success', LanguageHelper::get( 'SETTING_SUCCESS_POST_ACTION' ) );

                $this->app->response()->redirect( $this->app->urlFor( 'globalSetting' ) );

            endif;

        endif;

    }

    public function backendAuditDatatable()
    {

        if ( $this->app->request()->isAjax() ) :

            AuditModel::getAuditDatatable();

        endif;

    }

    public function backendTrimAllAudit()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AuditModel::trimAllAudit();

            $this->app->flash( 'success', LanguageHelper::get( 'TRIM_AUDIT_SUCCESS' ) );

            $this->app->response()->redirect( $this->app->urlFor( 'allAuditTrails' ) );

        endif;

    }

    public function backendDeleteAllAudit()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AuditModel::deleteAllAudit();

            $this->app->flash( 'success', LanguageHelper::get( 'CLEAR_AUDIT_SUCCESS' ) );

            $this->app->response()->redirect( $this->app->urlFor( 'allAuditTrails' ) );

        endif;

    }

}