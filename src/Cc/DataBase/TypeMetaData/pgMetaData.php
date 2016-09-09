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

namespace Cc\DB\MetaData;

/**
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>
 * @package Cc
 * @subpackage DataBase  
 * @category MetaData
 * @todo se nesesita mejorar la decodificacion de arrays de posgresql
 */
class pgARRAY extends \ArrayObject implements iMetaData
{

    /**
     *
     * @var \Cc\DB\Drivers\pgsql 
     */
    protected $key;

    public function __construct($input = [], &$key)
    {
        $this->key = &$key;
        if (is_array($input) || is_object($input))
        {
            parent::__construct($input);
        } elseif (is_string($input))
        {
            $array = $this->Decode($input);
            parent::__construct($array);
        }
    }

    public function __toString()
    {
        return self::Encode($this->getArrayCopy());
    }

    private static function Format($var, $a = NULL)
    {
        if (is_null($var) || (is_string($var) && strtolower($var) == 'null'))
        {
            return 'NULL';
        } elseif (is_int($var) || is_float($var) || is_double($var))
        {
            return $var;
        } elseif (is_bool($var))
        {
            return $var ? 'true' : 'false';
        } elseif ((is_array($var) || $var instanceof \Traversable) && !$var instanceof \Cc\Json)
        {
            $t = '';
            foreach ($var as $v)
            {
                $t.=self::Format($v, 'array') . ',';
            }
            $t = '{' . substr($t, 0, -1) . '}';
            if ($a != 'array')
            {
                return "'" . $t . "'";
            } else
            {
                return $t;
            }
        } else
        {
            if ($a == 'array')
            {
                return '"' . $var . '"';
            }
            return "'" . $var . "'";
        }
    }

    public static function Encode(array $array)
    {
        return self::Format($array, 'array');
    }

    public static function Decode($str)
    {

        if (is_string($str) && ($str[0] == '{' && $str[strlen($str) - 1] == '}' ))
        {
            $string = self::ConverJson($str);

            if ($r = json_decode($string))
            {

                return self::UnserializeValues($r);
            } else
            {

                return false;
            }
            //  $unser = $this->UnserializeArrayPostgeSql($str)
        }

        return false;
    }

    private static function UnserializeValues($array)
    {
        foreach ($array as $i => &$v)
        {
            if (is_string($i))
                return $array;
            if (is_string($v))
            {
                $class_name = "\\Cc\\DB\\MetaData\\pg" . $this->key;
                $class_name2 = "\\Cc\\DB\\MetaData\\" . $this->key;

                if (class_exists($class_name, false))
                {
                    $v = new $class_name($v, $this->key);
                } elseif (class_exists($class_name2, false))
                {
                    $v = new $class_name2($v, $this->key);
                }
            } elseif (is_array($v))
            {
                $v = self::UnserializeValues($v);
            }
        }
    }

    public static function ConverJson($entrada)
    {
        if (is_array($entrada))
        {
            $ex = preg_replace_callback('/(\w{1,})|(\"\w{1,}\")/', function($w)
            {
                if (preg_match('/\"(\w{1,})\"/', $w[0]) || is_numeric($w[0]))
                {
                    return $w[0];
                }
                return '"' . $w[0] . '"';
            }, substr($entrada[0], 1, -1));

            $entrada = '[' . $ex . ']';
        }
        return preg_replace_callback("/\{.*\}/", __METHOD__, $entrada);
    }

    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }

}

/**
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>
 * @package Cc
 * @subpackage DataBase  
 * @category MetaData
 */
class json extends \Cc\Json implements iMetaData
{

    protected $key;

    public function __construct($json, $key)
    {
        $this->key = $key;
        if ($json instanceof \Cc\Json)
        {
            $this->Copy($json);
        }
        parent::__construct($json);
    }

}

/**
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>
 * @package Cc
 * @subpackage DataBase  
 * @category MetaData
 */
class xml extends \DOMDocument implements iMetaData
{

    protected $key;

    public function __construct($doc, $key)
    {
        $this->key = $key;
        parent::__construct();
        if ($doc instanceof \DOMDocument)
        {
            $this->loadXML($doc->saveXML());
        } else
        {
            $this->loadXML($doc);
        }
    }

    public function __toString()
    {
        return $this->saveXML();
    }

    public function jsonSerialize()
    {
        return $this->saveXML();
    }

}
