<?php defined('FRTCFTYU') or die('No direct script access.');

return array (
    
    'mysql' => array(
        'db' => array(
            'driver' => 'PDO_MYSQL',
            'DEVELOPMENT' => array (
                                    'host'      => 'localhost',
                                    'database'  => 'database_name',
                                    'username'  => 'root',
                                    'password'  => 'SYSADM',
                                ),
            'PRODUCTION' => array (
                                    'host'      => 'localhost',
                                    'database'  => 'database_name',
                                    'username'  => 'root',
                                    'password'  => 'SYSADM',
                                )
        )
    )
);