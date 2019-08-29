<?php namespace Helpers;


use Helpers\Huge\Core\HugeSession;
use Controllers\GlobalController;

class LanguageHelper extends GlobalController
{
    private static $lang;

    public function __construct()
    {

        parent::__construct();

    }

    public static function get( $key )
    {

        if ( ! $key ) :

            return null;

        endif;

        if ( ! self::$lang ) :

            if ( HugeSession::get( 'language' ) ) :

                $langFile = realpath( 'comestoarra/Helpers/Language/Lang_' . HugeSession::get( 'language' ) . '.php' );

                if ( file_exists( $langFile ) ) :

                    self::$lang = require_once ( $langFile );

                else :

                    $output = json_encode(
                    [ //create JSON data
                        'author'=>'Comestoarra Labs',
                        'email'=>'labs@comestoarra.com',
                        'website'=>'comestoarra.com',
                        'messages' => HugeSession::get( 'language' ) . ' Translation file was not exists !'
                    ]);

                    die( $output ); //exit script outputting json

                endif;

            else :

                $langFile = realpath( 'comestoarra/Helpers/Language/Lang_' . DEFAULT_LANGUAGE . '.php' );

                if ( file_exists( $langFile ) ) :

                    self::$lang = require_once ( $langFile );

                else :

                    $output = json_encode(
                    [ //create JSON data
                        'author'=>'Comestoarra Labs',
                        'email'=>'labs@comestoarra.com',
                        'website'=>'comestoarra.com',
                        'messages' => DEFAULT_LANGUAGE . ' Translation file was not exists !'
                    ]);

                    die( $output ); //exit script outputting json

                endif;

            endif;

        endif;


        if ( ! array_key_exists( $key, self::$lang ) ) :

            return null;

        endif;

        return self::$lang[ $key ];
    }

    public static function all()
    {

        if ( ! self::$lang ) :

            if ( HugeSession::get( 'language' ) ) :

                $langFile = realpath( 'comestoarra/Helpers/Language/Lang_' . HugeSession::get( 'language' ) . '.php' );

                if ( file_exists( $langFile ) ) :

                    self::$lang = require_once ( $langFile );

                else :

                    $output = json_encode(
                    [ //create JSON data
                        'author'=>'Comestoarra Labs',
                        'email'=>'labs@comestoarra.com',
                        'website'=>'comestoarra.com',
                        'messages' => HugeSession::get( 'language' ) . ' Translation file was not exists !'
                    ]);

                    die( $output ); //exit script outputting json

                endif;

            else :

                $langFile = realpath( 'comestoarra/Helpers/Language/Lang_' . DEFAULT_LANGUAGE . '.php' );

                if ( file_exists( $langFile ) ) :

                    self::$lang = require_once ( $langFile );

                else :

                    $output = json_encode(
                    [ //create JSON data
                        'author'=>'Comestoarra Labs',
                        'email'=>'labs@comestoarra.com',
                        'website'=>'comestoarra.com',
                        'messages' => DEFAULT_LANGUAGE . ' Translation file was not exists !'
                    ]);

                    die( $output ); //exit script outputting json

                endif;

            endif;

        endif;

        return self::$lang;

    }

}
