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
    protected static $Hooks;
    public static $events = [];
    private static $View;
    private static $Layaut;

    public static function Configure(&$view, &$layaut)
    {
        self::$View = $view;
        self::$Layaut = $layaut;
    }

    public final function Add($name, \Closure $claback)
    {
        $this->events[$name] = $claback;
    }

    public static final function Start(Config $conf)
    {
        foreach ($conf->Hooks as $class)
        {
            $hook = new $class();
            $hook->View = & self::$View;
            $hook->Layaut = & self::$Layaut;

            self::$Hooks[] = $hook;
        }
    }

    public static final function Tinger($events, ...$params)
    {
        if (isset(static::$events[$events]))
        {
            $clousure = \Closure::bind(static::$events[$events], static::$Hook);
            $clousure(...$params);
        }
        foreach (self::$Hooks as &$hook)
        {
            if (method_exists($hook, $events))
                $hook->{$events}(...$params);
        }
    }

    public static final function TingerAndDependence($events)
    {
        if (isset(static::$events[$events]))
        {
            $clousure = \Closure::bind(static::$events[$events], static::$Hook);
            $clousure(...Mvc::App()->DependenceInyector->ParamFunction(static::$events[$events]));
        }
        foreach (self::$Hooks as &$hook)
        {
            if (method_exists($hook, $events))
            {
                $params = Mvc::App()->DependenceInyector->ParamFunction([$hook, $events]);
                $hook->{$events}(...$params);
            }
        }
    }

}
