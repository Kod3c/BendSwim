<?php namespace Models\Backend;

use Helpers\CaptchaHelper;
use Helpers\CsrfHelper;
use Helpers\GlobalHelper;
use Helpers\Huge\Core\HugeConfig;
use Helpers\Huge\Core\HugeRequest;
use Helpers\Huge\Core\HugeSession;
use Helpers\LanguageHelper;
use Helpers\MailHelper;
use Helpers\RainCaptchaHelper;
use Models\Entity\Instructor;
use Models\Entity\Huge\User;
use Models\Entity\Swimmer;
use Models\Entity\Role;
use Slim\Slim;

class RegistrationModel
{

    public static function registerNewUser()
    {

        $user_name = strip_tags ( HugeRequest::post( 'user_name' ) );
        $user_email = strip_tags ( HugeRequest::post( 'user_email' ) );
        $user_password_new = HugeRequest::post( 'user_password_new' );
        $user_password_repeat = HugeRequest::post( 'user_password_repeat' );

        $validation_result = self::registrationInputValidation ( HugeRequest::post( 'captcha' ), $user_name, $user_password_new, $user_password_repeat, $user_email );

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( UserModel::doesUsernameAlreadyExist ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        if ( UserModel::doesEmailAlreadyExist ( $user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1 ( uniqid ( mt_rand(), true ) );

        if ( ! self::writeNewUserToDatabase ( $user_name, $user_password_hash, $user_email, time(), $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        endif;

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $user_id = UserModel::getUserIdByUsername ( $user_name );

        if ( ! $user_id ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;

        // send verification email
        if ( self::sendVerificationEmail ( $user_id, $user_email, $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED') );
            return true;

        endif;

        // if verification email sending failed: instantly delete the user
        self::rollbackRegistrationByUserId ( $user_id );
        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED') );
        return false;
    }

    public static function registerNewAdmin()
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $user_name = strip_tags ( HugeRequest::post( 'user_name' ) );
        $user_email = strip_tags ( HugeRequest::post( 'user_email' ) );
        $user_password_new = HugeRequest::post( 'user_password_new' );
        $user_password_repeat = HugeRequest::post( 'user_password_repeat' );
        $user_account_type = HugeRequest::post( 'user_account_type' );
        $user_role = HugeRequest::post( 'user_role' );

        $validation_result = self::registrationAdminInputValidation ( $user_name, $user_password_new, $user_password_repeat, $user_email );

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( UserModel::doesUsernameAlreadyExist ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        if ( UserModel::doesEmailAlreadyExist ( $user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1 ( uniqid ( mt_rand(), true ) );

        if ( ! self::writeNewAdminToDatabase ( $user_name, $user_password_hash, $user_account_type, $user_role, $user_email, time(), $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        endif;

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $user_id = UserModel::getUserIdByUsername ( $user_name );

        if ( ! $user_id ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;

        // send verification email
        if ( self::sendVerificationEmail ( $user_id, $user_email, $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED') );
            return true;

        endif;

        // if verification email sending failed: instantly delete the user
        self::rollbackRegistrationByUserId ( $user_id );
        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED') );
        return false;
    }

    public static function registerNewInstructor()
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $Instructor_id = strip_tags ( HugeRequest::post( 'Instructor_id' ) );
        $user_name = strip_tags ( HugeRequest::post( 'user_name' ) );
        $user_email = strip_tags ( HugeRequest::post( 'user_email' ) );
        $user_password_new = HugeRequest::post( 'user_password_new' );
        $user_password_repeat = HugeRequest::post( 'user_password_repeat' );

        $validation_result = self::registrationAdminInputValidation ( $user_name, $user_password_new, $user_password_repeat, $user_email );

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( UserModel::doesUsernameAlreadyExist ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        if ( UserModel::doesEmailAlreadyExist ( $user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1 ( uniqid ( mt_rand(), true ) );

        if ( ! self::writeNewInstructorToDatabase ( $Instructor_id, $user_name, $user_password_hash, $user_email, time(), $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        endif;

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $user_id = UserModel::getInstructorUserIdByUsername ( $user_name );

        if ( ! $user_id ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;

        // send verification email
        if ( self::sendVerificationEmail ( $user_id, $user_email, $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED') );
            return true;

        endif;

        // if verification email sending failed: instantly delete the user
        self::rollbackRegistrationByUserId ( $user_id );
        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED') );
        return false;
    }

    public static function registerNewSwimmer()
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $Swimmer_id = strip_tags ( HugeRequest::post( 'Swimmer_id' ) );
        $user_name = strip_tags ( HugeRequest::post( 'user_name' ) );
        $user_email = strip_tags ( HugeRequest::post( 'user_email' ) );
        $user_password_new = HugeRequest::post( 'user_password_new' );
        $user_password_repeat = HugeRequest::post( 'user_password_repeat' );

        $validation_result = self::registrationAdminInputValidation ( $user_name, $user_password_new, $user_password_repeat, $user_email );

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( UserModel::doesUsernameAlreadyExist ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        if ( UserModel::doesEmailAlreadyExist ( $user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1 ( uniqid ( mt_rand(), true ) );

        if ( ! self::writeNewSwimmerToDatabase ( $Swimmer_id, $user_name, $user_password_hash, $user_email, time(), $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        endif;

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $user_id = UserModel::getSwimmerUserIdByUsername ( $user_name );

        if ( ! $user_id ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;

        // send verification email
        if ( self::sendVerificationEmail ( $user_id, $user_email, $user_activation_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED') );
            return true;

        endif;

        // if verification email sending failed: instantly delete the user
        self::rollbackRegistrationByUserId ( $user_id );
        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED') );
        return false;
    }

    public static function registrationInputValidation( $captcha, $user_name, $user_password_new, $user_password_repeat, $user_email )
    {

        $raincaptcha = new RainCaptchaHelper();

        if ( ! $raincaptcha->checkAnswer( $captcha ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_CAPTCHA_WRONG') );
            return false;

        endif;

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // if username, email and password are all correctly validated
        if ( self::validateUserName ( $user_name ) AND self::validateUserEmail ( $user_email ) AND self::validateUserPassword ( $user_password_new, $user_password_repeat ) ) :

            return true;

        endif;

        // otherwise, return false
        return false;
    }

    public static function registrationAdminInputValidation( $user_name, $user_password_new, $user_password_repeat, $user_email )
    {

        // if username, email and password are all correctly validated
        if ( self::validateUserName ( $user_name ) AND self::validateUserEmail ( $user_email ) AND self::validateUserPassword ( $user_password_new, $user_password_repeat ) ) :

            return true;

        endif;

        // otherwise, return false
        return false;
    }

    public static function registrationEmailValidation( $user_email )
    {

        if ( self::validateUserEmail ( $user_email ) ) :

            return true;

        endif;

        // otherwise, return false
        return false;
    }

    public static function validateUserName( $user_name )
    {
        if ( empty ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_USERNAME_FIELD_EMPTY') );
            return false;

        endif;

        // if username is too short (2), too long (64) or does not fit the pattern (aZ09)
        if ( ! preg_match ( '/^[a-zA-Z0-9]{2,64}$/', $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN') );
            return false;

        endif;

        return true;
    }

    public static function validateUserEmail( $user_email )
    {
        if ( empty ( $user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_EMAIL_FIELD_EMPTY') );
            return false;

        endif;

        // validate the email with PHP's internal filter
        // side-fact: Max length seems to be 254 chars
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if ( ! filter_var ( $user_email, FILTER_VALIDATE_EMAIL ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN') );
            return false;

        endif;

        return true;
    }

    public static function validateUserPassword( $user_password_new, $user_password_repeat )
    {
        if ( empty ( $user_password_new ) OR empty ( $user_password_repeat ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_FIELD_EMPTY') );
            return false;

        endif;

        if ( $user_password_new !== $user_password_repeat ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_REPEAT_WRONG') );
            return false;

        endif;

        if ( strlen ( $user_password_new ) < 6 ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_TOO_SHORT') );
            return false;

        endif;

        return true;
    }

    public static function writeNewUserToDatabase( $user_name, $user_password_hash, $user_email, $user_creation_timestamp, $user_activation_hash )
    {
        $data = new User();

        $data->user_name                = GlobalHelper::valueSafe( $user_name );
        $data->user_password_hash       = GlobalHelper::valueSafe( $user_password_hash );
        $data->user_email               = GlobalHelper::valueSafe( $user_email );
        $data->user_account_type        = '1';
        $data->role_id                  = self::getDefaultPublicRole();
        $data->user_creation_timestamp  = GlobalHelper::valueSafe( $user_creation_timestamp );
        $data->user_activation_hash     = GlobalHelper::valueSafe( $user_activation_hash );
        $data->user_provider_type       = 'DEFAULT';

        $data->save();

        if ( count( $data ) == 1 ) :

            return true;

        endif;

        return false;
    }

    public static function getDefaultPublicRole()
    {
        $role = Role::where( 'register_default', '=', 1 )->take( 1 )->select( 'role_id' )->first();

        if ( $role ) :

            return $role->role_id;

        endif;

        return 0;
    }

    public static function getDefaultInstructorRole()
    {
        $role = Role::where( 'Instructor_default', '=', 1 )->take( 1 )->select( 'role_id' )->first();

        if ( $role ) :

            return $role->role_id;

        endif;

        return 0;
    }

    public static function getDefaultSwimmerRole()
    {
        $role = Role::where( 'Swimmer_default', '=', 1 )->take( 1 )->select( 'role_id' )->first();

        if ( $role ) :

            return $role->role_id;

        endif;

        return 0;
    }

    public static function getDefaultSuperAdminRole()
    {
        $role = Role::where( 'role_name', '=', 'Super Admin', 'admin_default', '=', 1 )->take( 1 )->select( 'role_id' )->first();

        if ( $role ) :

            return $role->role_id;

        endif;

        return 0;
    }

    public static function getDefaultNormalAdminRole()
    {
        $role = Role::where( 'role_name', '=', 'Normal Admin', 'admin_default', '=', 1 )->take( 1 )->select( 'role_id' )->first();

        if ( $role ) :

            return $role->role_id;

        endif;

        return 0;
    }

    public static function writeNewAdminToDatabase( $user_name, $user_password_hash, $user_account_type, $user_role, $user_email, $user_creation_timestamp, $user_activation_hash )
    {
        $data = new User();

        $data->user_name                = GlobalHelper::valueSafe( $user_name );
        $data->user_password_hash       = GlobalHelper::valueSafe( $user_password_hash );
        $data->user_account_type        = GlobalHelper::valueSafe( $user_account_type );
        $data->role_id                  = GlobalHelper::valueSafe( $user_role );
        $data->user_email               = GlobalHelper::valueSafe( $user_email );
        $data->user_creation_timestamp  = GlobalHelper::valueSafe( $user_creation_timestamp );
        $data->user_activation_hash     = GlobalHelper::valueSafe( $user_activation_hash );
        $data->user_provider_type       = 'DEFAULT';

        $data->save();

        if ( count( $data ) == 1 ) :

            return true;

        endif;

        return false;
    }

    public static function writeNewInstructorToDatabase( $Instructor_id, $user_name, $user_password_hash, $user_email, $user_creation_timestamp, $user_activation_hash )
    {

        $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
            'user_name'    => GlobalHelper::valueSafe( $user_name ),
            'password'     => GlobalHelper::valueSafe( $user_password_hash ),
            'can_login'    => "1",
            'updated'      => date('Y-m-d H:i:s')

        ] );

        if ( $Instructor ) :

            $checkAvatar = InstructorModel::checkAvatarOfInstructor( $Instructor_id );

            $data = new User();

            $data->user_name                = GlobalHelper::valueSafe( $user_name );
            $data->user_password_hash       = GlobalHelper::valueSafe( $user_password_hash );
            $data->user_account_type        = "1";

            if( $checkAvatar != "" ) :

                $data->user_has_avatar      = TRUE;

            endif;

            $data->role_id                  = self::getDefaultInstructorRole();
            $data->user_email               = GlobalHelper::valueSafe( $user_email );
            $data->user_creation_timestamp  = GlobalHelper::valueSafe( $user_creation_timestamp );
            $data->user_activation_hash     = GlobalHelper::valueSafe( $user_activation_hash );
            $data->user_provider_type       = 'BSSInstructor Registration';

            $data->save();

            if ( count( $data ) == 1 ) :

                return true;

            endif;

        else :

            Slim::getInstance()->flash( 'error', 'Instructor auth failed to be updated !' );

        endif;

        return false;
    }

    public static function writeNewSwimmerToDatabase( $Swimmer_id, $user_name, $user_password_hash, $user_email, $user_creation_timestamp, $user_activation_hash )
    {

        $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
            'user_name'    => GlobalHelper::valueSafe( $user_name ),
            'password'     => GlobalHelper::valueSafe( $user_password_hash ),
            'can_login'    => "1",
            'updated'      => date('Y-m-d H:i:s')

        ] );

        if ( $Swimmer ) :

            $checkAvatar = SwimmerModel::checkAvatarOfSwimmer( $Swimmer_id );

            $data = new User();

            $data->user_name                = GlobalHelper::valueSafe( $user_name );
            $data->user_password_hash       = GlobalHelper::valueSafe( $user_password_hash );
            $data->user_account_type        = "1";

            if( $checkAvatar != "" ) :

                $data->user_has_avatar      = TRUE;

            endif;

            $data->role_id                  = self::getDefaultSwimmerRole();
            $data->user_email               = GlobalHelper::valueSafe( $user_email );
            $data->user_creation_timestamp  = GlobalHelper::valueSafe( $user_creation_timestamp );
            $data->user_activation_hash     = GlobalHelper::valueSafe( $user_activation_hash );
            $data->user_provider_type       = 'BSSSwimmer Registration';

            $data->save();

            if ( count( $data ) == 1 ) :

                return true;

            endif;

        else :

            Slim::getInstance()->flash( 'error', 'Swimmer auth failed to be updated !' );

        endif;

        return false;
    }

    public static function rollbackRegistrationByUserId( $user_id )
    {

        $query = User::where( 'user_id', '=', $user_id )->delete();

        return $query;

    }

    public static function sendVerificationEmail( $user_id, $user_email, $user_activation_hash )
    {
        $body = HugeConfig::get('EMAIL_VERIFICATION_CONTENT') . BASE_DIR . HugeConfig::get('EMAIL_VERIFICATION_URL')
            . '/' . urlencode( $user_id ) . '/' . urlencode( $user_activation_hash );

        $mail = new MailHelper();
        $mail_sent = $mail->sendMail ( $user_email, HugeConfig::get('EMAIL_VERIFICATION_FROM_EMAIL' ),
            HugeConfig::get('EMAIL_VERIFICATION_FROM_NAME'), HugeConfig::get('EMAIL_VERIFICATION_SUBJECT'), $body
        );

        if ( $mail_sent ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL') );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR') . $mail->getError() );
            return false;

        endif;
    }

    public static function verifyNewUser( $user_id, $user_activation_verification_code )
    {
        $where      = ['user_id' => $user_id, 'user_activation_hash' => $user_activation_verification_code];

        $query = User::where( $where )->take( 1 )->update( array(
            'user_active' => 1,
            'user_activation_hash'   => NULL
        ));

        if ( count( $query ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_ACCOUNT_ACTIVATION_FAILED') );
        return false;
    }
}
