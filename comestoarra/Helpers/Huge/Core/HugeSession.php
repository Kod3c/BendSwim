<?php namespace Helpers\Huge\Core;

use Models\Entity\Huge\User;

class HugeSession
{

    public static function init()
    {
        if ( session_status() == PHP_SESSION_NONE || session_id() == '' ) :

            ini_set( 'session.use_only_cookies', true );
            session_cache_limiter( false );
            session_start();

            if ( ! isset( $_SESSION['cbn_generated'] ) || $_SESSION['cbn_generated'] < ( time() - 30 ) ) :

                static::regenerate();
                $_SESSION['cbn_generated'] = time();

            endif;

        endif;
    }

    public static function regenerate()
    {
        if ( session_id() == '' ) :

            session_regenerate_id( true );
            return session_id();

        endif;
    }

    public static function set( $key, $value )
    {
        $_SESSION[$key] = $value;
    }

    public static function get( $key )
    {
        if ( isset ( $_SESSION[ $key ] ) ) :

            if ( is_string( $_SESSION[ $key ] ) ) :

                // filter the value for XSS vulnerabilities
                HugeFilter::XSSFilter( $_SESSION[ $key ] );

                return $_SESSION[ $key ];

            else :

                return $_SESSION[ $key ];

            endif;

        endif;

        return false;

    }

    public static function add( $key, $value )
    {
        $_SESSION[ $key ][] = $value;
    }

    /**
     * deletes the session (= logs the user out)
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }

    /**
     * update session id in database
     *
     * @access public
     * @static static method
     * @param  string $userId
     * @param  string $sessionId
     * @return string
     */
    public static function updateSessionId( $userId, $sessionId = null )
    {
        $query = User::where( 'user_id', '=', $userId )->update(array(
            'session_id' => $sessionId
        ));

        return $query;

    }

    /**
     * checks for session concurrency
     *
     * This is done as the following:
     * UserA logs in with his session id('123') and it will be stored in the database.
     * Then, UserB logs in also using the same email and password of UserA from another PC,
     * and also store the session id('456') in the database
     *
     * Now, Whenever UserA performs any action,
     * You then check the session_id() against the last one stored in the database('456'),
     * If they don't match then log both of them out.
     *
     * @access public
     * @static static method
     * @return bool
     * @see Session::updateSessionId()
     * @see http://stackoverflow.com/questions/6126285/php-stop-concurrent-user-logins
     */
    public static function isConcurrentSessionExists()
    {

        $session_id = session_id();
        $userId     = HugeSession::get( 'user_id' );

        if ( isset( $userId ) && isset ( $session_id ) ) :

            $result = User::where('user_id', '=', $userId)->take( 1 )->select( 'session_id' )->first();

            $userSessionId = ! empty ( $result ) ? $result->session_id : null;

            return $session_id !== $userSessionId;

        endif;

        return false;
    }

    /**
     * Checks if the user is logged in or not
     *
     * @return bool user's login status
     */
    public static function userIsLoggedIn()
    {
        return ( self::get( 'user_logged_in' ) ? true : false );
    }

}
