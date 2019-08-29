<?php namespace Helpers\Huge\Core;

class HugeConfig
{
    private static $config;

    public static function get( $key )
    {
        // if not $key
        if ( ! $key ) :

            return null;

        endif;

        // load config file (this is only done once per application lifecycle)
        if ( ! self::$config ) :

            self::$config = require( 'Config/Config.php' );

        endif;

        // check if array key exists
        if ( ! array_key_exists( $key, self::$config ) ) :

            return null;

        endif;

        return self::$config[ $key ];
    }
}