<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/AppointmentController.php
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
use Models\Backend\InstructorModel;
use Models\Backend\AppointmentModel;
use Models\Backend\SwimmerModel;
use Models\Backend\UserModel;

class AppointmentController extends GlobalController
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
        $this->permission = UserModel::checkUserRolePermission( 'appointment' );

    }

    public function backendAllAppointment()
    {

        if ( isset ( $this->permission ) ) :

            $this->data['PERMISSION']             = $this->permission;

        endif;

        $this->data['CONTENT']                    = "Success Login !";
        $this->data['APPOINTMENT_ACTIVE']         = "active";
        $this->data['STICKY_NAV_LOWER']           = FALSE;
        $this->data['CONTENT_FLUID']              = TRUE;
        $this->data['time']                       = $this->timezone->now( TIMEZONE );

        $this->data['ALL_APPOINTMENT']            = AppointmentModel::getAllAppointment();
        $this->data['ALL_Swimmer']                = SwimmerModel::getAllSwimmer();
        $this->data['ALL_Instructor']                = InstructorModel::getAllInstructor();
        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );


        $this->app->render('Backend/Content/Appointment/Index.twig', $this->data);

    }

    public function backendAjaxSwimmerAppointmentInstructorTimeTable()
    {

        if ( $this->app->request()->isAjax() ) :

            $ajaxTimeTable = InstructorModel::getAjaxInstructorTimetable( $this->app->request()->post( 'Instructor_id' ) );

            if ( $ajaxTimeTable['timetable_sunday_start'] != "Request Only" && $ajaxTimeTable['timetable_sunday_end'] != "Request Only" ) :

                $sunday = $ajaxTimeTable['timetable_sunday_start'] . " - " . $ajaxTimeTable['timetable_sunday_end'];

            else :

                $sunday = "<span class='label label-danger'>Request Only</span>";

            endif;

            if ( $ajaxTimeTable['timetable_monday_start'] != "Request Only" && $ajaxTimeTable['timetable_monday_end'] != "Request Only" ) :

                $monday = $ajaxTimeTable['timetable_monday_start'] . " - " . $ajaxTimeTable['timetable_monday_end'];

            else :

                $monday = "<span class='label label-danger'>Request Only</span>";

            endif;

            if ( $ajaxTimeTable['timetable_tuesday_start'] != "Request Only" && $ajaxTimeTable['timetable_tuesday_end'] != "Request Only" ) :

                $tuesday = $ajaxTimeTable['timetable_tuesday_start'] . " - " . $ajaxTimeTable['timetable_tuesday_end'];

            else :

                $tuesday = "<span class='label label-danger'>Request Only</span>";

            endif;

            if ( $ajaxTimeTable['timetable_wednesday_start'] != "Request Only" && $ajaxTimeTable['timetable_wednesday_end'] != "Request Only" ) :

                $wednesday = $ajaxTimeTable['timetable_wednesday_start'] . " - " . $ajaxTimeTable['timetable_wednesday_end'];

            else :

                $wednesday = "<span class='label label-danger'>Request Only</span>";

            endif;

            if ( $ajaxTimeTable['timetable_thursday_start'] != "Request Only" && $ajaxTimeTable['timetable_thursday_end'] != "Request Only" ) :

                $thursday = $ajaxTimeTable['timetable_thursday_start'] . " - " . $ajaxTimeTable['timetable_thursday_end'];

            else :

                $thursday = "<span class='label label-danger'>Request Only</span>";

            endif;

            if ( $ajaxTimeTable['timetable_friday_start'] != "Request Only" && $ajaxTimeTable['timetable_friday_end'] != "Request Only" ) :

                $friday = $ajaxTimeTable['timetable_friday_start'] . " - " . $ajaxTimeTable['timetable_friday_end'];

            else :

                $friday = "<span class='label label-danger'>Request Only</span>";

            endif;

            if ( $ajaxTimeTable['timetable_saturday_start'] != "Request Only" && $ajaxTimeTable['timetable_saturday_end'] != "Request Only" ) :

                $saturday = $ajaxTimeTable['timetable_saturday_start'] . " - " . $ajaxTimeTable['timetable_saturday_end'];

            else :

                $saturday = "<span class='label label-danger'>Request Only</span>";

            endif;


            echo "<table class=\"table table-bordered table-hover\">";
            echo "<tbody>";
            echo "<tr>";
            echo "<td>Sun</td>";
            echo "<td>".$sunday."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Mon</td>";
            echo "<td>".$monday."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Tue</td>";
            echo "<td>".$tuesday."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Wed</td>";
            echo "<td>".$wednesday."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Thu</td>";
            echo "<td>".$thursday."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Fri</td>";
            echo "<td>".$friday."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Sat</td>";
            echo "<td>".$saturday."</td>";
            echo "</tr>";
            echo "</tbody>";
            echo "</table>";

        endif;


    }

    public function backendAjaxSwimmerAppointmentInstructorDayTimeTable()
    {

        if ( $this->app->request()->isAjax() ) :

            $ajaxTimeTable = InstructorModel::getAjaxInstructorTimetable( $this->app->request()->post( 'Instructor_id' ) );

            if ( $this->app->request()->post( 'day_parse' ) == "sunday" ) :

                if ( $ajaxTimeTable['timetable_sunday_start'] != "Request Only" && $ajaxTimeTable['timetable_sunday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_sunday_start'] . " - " . $ajaxTimeTable['timetable_sunday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_sunday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_sunday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;

            if ( $this->app->request()->post( 'day_parse' ) == "monday" ) :

                if ( $ajaxTimeTable['timetable_monday_start'] != "Request Only" && $ajaxTimeTable['timetable_monday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_monday_start'] . " - " . $ajaxTimeTable['timetable_monday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_monday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_monday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;

            if ( $this->app->request()->post( 'day_parse' ) == "tuesday" ) :

                if ( $ajaxTimeTable['timetable_tuesday_start'] != "Request Only" && $ajaxTimeTable['timetable_tuesday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_tuesday_start'] . " - " . $ajaxTimeTable['timetable_tuesday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_tuesday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_tuesday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;

            if ( $this->app->request()->post( 'day_parse' ) == "wednesday" ) :

                if ( $ajaxTimeTable['timetable_wednesday_start'] != "Request Only" && $ajaxTimeTable['timetable_wednesday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_wednesday_start'] . " - " . $ajaxTimeTable['timetable_wednesday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_wednesday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_wednesday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;

            if ( $this->app->request()->post( 'day_parse' ) == "thursday" ) :

                if ( $ajaxTimeTable['timetable_thursday_start'] != "Request Only" && $ajaxTimeTable['timetable_thursday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_thursday_start'] . " - " . $ajaxTimeTable['timetable_thursday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_thursday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_thursday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;

            if ( $this->app->request()->post( 'day_parse' ) == "friday" ) :

                if ( $ajaxTimeTable['timetable_friday_start'] != "Request Only" && $ajaxTimeTable['timetable_friday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_friday_start'] . " - " . $ajaxTimeTable['timetable_friday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_friday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_friday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;

            if ( $this->app->request()->post( 'day_parse' ) == "saturday" ) :

                if ( $ajaxTimeTable['timetable_saturday_start'] != "Request Only" && $ajaxTimeTable['timetable_saturday_end'] != "Request Only" ) :

                    $timeAtThatDay = $ajaxTimeTable['timetable_saturday_start'] . " - " . $ajaxTimeTable['timetable_saturday_end'];
                    $timeAtThatDayStartTime = $ajaxTimeTable['timetable_saturday_start'];
                    $timeAtThatDayEndTime = $ajaxTimeTable['timetable_saturday_end'];

                else :

                    $timeAtThatDay = "Request Only";
                    $timeAtThatDayStartTime = "08:00";
                    $timeAtThatDayEndTime = "21:00";

                endif;

            endif;


            if ( ! empty ( $timeAtThatDay ) ) :

                if ( $timeAtThatDay == "Request Only" ) :

                    echo " <p class='help-block'>Instructor time table on " . " <span class='label label-primary'>" . $this->app->request()->post( 'day_parse' ) . "</span>" . " was <span class='label label-danger'>" . $timeAtThatDay . "</span></p>";

                    if ( ! empty ( $timeAtThatDayStartTime ) && ! empty ( $timeAtThatDayEndTime ) ) :

                        $patternn = "/^0/";  // Regex

                        $integerTimeStartFormat = str_replace ( ":00", "", $timeAtThatDayStartTime );
                        $integerTimeEndFormat = str_replace ( ":00", "", $timeAtThatDayEndTime );

                        $rpltxt = "";

                        $resultPregReplaceStartTime = preg_replace ( $patternn, $rpltxt, $integerTimeStartFormat );
                        $resultPregReplaceEndTime = preg_replace ( $patternn, $rpltxt, $integerTimeEndFormat );

                        $intervalTime = $resultPregReplaceEndTime - $resultPregReplaceStartTime;

                        echo $intervalTime . " hours<br/><hr>";

                        $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":00";
                        $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":30";

                        for ( $i = $resultPregReplaceStartTime; $i < $resultPregReplaceEndTime - 1; $i++ ) :

                            $resultPregReplaceStartTime += 1;
                            $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":00";
                            $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":30";

                        endfor;

                        $tempTimeSlotAll[] = $resultPregReplaceEndTime . ":00";

                        $getInstructorAvailableTimeSlotAtThatDay = AppointmentModel::getInstructorAvailableTimeSlotAtThatDay( $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'appointment_date_pick' ) );

                        foreach ( $getInstructorAvailableTimeSlotAtThatDay as $slot ) :

                            $getInstructorAvailableTimeSlotAtThatDayAsArray[] = $slot["appointment_time"];

                        endforeach;

                        echo "<div class='form-group'><label for='appointment_time_pick'>Lesson Time Available</label><hr>";

                        if ( ! empty ( $tempTimeSlotAll ) ) :

                            if ( ! empty ( $getInstructorAvailableTimeSlotAtThatDayAsArray ) ) :

                                $resultAvailableSlotTimeInstructor = array_diff( $tempTimeSlotAll, $getInstructorAvailableTimeSlotAtThatDayAsArray );

                            else :

                                $resultAvailableSlotTimeInstructor = $tempTimeSlotAll;

                            endif;

                            if ( ! empty ( $resultAvailableSlotTimeInstructor ) ) :

                                foreach ( $resultAvailableSlotTimeInstructor as $final ) :

                                    $finalArrayAfterCompare[] = $final;
                                    echo "<input type='radio' id='appointment_time_pick' name='appointment_time_pick' value='". $final ."'> " . $final . "&nbsp;&nbsp;&nbsp;";

                                endforeach;

                            endif;

                        endif;

                        echo "</div>";

                    endif;

                    
                else :

                    echo " <p class='help-block'>Instructor time table on " . " <span class='label label-primary'>" . $this->app->request()->post( 'day_parse' ) . "</span>" . " was at : <span class='label label-primary'>" . $timeAtThatDay . "</span></p>";

                    if ( ! empty ( $timeAtThatDayStartTime ) && ! empty ( $timeAtThatDayEndTime ) ) :

                        $patternn = "/^0/";  // Regex

                        $integerTimeStartFormat = str_replace ( ":00", "", $timeAtThatDayStartTime );
                        $integerTimeEndFormat = str_replace ( ":00", "", $timeAtThatDayEndTime );

                        $rpltxt = "";

                        $resultPregReplaceStartTime = preg_replace ( $patternn, $rpltxt, $integerTimeStartFormat );
                        $resultPregReplaceEndTime = preg_replace ( $patternn, $rpltxt, $integerTimeEndFormat );

                        $intervalTime = $resultPregReplaceEndTime - $resultPregReplaceStartTime;

                        echo $intervalTime . " hours<br/><hr>";

                        $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":00";
                        $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":30";

                        for ( $i = $resultPregReplaceStartTime; $i < $resultPregReplaceEndTime - 1; $i++ ) :

                            $resultPregReplaceStartTime += 1;
                            $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":00";
                            $tempTimeSlotAll[] = $resultPregReplaceStartTime . ":30";

                        endfor;

                        $tempTimeSlotAll[] = $resultPregReplaceEndTime . ":00";

                        $getInstructorAvailableTimeSlotAtThatDay = AppointmentModel::getInstructorAvailableTimeSlotAtThatDay( $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'appointment_date_pick' ) );

                        foreach ( $getInstructorAvailableTimeSlotAtThatDay as $slot ) :

                            $getInstructorAvailableTimeSlotAtThatDayAsArray[] = $slot["appointment_time"];

                        endforeach;

                        echo "<div class='form-group'><label for='appointment_time_pick'>Lesson Time Available</label><hr>";

                        if ( ! empty ( $tempTimeSlotAll ) ) :

                            if ( ! empty ( $getInstructorAvailableTimeSlotAtThatDayAsArray ) ) :

                                $resultAvailableSlotTimeInstructor = array_diff( $tempTimeSlotAll, $getInstructorAvailableTimeSlotAtThatDayAsArray );

                            else :

                                $resultAvailableSlotTimeInstructor = $tempTimeSlotAll;

                            endif;

                            if ( ! empty ( $resultAvailableSlotTimeInstructor ) ) :

                                foreach ( $resultAvailableSlotTimeInstructor as $final ) :

                                    $finalArrayAfterCompare[] = $final;
                                    echo "<input type='radio' id='appointment_time_pick' name='appointment_time_pick' value='". $final ."'> " . $final . "&nbsp;&nbsp;&nbsp;";

                                endforeach;

                            endif;

                        endif;

                        echo "</div>";

                    endif;

                endif;

            endif;

        endif;

    }

    public function backendActionAddAppointmentSwimmer()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::registerNewSwimmerAppointment(

                $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'Swimmer_id' ), $this->app->request()->post( 'type' ), $this->app->request()->post( 'appointment_date_pick' ), $this->app->request()->post( 'appointment_time_pick' ), $this->app->request()->post( 'notes' )

            );
            
            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Swimmers' ) . $this->app->request()->post( 'Swimmer_id' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Swimmer Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );
                    
            endif;

        endif;

    }

    public function backendActionAddAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::registerNewSwimmerInstructorAppointment(

                $this->app->request()->post( 'Swimmer_id' ), $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'type' ), $this->app->request()->post( 'appointment_date_pick' ), $this->app->request()->post( 'appointment_time_pick' ), $this->app->request()->post( 'notes' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'appointments' ) );

        endif;

    }

    public function backendActionQuickAddAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::registerNewSwimmerInstructorAppointment(

                $this->app->request()->post( 'Swimmer_id' ), $this->app->request()->post( 'Instructor_id' ), $this->app->request()->post( 'type' ), $this->app->request()->post( 'appointment_date_pick' ), $this->app->request()->post( 'appointment_time_pick' ), $this->app->request()->post( 'notes' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

    }

    public function backendActionActivatedAppointmentSwimmer()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::activatedSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Swimmers' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Swimmer Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );
                    
            endif;

        endif;

    }

    public function backendActionActivatedAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::activatedSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'appointments' ) );

        endif;

    }

    public function backendActionDashboardActivatedAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::activatedSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

    }

    public function backendActionCancelledAppointmentSwimmer()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::cancelledSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Swimmers' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Swimmer Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );
                    
            endif;

        endif;

    }

    public function backendActionCancelledAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::cancelledSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'appointments' ) );

        endif;

    }

    public function backendActionDashboardCancelledAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::cancelledSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

    }

    public function backendActionDeletedAppointmentSwimmer()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::deletedSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'Swimmers' ) );

        endif;

    }

    public function backendActionDeletedAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::deletedSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'appointments' ) );

        endif;

    }

    public function backendActionDashboardDeletedAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::deletedSwimmerAppointment(

                $this->app->request()->post( 'appointment_id' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

    }

    public function backendActionDashboardFinishedAppointmentSwimmerInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::finishedSwimmerAppointmentByInstructor(

                $this->app->request()->post( 'appointment_id' ), $this->app->request()->post( 'notes_Instructor' )

            );

            $this->app->response()->redirect( $this->app->urlFor( 'dashboard' ) );

        endif;

    }

    public function backendActionActivatedAppointmentSwimmerByInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::activatedSwimmerAppointmentByInstructor(

                $this->app->request()->post( 'appointment_id' ), $this->app->request()->post( 'notes_Instructor' )

            );
            
            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Instructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionCancelledAppointmentSwimmerByInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::cancelledSwimmerAppointmentByInstructor(

                $this->app->request()->post( 'appointment_id' ), $this->app->request()->post( 'notes_Instructor' )

            );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Instructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionFinishedAppointmentSwimmerByInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::finishedSwimmerAppointmentByInstructor(

                $this->app->request()->post( 'appointment_id' ), $this->app->request()->post( 'notes_Instructor' )

            );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Instructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

    public function backendActionDeletedAppointmentSwimmerByInstructor()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            AppointmentModel::deletedSwimmerAppointmentByInstructor(

                $this->app->request()->post( 'appointment_id' )

            );

            if ( $this->data['PROVIDER'] == 'DEFAULT' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'Instructors' ) );
            
            elseif ( $this->data['PROVIDER'] == 'BSS Instructor Registration' ) :
                
                $this->app->response()->redirect( $this->app->urlFor( 'myProfile' ) );
                    
            endif;

        endif;

    }

}