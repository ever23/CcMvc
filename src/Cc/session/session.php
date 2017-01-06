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
 * maneja las sessiones del servidor
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package Cc
 * @subpackage Session 
 */
abstract class SESSION extends \SessionHandler implements \ArrayAccess
{

    protected $_SESSION = [];

    // private $ID = NULL;



    public function __debugInfo()
    {
        return $this->_SESSION;
    }

    public abstract function Start($id = NULL);

    public abstract function SetCookie($cache = NULL, $TIME = NULL, $path = NULL, $dominio = NULL, $secure = false, $httponly = false);

    public abstract function SetName($name);

    public abstract function Commit();

    public abstract function GetName();

    public abstract function GetCookieParams();

    public abstract function GetId();

    public function Del()
    {
        $session = $this->_SESSION;
        foreach ($session as $i => $see)
        {
            unset($this->_SESSION[$i]);
        }

        $this->_SESSION = array();
    }

    public function GetVar($var)
    {
        if (!empty($this->_SESSION[$var]))
        {
            return $this->_SESSION[$var];
        }
        return NULL;
    }

    public function __get($var)
    {
        return self::GetVar($var);
    }

    public function SetVar($var, $value)
    {
        $this->_SESSION[$var] = $value;
    }

    public function __set($n, $v)
    {
        self::SetVar($n, $v);
    }

    public function DelVar($var)
    {
        if (!empty($this->_SESSION[$var]))
        {
            unset($this->_SESSION[$var]);
        }
    }

    public function _empty($var)
    {

        return empty($this->_SESSION[$var]);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return isset($this->_SESSION[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

}
