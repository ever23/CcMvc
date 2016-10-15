<?php

return
        [
            //'host' => '192.168.43.152',
            'host' => '127.0.0.1',
            'port' => '12345',
            'ProcessResponseFrame' => 'WsMsjJson',
            'App' =>
            [
                'app' => realpath(dirname(__FILE__).'/../') . '/',
            ]
];


