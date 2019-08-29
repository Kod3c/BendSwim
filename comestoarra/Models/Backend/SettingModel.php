<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\LanguageHelper;
use Models\Entity\Bstats;
use Models\Entity\Setting;
use Slim\Slim;

class SettingModel
{

    public static function getAllSetting()
    {

        $setting = Setting::all();

        return $setting;
    }

    public static function getFieldSetting( $field )
    {

        $setting = Setting::take( 1 )->select( $field )->first();

        return $setting->$field;
    }

    public static function saveGlobalSetting( $id, $site_url, $site_name, $site_title, $meta_desc, $meta_key, $owner_name, $owner_address, $owner_email, $owner_phone, $upload_path, $files_allowed, $user_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $query = Setting::where( 'id', '=', $id )->take( 1 )->update( array(
            "site_url"              => $site_url,
            "site_name"             => $site_name,
            "site_title"            => $site_title,
            "meta_desc"             => $meta_desc,
            "meta_key"              => $meta_key,
            "owner_name"            => $owner_name,
            "owner_address"         => $owner_address,
            "owner_email"           => $owner_email,
            "owner_phone"           => $owner_phone,
            "upload_path"           => $upload_path,
            "files_allowed"         => str_replace(' ', '', $files_allowed),
            "last_update_date"      => date( 'Y-m-d H:i:s' ),
            "last_update_user"      => $user_id
        ));

        if ( count( $query ) == 1 ) :

            return true;

        endif;

        return false;

    }

    public static function saveMailSetting( $id, $mail_engine, $mail_used_smtp, $mail_smtp_host, $mail_smtp_auth, $mail_smtp_username, $mail_smtp_password, $mail_smtp_port, $mail_smtp_encryption, $mail_sendgrid_api, $user_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $query = Setting::where( 'id', '=', $id )->take( 1 )->update( array(
            "mail_engine"           => $mail_engine,
            "mail_used_smtp"        => $mail_used_smtp,
            "mail_smtp_host"        => $mail_smtp_host,
            "mail_smtp_auth"        => $mail_smtp_auth,
            "mail_smtp_username"    => $mail_smtp_username,
            "mail_smtp_password"    => $mail_smtp_password,
            "mail_smtp_port"        => $mail_smtp_port,
            "mail_smtp_encryption"  => $mail_smtp_encryption,
            "mail_sendgrid_api"     => $mail_sendgrid_api,
            "last_update_date"      => date( 'Y-m-d H:i:s' ),
            "last_update_user"      => $user_id
        ));

        if ( count( $query ) == 1 ) :

            return true;

        endif;

        return false;
    }

    public static function saveLogoSetting( $id, $app_logo, $user_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $query = Setting::where( 'id', '=', $id )->take( 1 )->update( array(
            "app_logo"              => $app_logo,
            "last_update_date"      => date( 'Y-m-d H:i:s' ),
            "last_update_user"      => $user_id
        ));

        if ( count( $query ) == 1 ) :

            return true;

        endif;

        return false;

    }

    public static function saveFaviconSetting( $id, $app_favicon, $user_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $query = Setting::where( 'id', '=', $id )->take( 1 )->update( array(
            "app_favicon"           => $app_favicon,
            "last_update_date"      => date( 'Y-m-d H:i:s' ),
            "last_update_user"      => $user_id
        ));

        if ( count( $query ) == 1 ) :

            return true;

        endif;

        return false;

    }

    public static function getBrowserStatistics()
    {

        // FOR MYSQL < 5.7.x
        // $query = Bstats::where('total', '>', '0')->select('browser as label', 'total as value')
        //     ->groupBy('browser')
        //     ->get();

        // FOR MYSQL >= 5.7.x
        $query = Bstats::where('total', '>', '0')->select('browser as label', 'total as value')->get();

        if ( count( $query ) > 0 ) :

            return $query;

        endif;

        return false;

    }

}
