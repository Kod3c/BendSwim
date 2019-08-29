<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/InstructorController.php
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
use Helpers\LanguageHelper;
use Models\Backend\AppointmentModel;
use Models\Backend\AuditModel;
use Models\Backend\InstructorModel;
use Models\Backend\RegistrationModel;
use Models\Backend\UserModel;

class InstructorController extends GlobalController
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
        $this->permission = UserModel::checkUserRolePermission( 'Instructor' );

    }

    public function backendAllInstructor()
    {

        if ( isset ( $this->permission ) ) :

            $this->data['PERMISSION']             = $this->permission;

        endif;

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['Instructor_ACTIVE']             = "active";
        $this->data['STICKY_NAV_LOWER']           = FALSE;
        $this->data['CONTENT_FLUID']              = TRUE;
        $this->data['time']                       = $this->timezone->now( TIMEZONE );

        $this->data['ALL_Instructor']                = InstructorModel::getAllInstructor();

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );


        $this->app->render('Backend/Content/Instructor/Index.twig', $this->data);

    }

    public function backendActionAddAuthInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            RegistrationModel::registerNewInstructor();

            $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );

        endif;

    }

    public function backendInstructorProfile( $Instructor_id )
    {
        if ( isset ( $this->permission ) ) :

            $this->data['PERMISSION']             = $this->permission;

        endif;

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['Instructor_ACTIVE']             = "active";
        $this->data['STICKY_NAV_LOWER']           = FALSE;
        $this->data['CONTENT_FLUID']              = TRUE;
        $this->data['HUGE_AVATAR_MAX_SIZE']       = HUGE_AVATAR_MAX_SIZE;
        $this->data['PUBLIC_DIR']                 = PUBLIC_DIR;

        $this->data['Instructor_PROFILE']            = InstructorModel::getProfileOfInstructor( $Instructor_id );

        $this->data['Swimmer_APPOINTMENT']        = InstructorModel::getAppointmentDataOfSwimmer( $Instructor_id );

        $this->data['ALL_APPOINTMENT_Instructor']    = AppointmentModel::getAllInstructorAppointment( $Instructor_id );


        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/Instructor/InstructorProfile.twig', $this->data);
    }

    public function backendActionUpdateProfile()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::updateProfileDetailsOfInstructor(
                $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'first_name' ), $this->app->request()->post( 'last_name' ), $this->app->request()->post( 'address' ), $this->app->request()->post( 'bio' ), $this->app->request()->post( 'specialties' ), $this->app->request()->post( 'phone' ), $this->app->request()->post( 'cellphone' ), $this->app->request()->post( 'birthdate' )
            );
            
            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );
                
            elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionChangePassword()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::setNewPassword(
                $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'user_name' ), $this->app->request()->post( 'user_password_new' ), $this->app->request()->post( 'user_password_repeat' )
            );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );
                
            elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionChangeEmail()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::editInstructorEmail( $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'user_name' ), $this->app->request()->post( 'user_email' ) );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );
                
            elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionChangeUsername()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::editInstructorUserName( $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'user_email' ), $this->app->request()->post( 'user_name' ) );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );
                
            elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionUploadAvatar()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            //$storage = new \Upload\Storage\FileSystem('/path/to/directory');
            $upload_path = "public/avatars/Instructors/";

            if ( ! is_dir ( $upload_path ) ) :

                //CREATE DIRECTORY
                mkdir( $upload_path, DIR_WRITE_MODE );

            endif;

            $storage = new \Upload\Storage\FileSystem( $upload_path );
            $file = new \Upload\File( 'avatar_file', $storage );

            // Optionally you can rename the file on upload
            $new_filename = uniqid();
            $file->setName( 'avatar_' . $this->app->request()->post( 'first_name' ) . '_' . $this->app->request()->post( 'last_name' ) . '_' . $new_filename );

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
            $avatar = array(
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

                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );

            else :

                InstructorModel::saveInstructorAvatar( $this->app->request()->post( 'user_email' ), $this->app->request()->post( 'Instructor_id' ), $avatar['name'] );

                if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                    $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );
                
                elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :
                    
                    $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                        
                endif;

            endif;

        endif;

    }

    public function backendActionUpdateTimetable()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::updateTimeTable( $this->app->request()->post( 'Instructor_id' ) );
            
            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) . $this->app->request()->post( 'Instructor_id' ) );
                
            elseif ( $this->data['PROVIDER'] == 'BSSInstructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionAddInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::registerNewInstructor();

            $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );

        endif;

    }

    public function backendActionInstructorTimeTable()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            InstructorModel::updateTimeTable( $this->app->request()->post( 'Instructor_id' ) );
            $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );

        endif;

    }

}