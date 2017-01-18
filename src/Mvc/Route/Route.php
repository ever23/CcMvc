<?php

/*
 * Copyright (C) 2017 Enyerber Franco
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Cc\Mvc\Router;

/**
 * Description of Route
 *
 * @author enyerber
 */
class Route
{

    public $url;
    public $has;
    public $math;
    public $controller;
    public static $routes = [];
    private $pcre = [
        'numeric' => '^(\d)$',
        'alpha' => '',
        'alpah_num' => ''
    ];

    public function __construct($controller, $url)
    {
        $this->url = $url;
        $this->controller = $controller;
        self::$routes[$controller] = $this;
    }

    public function has($has)
    {
        $this->has = $has;
        self::$routes[$has] = $this;
    }

    public function cuando($name, $exprecion)
    {
        $this->math[$name] = $exprecion;
    }

}
