<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\GlobalHelper;
use Helpers\Huge\Core\HugeRequest;
use Helpers\LanguageHelper;
use Models\Entity\Appointment;
use Models\Entity\Huge\User;
use Models\Entity\Swimmer;
use Slim\Slim;

class SwimmerModel
{

    public static function getAllSwimmer()
    {

        $Swimmer = Swimmer::all();

        if ( $Swimmer ) :

            return $Swimmer;

        endif;
    }

    public static function getProfileOfSwimmer( $Swimmer_id )
    {

        $query = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->first();

        if ( count( $query ) == 1 ) :

            return $query;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_Swimmer_DOES_NOT_EXIST' ) );

            Slim::getInstance()->response()->redirect( Slim::getInstance()->urlFor( 'Swimmers' ) );

        endif;

        return false;

    }

    public static function getMyProfileOfSwimmer( $username )
    {

        $query = Swimmer::where( 'user_name', '=', $username )->take( 1 )->first();

        if ( count( $query ) == 1 ) :

            return $query;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_Swimmer_DOES_NOT_EXIST' ) );

            Slim::getInstance()->response()->redirect( Slim::getInstance()->urlFor( 'Swimmers' ) );

        endif;

        return false;

    }

    public static function checkAvatarOfSwimmer( $Swimmer_id )
    {

        $query = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->first();

        if ( $query ) :

            return $query->avatar;

        endif;

    }

    public static function checkAvatarOfSwimmerAfterLogin( $username )
    {

        $query = Swimmer::where( 'user_name', '=', $username )->take( 1 )->first();

        if ( $query ) :

            return $query->avatar;

        endif;

    }

    public static function getIdOfSwimmerAfterLogin( $username )
    {

        $query = Swimmer::where( 'user_name', '=', $username )->take( 1 )->first();

        if ( $query ) :

            return $query->Swimmer_id;

        endif;

    }

    public static function updateProfileDetailsOfSwimmer( $Swimmer_id, $first_name, $last_name, $address, $bio, $gender, $phone, $cellphone, $birthdate )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'address'      => $address,
            'phone'        => $phone,
            'cellphone'    => $cellphone,
            'birthdate'    => $birthdate,
            'bio'          => $bio,
            'gender'       => $gender,
            'updated'      => date('Y-m-d H:i:s')


        ] );

        if ( $Swimmer ) :

            Slim::getInstance()->flash( 'success', 'Swimmer profile details has been updated !' );

        else :

            Slim::getInstance()->flash( 'error', 'Swimmer profile failed to be updated !' );

        endif;

        return false;

    }

    public static function saveSwimmerAvatar( $user_email, $Swimmer_id, $avatar )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $where      = ['user_email' => $user_email, 'user_provider_type' => 'BSSSwimmer Registration'];

        $checkFromUserAuth = User::where( $where )->take(1)->first();

        if ( count ( $checkFromUserAuth == 1 ) ) :

            $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
                'avatar'    => $avatar,
                'updated'   => date('Y-m-d H:i:s')

            ] );

            if ( $Swimmer ) :

                User::where( $where )->take(1)->update( array(
                    'user_has_avatar' => TRUE
                ));

                Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_Swimmer_AVATAR_SUCCESS_UPDATED' ) );

            else :

                Slim::getInstance()->flash( 'error', 'Swimmer profile failed to be updated, please add auth info FIRST !' );

            endif;


        else :

            $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
                'avatar'    => $avatar,
                'updated'   => date('Y-m-d H:i:s')

            ] );

            if ( $Swimmer ) :

                Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_Swimmer_AVATAR_SUCCESS_UPDATED' ) );

            else :

                Slim::getInstance()->flash( 'error', 'Swimmer avatar failed to be updated !' );

            endif;

        endif;

        return false;

    }


    public static function registerNewSwimmer()
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $user_email = strip_tags ( HugeRequest::post( 'user_email' ) );
        $first_name = strip_tags ( HugeRequest::post( 'first_name' ) );
        $last_name = strip_tags ( HugeRequest::post( 'last_name' ) );
        $address = HugeRequest::post( 'address' );
        $phone = HugeRequest::post( 'phone' );
        $cellphone = HugeRequest::post( 'cellphone' );
        $birthdate = HugeRequest::post( 'birthdate' );
        $gender = HugeRequest::post( 'gender' );
        $bio = HugeRequest::post( 'bio' );

        $validation_result = RegistrationModel::registrationEmailValidation ( $user_email );

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        if ( ! self::writeNewSwimmerToDatabase ( $user_email, $first_name, $last_name, $address, $phone, $cellphone, $gender, $bio, $birthdate, date('Y-m-d H:i:s') ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        else :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_SUCCESSFULLY_CREATED') );

        endif;

        return false;
    }

    public static function registerNewSwimmerForLogin()
    {

        $user_name = strip_tags ( HugeRequest::post( 'user_name' ) );
        $user_email = strip_tags ( HugeRequest::post( 'user_email' ) );
        $user_password_new = HugeRequest::post( 'user_password_new' );
        $user_password_repeat = HugeRequest::post( 'user_password_repeat' );
        $first_name = strip_tags ( HugeRequest::post( 'first_name' ) );
        $last_name = strip_tags ( HugeRequest::post( 'last_name' ) );
        $address = HugeRequest::post( 'address' );
        $phone = HugeRequest::post( 'phone' );
        $cellphone = HugeRequest::post( 'cellphone' );
        $birthdate = HugeRequest::post( 'birthdate' );
        $gender = HugeRequest::post( 'gender' );
        $bio = HugeRequest::post( 'bio' );

        $validation_result = RegistrationModel::registrationAdminInputValidation ( $user_name, $user_password_new, $user_password_repeat, $user_email );

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( self::doesUsernameAlreadyExist ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        if ( self::doesEmailAlreadyExist ( $user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        if ( ! self::writeNewSwimmerForLoginToDatabase ( $user_name, $user_password_hash, $user_email, $first_name, $last_name, $address, $phone, $cellphone, $gender, $bio, $birthdate, date('Y-m-d H:i:s') ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        else :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_SUCCESSFULLY_CREATED') );

        endif;

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $user_id = self::getUserIdByUsername ( $user_name );

        if ( ! $user_id ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;

        return false;
    }

    public static function doesUsernameAlreadyExist( $user_name )
    {
        $Swimmer = Swimmer::where( 'user_name', '=', $user_name )->take( 1 )->select( 'Swimmer_id' )->first();

        if ( ! $Swimmer ) :

            return false;

        endif;

        return true;
    }

    public static function doesEmailAlreadyExist( $user_email )
    {
        $Swimmer = Swimmer::where( 'user_email', '=', $user_email )->take( 1 )->select( 'Swimmer_id' )->first();

        if ( ! $Swimmer ) :

            return false;

        endif;

        return true;
    }

    public static function getUserIdByUsername( $user_name )
    {

        $where      = ['user_name' => $user_name];

        $query = Swimmer::where( $where )->take( 1 )->select( 'Swimmer_id' )->first();

        // return one row (we only have one result or nothing)
        return $query->Swimmer_id;
    }

    public static function setNewPassword ( $Swimmer_id, $user_name, $user_password_new, $user_password_repeat )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // validate the password
        if ( ! UserModel::validateNewPassword ( $user_name, $user_password_new, $user_password_repeat ) ) :

            return false;

        endif;

        // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( self::saveNewSwimmerPassword ( $Swimmer_id, $user_name, $user_password_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL') );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_FAILED') );
            return false;

        endif;

    }

    public static function saveNewSwimmerPassword( $Swimmer_id, $user_name, $user_password_hash )
    {
        $where      = ['user_name' => $user_name, 'user_provider_type' => 'BSSSwimmer Registration'];

        $query = User::where( $where )->take(1)->update( array(
            'user_password_hash' => $user_password_hash,
            'user_password_reset_hash'   => NULL,
            'user_password_reset_timestamp'   => NULL
        ));

        if ( count( $query == 1 ) ) :

            $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
                'password'   => $user_password_hash,
                'updated'    => date('Y-m-d H:i:s')

            ] );

            if ( $Swimmer ) :

                return true;

            else :

                return false;

            endif;

        endif;

        return false;
    }

    public static function editSwimmerEmail( $Swimmer_id, $user_name, $new_user_email )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // email provided ?
        if ( empty ( $new_user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_EMAIL_FIELD_EMPTY' ) );
            return false;

        endif;

//        // check if new email is same like the old one
//        if ( $new_user_email == HugeSession::get( 'user_email' ) ) :
//
//            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_EMAIL_SAME_AS_OLD_ONE' ) );
//            return false;
//
//        endif;

        // user's email must be in valid email format, also checks the length
        // @see http://stackoverflow.com/questions/21631366/php-filter-validate-email-max-length
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if ( ! filter_var ( $new_user_email, FILTER_VALIDATE_EMAIL ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN' ) );
            return false;

        endif;

        // strip tags, just to be sure
        $new_user_email = substr( strip_tags( $new_user_email ), 0, 254 );

        // check if user's email already exists
        if ( self::doesEmailAlreadyExist ( $new_user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        // write to database, if successful ...
        // ... then write new email to session, Gravatar too (as this relies to the user's email address)
        if ( self::saveNewEmailAddress ( $Swimmer_id, $user_name, $new_user_email ) ) :

//            HugeSession::set( 'user_email', $new_user_email );
//            HugeSession::set( 'user_gravatar_image_url', HugeAvatar::getGravatarLinkByEmail( $new_user_email ) );
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_EMAIL_CHANGE_SUCCESSFUL' ) );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
        return false;
    }

    public static function saveNewEmailAddress( $Swimmer_id, $user_name, $new_user_email )
    {
        $user = User::where( 'user_name', '=', $user_name )->take( 1 )->update( array( 'user_email' => $new_user_email ) );

        if ( $user ) :

            $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
                'user_email'   => $new_user_email,
                'updated'      => date('Y-m-d H:i:s')
            ] );

            if ( $Swimmer ) :

                return true;

            else :

                return false;

            endif;

        endif;

        return false;
    }

    public static function editSwimmerUserName( $Swimmer_id, $user_email, $new_user_name )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // new username same as old one ?
//        if ( $new_user_name == HugeSession::get( 'user_name' ) ) :
//
//            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_SAME_AS_OLD_ONE' ) );
//            return false;
//
//        endif;

        // username cannot be empty and must be azAZ09 and 2-64 characters
        if ( ! preg_match ( "/^[a-zA-Z0-9]{2,64}$/", $new_user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN' ) );
            return false;

        endif;

        // clean the input, strip usernames longer than 64 chars (maybe fix this ?)
        $new_user_name = substr( strip_tags ( $new_user_name ), 0, 64 );

        // check if new username already exists
        if ( self::doesUsernameAlreadyExist( $new_user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        $status_of_action = self::saveNewUserName( $Swimmer_id, $user_email, $new_user_name );

        if ( $status_of_action ) :

//            HugeSession::set( 'user_name', $new_user_name );
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_USERNAME_CHANGE_SUCCESSFUL' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;
    }

    public static function saveNewUserName( $Swimmer_id, $user_email, $new_user_name )
    {

        $user = User::where( 'user_email', '=', $user_email )->take( 1 )->update( [ 'user_name' => $new_user_name ] );

        if ( $user ) :

            $Swimmer = Swimmer::where( 'Swimmer_id', '=', $Swimmer_id )->take( 1 )->update( [
                'user_name'    => $new_user_name,
                'updated'      => date('Y-m-d H:i:s')
            ] );

            if ( $Swimmer ) :

                return true;

            else :

                return false;

            endif;

        endif;

        return false;
    }

    public static function writeNewSwimmerToDatabase( $user_email, $first_name, $last_name, $address, $phone, $cellphone, $gender, $bio, $birthdate, $created )
    {
        $data = new Swimmer();

        $data->user_email               = GlobalHelper::valueSafe( $user_email );
        $data->first_name               = GlobalHelper::valueSafe( $first_name );
        $data->last_name                = GlobalHelper::valueSafe( $last_name );
        $data->address                  = GlobalHelper::valueSafe( $address );
        $data->phone                    = GlobalHelper::valueSafe( $phone );
        $data->cellphone                = GlobalHelper::valueSafe( $cellphone );
        $data->gender                   = GlobalHelper::valueSafe( $gender );
        $data->bio                      = GlobalHelper::valueSafe( $bio );
        $data->birthdate                = GlobalHelper::valueSafe( $birthdate );
        $data->created                  = GlobalHelper::valueSafe( $created );

        $data->save();

        if ( count( $data ) == 1 ) :

            return true;

        endif;

        return false;
    }

    public static function writeNewSwimmerForLoginToDatabase( $user_name, $user_password_hash, $user_email, $first_name, $last_name, $address, $phone, $cellphone, $gender, $bio, $birthdate, $created )
    {
        $data = new Swimmer();

        $data->user_name                = GlobalHelper::valueSafe( $user_name );
        $data->password                 = GlobalHelper::valueSafe( $user_password_hash );
        $data->user_email               = GlobalHelper::valueSafe( $user_email );
        $data->first_name               = GlobalHelper::valueSafe( $first_name );
        $data->last_name                = GlobalHelper::valueSafe( $last_name );
        $data->address                  = GlobalHelper::valueSafe( $address );
        $data->phone                    = GlobalHelper::valueSafe( $phone );
        $data->cellphone                = GlobalHelper::valueSafe( $cellphone );
        $data->gender                   = GlobalHelper::valueSafe( $gender );
        $data->bio                      = GlobalHelper::valueSafe( $bio );
        $data->birthdate                = GlobalHelper::valueSafe( $birthdate );
        $data->created                  = GlobalHelper::valueSafe( $created );

        $data->save();

        if ( count( $data ) == 1 ) :

            return true;

        endif;

        return false;
    }

}