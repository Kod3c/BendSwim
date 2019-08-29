<?php namespace Models\Backend;

use Helpers\LanguageHelper;
use Models\Entity\Appointment;
use Models\Entity\Instructor;
use Models\Entity\Huge\User;
use Models\Entity\Swimmer;
use Slim\Slim;

class DashboardModel
{

    public static function getUserCount()
    {

        $users = User::all();

        return count( $users );
    }

    public static function getSwimmerCount()
    {

        $Swimmer = Swimmer::all();

        return count( $Swimmer );

    }

    public static function getInstructorCount()
    {

        $Instructor = Instructor::all();

        return count( $Instructor );

    }

    public static function getTodayAppointmentCount()
    {

        $appointment = Appointment::where( 'appointment_date', '=', date( 'Y-m-d' ) )->get();

        return count( $appointment );

    }

    public static function getInstructorTodayAppointmentCount( $username )
    {

        $getId = InstructorModel::getIdOfInstructorAfterLogin( $username );

        if ( $getId ) :

            $where = [ 'Instructor_id' => $getId, 'appointment_date' => date( 'Y-m-d' ) ];

            $appointment = Appointment::where( $where )->get();

            return count( $appointment );

        endif;

        return false;

    }

    public static function getSwimmerTodayAppointmentCount( $username )
    {

        $getId = SwimmerModel::getIdOfSwimmerAfterLogin( $username );

        if ( $getId ) :

            $where = [ 'Swimmer_id' => $getId, 'appointment_date' => date( 'Y-m-d' ) ];

            $appointment = Appointment::where( $where )->get();

            return count( $appointment );

        endif;

        return false;

    }

    public static function getFinishedAppointmentCount()
    {

        $appointment = Appointment::where( 'finished', '=', 1 )->get();

        return count( $appointment );

    }

    public static function getCancelledAppointmentCount()
    {

        $appointment = Appointment::where( 'cancelled', '=', 1 )->get();

        return count( $appointment );

    }

    public static function getAllTodayAppointment()
    {

        $where = [ 'appointment.appointment_date' => date( 'Y-m-d' ) ];

        $appointment = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->where( $where )->select('appointment.appointment_id', 'appointment.Instructor_id', 'appointment.Swimmer_id', 'appointment.type', 'appointment_date', 'appointment_time', 'appointment.start', 'appointment.end', 'appointment.notes', 'appointment.notes_Instructor', 'appointment.cancelled', 'appointment.finished', 'appointment.created', 'Instructors.avatar as Instructor_avatar', 'Instructors.first_name as Instructor_firstname', 'Instructors.last_name as Instructor_lastname', 'Instructors.specialties as Instructor_specialties', 'Swimmers.Swimmer_id as Swimmer_id', 'Swimmers.avatar as Swimmer_avatar', 'Swimmers.first_name as Swimmer_firstname', 'Swimmers.last_name as Swimmer_lastname', 'Swimmers.birthdate as Swimmer_birthdate')->orderBy('appointment.appointment_time', 'asc')->get();

        return $appointment;
    }

    public static function getAllSwimmerAppointment( $username )
    {

        $getId = SwimmerModel::getIdOfSwimmerAfterLogin( $username );

        if ( $getId ) :

            $where = [ 'appointment.Swimmer_id' => $getId ];

            $appointment = Appointment::leftJoin('Instructors', 'appointment.Instructor_id', '=', 'Instructors.Instructor_id')->leftJoin('Swimmers', 'appointment.Swimmer_id', '=', 'Swimmers.Swimmer_id')->where( $where )->select('appointment.appointment_id', 'appointment.Instructor_id', 'appointment.Swimmer_id', 'appointment.type', 'appointment_date', 'appointment_time', 'appointment.start', 'appointment.end', 'appointment.notes', 'appointment.notes_Instructor', 'appointment.cancelled', 'appointment.finished', 'appointment.created', 'Instructors.avatar as Instructor_avatar', 'Instructors.first_name as Instructor_firstname', 'Instructors.last_name as Instructor_lastname', 'Instructors.specialties as Instructor_specialties', 'Swimmers.Swimmer_id as Swimmer_id', 'Swimmers.avatar as Swimmer_avatar', 'Swimmers.first_name as Swimmer_firstname', 'Swimmers.last_name as Swimmer_lastname', 'Swimmers.birthdate as Swimmer_birthdate')->orderBy('appointment.appointment_time', 'asc')->get();

            return $appointment;

        endif;

        return false;

    }

}
