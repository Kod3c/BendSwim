<?php namespace Models\Backend;

use Helpers\LanguageHelper;

use Helpers\CsrfHelper;
use Helpers\MailHelper;
use Helpers\Huge\Core\HugeConfig;
use LiveControl\EloquentDataTable\DataTable;
use LiveControl\EloquentDataTable\VersionTransformers\Version109Transformer;
use Models\Entity\Appointment;
use Slim\Slim;

class AppointmentModel
{

    public static function getAllAppointment()
    {

        $appointment = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->select('appointment.appointment_id', 'appointment.Instructor_id', 'appointment.Swimmer_id', 'appointment.type', 'appointment_date', 'appointment_time', 'appointment.start', 'appointment.end', 'appointment.notes', 'appointment.notes_Instructor', 'appointment.cancelled', 'appointment.finished', 'appointment.created', 'appointment.updated', 'Instructors.first_name as Instructor_firstname', 'Instructors.last_name as Instructor_lastname', 'Instructors.avatar as Instructor_avatar', 'Instructors.specialties as Instructor_specialties', 'Swimmers.Swimmer_id as Swimmer_id', 'Swimmers.first_name as Swimmer_firstname', 'Swimmers.last_name as Swimmer_lastname', 'Swimmers.avatar as Swimmer_avatar')->get();

        return $appointment;
    }

    public static function getAllInstructorAppointment( $Instructor_id )
    {

        $where = [ 'appointment.Instructor_id' => $Instructor_id, 'appointment.cancelled' => 0 ];

        $appointment = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->where( $where )->select('appointment.appointment_id', 'appointment.Instructor_id', 'appointment.Swimmer_id', 'appointment.type', 'appointment_date', 'appointment_time', 'appointment.start', 'appointment.end', 'appointment.notes', 'appointment.finished', 'Instructors.first_name as Instructor_firstname', 'Instructors.last_name as Instructor_lastname', 'Instructors.specialties as Instructor_specialties', 'Swimmers.Swimmer_id as Swimmer_id', 'Swimmers.first_name as Swimmer_firstname', 'Swimmers.last_name as Swimmer_lastname')->get();

        return $appointment;
    }

    public static function getAllInstructorMyAppointment( $username )
    {

        $getId = InstructorModel::getIdOfInstructorAfterLogin( $username );

        if ( $getId ) :

            $where = [ 'appointment.Instructor_id' => $getId, 'appointment.cancelled' => 0 ];

            $appointment = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->where( $where )->select('appointment.appointment_id', 'appointment.Instructor_id', 'appointment.Swimmer_id', 'appointment.type', 'appointment_date', 'appointment_time', 'appointment.start', 'appointment.end', 'appointment.notes', 'appointment.finished', 'Instructors.first_name as Instructor_firstname', 'Instructors.last_name as Instructor_lastname', 'Instructors.specialties as Instructor_specialties', 'Swimmers.Swimmer_id as Swimmer_id', 'Swimmers.first_name as Swimmer_firstname', 'Swimmers.last_name as Swimmer_lastname')->get();

            return $appointment;

        endif;
    }

    public static function getAppointmentDataOfSwimmer( $Swimmer_id ) {

        $where = [ 'Swimmer_id' => $Swimmer_id ];

        $query = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->where( $where )->get();

        return $query;

    }

    public static function getMyAppointmentDataOfSwimmer( $username ) {

        $getId = SwimmerModel::getIdOfSwimmerAfterLogin( $username );

        if ( $getId ) :

            $where = [ 'Swimmer_id' => $getId ];

            $query = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->where( $where )->get();

            return $query;

        endif;

        return false;

    }

    public static function getInstructorAvailableTimeSlotAtThatDay( $Instructor_id, $appointment_date ) {

        $where = [ 'Instructor_id' => $Instructor_id, 'appointment_date' => $appointment_date, 'cancelled' => 0 ];

        $query = Appointment::where( $where )->select( 'appointment_time' )->get();

        return $query;

    }

    public static function registerNewSwimmerAppointment( $Instructor_id, $Swimmer_id, $type, $appointment_date, $appointment_time, $notes )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( $appointment_date == '' ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_ERROR_APPOINTMENT_FORM') );
            return false;

        endif;

        $data = new Appointment();

        $data->Instructor_id               = $Instructor_id;
        $data->Swimmer_id               = $Swimmer_id;
        $data->type                     = $type;
        $data->appointment_date         = $appointment_date;
        $data->appointment_time         = $appointment_time;
        $data->start                    = $appointment_time ? $appointment_date.'T'.$appointment_time : $appointment_date;
        $data->end                      = $appointment_date;
        $data->notes                    = $notes;
        $data->created                  = date( 'Y-m-d H:i:s' );

        $data->save();

        if ( count( $data == 1 ) ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_SUCCESS_APPOINTMENT') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_ERROR_APPOINTMENT') );
        return false;
    }

    public static function registerNewSwimmerInstructorAppointment( $Swimmer_id, $Instructor_id, $type, $appointment_date, $appointment_time, $notes )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( $appointment_date == '' ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_ERROR_APPOINTMENT_FORM') );
            return false;

        endif;

        $data = new Appointment();

        $data->Instructor_id               = $Instructor_id;
        $data->Swimmer_id               = $Swimmer_id;
        $data->type                     = $type;
        $data->appointment_date         = $appointment_date;
        $data->appointment_time         = $appointment_time;
        $data->start                    = $appointment_time ? $appointment_date.'T'.$appointment_time : $appointment_date;
        $data->end                      = $appointment_date;
        $data->notes                    = $notes;
        $data->created                  = date( 'Y-m-d H:i:s' );

        $data->save();

        if ( count( $data == 1 ) ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_SUCCESS_APPOINTMENT') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_ERROR_APPOINTMENT') );
        return false;
    }

    public static function activatedSwimmerAppointment( $appointment_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $activated = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->update( [
            'cancelled'    => 0,
            'updated'      => date('Y-m-d H:i:s')
        ] );

        if ( $activated ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_ACTIVATE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_ACTIVATE') );
        return false;

    }

    public static function cancelledSwimmerAppointment( $appointment_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $cancel = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->update( [
            'cancelled'    => 1,
            'updated'      => date('Y-m-d H:i:s')
        ] );

        if ( $cancel ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_CANCEL') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_CANCEL') );
        return false;

    }

    public static function deletedSwimmerAppointment( $appointment_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->delete();

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_DELETE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_DELETE') );
        return false;

    }

    public static function activatedSwimmerAppointmentByInstructor( $appointment_id, $notes_Instructor )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $activated = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->update( [
            'notes_Instructor'    => $notes_Instructor,
            'cancelled'    => 0,
            'updated'      => date('Y-m-d H:i:s')
        ] );

        if ( $activated ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_ACTIVATE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_ACTIVATE') );
        return false;

    }

    public static function cancelledSwimmerAppointmentByInstructor( $appointment_id, $notes_Instructor )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $cancel = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->update( [
            'notes_Instructor'    => $notes_Instructor,
            'cancelled'    => 1,
            'updated'      => date('Y-m-d H:i:s')
        ] );

        if ( $cancel ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_CANCEL') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_CANCEL') );
        return false;

    }

    public static function finishedSwimmerAppointmentByInstructor( $appointment_id, $notes_Instructor )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $cancel = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->update( [
            'notes_Instructor'    => $notes_Instructor,
            'finished'    => 1,
            'updated'      => date('Y-m-d H:i:s')
        ] );

        if ( $cancel ) :

            // TODO[rizkiwisnuaji] IMPLEMENTS EMAIL AND INBOX NOTIFICATION TO Instructor AND Swimmer

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_CANCEL') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_CANCEL') );
        return false;

    }

    public static function deletedSwimmerAppointmentByInstructor( $appointment_id )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Appointment::where( 'appointment_id', '=', $appointment_id )->take( 1 )->delete();

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_SUCCESS_DELETE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_Swimmer_APPOINTMENT_ERROR_DELETE') );
        return false;

    }

}
