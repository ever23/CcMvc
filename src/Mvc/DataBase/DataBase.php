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

    public function __construct($host = NULL, $username = NULL, $passwd = NULL, $dbname = "", $port = NULL, $socket = NULL)
    {
        try
        {
            ob_start();
            parent::__construct($host, $username, $passwd, $dbname, $port, $socket);
            ob_end_flush();
        } catch (\mysqli_sql_exception $ex)
        {
            if ($this->connect_errno == '1049')
            {
                $this->install($host, $username, $passwd, $dbname, $port, $socket);
            } else
            {
                ob_end_flush();
            }
        }
    }

    private function install($host, $username, $passwd, $dbname, $port, $socket)
    {
        parent::__construct($host, $username, $passwd, "", $port, $socket);
        $this->query("Create database " . $dbname);

        $this->select_db($dbname);
        if (!$this->error)
        {

            try
            {
                $console = new Console\Migracion($this);
                $console->DatabaseFromModel();
            } catch (\Exception $ex)
            {
                
            }
            if (!Mvc::App()->IsConsole())
            {
                ob_end_clean();
            } else
            {
                ob_end_flush();
            }
        }
    }

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

    public function __construct($dsn, $username = null, $password = null, $options = NULL)
    {
        $installSqlite = false;
        if (preg_match('/^sqlite(\d{0,2}):(.*)/', $dsn, $m))
        {
            if (!file_exists($m[2]))
            {
                $installSqlite = true;
            }
        }
        parent::__construct($dsn, $username, $password, $options);
        if ($installSqlite)
        {
            ob_start();
            try
            {
                $console = new Console\Migracion($this);
                $console->DatabaseFromModel();
            } catch (\Exception $ex)
            {
                
            }
            if (!Mvc::App()->IsConsole())
            {
                ob_end_clean();
            } else
            {
                ob_end_flush();
            }
        }
    }

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
            ob_start();
            try
            {
                $console = new Console\Migracion($this);
                $console->DatabaseFromModel();
            } catch (\Exception $ex)
            {
                
            }
            if (!Mvc::App()->IsConsole())
            {
                ob_end_clean();
            } else
            {
                ob_end_flush();
            }
        }
    }

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
