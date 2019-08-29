<?php namespace Helpers\Huge\Core;

class HugeText
{
    private static $texts;

    public static function get( $key )
    {
        // if not $key
        if ( ! $key ) :

            return null;

        endif;

        // load config file (this is only done once per application lifecycle)
        if ( ! self::$texts ) :

            if ( HugeSession::get( 'language' ) == 'ID' ) :

                self::$texts = require('Config/Text_ID.php');

            else :

                self::$texts = require( 'Config/Text_EN.php' );

            endif;

        endif;

        // check if array key exists
        if ( ! array_key_exists( $key, self::$texts ) ) :

            return null;

        endif;

        return self::$texts[ $key ];
    }

}
