<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\GlobalHelper;
use Helpers\Huge\Core\HugeRequest;
use Helpers\LanguageHelper;
use Models\Entity\Appointment;
use Models\Entity\Instructor;
use Models\Entity\Huge\User;
use Slim\Slim;
use stdClass;

class InstructorModel
{

    public static function getAllInstructor()
    {


        $query = Instructor::all();

        $all_Instructor = [];

        foreach ( $query as $d) :

            $all_Instructor[$d->Instructor_id] = new stdClass(); // Generic empty class, instantiate an empty generic php object
            $all_Instructor[$d->Instructor_id]->Instructor_id            = $d->Instructor_id;
            $all_Instructor[$d->Instructor_id]->user_name             = $d->user_name;
            $all_Instructor[$d->Instructor_id]->password              = $d->password;
            $all_Instructor[$d->Instructor_id]->user_email            = $d->user_email;
            $all_Instructor[$d->Instructor_id]->first_name            = $d->first_name;
            $all_Instructor[$d->Instructor_id]->last_name             = $d->last_name;
            $all_Instructor[$d->Instructor_id]->address               = $d->address;
            $all_Instructor[$d->Instructor_id]->phone                 = $d->phone;
            $all_Instructor[$d->Instructor_id]->cellphone             = $d->cellphone;
            $all_Instructor[$d->Instructor_id]->gender                = $d->gender;
            $all_Instructor[$d->Instructor_id]->birthdate             = $d->birthdate;
            $all_Instructor[$d->Instructor_id]->bio                   = $d->bio;
            $all_Instructor[$d->Instructor_id]->avatar                = $d->avatar;
            $all_Instructor[$d->Instructor_id]->specialties           = $d->specialties;
            $all_Instructor[$d->Instructor_id]->can_login             = $d->can_login;

            $all_Instructor[$d->Instructor_id]->timetable_sunday_start    = ( json_decode( $d->timetable )->sunday == "Request Only" ) ? json_decode( $d->timetable )->sunday : json_decode( $d->timetable )->sunday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_sunday_end      = ( json_decode( $d->timetable )->sunday == "Request Only" ) ? json_decode( $d->timetable )->sunday : json_decode( $d->timetable )->sunday->end ;

            $all_Instructor[$d->Instructor_id]->timetable_monday_start    = ( json_decode( $d->timetable )->monday == "Request Only" ) ? json_decode( $d->timetable )->monday : json_decode( $d->timetable )->monday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_monday_end      = ( json_decode( $d->timetable )->monday == "Request Only" ) ? json_decode( $d->timetable )->monday : json_decode( $d->timetable )->monday->end ;

            $all_Instructor[$d->Instructor_id]->timetable_tuesday_start   = ( json_decode( $d->timetable )->tuesday == "Request Only" ) ? json_decode( $d->timetable )->tuesday : json_decode( $d->timetable )->tuesday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_tuesday_end     = ( json_decode( $d->timetable )->tuesday == "Request Only" ) ? json_decode( $d->timetable )->tuesday : json_decode( $d->timetable )->tuesday->end ;

            $all_Instructor[$d->Instructor_id]->timetable_wednesday_start = ( json_decode( $d->timetable )->wednesday == "Request Only" ) ? json_decode( $d->timetable )->wednesday : json_decode( $d->timetable )->wednesday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_wednesday_end   = ( json_decode( $d->timetable )->wednesday == "Request Only" ) ? json_decode( $d->timetable )->wednesday : json_decode( $d->timetable )->wednesday->end ;

            $all_Instructor[$d->Instructor_id]->timetable_thursday_start  = ( json_decode( $d->timetable )->thursday == "Request Only" ) ? json_decode( $d->timetable )->thursday : json_decode( $d->timetable )->thursday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_thursday_end    = ( json_decode( $d->timetable )->thursday == "Request Only" ) ? json_decode( $d->timetable )->thursday : json_decode( $d->timetable )->thursday->end ;

            $all_Instructor[$d->Instructor_id]->timetable_friday_start    = ( json_decode( $d->timetable )->friday == "Request Only" ) ? json_decode( $d->timetable )->friday : json_decode( $d->timetable )->friday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_friday_end      = ( json_decode( $d->timetable )->friday == "Request Only" ) ? json_decode( $d->timetable )->friday : json_decode( $d->timetable )->friday->end ;

            $all_Instructor[$d->Instructor_id]->timetable_saturday_start  = ( json_decode( $d->timetable )->saturday == "Request Only" ) ? json_decode( $d->timetable )->saturday : json_decode( $d->timetable )->saturday->start ;
            $all_Instructor[$d->Instructor_id]->timetable_saturday_end    = ( json_decode( $d->timetable )->saturday == "Request Only" ) ? json_decode( $d->timetable )->saturday : json_decode( $d->timetable )->saturday->end ;


        endforeach;

        return $all_Instructor;
    }

    public static function getProfileOfInstructor( $Instructor_id )
    {

        $query = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->first();

        if ( count( $query ) == 1 ) :

            $profile = [];

            $profile['Instructor_id']            = $query->Instructor_id;
            $profile['user_name']             = $query->user_name;
            $profile['password']              = $query->password;
            $profile['user_email']            = $query->user_email;
            $profile['first_name']            = $query->first_name;
            $profile['last_name']             = $query->last_name;
            $profile['address']               = $query->address;
            $profile['phone']                 = $query->phone;
            $profile['cellphone']             = $query->cellphone;
            $profile['gender']                = $query->gender;
            $profile['birthdate']             = $query->birthdate;
            $profile['bio']                   = $query->bio;
            $profile['avatar']                = $query->avatar;
            $profile['specialties']           = $query->specialties;
            $profile['can_login']             = $query->can_login;

            $profile['timetable_sunday_start']    = ( json_decode( $query->timetable )->sunday == "Request Only" ) ? json_decode( $query->timetable )->sunday : json_decode( $query->timetable )->sunday->start ;
            $profile['timetable_sunday_end']      = ( json_decode( $query->timetable )->sunday == "Request Only" ) ? json_decode( $query->timetable )->sunday : json_decode( $query->timetable )->sunday->end ;

            $profile['timetable_monday_start']    = ( json_decode( $query->timetable )->monday == "Request Only" ) ? json_decode( $query->timetable )->monday : json_decode( $query->timetable )->monday->start ;
            $profile['timetable_monday_end']      = ( json_decode( $query->timetable )->monday == "Request Only" ) ? json_decode( $query->timetable )->monday : json_decode( $query->timetable )->monday->end ;

            $profile['timetable_tuesday_start']   = ( json_decode( $query->timetable )->tuesday == "Request Only" ) ? json_decode( $query->timetable )->tuesday : json_decode( $query->timetable )->tuesday->start ;
            $profile['timetable_tuesday_end']     = ( json_decode( $query->timetable )->tuesday == "Request Only" ) ? json_decode( $query->timetable )->tuesday : json_decode( $query->timetable )->tuesday->end ;

            $profile['timetable_wednesday_start'] = ( json_decode( $query->timetable )->wednesday == "Request Only" ) ? json_decode( $query->timetable )->wednesday : json_decode( $query->timetable )->wednesday->start ;
            $profile['timetable_wednesday_end']   = ( json_decode( $query->timetable )->wednesday == "Request Only" ) ? json_decode( $query->timetable )->wednesday : json_decode( $query->timetable )->wednesday->end ;

            $profile['timetable_thursday_start']  = ( json_decode( $query->timetable )->thursday == "Request Only" ) ? json_decode( $query->timetable )->thursday : json_decode( $query->timetable )->thursday->start ;
            $profile['timetable_thursday_end']    = ( json_decode( $query->timetable )->thursday == "Request Only" ) ? json_decode( $query->timetable )->thursday : json_decode( $query->timetable )->thursday->end ;

            $profile['timetable_friday_start']    = ( json_decode( $query->timetable )->friday == "Request Only" ) ? json_decode( $query->timetable )->friday : json_decode( $query->timetable )->friday->start ;
            $profile['timetable_friday_end']      = ( json_decode( $query->timetable )->friday == "Request Only" ) ? json_decode( $query->timetable )->friday : json_decode( $query->timetable )->friday->end ;

            $profile['timetable_saturday_start']  = ( json_decode( $query->timetable )->saturday == "Request Only" ) ? json_decode( $query->timetable )->saturday : json_decode( $query->timetable )->saturday->start ;
            $profile['timetable_saturday_end']    = ( json_decode( $query->timetable )->saturday == "Request Only" ) ? json_decode( $query->timetable )->saturday : json_decode( $query->timetable )->saturday->end ;

            return $profile;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_Instructor_DOES_NOT_EXIST' ) );

            Slim::getInstance()->response()->redirect( Slim::getInstance()->urlFor( 'Instructors' ) );

        endif;

        return false;

    }

    public static function getMyProfileOfInstructor( $username )
    {

        $query = Instructor::where( 'user_name', '=', $username )->take( 1 )->first();

        if ( count( $query ) == 1 ) :

            $profile = [];

            $profile['Instructor_id']            = $query->Instructor_id;
            $profile['user_name']             = $query->user_name;
            $profile['password']              = $query->password;
            $profile['user_email']            = $query->user_email;
            $profile['first_name']            = $query->first_name;
            $profile['last_name']             = $query->last_name;
            $profile['address']               = $query->address;
            $profile['phone']                 = $query->phone;
            $profile['cellphone']             = $query->cellphone;
            $profile['gender']                = $query->gender;
            $profile['birthdate']             = $query->birthdate;
            $profile['bio']                   = $query->bio;
            $profile['avatar']                = $query->avatar;
            $profile['specialties']           = $query->specialties;
            $profile['can_login']             = $query->can_login;

            $profile['timetable_sunday_start']    = ( json_decode( $query->timetable )->sunday == "Request Only" ) ? json_decode( $query->timetable )->sunday : json_decode( $query->timetable )->sunday->start ;
            $profile['timetable_sunday_end']      = ( json_decode( $query->timetable )->sunday == "Request Only" ) ? json_decode( $query->timetable )->sunday : json_decode( $query->timetable )->sunday->end ;

            $profile['timetable_monday_start']    = ( json_decode( $query->timetable )->monday == "Request Only" ) ? json_decode( $query->timetable )->monday : json_decode( $query->timetable )->monday->start ;
            $profile['timetable_monday_end']      = ( json_decode( $query->timetable )->monday == "Request Only" ) ? json_decode( $query->timetable )->monday : json_decode( $query->timetable )->monday->end ;

            $profile['timetable_tuesday_start']   = ( json_decode( $query->timetable )->tuesday == "Request Only" ) ? json_decode( $query->timetable )->tuesday : json_decode( $query->timetable )->tuesday->start ;
            $profile['timetable_tuesday_end']     = ( json_decode( $query->timetable )->tuesday == "Request Only" ) ? json_decode( $query->timetable )->tuesday : json_decode( $query->timetable )->tuesday->end ;

            $profile['timetable_wednesday_start'] = ( json_decode( $query->timetable )->wednesday == "Request Only" ) ? json_decode( $query->timetable )->wednesday : json_decode( $query->timetable )->wednesday->start ;
            $profile['timetable_wednesday_end']   = ( json_decode( $query->timetable )->wednesday == "Request Only" ) ? json_decode( $query->timetable )->wednesday : json_decode( $query->timetable )->wednesday->end ;

            $profile['timetable_thursday_start']  = ( json_decode( $query->timetable )->thursday == "Request Only" ) ? json_decode( $query->timetable )->thursday : json_decode( $query->timetable )->thursday->start ;
            $profile['timetable_thursday_end']    = ( json_decode( $query->timetable )->thursday == "Request Only" ) ? json_decode( $query->timetable )->thursday : json_decode( $query->timetable )->thursday->end ;

            $profile['timetable_friday_start']    = ( json_decode( $query->timetable )->friday == "Request Only" ) ? json_decode( $query->timetable )->friday : json_decode( $query->timetable )->friday->start ;
            $profile['timetable_friday_end']      = ( json_decode( $query->timetable )->friday == "Request Only" ) ? json_decode( $query->timetable )->friday : json_decode( $query->timetable )->friday->end ;

            $profile['timetable_saturday_start']  = ( json_decode( $query->timetable )->saturday == "Request Only" ) ? json_decode( $query->timetable )->saturday : json_decode( $query->timetable )->saturday->start ;
            $profile['timetable_saturday_end']    = ( json_decode( $query->timetable )->saturday == "Request Only" ) ? json_decode( $query->timetable )->saturday : json_decode( $query->timetable )->saturday->end ;

            return $profile;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_Instructor_DOES_NOT_EXIST' ) );

        endif;

        return false;

    }

    public static function getAjaxInstructorTimetable( $Instructor_id )
    {

        $query = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->first();

        if ( count( $query ) == 1 ) :

            $ajaxTimeTable = [];

            $ajaxTimeTable['timetable_sunday_start']    = ( json_decode( $query->timetable )->sunday == "Request Only" ) ? json_decode( $query->timetable )->sunday : json_decode( $query->timetable )->sunday->start ;
            $ajaxTimeTable['timetable_sunday_end']      = ( json_decode( $query->timetable )->sunday == "Request Only" ) ? json_decode( $query->timetable )->sunday : json_decode( $query->timetable )->sunday->end ;

            $ajaxTimeTable['timetable_monday_start']    = ( json_decode( $query->timetable )->monday == "Request Only" ) ? json_decode( $query->timetable )->monday : json_decode( $query->timetable )->monday->start ;
            $ajaxTimeTable['timetable_monday_end']      = ( json_decode( $query->timetable )->monday == "Request Only" ) ? json_decode( $query->timetable )->monday : json_decode( $query->timetable )->monday->end ;

            $ajaxTimeTable['timetable_tuesday_start']   = ( json_decode( $query->timetable )->tuesday == "Request Only" ) ? json_decode( $query->timetable )->tuesday : json_decode( $query->timetable )->tuesday->start ;
            $ajaxTimeTable['timetable_tuesday_end']     = ( json_decode( $query->timetable )->tuesday == "Request Only" ) ? json_decode( $query->timetable )->tuesday : json_decode( $query->timetable )->tuesday->end ;

            $ajaxTimeTable['timetable_wednesday_start'] = ( json_decode( $query->timetable )->wednesday == "Request Only" ) ? json_decode( $query->timetable )->wednesday : json_decode( $query->timetable )->wednesday->start ;
            $ajaxTimeTable['timetable_wednesday_end']   = ( json_decode( $query->timetable )->wednesday == "Request Only" ) ? json_decode( $query->timetable )->wednesday : json_decode( $query->timetable )->wednesday->end ;

            $ajaxTimeTable['timetable_thursday_start']  = ( json_decode( $query->timetable )->thursday == "Request Only" ) ? json_decode( $query->timetable )->thursday : json_decode( $query->timetable )->thursday->start ;
            $ajaxTimeTable['timetable_thursday_end']    = ( json_decode( $query->timetable )->thursday == "Request Only" ) ? json_decode( $query->timetable )->thursday : json_decode( $query->timetable )->thursday->end ;

            $ajaxTimeTable['timetable_friday_start']    = ( json_decode( $query->timetable )->friday == "Request Only" ) ? json_decode( $query->timetable )->friday : json_decode( $query->timetable )->friday->start ;
            $ajaxTimeTable['timetable_friday_end']      = ( json_decode( $query->timetable )->friday == "Request Only" ) ? json_decode( $query->timetable )->friday : json_decode( $query->timetable )->friday->end ;

            $ajaxTimeTable['timetable_saturday_start']  = ( json_decode( $query->timetable )->saturday == "Request Only" ) ? json_decode( $query->timetable )->saturday : json_decode( $query->timetable )->saturday->start ;
            $ajaxTimeTable['timetable_saturday_end']    = ( json_decode( $query->timetable )->saturday == "Request Only" ) ? json_decode( $query->timetable )->saturday : json_decode( $query->timetable )->saturday->end ;

            return $ajaxTimeTable;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_Instructor_DOES_NOT_EXIST' ) );

            Slim::getInstance()->response()->redirect( Slim::getInstance()->urlFor( 'Instructors' ) );

        endif;

        return false;

    }

    public static function checkAvatarOfInstructor( $Instructor_id )
    {

        $query = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->first();

        if ( $query ) :

            return $query->avatar;

        endif;

    }

    public static function checkAvatarOfInstructorAfterLogin( $username )
    {

        $query = Instructor::where( 'user_name', '=', $username )->take( 1 )->first();

        if ( $query ) :

            return $query->avatar;

        endif;

    }

    public static function getIdOfInstructorAfterLogin( $username )
    {

        $query = Instructor::where( 'user_name', '=', $username )->take( 1 )->first();

        if ( $query ) :

            return $query->Instructor_id;

        endif;

    }

    public static function getAppointmentDataOfSwimmer( $Instructor_id )
    {

        $query = Appointment::leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->where( 'Instructor_id', '=', $Instructor_id )->get();

        return $query;

    }

    public static function getMyAppointmentDataOfSwimmer( $username )
    {

        $getId = self::getIdOfInstructorAfterLogin( $username );

        if ( $getId ) :

            $query = Appointment::leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->where( 'Instructor_id', '=', $getId )->get();

            return $query;

        endif;

        return false;

    }

    public static function registerNewInstructor()
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
        $specialties = HugeRequest::post( 'specialties' );

        $validation_result = RegistrationModel::registrationEmailValidation ( $user_email );

        if ( ! GlobalHelper::checkValidEmail( $user_email ) ) :

            Slim::getInstance()->flash( 'error', 'Input not valid !' );
            return false;

        endif;

        if ( ! $validation_result ) :

            return false;

        endif;

        if ( ! self::writeNewInstructorToDatabase ( $user_email, $first_name, $last_name, $address, $phone, $cellphone, $gender, $bio, $specialties, $birthdate, date('Y-m-d H:i:s') ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ACCOUNT_CREATION_FAILED' ) );

        else :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Instructor_SUCCESSFULLY_CREATED') );

        endif;

        return false;
    }


    public static function writeNewInstructorToDatabase( $user_email, $first_name, $last_name, $address, $phone, $cellphone, $gender, $bio, $specialties, $birthdate, $created )
    {

        $timetable_slot = array(
            'sunday'    => "Request Only",
            'monday'    => "Request Only",
            'tuesday'   => "Request Only",
            'wednesday' => "Request Only",
            'thursday'  => "Request Only",
            'friday'    => "Request Only",
            'saturday'  => "Request Only"
        );

        $timetable = json_encode( $timetable_slot );

        $data = new Instructor();

        $data->user_email               = GlobalHelper::valueSafe( $user_email );
        $data->first_name               = GlobalHelper::valueSafe( $first_name );
        $data->last_name                = GlobalHelper::valueSafe( $last_name );
        $data->address                  = GlobalHelper::valueSafe( $address );
        $data->phone                    = GlobalHelper::valueSafe( $phone );
        $data->cellphone                = GlobalHelper::valueSafe( $cellphone );
        $data->gender                   = GlobalHelper::valueSafe( $gender );
        $data->bio                      = GlobalHelper::valueSafe( $bio );
        $data->specialties              = GlobalHelper::valueSafe( $specialties );
        $data->timetable                = $timetable;
        $data->birthdate                = GlobalHelper::valueSafe( $birthdate );
        $data->created                  = GlobalHelper::valueSafe( $created );

        $data->save();

        if ( count( $data ) == 1 ) :

            return true;

        endif;

        return false;
    }

    public static function saveInstructorAvatar( $user_email, $Instructor_id, $avatar )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $where      = ['user_email' => $user_email, 'user_provider_type' => 'BSS Instructor Registration'];

        $checkFromUserAuth = User::where( $where )->take(1)->first();

        if ( count ( $checkFromUserAuth == 1 ) ) :

            $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
                'avatar'    => $avatar,
                'updated'   => date('Y-m-d H:i:s')

            ] );

            if ( $Instructor ) :

                User::where( $where )->take(1)->update( array(
                    'user_has_avatar' => TRUE
                ));

                Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_Instructor_AVATAR_SUCCESS_UPDATED' ) );

            else :

                Slim::getInstance()->flash( 'error', 'Instructor profile failed to be updated, please add auth info FIRST !' );

            endif;


        else :

            $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
                'avatar'    => $avatar,
                'updated'   => date('Y-m-d H:i:s')

            ] );

            if ( $Instructor ) :

                Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_Instructor_AVATAR_SUCCESS_UPDATED' ) );

            else :

                Slim::getInstance()->flash( 'error', 'Instructor avatar failed to be updated !' );

            endif;

        endif;

        return false;

    }

    public static function editInstructorEmail( $Instructor_id, $user_name, $new_user_email )
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
        if ( self::saveNewEmailAddress ( $Instructor_id, $user_name, $new_user_email ) ) :

//            HugeSession::set( 'user_email', $new_user_email );
//            HugeSession::set( 'user_gravatar_image_url', HugeAvatar::getGravatarLinkByEmail( $new_user_email ) );
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_EMAIL_CHANGE_SUCCESSFUL' ) );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
        return false;
    }

    public static function saveNewEmailAddress( $Instructor_id, $user_name, $new_user_email )
    {
        $user = User::where( 'user_name', '=', $user_name )->take( 1 )->update( array( 'user_email' => $new_user_email ) );

        if ( $user ) :

            $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
                'user_email'   => $new_user_email,
                'updated'      => date('Y-m-d H:i:s')
            ] );

            if ( $Instructor ) :

                return true;

            else :

                return false;

            endif;

        endif;

        return false;
    }

    public static function doesUsernameAlreadyExist( $user_name )
    {
        $user = User::where( 'user_name', '=', $user_name )->take( 1 )->select( 'user_id' )->first();

        $Instructor = Instructor::where( 'user_name', '=', $user_name )->take( 1 )->select( 'Instructor_id' )->first();

        if ( ! $user AND ! $Instructor ) :

            return false;

        endif;

        return true;
    }

    public static function doesEmailAlreadyExist( $user_email )
    {
        $user = User::where( 'user_email', '=', $user_email )->take( 1 )->select( 'user_id' )->first();

        $Instructor = Instructor::where( 'user_email', '=', $user_email )->take( 1 )->select( 'Instructor_id' )->first();

        if ( ! $user AND ! $Instructor ) :

            return false;

        endif;

        return true;
    }

    public static function editInstructorUserName( $Instructor_id, $user_email, $new_user_name )
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

        $status_of_action = self::saveNewUserName( $Instructor_id, $user_email, $new_user_name );

        if ( $status_of_action ) :

//            HugeSession::set( 'user_name', $new_user_name );
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_USERNAME_CHANGE_SUCCESSFUL' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;
    }

    public static function saveNewUserName( $Instructor_id, $user_email, $new_user_name )
    {
        $user = User::where( 'user_email', '=', $user_email )->take( 1 )->update( array( 'user_name' => $new_user_name ) );

        if ( $user ) :

            $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
                'user_name'    => $new_user_name,
                'updated'      => date('Y-m-d H:i:s')
            ] );

            if ( $Instructor ) :

                return true;

            else :

                return false;

            endif;

        endif;

        return false;
    }

    public static function updateProfileDetailsOfInstructor( $Instructor_id, $first_name, $last_name, $address, $bio, $specialties, $phone, $cellphone, $birthdate )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'address'      => $address,
            'bio'          => $bio,
            'specialties'  => $specialties,
            'phone'        => $phone,
            'cellphone'    => $cellphone,
            'birthdate'    => $birthdate,
            'updated'      => date('Y-m-d H:i:s')


        ] );

        if ( $Instructor ) :

            Slim::getInstance()->flash( 'success', 'Instructor profile details has been updated !' );

        else :

            Slim::getInstance()->flash( 'error', 'Instructor profile failed to be updated !' );

        endif;

        return false;

    }

    public static function setNewPassword ( $Instructor_id, $user_name, $user_password_new, $user_password_repeat )
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

        if ( self::saveNewInstructorPassword ( $Instructor_id, $user_name, $user_password_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL') );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_FAILED') );
            return false;

        endif;

    }

    public static function saveNewInstructorPassword( $Instructor_id, $user_name, $user_password_hash )
    {
        $where      = ['user_name' => $user_name, 'user_provider_type' => 'BSS Instructor Registration'];

        $query = User::where( $where )->take(1)->update( array(
            'user_password_hash' => $user_password_hash,
            'user_password_reset_hash'   => NULL,
            'user_password_reset_timestamp'   => NULL
        ));

        if ( count( $query == 1 ) ) :

            $Instructor = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update( [
                'password'   => $user_password_hash,
                'updated'    => date('Y-m-d H:i:s')

            ] );

            if ( $Instructor ) :

                return true;

            else :

                return false;

            endif;

        endif;

        return false;
    }

    public static function updateTimeTable( $Instructor_id )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $sunday_timepicker_start = HugeRequest::post( 'sunday_timepicker_start' );
        $sunday_timepicker_end = HugeRequest::post( 'sunday_timepicker_end' );
        if ( $sunday_timepicker_start == "" AND $sunday_timepicker_end == "" ) :
            $sunday = "Request Only";
        else :
            $sunday = array(

                "start" => $sunday_timepicker_start,
                "end"   => $sunday_timepicker_end

            );
        endif;

        $monday_timepicker_start = HugeRequest::post( 'monday_timepicker_start' );
        $monday_timepicker_end = HugeRequest::post( 'monday_timepicker_end' );
        if ( $monday_timepicker_start == "" AND $monday_timepicker_end == "" ) :
            $monday = "Request Only";
        else :
            $monday = array(

                "start" => $monday_timepicker_start,
                "end"   => $monday_timepicker_end

            );
        endif;

        $tuesday_timepicker_start = HugeRequest::post( 'tuesday_timepicker_start' );
        $tuesday_timepicker_end = HugeRequest::post( 'tuesday_timepicker_end' );
        if ( $tuesday_timepicker_start == "" AND $tuesday_timepicker_end == "" ) :
            $tuesday = "Request Only";
        else :
            $tuesday = array(

                "start" => $tuesday_timepicker_start,
                "end"   => $tuesday_timepicker_end

            );
        endif;

        $wednesday_timepicker_start = HugeRequest::post( 'wednesday_timepicker_start' );
        $wednesday_timepicker_end = HugeRequest::post( 'wednesday_timepicker_end' );
        if ( $wednesday_timepicker_start == "" AND $wednesday_timepicker_end == "" ) :
            $wednesday = "Request Only";
        else :
            $wednesday = array(

                "start" => $wednesday_timepicker_start,
                "end"   => $wednesday_timepicker_end

            );
        endif;

        $thursday_timepicker_start = HugeRequest::post( 'thursday_timepicker_start' );
        $thursday_timepicker_end = HugeRequest::post( 'thursday_timepicker_end' );
        if ( $thursday_timepicker_start == "" AND $thursday_timepicker_end == "" ) :
            $thursday = "Request Only";
        else :
            $thursday = array(

                "start" => $thursday_timepicker_start,
                "end"   => $thursday_timepicker_end

            );
        endif;

        $friday_timepicker_start = HugeRequest::post( 'friday_timepicker_start' );
        $friday_timepicker_end = HugeRequest::post( 'friday_timepicker_end' );
        if ( $friday_timepicker_start == "" AND $friday_timepicker_end == "" ) :
            $friday = "Request Only";
        else :
            $friday = array(

                "start" => $friday_timepicker_start,
                "end"   => $friday_timepicker_end

            );
        endif;

        $saturday_timepicker_start = HugeRequest::post( 'saturday_timepicker_start' );
        $saturday_timepicker_end = HugeRequest::post( 'saturday_timepicker_end' );
        if ( $saturday_timepicker_start == "" AND $saturday_timepicker_end == "" ) :
            $saturday = "Request Only";
        else :
            $saturday = array(

                "start" => $saturday_timepicker_start,
                "end"   => $saturday_timepicker_end

            );
        endif;

        $timetable_slot = array(
            'sunday'    => $sunday,
            'monday'    => $monday,
            'tuesday'   => $tuesday,
            'wednesday' => $wednesday,
            'thursday'  => $thursday,
            'friday'    => $friday,
            'saturday'  => $saturday
        );

        $timetable = json_encode( $timetable_slot );

        $update = Instructor::where( 'Instructor_id', '=', $Instructor_id )->take( 1 )->update(
            array(
                "timetable" =>  $timetable,
            )
        );

        if ( $update ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Instructor_TIMETABLE_SUCCESS_UPDATED') );
            return true;

        endif;


        return false;
    }

}