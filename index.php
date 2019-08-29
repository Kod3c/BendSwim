<?php

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	index.php
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

if ( ! file_exists ( 'public/api/_comestoarra_labs_') ) :

        $url = 'install_guide';
        header('location: '.$url);

    else :

        if ( file_exists ( 'vendor/autoload.php' ) ) :

            require_once realpath("vendor/autoload.php");

        else :

            $output = json_encode(
                [ //create JSON data
                    'author'=>'Comestoarra',
                    'email'=>'labs@comestoarra.com',
                    'website'=>'comestoarra.com',
                    'messages' => 'Please install_guide via composer.json'
                ]);

            die( $output ); //exit script outputting json

        endif;

        require_once realpath("comestoarra/Core/_comestoarra_autoload_.php");

        /*
        |--------------------------------------------------------------------------
        | Finally, run the app !
        |--------------------------------------------------------------------------
        |
        | Descriptions
        |
        */
        $app->run();

endif;