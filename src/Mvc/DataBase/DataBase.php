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

namespace Cc\Mvc;

use Cc\Cache;
use Cc\iDataBase;
use Cc\Mvc;

/**
 * @package CcMvc
 * @subpackage DataBase
 * 
 */
class DBRow extends \Cc\DBRow
{
    
}

/**
 * @package CcMvc
 * @subpackage DataBase
 * 
 */
class MySQLi extends \Cc\MySQLi
{

    /**
     * 
     * @param string $tabla
     * @return \Cc\Mvc\DBtabla
     */
    public function Tab($tabla)
    {

        return new DBtabla($this, $tabla);
    }

}

/**
 * @package CcMvc
 * @subpackage DataBase
 * 
 */
class PDO extends \Cc\PDO
{

    /**
     * 
     * @param string $tabla
     * @return \Cc\Mvc\DBtabla
     */
    public function Tab($tabla)
    {
        return new DBtabla($this, $tabla);
    }

}

/**
 * @package CcMvc
 * @subpackage DataBase
 * 
 */
class SQLite3 extends \Cc\SQLite3
{

    /**
     * 
     * @param string $tabla
     * @return \Cc\Mvc\DBtabla
     */
    public function Tab($tabla)
    {
        return new DBtabla($this, $tabla);
    }

}
