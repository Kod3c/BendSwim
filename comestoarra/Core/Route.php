<?php

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Core/Route.php
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

/*
 *---------------------------------------------------------------
 * FRONTEND ROUTES
 *---------------------------------------------------------------
 */
$app->group( '', function() use ( $app ) {

    $app->get( '/', 'Controllers\Frontend\FrontendController:frontendHome' )->name( 'homepage' );

});

/*
 *---------------------------------------------------------------
 * BACKEND ROUTES
 *---------------------------------------------------------------
 */
$app->group( '/manage', function() use ( $app ) {

    $app->get( '/', 'Controllers\Backend\AuthController:backendAuthorized' )->name( 'manage' );

    $app->get( '/login', 'Controllers\Backend\AuthController:backendLogin' )->name( 'login' );

    $app->post( '/login/action', 'Controllers\Backend\AuthController:backendActionLogin' )->name( 'loginAction' );

    $app->map( '/login/verifypasswordreset/(:username)/(:hash)', 'Controllers\Backend\AuthController:backendPasswordReset' )->via( 'GET', 'POST' )->name( 'loginPasswordReset' );

    $app->post( '/login/setpassword', 'Controllers\Backend\AuthController:backendActionPasswordReset' )->name( 'resetPasswordAction' );

    $app->map( '/login/verify/(:id)/(:activation)', 'Controllers\Backend\AuthController:backendActionSignupVerify' )->via( 'GET', 'POST' )->name( 'loginVerify' );

    $app->map( '/login/cookie', 'Controllers\Backend\AuthController:backendLoginWithCookie' )->via( 'GET', 'POST' )->name( 'loginCookie' );

    $app->get( '/signup', 'Controllers\Backend\AuthController:backendSignup' )->name( 'signup' );

    $app->post( '/signup', 'Controllers\Backend\AuthController:backendActionSignup' )->name( 'signupAction' );

    $app->get( '/forgot', 'Controllers\Backend\AuthController:backendForgot' )->name( 'forgot' );

    $app->post( '/forgot', 'Controllers\Backend\AuthController:backendActionForgot' )->name( 'forgotAction' );

    $app->get( '/logout', 'Controllers\Backend\AuthController:backendLogout' )->name( 'logout' );

    $app->get( '/dashboard', 'Controllers\Backend\DashboardController:backendDashboard' )->name( 'dashboard' );

    $app->get( '/profile', 'Controllers\Backend\UserController:backendProfile' )->name( 'myProfile' );

    $app->post( '/profile/action', 'Controllers\Backend\UserController:backendActionUpdateProfile' )->name( 'updateProfileAction' );

    $app->post( '/profile/change-password', 'Controllers\Backend\UserController:backendActionChangePassword' )->name( 'changePasswordAction' );

    $app->post( '/profile/change-email', 'Controllers\Backend\UserController:backendActionChangeEmail' )->name( 'changeEmailAction' );

    $app->post( '/profile/change-username', 'Controllers\Backend\UserController:backendActionChangeUsername' )->name( 'changeUsernameAction' );

    $app->post( '/profile/upload-avatar', 'Controllers\Backend\UserController:backendActionUploadAvatar' )->name( 'uploadAvatarAction' );

    $app->group( '/users', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\UserController:backendAllUser' )->name( 'users' );

        $app->map( '/datatable/all', 'Controllers\Backend\UserController:backendUserDatatable' )->via( 'POST' )->name( 'userDataTable' );

        $app->post( '/add-user/action', 'Controllers\Backend\UserController:backendActionAddUser' )->name( 'addUserAction' );

        $app->post( '/add-role/action', 'Controllers\Backend\UserController:backendActionAddRole' )->name( 'addRoleAction' );

        $app->post( '/suspend/action', 'Controllers\Backend\UserController:backendActionSuspendUser' )->name( 'suspendUserAction' );

        $app->post( '/ban/action', 'Controllers\Backend\UserController:backendActionBanUser' )->name( 'banUserAction' );

        $app->post( '/root/action', 'Controllers\Backend\UserController:backendActionRootUser' )->name( 'rootUserAction' );

        $app->post( '/role/action', 'Controllers\Backend\UserController:backendActionRoleUser' )->name( 'roleUserAction' );

        $app->post( '/update-role', 'Controllers\Backend\UserController:backendUpdateRole' )->name( 'updateRoleAction' );

        $app->post( '/delete-role', 'Controllers\Backend\UserController:backendDeleteRole' )->name( 'deleteRoleAction' );

        $app->get( '/(:id)', 'Controllers\Backend\UserController:backendUserProfile' );

    });

    $app->group( '/Swimmer', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\SwimmerController:backendAllSwimmer' )->name( 'Swimmers' );

        $app->post( '/add-Swimmer/action', 'Controllers\Backend\SwimmerController:backendActionAddSwimmer' )->name( 'addSwimmerAction' );

        $app->post( '/add-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionAddAppointmentSwimmer' )->name( 'addSwimmerAppointmentAction' );

        $app->post( '/add-Swimmer-Instructor-appointment/action', 'Controllers\Backend\AppointmentController:backendActionAddAppointmentSwimmerInstructor' )->name( 'addSwimmerInstructorAppointmentAction' );

        $app->post( '/quick-add-Swimmer-Instructor-appointment/action', 'Controllers\Backend\AppointmentController:backendActionQuickAddAppointmentSwimmerInstructor' )->name( 'quickAddSwimmerInstructorAppointmentAction' );

        $app->post( '/activated-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionActivatedAppointmentSwimmer' )->name( 'activatedSwimmerAppointmentAction' );

        $app->post( '/cancelled-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionCancelledAppointmentSwimmer' )->name( 'cancelledSwimmerAppointmentAction' );

        $app->post( '/deleted-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDeletedAppointmentSwimmer' )->name( 'deletedSwimmerAppointmentAction' );

        $app->post( '/ajax-Instructor-time-table/action', 'Controllers\Backend\AppointmentController:backendAjaxSwimmerAppointmentInstructorTimeTable' )->name( 'SwimmerAppointmentInstructorTimeTable' );

        $app->post( '/ajax-Instructor-day-selected/action', 'Controllers\Backend\AppointmentController:backendAjaxSwimmerAppointmentInstructorDayTimeTable' )->name( 'SwimmerAppointmentInstructorDayTimeTable' );

        $app->post( '/activated-Swimmer-Instructor-appointment/action', 'Controllers\Backend\AppointmentController:backendActionActivatedAppointmentSwimmerInstructor' )->name( 'activatedSwimmerInstructorAppointmentAction' );

        $app->post( '/cancelled-Swimmer-Instructor-appointment/action', 'Controllers\Backend\AppointmentController:backendActionCancelledAppointmentSwimmerInstructor' )->name( 'cancelledSwimmerInstructorAppointmentAction' );

        $app->post( '/deleted-Swimmer-Instructor-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDeletedAppointmentSwimmerInstructor' )->name( 'deletedSwimmerInstructorAppointmentAction' );

        $app->post( '/finished-Swimmer-Instructor-dashboard-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDashboardFinishedAppointmentSwimmerInstructor' )->name( 'finishedSwimmerInstructorAppointmentDashboardAction' );

        $app->post( '/activated-Swimmer-Instructor-dashboard-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDashboardActivatedAppointmentSwimmerInstructor' )->name( 'activatedSwimmerInstructorAppointmentDashboardAction' );

        $app->post( '/cancelled-Swimmer-Instructor-dashboard-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDashboardCancelledAppointmentSwimmerInstructor' )->name( 'cancelledSwimmerInstructorAppointmentDashboardAction' );

        $app->post( '/deleted-Swimmer-Instructor-dashboard-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDashboardDeletedAppointmentSwimmerInstructor' )->name( 'deletedSwimmerInstructorAppointmentDashboardAction' );

        $app->post( '/add-auth/action', 'Controllers\Backend\SwimmerController:backendActionAddAuthSwimmer' )->name( 'addSwimmerAuthAction' );

        $app->get( '/(:id)', 'Controllers\Backend\SwimmerController:backendSwimmerProfile' );

        $app->post( '/profile/action', 'Controllers\Backend\SwimmerController:backendActionUpdateProfile' )->name( 'updateSwimmerProfileAction' );

        $app->post( '/profile/upload-avatar', 'Controllers\Backend\SwimmerController:backendActionUploadAvatar' )->name( 'uploadSwimmerAvatarAction' );

        $app->post( '/profile/change-password', 'Controllers\Backend\SwimmerController:backendActionChangePassword' )->name( 'changeSwimmerPasswordAction' );

        $app->post( '/profile/change-email', 'Controllers\Backend\SwimmerController:backendActionChangeEmail' )->name( 'changeSwimmerEmailAction' );

        $app->post( '/profile/change-username', 'Controllers\Backend\SwimmerController:backendActionChangeUsername' )->name( 'changeSwimmerUsernameAction' );

    });

    $app->group( '/Instructor', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\InstructorController:backendAllInstructor' )->name( 'Instructors' );

        $app->post( '/add-Instructor/action', 'Controllers\Backend\InstructorController:backendActionAddInstructor' )->name( 'addInstructorAction' );

        $app->post( '/add-auth/action', 'Controllers\Backend\InstructorController:backendActionAddAuthInstructor' )->name( 'addInstructorAuthAction' );

        $app->post( '/timetable/action', 'Controllers\Backend\InstructorController:backendActionInstructorTimeTable' )->name( 'timeTableAction' );

        $app->get( '/(:id)', 'Controllers\Backend\InstructorController:backendInstructorProfile' );

        $app->post( '/profile/action', 'Controllers\Backend\InstructorController:backendActionUpdateProfile' )->name( 'updateInstructorProfileAction' );

        $app->post( '/profile/upload-avatar', 'Controllers\Backend\InstructorController:backendActionUploadAvatar' )->name( 'uploadInstructorAvatarAction' );

        $app->post( '/profile/change-password', 'Controllers\Backend\InstructorController:backendActionChangePassword' )->name( 'changeInstructorPasswordAction' );

        $app->post( '/profile/change-email', 'Controllers\Backend\InstructorController:backendActionChangeEmail' )->name( 'changeInstructorEmailAction' );

        $app->post( '/profile/change-username', 'Controllers\Backend\InstructorController:backendActionChangeUsername' )->name( 'changeInstructorUsernameAction' );

        $app->post( '/profile/update-timetable', 'Controllers\Backend\InstructorController:backendActionUpdateTimetable' )->name( 'updateInstructorTimetableAction' );

        $app->post( '/activated-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionActivatedAppointmentSwimmerByInstructor' )->name( 'activatedSwimmerAppointmentByInstructorAction' );

        $app->post( '/cancelled-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionCancelledAppointmentSwimmerByInstructor' )->name( 'cancelledSwimmerAppointmentByInstructorAction' );

        $app->post( '/finished-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionFinishedAppointmentSwimmerByInstructor' )->name( 'finishedSwimmerAppointmentByInstructorAction' );

        $app->post( '/deleted-Swimmer-appointment/action', 'Controllers\Backend\AppointmentController:backendActionDeletedAppointmentSwimmerByInstructor' )->name( 'deletedSwimmerAppointmentByInstructorAction' );

    });

    $app->group( '/appointment', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\AppointmentController:backendAllAppointment' )->name( 'appointments' );

    });

    $app->group( '/assets', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\AssetsmanagerController:backendAssetManager' )->name( 'assetsmanager' );
        
    });

    $app->group( '/calendar', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\CalendarController:backendCalendarManager' )->name( 'calendar' );

    });

    $app->group( '/mailbox', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\MailboxController:backendMailbox' )->name( 'mailbox' );

        $app->post( '/', 'Controllers\Backend\MailboxController:backendComposeMail' )->name( 'composeMessage' );

        $app->post( '/compose-message-from-profile', 'Controllers\Backend\MailboxController:backendComposeMailFromProfile' )->name( 'composeMessageFromProfileAction' );

        $app->post( '/delete-by-sender', 'Controllers\Backend\MailboxController:backendDeleteMailBySender' )->name( 'deleteMailBySender' );

        $app->post( '/delete-by-receiver', 'Controllers\Backend\MailboxController:backendDeleteMailByReceiver' )->name( 'deleteMailByReceiver' );

        $app->post( '/mark-as-read', 'Controllers\Backend\MailboxController:backendMarkAsRead' )->name( 'markAsRead' );

        $app->post( '/mark-as-unread', 'Controllers\Backend\MailboxController:backendMarkAsUnread' )->name( 'markAsUnread' );

        $app->post( '/mark-as-archived', 'Controllers\Backend\MailboxController:backendMarkAsArchived' )->name( 'markAsArchived' );

        $app->post( '/mark-as-unarchived', 'Controllers\Backend\MailboxController:backendMarkAsUnarchived' )->name( 'markAsUnarchived' );

    });

    $app->group( '/setting', function() use ( $app ) {

        $app->get( '/', 'Controllers\Backend\SettingController:backendGlobalSetting' )->name( 'globalSetting' );

        $app->post( '/setting/update-global', 'Controllers\Backend\SettingController:backendActionUpdateGlobalSetting' )->name( 'updateGlobalSettingAction' );

        $app->post( '/setting/update-mail', 'Controllers\Backend\SettingController:backendActionUpdateMailSetting' )->name( 'updateMailSettingAction' );

        $app->post( '/setting/update-logo', 'Controllers\Backend\SettingController:backendActionUpdateGlobalLogo' )->name( 'updateGlobalLogoAction' );

        $app->post( '/setting/update-favicon', 'Controllers\Backend\SettingController:backendActionUpdateGlobalFavicon' )->name( 'updateGlobalFaviconAction' );

        $app->get( '/audit', 'Controllers\Backend\SettingController:backendAllAudit' )->name( 'allAuditTrails' );

        $app->map( '/datatable/all', 'Controllers\Backend\SettingController:backendAuditDatatable' )->via( 'POST' )->name( 'auditDataTable' );

        $app->post( '/trim-data', 'Controllers\Backend\SettingController:backendTrimAllAudit' )->name( 'trimLogs' );

        $app->post( '/delete-data', 'Controllers\Backend\SettingController:backendDeleteAllAudit' )->name( 'deleteLogs' );

    });

});