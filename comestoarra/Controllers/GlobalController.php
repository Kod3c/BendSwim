<?php namespace Controllers;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/GlobalControllers.php
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

use Helpers\GlobalHelper;
use Helpers\Huge\Core\HugeAuth;
use Helpers\Huge\Core\HugeSession as ComestoarraSession;
use Models\Backend\InstructorModel;
use Models\Backend\MailboxModel;
use Models\Backend\SwimmerModel;
use Models\Backend\SettingModel;
use Models\Entity\Profile;
use Slim\Slim as ComestoarraSlim;
use Models\Entity\Huge\User;
use Models\Entity\Audit;
use Models\Entity\Setting;
use Carbon\Carbon as ComestoarraCarbon;
use Tracy\Debugger as ComestoarraDebugger;
use Helpers\RainCaptchaHelper;
use Helpers\LanguageHelper;

class GlobalController
{
    protected $app;

    const APP_NAME                  = "DCAS";
    const APP_EMAIL                 = "labs@comestoarra.com";
    const OWNER_EMAIL               = "rizkiwisnuaji@comestoarra.com";
    const LOGIN_ATTEMPT_LIMIT       = 5;
    const LOGIN_SUSPEND_TIME        = '15';
    const FRONTEND_TEMPLATE         = 'Default';

    public function __construct( )
    {

        /*
        |--------------------------------------------------------------------------
        | INSTANCE APP GLOBAL
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->app  = ComestoarraSlim::getInstance();

        /*
        |--------------------------------------------------------------------------
        | INSTANCE APP LANG
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->lang  = LanguageHelper::all();

        HugeAuth::checkSessionConcurrency();
        
        /*
        |--------------------------------------------------------------------------
        | FRONTEND TEMPLATE
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->data['FRONTEND_TEMPLATE'] = self::FRONTEND_TEMPLATE;

        /*
        |--------------------------------------------------------------------------
        | SET INITIAL ARRAY DATA FOR TWIG
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->data = [];
        $this->data['BASE_DIR'] = BASE_DIR;
        $this->data['ID']       = ComestoarraSession::get('user_id');
        $this->data['Swimmer_ID']  = SwimmerModel::getIdOfSwimmerAfterLogin( ComestoarraSession::get('user_name') );
        $this->data['Instructor_ID']  = InstructorModel::getIdOfInstructorAfterLogin( ComestoarraSession::get('user_name') );
        $this->data['USERNAME'] = ComestoarraSession::get('user_name');
        $this->data['EMAIL']    = ComestoarraSession::get('user_email');
        $this->data['PROVIDER'] = ComestoarraSession::get('user_provider_type');
        $this->data['AVATAR']   = ComestoarraSession::get('user_avatar_file');
        $this->data['AVATAR_Instructor'] = InstructorModel::checkAvatarOfInstructorAfterLogin( ComestoarraSession::get('user_name') );
        $this->data['AVATAR_Swimmer'] = SwimmerModel::checkAvatarOfSwimmerAfterLogin( ComestoarraSession::get('user_name') );
        $this->data['ROLE']     = ComestoarraSession::get('user_account_type');
        $this->data['LANG']     = $this->lang;
        $this->data['ENV']      = ENVIRONMENT;

        /*
        |--------------------------------------------------------------------------
        | SET ASSETS PATH
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->data['PUBLIC_DIR']                 = PUBLIC_DIR;
        $this->data['PUBLIC_ASSETS']              = PUBLIC_ASSETS;
        $this->data['HUGE_PATH_AVATARS']          = HUGE_PATH_AVATARS;
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
        $this->data['FULLCALENDAR_CSS']           = FULLCALENDAR_CSS;
        $this->data['FULLCALENDAR_PRINT_CSS']     = FULLCALENDAR_PRINT_CSS;
        $this->data['FULLCALENDAR_JS']            = FULLCALENDAR_JS;
        $this->data['FULLCALENDAR_MOMENT_JS']     = FULLCALENDAR_MOMENT_JS;

        /*
        |--------------------------------------------------------------------------
        | GET APP RESPONSES
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->ip       = $this->app->request()->getIp();
        $this->port     = $this->app->request()->getPort();
        $this->root     = $this->app->request()->getRootUri();
        $this->resource = $this->app->request()->getResourceUri();
        $this->host     = $this->app->request()->getHost();
        $this->method   = $this->app->request()->getMethod();
        $this->body     = $this->app->request()->getBody();
        $this->path     = $this->app->request()->getPath();

        /*
        |--------------------------------------------------------------------------
        | GET APP SETTINGS
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        // ACCESS GLOBALLY
        $this->id         	        = SettingModel::getFieldSetting( 'id' );
        $this->siteUrl         	    = SettingModel::getFieldSetting( 'site_url' );
        $this->siteName        	    = SettingModel::getFieldSetting( 'site_name' );
        $this->siteTitle       	    = SettingModel::getFieldSetting( 'site_title' );
        $this->metaDesc        	    = SettingModel::getFieldSetting( 'meta_desc' );
        $this->metaKey         	    = SettingModel::getFieldSetting( 'meta_key' );
        $this->ownerName       	    = SettingModel::getFieldSetting( 'owner_name' );
        $this->ownerAddress    	    = SettingModel::getFieldSetting( 'owner_address' );
        $this->ownerEmail      	    = SettingModel::getFieldSetting( 'owner_email' );
        $this->ownerPhone      	    = SettingModel::getFieldSetting( 'owner_phone' );
        $this->uploadPath      	    = SettingModel::getFieldSetting( 'upload_path' );
        $this->filesAllowed    	    = SettingModel::getFieldSetting( 'files_allowed' );
        $this->appLogo         	    = SettingModel::getFieldSetting( 'app_logo' );
        $this->appFavicon      	    = SettingModel::getFieldSetting( 'app_favicon' );
        $this->mailEngine      	    = SettingModel::getFieldSetting( 'mail_engine' );
        $this->mailUsedSmtp    	    = SettingModel::getFieldSetting( 'mail_used_smtp' );
        $this->mailSmtpHost    	    = SettingModel::getFieldSetting( 'mail_smtp_host' );
        $this->mailSmtpAuth    	    = SettingModel::getFieldSetting( 'mail_smtp_auth' );
        $this->mailSmtpUsername 	= SettingModel::getFieldSetting( 'mail_smtp_username' );
        $this->mailSmtpPassword 	= SettingModel::getFieldSetting( 'mail_smtp_password' );
        $this->mailSmtpPort 		= SettingModel::getFieldSetting( 'mail_smtp_port' );
        $this->mailSmtpEncryption   = SettingModel::getFieldSetting( 'mail_smtp_encryption' );
        $this->mailSendGridApi      = SettingModel::getFieldSetting( 'mail_sendgrid_api' );
        $this->lastUpdateDate 	    = SettingModel::getFieldSetting( 'last_update_date' );
        $this->lastUpdateUser 	    = SettingModel::getFieldSetting( 'last_update_user' );

        // TWIG PARSER
        $this->data['IP']                   = $this->ip;
        $this->data['NAME']                 = $this->siteName;
        $this->data['TITLE']                = $this->siteTitle;
        $this->data['SETTING_ID'] 			= $this->id;
        $this->data['SITE_URL'] 			= $this->siteUrl;
        $this->data['SITE_NAME'] 			= $this->siteName;
        $this->data['SITE_TITLE'] 			= $this->siteTitle;
        $this->data['META_DESC'] 			= $this->metaDesc;
        $this->data['META_KEY'] 			= $this->metaKey;
        $this->data['OWNER_NAME'] 			= $this->ownerName;
        $this->data['OWNER_ADDRESS'] 		= $this->ownerAddress;
        $this->data['OWNER_EMAIL'] 			= $this->ownerEmail;
        $this->data['OWNER_PHONE'] 			= $this->ownerPhone;
        $this->data['UPLOAD_PATH'] 			= $this->uploadPath;
        $this->data['FILES_ALLOWED'] 		= $this->filesAllowed;
        $this->data['APP_LOGO'] 			= $this->appLogo;
        $this->data['APP_FAVICON'] 			= $this->appFavicon;
        $this->data['MAIL_ENGINE'] 			= $this->mailEngine;
        $this->data['MAIL_USED_SMTP'] 		= $this->mailUsedSmtp;
        $this->data['MAIL_SMTP_HOST'] 		=  $this->mailSmtpHost;
        $this->data['MAIL_SMTP_AUTH'] 		= $this->mailSmtpAuth;
        $this->data['MAIL_SMTP_USERNAME'] 	= $this->mailSmtpUsername;
        $this->data['MAIL_SMTP_PASSWORD'] 	= $this->mailSmtpPassword;
        $this->data['MAIL_SMTP_PORT'] 		= $this->mailSmtpPort;
        $this->data['MAIL_SMTP_ENCRYPTION'] = $this->mailSmtpEncryption;
        $this->data['MAIL_SENDGRID_API']    = $this->mailSendGridApi; // on progress
        $this->data['LAST_UPDATE_DATE'] 	= $this->lastUpdateDate;
        $this->data['LAST_UPDATE_USER'] 	= $this->lastUpdateUser;

        // GLOBAL ACCESS ASSETS
        $this->data['DEFAULT_LOGO_FAVICON'] = HUGE_PATH_AVATARS . 'default.jpg';
        $this->data['DEFAULT_FAVICON']      = HUGE_PATH_AVATARS . 'favicon.png';
        $this->data['SENDER_MAIL_AVATARS']  = HUGE_PATH_AVATARS;
        $this->data['UNREAD_MESSAGE_COUNT'] = MailboxModel::getCountUnreadMailUser( ComestoarraSession::get( 'user_id' ) );
        $this->data['UNREAD_MESSAGE_USER']          = MailboxModel::getAllUnreadMailUser( ComestoarraSession::get( 'user_id' ) );
        $this->data['ALL_USER_INBOX_MESSAGE']       = MailboxModel::getAllInboxMailUser( ComestoarraSession::get( 'user_id' ) );
        $this->data['ALL_USER_OUTBOX_MESSAGE']      = MailboxModel::getAllOutboxMailUser( ComestoarraSession::get( 'user_id' ) );
        $this->data['ALL_USER_ARCHIVED_MESSAGE']    = MailboxModel::getAllArchivedMailUser( ComestoarraSession::get( 'user_id' ) );

        // GLOBAL ACCESS PAGE NAMES
        $this->pages                        = [ 'dashboard', 'settings' ];

        /*
        |--------------------------------------------------------------------------
        | Carbon
        |--------------------------------------------------------------------------
        |
        | @documentation    https://github.com/briannesbitt/Carbon
        | @example  ComestoarraCarbon::now(TIMEZONE);
        |
        */
        $this->timezone = new ComestoarraCarbon();

        if ( ENVIRONMENT == 'development' || ENVIRONMENT == 'debug' ) :
            
            /*
            |--------------------------------------------------------------------------
            | Nette TRACY Debugger
            |--------------------------------------------------------------------------
            |
            | Nette Tracy Debugger
            | @documentation    https://github.com/nette/tracy
            |
            */
            if ( COMESTOARRA_DEBUGGER == 'tracy' ) :

                ComestoarraDebugger::enable();
                ComestoarraDebugger::$strictMode = TRUE;
                ComestoarraDebugger::$email = self::OWNER_EMAIL;
            
            else :
                
                /*
                |--------------------------------------------------------------------------
                | Whoops Debugger
                |--------------------------------------------------------------------------
                |
                | @documentation    https://github.com/filp/whoops
                |
                */
                $whoops = new \Whoops\Run;
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
                $whoops->register();
                
            endif;

        endif;

        /*
        |--------------------------------------------------------------------------
        | RAIN CAPTCHA HELPERS
        |--------------------------------------------------------------------------
        |
        | raincaptcha helpers
        | @documentation    http://simplemvcframework.com/documentation/v2.1/captcha-with-raincaptcha
        |
        */
        $this->raincaptcha    = new RainCaptchaHelper();
        
        /*
        |--------------------------------------------------------------------------
        | FILE MANAGER EKSR+TERNAL CDN SECRET KEY
        |--------------------------------------------------------------------------
        |
        | Description
        |
        */
        $this->data['CDN_SECRET_KEY']       = CDN_SECRET_KEY;

    }

}