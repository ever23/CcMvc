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

namespace Cc\Mvc\FormModel;

use Cc\Mvc;

/**
 * Representacion de un campo de un formulario
 *
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Modelo
 * @category FormModel
 * @method Campo String() String(string|array $validacion)  crea la configuracion de validacion para un string
 * @method Campo Number() Number(string|array $validacion)  crea la configuracion de validacion para un Numero
 * @method Campo Exadecimal() Exadecimal()  crea la configuracion de validacion para colores exadecimales 
 * @method Campo Email() Email(string|array $validacion)  crea la configuracion de validacion para email
 * @method Campo Date() Date(string|array $validacion)  crea la configuracion de validacion para una fecha
 * @method Campo Telf() Telf(string|array $validacion)  crea la configuracion de validacion para un numero telefonico
 * @method Campo Url() Url(string|array $validacion)  crea la configuracion de validacion para una Url
 * @method Campo Filename() Filename(string|array $validacion)  crea la configuracion de validacion para un nombre de archivo Filename
 */
class Campo
{

    /**
     * nombre del campo
     * @var string 
     */
    public $name;

    /**
     * tipo del campo
     * @var string 
     */
    public $type;

    /**
     * valor por defecto 
     * @var mixes 
     */
    public $default;

    /**
     * mensaje de error
     * @var string|\callable 
     */
    public $MSJerror;

    /**
     * valor
     * @var mixes 
     */
    public $Value;

    /**
     * configuracion para la validacion 
     * @var array 
     */
    public $valid = [];

    /**
     * callback para validacion
     * @var \callable      
     */
    public $calableValid = '';

    /**
     * configuracion para validacion generada por FormModel
     * @var array
     */
    protected $realValid = [];

    /**
     * en caso de ser de tipo select son las opciones 
     * @var array 
     */
    protected $options = [];

    /**
     * 
     * @param string $name nombre del campo
     * @param string $type tipo del campo
     */
    public function __construct($name, $type = 'text')
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * establece el tipo de campo
     * @param string $type
     * @return \Cc\Mvc\FormModel\Campo autoreferencia
     */
    public function &type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * establece la configuracion de validacion 
     * @param array|string $valid
     * @return \Cc\Mvc\FormModel\Campo autoreferencia
     */
    public function &Validator($valid)
    {
        if (is_array($valid))
        {
            $this->valid = $valid;
        } elseif (is_string($valid))
        {
            $this->valid = $valid;
        } elseif (is_callable($valid))
        {
            $this->calableValid = $valid;
        }
        return $this;
    }

    /**
     * crea la configuracion de validacion a partir de una cadena de texto
     * @param string $string
     * @return array
     */
    private function CreateValidFromString($string)
    {
        $str = preg_split('/\|/', $string);
        $options = [];
        foreach ($str as $v)
        {
            $exp1 = explode(':', $v);
            $name = $exp1[0];
            unset($exp1[0]);
            $exp = explode(',', implode(':', $exp1));
            if (count($exp) == 1)
            {
                $options[$name] = trim($exp[0]) == '' ? true : $exp[0];
            } else
            {
                $options[$name] = $exp;
            }
        }
        return $options;
    }

    /**
     * establece el valor por defecto
     * @param string $value
     * @return \Cc\Mvc\FormModel\Campo autoreferencia 
     */
    public function &DefaultValue($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * establece el mensaje de error en caso de fallar la validacion 
     * @param string $mesaje
     * @return \Cc\Mvc\FormModel\Campo
     */
    public function &MensajeError($mesaje)
    {
        $this->MSJerror = $mesaje;
        return $this;
    }

    /**
     * establece el valor real del campo
     * @param mixes $value
     */
    public function SetValue($value)
    {
        $this->Value = $value;
    }

    /**
     * establece la configuracion de validacion 
     * @param string $valid
     * @return \Cc\Mvc\FormModel\Campo
     * @internal solo usado por FormModel
     */
    public function &SetRealValid($valid)
    {
        $this->realValid = $valid;
        return $this;
    }

    /**
     * Obtiene el mensaje de error de un campo en caso de no pasar la validacion
     * @param string $valid
     * @return string
     */
    public function GetError($valid)
    {
        if (is_callable($this->MSJerror))
        {
            return $this->MSJerror($valid);
        } else
        {

            return $this->MSJerror;
        }
    }

    /**
     * retornar la configuracion de validacion
     * @return array
     * @internal 
     */
    public function GetValid()
    {
        if (is_array($this->valid))
        {
            $valid = $this->valid;
        } elseif (is_string($this->valid))
        {
            $valid = $this->CreateValidFromString($this->valid);
        }

        if ($this->options)
        {
            $valid+=['options' => $this->options];
        }
        return $valid;
    }

    /**
     * establece las opciones de un campo tipo select
     * @param array $options
     */
    public function in_options($options)
    {
        $this->options = $options;
    }

    /**
     * funcion magica para seleccionar el tipo de validacion 
     * @param string $name
     * @param string $arguments
     * @throws Exception
     * @internal 
     */
    public function __call($name, $arguments)
    {
        $valid = '\\Cc\\Mvc\\Valid';

        if (Mvc::App()->autoloadCore($valid . $name) || Mvc::App()->AutoloaderLib->GetLoader('model')->autoloadCore($valid . $name))
        {
            if (isset($arguments[0]) && is_string($arguments[0]))
            {
                $arguments[0] = $this->CreateValidFromString($arguments[0]);
            }
            $class = $valid . $name;
            $this->valid = $class::CreateValid(...$arguments);
        } else
        {
            throw new Exception("El metodo $name no esta definido");
        }
    }

}
