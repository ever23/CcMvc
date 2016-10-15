<?php

return
        [
            /**
              'DB' =>
              [
              'class' => 'DB_MySQLi',
              'param' =>['localhost','tuUser','tuClave','cine']
              ]
             */
            'DB' =>
            [
                'class' => '\\Cc\\Mvc\\SQLite3',
                'param' => [dirname(__FILE__) . '/../cine.db']
            ],
            //'debung' => false,
            'App' =>
            [
                'app' => realpath(dirname(__FILE__) . '/../') . '/',
            ],
];


