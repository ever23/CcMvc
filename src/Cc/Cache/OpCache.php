<?php

/*
 * Copyright (C) 2016 Enyerber Franco
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

namespace Cc;

/**
 * @ignore
 */
class OpCache
{

    public static function ExtencionLoaded()
    {
        return function_exists('opcache_compile_file');
    }

    public static function Compile($file)
    {
        return opcache_compile_file($file);
    }

    public static function IsCached($file)
    {
        return self::ExtencionLoaded() && opcache_is_script_cached($file);
    }

    public static function Reset()
    {
        return opcache_reset();
    }

}
