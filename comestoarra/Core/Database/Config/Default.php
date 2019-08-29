<?php

$comestoarra['database'] = array(
    'default'       => 'mysql',

    'connections'   => array(

        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'dcas',
            'port'      => '3306',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'cbn_',
        ),

        'sqlite' => array(
            'driver'   => 'sqlite',
            'database' => 'Storage/comestoarra.sqlite',
            'prefix'   => 'cbn_',
        ),

        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'dcas',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => 'cbn_',
            'schema'   => 'public',
        ),

        'sqlsrv' => array(
            'driver'   => 'sqlsrv',
            'host'     => '127.0.0.1',
            'database' => 'dcas',
            'username' => 'user',
            'password' => '',
            'prefix'   => 'cbn_',
        ),

    )
);