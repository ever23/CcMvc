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

use Cc\ValidDependence;

/**
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidArray extends ValidDependence
{

    public function __construct($value, $option = array())
    {
        $option['StrictValue'] = true;
        parent::__construct($value, $option);
    }

    public function Validate(&$value)
    {

        if (!is_array($value) && !($value instanceof \Traversable && $value instanceof \ArrayAccess && $value instanceof \Countable))
        {
            return false;
        }
        if (isset($this->option['maxlength']) && count($value) > $this->option['maxlength'])
        {
            return false;
        }
        if (isset($this->option['minlength']) && count($value) < $this->option['minlength'])
        {
            return false;
        }


        if (isset($this->option['ValidItems']))
        {
            foreach ($value as $i => $v)
            {
                if (($value[$i] = self::Filter($v, $this->option['ValidItems'], self::ReturnedBool)) === false)
                {


                    return false;
                }
            }
        }
        return $value;
    }

}

/**
 * VALIDA UNA CADENA DE CARACTERES 
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidString extends ValidDependence
{

    public function __construct($value, $option = array())
    {
        $option['StrictValue'] = true;
        parent::__construct($value, $option);
    }

    public function Validate(&$value)
    {
        if (!is_string($value))
        {
            return false;
        }
        $n = strlen($value);
        if (isset($this->option['min']) && $n < $this->option['min'])
        {
            return false;
        }
        if (isset($this->option['max']) && $n > $this->option['max'])
        {
            return false;
        }
        if (isset($this->option['maxlength']) && $n > $this->option['maxlength'])
        {
            return false;
        }
        if (isset($this->option['pattern']))
        {


            if (!preg_match("/" . $this->option['pattern'] . "/", $value))
            {
                return false;
            }
        }

        if (isset($this->option['alpha']) && !ctype_alpha($value))
        {
            return false;
        }
        if (isset($this->option['alpha_num']) && !ctype_alnum($value))
        {
            return false;
        }
        if (isset($this->option['size']) && $n != $this->option['size'])
        {
            return false;
        }
        if (isset($this->option['between']) && ($n <= $this->option['between'][0] || $n >= $this->option['between'][1]))
        {
            return false;
        }
        if (isset($this->option['numeric']) && !is_numeric($value))
        {
            return false;
        }
        if (isset($this->option['in']))
        {
            $in = false;
            foreach ($this->option['in'] as $v)
            {
                if ($v == $value)
                {
                    $in = true;
                }
            }
            if (!$in)
                return false;
        }
        if (isset($this->option['not_in']))
        {

            foreach ($this->option['not_in'] as $v)
            {
                if ($v == $value)
                {
                    return false;
                }
            }
        }
        if (isset($this->option['options']) && (is_array($this->option['options']) || $this->option['options'] instanceof \Traversable))
        {
            foreach ($this->option['options'] as $v)
            {

                if ((is_array($v) || $v instanceof \ArrayAccess) && isset($v['value']))
                {
                    if ($v['value'] == $value)
                    {
                        return $value;
                    }
                } else
                {
                    if ($v == $value)
                    {
                        return $value;
                    }
                }
            }
            return false;
        }
        return $value;
    }

}

/**
 * VALIDA UN NUMERO FLOTANTE O ENTERO 
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidNumber extends ValidDependence
{

    public function __construct($value, $option = array())
    {
        $option['StrictValue'] = true;
        parent::__construct($value, $option);
    }

    public function &__toString()
    {
        return $this->value;
    }

    public function Validate(&$value)
    {
        if (!is_numeric($value))
        {
            return false;
        }
        if (isset($this->option['min']) && $value < $this->option['min'])
        {

            return false;
        }
        if (isset($this->option['max']) && $value > $this->option['max'])
        {
            return false;
        }
        if (isset($this->option['step']))
        {
            $i = 0;
            do
            {
                if (($i * $this->option['step']) == $value)
                {
                    return $value;
                }
                $i++;
            } while (($i * $this->option['step']) > $value);

            return false;
        }
        if (isset($this->option['options']) && (is_array($this->option['options']) || $this->option['options'] instanceof \Traversable))
        {
            foreach ($this->option['options'] as $v)
            {
                if ((is_array($v) || $v instanceof \ArrayAccess) && isset($v['value']))
                {
                    if ($v['value'] == $value)
                    {
                        return $value;
                    }
                } else
                {
                    if ($v == $value)
                    {
                        return $value;
                    }
                }
            }
            return false;
        }

        return $value;
    }

}

/**
 * VALIDA UN NUMERO FLOTANTE O ENTERO 
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidExadecimal extends ValidDependence
{

    public function __construct($value, $option = array())
    {
        $option['StrictValue'] = true;
        parent::__construct($value, $option);
    }

    public function Validate(&$value)
    {
        if (!is_string($value))
        {
            return false;
        }
        if (!preg_match("/#\c{0,6}/", $value))
        {
            return false;
        }

        return $value;
    }

}

/**
 * VALIDA UN EMAIL
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidEmail extends ValidDependence
{

    public function __construct($value, $option = array())
    {
        $option['StrictValue'] = true;
        parent::__construct($value, $option);
    }

    public function Validate(&$value)
    {
        if (!is_string($value))
        {
            return false;
        }
        if (isset($this->option['multiple']))
        {
            $ex = explode(',', $value);
            foreach ($ex as $v)
            {
                if (!filter_var($v, FILTER_VALIDATE_EMAIL))
                {
                    return false;
                }
            }
        }
        if (filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            return $value;
        }
        return '';
    }

//put your code here
}

/**
 * valida un fecha 
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidDate extends ValidString
{

    /**
     *
     * @var \DateTime 
     */
    protected $value;
    public $option = ['format' => 'Y/m/d'];

    public function Validate(&$value)
    {
        if (!is_string($value))
        {
            return false;
        }
        $value = parent::Validate($value);
        if (is_bool($value) && $value == false)
        {
            return false;
        }
        $time = new \DateTime($value);
        if (isset($this->option['despues_de']) && $time->getTimestamp() <= (new \DateTime($this->option['despues_de']))->getTimestamp())
        {
            return false;
        }
        if (isset($this->option['before']) && $time->getTimestamp() <= (new \DateTime($this->option['before']))->getTimestamp())
        {
            return false;
        }
        if (isset($this->option['antes_de']) && $time->getTimestamp() >= (new \DateTime($this->option['antes_de']))->getTimestamp())
        {
            return false;
        }
        if (isset($this->option['after']) && $time->getTimestamp() >= (new \DateTime($this->option['after']))->getTimestamp())
        {
            return false;
        }
        return $time;
    }

    public function __toString()
    {
        return $this->value->format($this->option['format']);
    }

    public function __wakeup()
    {
        return $this->value->__wakeup();
    }

    public function diff($object, $absolute = NULL)
    {
        return $this->value->diff($object, $absolute);
    }

    public function format($format)
    {
        return $this->value->format($format);
    }

    public function getOffset()
    {
        return $this->value->getOffset();
    }

    public function getTimestamp()
    {
        return $this->value->getTimestamp();
    }

    public function getTimezone()
    {
        return $this->value->getTimezone();
    }

//put your code here
}

/**
 * valida un numero telefonico en el siguiente formato  
 * ejemplo 0414-7456-235 o +58414-7456-235
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidTelf extends ValidString
{

    public $option = ['StrictValue' => true, 'pattern' => '(\d{4}-\d{4}-\d{3})|(\+\d{4,}-\d{4}-\d{3})'];

//put your code here
}

/**
 * valida una direccion ip
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidIp extends ValidDependence
{

    public $option = ['StrictValue' => true];

    public function Validate(&$value)
    {
        if (!is_string($value))
        {
            return false;
        }
        if (filter_var($value, FILTER_VALIDATE_IP))
            return $value;
    }

}

/**
 * valida una url
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidUrl extends ValidDependence
{

    public $option = ['StrictValue' => true];

    public function Validate(&$value)
    {
        if (!is_string($value))
        {
            return false;
        }
        if (!filter_var($value, FILTER_VALIDATE_URL))
        {
            return false;
        }
        if (isset($this->option['active']) && !checkdnsrr($value))
        {
            return false;
        }

        return $value;
    }

//put your code here
}

/**
 * valida nombres de archivos
 * @package CcMvc
 * @subpackage Validacion
 */
class ValidFilename extends \Cc\ValidFilename
{
    
}
