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

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * MvcHook                                                       
 *         
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Hook
 * @internal usado para ejecutar los Hooks
 */
class MvcHook
{

    /**
     *
     * @var AbstractHook
     */
    protected static $Hook;
    public static $events = [];

    public static function Configure(&$view, &$layaut)
    {
        self::$Hook->View = $view;
        self::$Hook->Layaut = $layaut;
    }

    public final function Add($name, \Closure $claback)
    {
        $this->events[$name] = $claback;
    }

    public static final function &Start(Config $conf)
    {
        $class = $conf->Hooks['class'];
        static::$Hook = new $class();
        return static::$Hook;
    }

    public static final function Tinger($events, ...$params)
    {
        if (isset(static::$events[$events]))
        {
            $clousure = \Closure::bind(static::$events[$events], static::$Hook);
            $clousure(...$params);
        }
        return static::$Hook->{$events}(...$params);
    }

    public static final function TingerAndDependence($events)
    {
        if (isset(static::$events[$events]))
        {
            $clousure = \Closure::bind(static::$events[$events], static::$Hook);
            $clousure(...Mvc::App()->DependenceInyector->ParamFunction(static::$events[$events]));
        }
        if (method_exists(static::$Hook, $events))
        {
            return static::$Hook->{$events}(...Mvc::App()->DependenceInyector->ParamFunction([static::$Hook, $events]));
        }
    }

}
