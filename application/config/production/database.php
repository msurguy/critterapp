<?php

return array(
    'profile' => false,
    'default' => 'production',
    'connections' => array(
        'production' => array(
            'driver'    => 'mysql',
            'host'      => getenv('db_host'),
            'database'  => getenv('db_name'),
            'username'  => getenv('db_username'),
            'password'  => getenv('db_password'),
            'charset'  => 'utf8',
            'prefix'   => '',
        ))
);