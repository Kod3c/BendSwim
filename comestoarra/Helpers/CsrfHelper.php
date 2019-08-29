<?php namespace Helpers;

/*
|--------------------------------------------------------------------------
| VOLNIX\Csrf
|--------------------------------------------------------------------------
|
| Modified from VOLNIX\Csrf
| https://github.com/volnix/csrf
|
*/

use Helpers\Huge\Core\HugeSession;
use Helpers\Huge\Core\HugeRequest;

class CsrfHelper
{

    /**
     * The default token name
     */
//    const TOKEN_NAME = "comestoarra_csrf_token_HdQR8A7Ev4Y44PruRhdA2tdoNFQAaJAhOhOHLB4ELJo";
    const TOKEN_NAME = "382969d51fc6fef8efe8deaedb8206d793490c3efb78fc7a4902029c065c1c58";
    /**
     * (Re-)Generate a token and write it to session
     *
     * @param string $token_name - defaults to the default token name
     * @return void
     */
    public static function generateToken( $token_name = self::TOKEN_NAME )
    {
        // generate as random of a token as possible
        $salt = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : uniqid();
        $_SESSION[$token_name] = sha1( uniqid( sha1( $salt ), true ) );
    }
    /**
     * Get the token.  If it's not defined, this will go ahead and generate one.
     *
     * @param string $token_name - defaults to the default token name
     * @return string
     */
    public static function getToken( $token_name = self::TOKEN_NAME )
    {
        if ( empty ( $_SESSION[$token_name]  ) ) :

            static::generateToken( $token_name );

        endif;

        return $_SESSION[$token_name];
    }
    /**
     * Validate the token.  If there's not one yet, it will set one and return false.
     *
     * @param array $request_data - your whole POST/GET array - will index in with the token name to get the token.
     * @param string $token_name - defaults to the default token name
     * @return bool
     */
    public static function validate( $request_data = array(), $token_name = self::TOKEN_NAME )
    {
        if ( empty ( $_SESSION[$token_name] ) ) :

            static::generateToken( $token_name );
            return false;

        elseif ( empty ( $request_data[$token_name] ) ) :

            return false;

        else :

            return static::compare( $request_data[$token_name], static::getToken( $token_name ) );

        endif;
    }
    /**
     * Get a hidden input string with the token/token name in it.
     *
     * @param string $token_name - defaults to the default token name
     * @return string
     */
    public static function getHiddenInputString( $token_name = self::TOKEN_NAME )
    {
        return sprintf( '<input type="hidden" name="%s" value="%s"/>', $token_name, static::getToken( $token_name ) );
    }
    /**
     * Get a query string mark-up with the token/token name in it.
     *
     * @param string $token_name - defaults to the default token name
     * @return string
     */
    public static function getQueryString( $token_name = self::TOKEN_NAME )
    {
        return sprintf( '%s=%s', $token_name, static::getToken( $token_name ) );
    }
    /**
     * Constant-time string comparison.  This comparison function is timing-attack safe
     *
     * @param string $hasha
     * @param string $hashb
     * @return bool
     */
    public static function compare( $hasha = "", $hashb = "" )
    {
        // we want hashes_are_not_equal to be false by the end of this if the strings are identical
        // if the strings are NOT equal length this will return true, else false
        $hashes_are_not_equal = strlen( $hasha ) ^ strlen( $hashb );
        // compare the shortest of the two strings (the above line will still kick back a failure if the lengths weren't equal.  this just keeps us from over-flowing our strings when comparing
        $length = min( strlen( $hasha ), strlen( $hashb ) );
        $hasha = substr( $hasha, 0, $length );
        $hashb = substr( $hashb, 0, $length );
        // iterate through the hashes comparing them character by character
        // if a character does not match, then return true, so the hashes are not equal
        for ( $i = 0; $i < strlen( $hasha ); $i++ ) :

            $hashes_are_not_equal += !( ord( $hasha[$i] ) === ord( $hashb[$i] ) );

        endfor;
        // if not hashes are not equal, then hashes are equal
        return ! $hashes_are_not_equal;
    }
}