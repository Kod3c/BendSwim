<?php namespace Helpers\Huge\Core;

use Models\Backend\LoginModel;
use Slim\Slim;

class HugeAuth
{
    public static function checkAuthentication()
    {

        if ( ! LoginModel::isUserLoggedIn() ) :

            Slim::getInstance()->response()->redirect(  Slim::getInstance()->urlFor( 'login' ) );

            return false;

        endif;

        return true;
    }

    public static function checkAdminAuthentication()
    {

        // if user is not logged in or is not an admin (= not role type 7)
        if ( ! LoginModel::isUserLoggedIn() || HugeSession::get("user_account_type") != 7 ) :

            Slim::getInstance()->response()->redirect(  Slim::getInstance()->urlFor( 'login' ) );

            return false;

        endif;

        return true;
    }

    public static function checkSessionConcurrency()
    {
        if ( HugeSession::userIsLoggedIn() ) :

            if ( HugeSession::isConcurrentSessionExists() ) :

                Slim::getInstance()->response()->redirect(  Slim::getInstance()->urlFor( 'logout' ) );

            endif;

        endif;
    }

}
