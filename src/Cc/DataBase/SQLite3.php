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
 * Description of DB_SQLite3
 *
 * @author Enyerber Franco 
 * @package Cc
 * @subpackage DataBase
 */
class SQLite3 extends \SQLite3 implements iDataBase
{

    public $connect_error = '';

    public function __construct($filename, $flags = SQLITE3_OPEN_READWRITE, $encryption_key = null)
    {
        if (file_exists($filename))
        {
            if (is_string($flags))
            {
                $flags = SQLITE3_OPEN_READWRITE;
            }
            parent::__construct($filename, $flags, $encryption_key);
        } elseif (file_exists($flags))
        {

            parent::__construct($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryption_key);
            $sql = file_get_contents($flags);
            $this->exec($sql);
        } else
        {
            parent::__construct($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryption_key);
        }
    }

    /**
     * 
     * @param string $tab
     * @return \Cc\DBtabla
     */
    public function Tab($tab)
    {
        return new DBtabla($this, $tab);
    }

    public function errno()
    {

        return (int) $this->lastErrorCode();
    }

    public function error()
    {
        return $this->lastErrorMsg();
    }

    public function real_escape_string($sq)
    {
        return $this->escapeString($sq);
    }

    public function dbName()
    {
        
    }

    public function GetDriver()
    {
        $class = __NAMESPACE__ . "\\DB\\Drivers\\sqlite";
        if (!class_exists($class))
        {
            throw new Exception(" NO EXISTE EL DRIVER DE " . $class);
        }

        return new $class($this);
    }

//put your code here
}
