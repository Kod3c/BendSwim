<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\EncryptionHelper;
use Helpers\GlobalHelper;
use Helpers\LanguageHelper;
use Models\Entity\Bstats;
use Slim\Slim;
use Models\Entity\Huge\User;
use Helpers\Huge\Core\HugeAvatar;
use Helpers\Huge\Core\HugeSession;
use Helpers\Huge\Core\HugeConfig;
use Helpers\Huge\Core\HugeText;

final class LoginModel
{

    public static function login( $user_name, $user_password, $set_remember_me_cookie = null )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( GlobalHelper::regexString( $user_name ) === TRUE ) : // If FALSE, then username

            if ( ! GlobalHelper::checkValidEmail( $user_name ) ) :

                Slim::getInstance()->flash( 'error', 'Input not valid !' );
                return false;

            endif;

        endif;

        if ( empty ( $user_name ) OR empty ( $user_password ) ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_USERNAME_OR_PASSWORD_FIELD_EMPTY' ) );
            return false;

        endif;

        // checks if user exists, if login is not blocked (due to failed logins) and if password fits the hash
        $result = self::validateAndGetUser ( $user_name, $user_password );

        // check if that user exists. We don't give back a cause in the feedback to avoid giving an attacker details.
        if ( ! $result ) :
            
            // Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_LOGIN_FAILED' ) );
            return false;

        endif;

        // stop the user's login if account has been soft deleted
        if ( $result->user_deleted == 1 ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_DELETED' ) );
            return false;

        endif;

        // stop the user from logging in if user has a suspension, display how long they have left in the feedback.
        if ( $result->user_suspension_timestamp != null && $result->user_suspension_timestamp - time() > 0 ) :

            $suspensionTimer = HugeText::get( 'FEEDBACK_ACCOUNT_SUSPENDED' ) . round ( abs ( $result->user_suspension_timestamp - time() )/60/60, 2 ) . " hours left";
            Slim::getInstance()->flash( 'error', $suspensionTimer );
            return false;

        endif;

        // reset the failed login counter for that user (if necessary)
        if ( $result->user_last_failed_login > 0 ) :

            self::resetFailedLoginCounterOfUser ($result->user_name);

        endif;

        // save timestamp of this login in the database line of that user
        self::saveTimestampOfLoginOfUser ( $result->user_name );

        // if user has checked the "remember me" checkbox, then write token into database and into cookie
        if ( $set_remember_me_cookie ) :

            self::setRememberMeInDatabaseAndCookie( $result->user_id );

        endif;

        // successfully logged in, so we write all necessary data into the session and set "user_logged_in" to true
        self::setSuccessfulLoginIntoSession(
            $result->user_id, $result->user_name, $result->user_email, $result->user_account_type, $result->role_id, $result->user_provider_type
        );

        AuditModel::saveAuditTrails( $result->user_id, ucfirst( $result->user_name ) . ' ' . LanguageHelper::get( 'AUDIT_SUCCESS_LOGIN' ) . ' ' . LanguageHelper::get( 'GLOBAL_FROM' ) . ' : ' . Slim::getInstance()->request()->getIp() );

        self::saveBrowserStats( GlobalHelper::getBrowser() );

        // return true to make clear the login was successful
        // maybe do this in dependence of setSuccessfulLoginIntoSession ?
        return true;

    }

    public static function saveBrowserStats( $browser_name_detect )
    {

        $get_total_target = Bstats::where( 'browser', '=', $browser_name_detect['name'] )->select( 'total' )->take( 1 )->first();

        $query = Bstats::where( 'browser', '=', $browser_name_detect['name'] )
            ->take( 1 )
            ->update( array( "total" =>  $get_total_target['total'] + 1 )
        );

        if ( count( $query ) == 1 ) :

            return true;

        endif;

        return false;

    }

    /**
     * Validates the inputs of the users, checks if password is correct etc.
     * If successful, user is returned
     *
     * @param $user_name
     * @param $user_password
     *
     * @return bool|mixed
     */
    private static function validateAndGetUser( $user_name, $user_password )
    {
        // brute force attack mitigation: use session failed login count and last failed login for not found users.
        // block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
        // (limits user searches in database)
        if ( HugeSession::get( 'failed-login-count' ) >= 3 AND ( HugeSession::get( 'last-failed-login' ) > ( time() - 30 ) ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_LOGIN_FAILED_3_TIMES') );
            return false;

        endif;

        // get all data of that user (to later check if password and password_hash fit)
        $result = UserModel::getUserDataByUsername ( $user_name );

        // check if that user exists. We don't give back a cause in the feedback to avoid giving an attacker details.
        if ( ! $result ) :

            self::incrementUserNotFoundCounter();
            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_USERNAME_OR_PASSWORD_WRONG') );
            return false;

        endif;

        // block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
        if ( ( $result->user_failed_logins  >= 3 ) AND ( $result->user_last_failed_login > ( time() - 30 ) ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_WRONG_3_TIMES') );
            return false;

        endif;

        // if hash of provided password does NOT match the hash in the database: +1 failed-login counter
        if ( ! password_verify ( $user_password, $result->user_password_hash ) ) :

            self::incrementFailedLoginCounterOfUser ( $result->user_name );
            // we say "password wrong" here, but less details like "login failed" would be better (= less information)
            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_WRONG') );
            return false;

        endif;

        // if user is not active (= has not verified account by verification mail)
        if ( $result->user_active != 1 ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET') );
            return false;

        endif;

        self::resetUserNotFoundCounter();

        return $result;
    }

    /**
     * Reset the failed-login-count to 0.
     * Reset the last-failed-login to an empty string.
     *
     */
    private static function resetUserNotFoundCounter()
    {
        HugeSession::set('failed-login-count', 0);
        HugeSession::set('last-failed-login', '');
    }
    
    /**
     * Increment the failed-login-count by 1.
     * Add timestamp to last-failed-login.
     */
    private static function incrementUserNotFoundCounter()
    {
        // Username enumeration prevention: set session failed login count and last failed login for users not found
        HugeSession::set( 'failed-login-count', HugeSession::get('failed-login-count') + 1 );
        HugeSession::set( 'last-failed-login', time() );
    }

    /**
     * performs the login via cookie (for DEFAULT user account, FACEBOOK-accounts are handled differently)
     * TODO add throttling here ?
     *
     * @param $cookie string The cookie "remember_me"
     *
     * @return bool success state
     */
    public static function loginWithCookie( $cookie )
    {
        // do we have a cookie ?
        if ( ! $cookie ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_COOKIE_INVALID' ) );
            return false;

        endif;

        // before list(), check it can be split into 3 strings.
        if ( count ( explode(':', $cookie)) !== 3 ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_COOKIE_INVALID' ) );
            return false;

        endif;

        // check cookie's contents, check if cookie contents belong together or token is empty
        list ( $user_id, $token, $hash ) = explode( ':', $cookie );

        $user_id = EncryptionHelper::decrypt( $user_id );

        if ( $hash !== hash( 'sha256', $user_id . ':' . $token ) OR empty ( $token ) OR empty ( $user_id ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_COOKIE_INVALID' ) );
            return false;

        endif;

        // get data of user that has this id and this token
        $result = UserModel::getUserDataByUserIdAndToken ( $user_id, $token );

        // if user with that id and exactly that cookie token exists in database
        if ( $result ) :

            // successfully logged in, so we write all necessary data into the session and set "user_logged_in" to true
            self::setSuccessfulLoginIntoSession( $result->user_id, $result->user_name, $result->user_email, $result->user_account_type, $result->role_id, $result->user_provider_type );
            // save timestamp of this login in the database line of that user
            self::saveTimestampOfLoginOfUser( $result->user_name );

            // NOTE: we don't set another remember_me-cookie here as the current cookie should always
            // be invalid after a certain amount of time, so the user has to login with username/password
            // again from time to time. This is good and safe ! ;)

            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_COOKIE_LOGIN_SUCCESSFUL' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_COOKIE_INVALID' ) );
            return false;

        endif;
    }

    /**
     * Log out process: delete cookie, delete session
     */
    public static function logout()
    {

        if ( LoginModel::isUserLoggedIn() ) :

            $user_id = HugeSession::get( 'user_id' );
            $user_name = HugeSession::get( 'user_name' );

            AuditModel::saveAuditTrails( $user_id, ucfirst( $user_name ) . ' ' . LanguageHelper::get( 'AUDIT_SUCCESS_LOGOUT' ) . ' ' . LanguageHelper::get( 'GLOBAL_FROM' ) . ' : ' . Slim::getInstance()->request()->getIp() );

            LoginModel::deleteCookie( $user_id );

            HugeSession::destroy();

            HugeSession::updateSessionId( $user_id );

            Slim::getInstance()->response()->redirect( 'login' );

        else :

            Slim::getInstance()->response()->redirect( Slim::getInstance()->urlFor( 'manage' ) );

        endif;

    }

    public static function setSuccessfulLoginIntoSession( $user_id, $user_name, $user_email, $user_account_type, $role_id, $user_provider_type )
    {

        HugeSession::set( 'user_id', $user_id );
        HugeSession::set( 'user_name', $user_name );
        HugeSession::set( 'user_email', $user_email );
        HugeSession::set( 'user_account_type', $user_account_type );
        HugeSession::set( 'user_role_id', $role_id );
        HugeSession::set( 'user_provider_type', $user_provider_type );

        if ( $user_provider_type == 'DEFAULT' ) :

            // get and set avatars
            HugeSession::set( 'user_avatar_file', HugeAvatar::getPublicUserAvatarFilePathByUserId( $user_id ) );
            HugeSession::set( 'user_gravatar_image_url', HugeAvatar::getGravatarLinkByEmail( $user_email ) );

        endif;
        
        HugeSession::set( 'global_language', 'en' );

        // finally, set user as logged-in
        HugeSession::set( 'user_logged_in', true );

        // update session id in database
        HugeSession::updateSessionId( $user_id, session_id() );

        // set session cookie setting manually,
        // Why? because you need to explicitly set session expiry, path, domain, secure, and HTTP.
        // @see https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Cookies
        setcookie( session_name(), session_id(), time() + HugeConfig::get( 'SESSION_RUNTIME' ), HugeConfig::get( 'COOKIE_PATH' ), HugeConfig::get( 'COOKIE_DOMAIN' ), HugeConfig::get( 'COOKIE_SECURE' ), HugeConfig::get( 'COOKIE_HTTP' ) );

    }

    /**
     * Increments the failed-login counter of a user
     *
     * @param $user_name
     */
    public static function incrementFailedLoginCounterOfUser( $user_name )
    {

        $user_failed_logins = User::where( 'user_name', '=', $user_name )->orWhere( 'user_email', '=', $user_name )->take(1)->value( 'user_failed_logins' );

        $query = User::where( 'user_name', '=', $user_name )->orWhere( 'user_email', '=', $user_name )->update(array(
            'user_failed_logins' => $user_failed_logins + 1,
            'user_last_failed_login'   => date('Y-m-d H:i:s')
        ));

        return $query;

    }

    /**
     * Resets the failed-login counter of a user back to 0
     *
     * @param $user_name
     */
    public static function resetFailedLoginCounterOfUser( $user_name )
    {

        $query = User::where( 'user_name', '=', $user_name )->where( 'user_failed_logins', '!=', 0 )->update( array(
            'user_failed_logins' => 0,
            'user_last_failed_login'   => NULL
        ));

        return $query;

    }

    /**
     * Write timestamp of this login into database (we only write a "real" login via login form into the database,
     * not the session-login on every page request
     *
     * @param $user_name
     */
    public static function saveTimestampOfLoginOfUser( $user_name )
    {
        User::where( 'user_name', '=', $user_name )->take( 1 )->update( array(
            'user_last_login_timestamp' => time()
        ));

    }

    /**
     * Write remember-me token into database and into cookie
     * Maybe splitting this into database and cookie part ?
     *
     * @param $user_id
     */
    public static function setRememberMeInDatabaseAndCookie( $user_id )
    {
        // generate 64 char random string
        $random_token_string = hash( 'sha256', mt_rand() );

        User::where( 'user_id', '=', $user_id )->take( 1 )->update( array(
            'user_remember_me_token' => $random_token_string
        ));

        // generate cookie string that consists of user id, random string and combined hash of both
        // never expose the original user id, instead, encrypt it.
        $cookie_string_first_part = EncryptionHelper::encrypt ( $user_id ) . ':' . $random_token_string;
        $cookie_string_hash       = hash('sha256', $user_id . ':' . $random_token_string);
        $cookie_string            = $cookie_string_first_part . ':' . $cookie_string_hash;

        // set cookie
        setcookie( 'remember_me', $cookie_string, time() + HugeConfig::get( 'COOKIE_RUNTIME' ), HugeConfig::get( 'COOKIE_PATH' ), HugeConfig::get( 'COOKIE_DOMAIN' ), HugeConfig::get( 'COOKIE_SECURE' ), HugeConfig::get( 'COOKIE_HTTP' ) );

    }

    /**
     * Deletes the cookie
     * It's necessary to split deleteCookie() and logout() as cookies are deleted without logging out too!
     * Sets the remember-me-cookie to ten years ago (3600sec * 24 hours * 365 days * 10).
     * that's obviously the best practice to kill a cookie @see http://stackoverflow.com/a/686166/1114320
     */
    public static function deleteCookie( $user_id = null )
    {
        // is $user_id was set, then clear remember_me token in database
        if ( isset ( $user_id ) ) :

            User::where( 'user_id', '=', $user_id )->take(1)->update(array(
                'user_remember_me_token' => NULL
            ));

        endif;

        // delete remember_me cookie in browser
        setcookie( 'remember_me', false, time() - ( 3600 * 24 * 3650 ), HugeConfig::get( 'COOKIE_PATH' ), HugeConfig::get( 'COOKIE_DOMAIN' ), HugeConfig::get( 'COOKIE_SECURE' ), HugeConfig::get( 'COOKIE_HTTP' ) );

    }

    /**
     * Returns the current state of the user's login
     *
     * @return bool user's login status
     */
    public static function isUserLoggedIn()
    {
        return HugeSession::userIsLoggedIn();
    }
}
