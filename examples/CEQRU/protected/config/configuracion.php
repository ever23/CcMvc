<?php

return
        [

            'App' =>
            [
                'app' => realpath(dirname(__FILE__) . '/../') . '/',
            ],
            'Autenticate' =>
            [
                'class' => '\\Cc\\Mvc\\AutenticaRQU',
                'param' =>
                [
                    ['index/login', 'index/ingresar'],
                    ['user', 'pass']
                ],
                'SessionName' => 'RafaelQuevedoUrbina',
                'SessionCookie' =>
                [
                    'path' => NULL, //dirname($_SERVER['PHP_SELF']) == '\\' ? '/' : dirname($_SERVER['PHP_SELF']) . '/',
                    'cahe' => 'nocache,private',
                    'time' => 21600,
                    'dominio' => NULL,
                    'httponly' => true,
                    'ReadAndClose' => true
                ],
            ],
            'DB' =>
            [
                'class' => '\\Cc\\MySQLi',
                'param' => include('UserDatabase.php')
            ]
];


