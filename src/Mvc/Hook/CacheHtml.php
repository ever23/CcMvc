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

/**
 * Description of CacheHtml
 *
 * @author enyerber
 */
class CacheHtml extends AbstractHook
{

    protected $cache = [];
    protected $cache2 = [];
    protected $Cached = false;

    public function AppRun()
    {
        $cache = Cache::IsSave($this->CacheRouter['request']) ? Cache::Get($this->CacheRouter['request']) : (Cache::IsSave($this->CacheRouter['requestStatic']) ? Cache::Get($this->CacheRouter['requestStatic']) : []);
        if (isset($cache['type']) && $cache['type'] == 'Controllers' && isset($cache['RealFile']))
        {
            
        }
    }

}
