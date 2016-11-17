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

/**
 * Description of InternalSession
 *
 * @author usuario
 */
class InternalSession extends Session
{

    /**
     *  CONSTRUCTOR DE LA CLASE
     *  @param array $exet UN ARRAY CON EL NOMBRE DE LOS CONTROLADORES DONDE NO SE ARA UN EXEPCION Y NO SE EJECUTARA LA AUTENTICACION
     *  GENERALMENTE ESTOS NOMBRES DEVEN SER DEFINIDOS EN EL DOCUMENTO DE CONFIGURACION DE LA APP EN LOS PARAMENTROS  DE ATENTICACION
     */
    public function __construct(array $exet = [], array $param = [])
    {

        parent::__construct();
        $conf = Mvc::App()->Config();
        if (!is_dir($conf['App']['Cache']))
            mkdir($conf['App']['Cache']);
        $path = $conf['App']['Cache'] . 'session' . DIRECTORY_SEPARATOR;
        if (!is_dir($path))
            mkdir($path);
        session_save_path($path);
        $this->EstableceParam($param);
        $this->exept = $exet;
        self::$ReadAndClose = (isset($conf['Autenticate']['SessionCookie']['ReadAndClose']) ? $conf['Autenticate']['SessionCookie']['ReadAndClose'] : false) && $this->is_Autenticable();
    }

    /**
     * @access private
     * @param type $n
     * @param type $v
     */
    public function __set($n, $v)
    {
        if ($this->statusClose)
        {
            ErrorHandle::Notice("La modificacion del valor de \$_SESSION[$n] no surtira efecto ya que se cerro el archivo de session");
        }
        parent::__set($n, $v);
    }

    /**
     * @access private
     * @param type $offset
     * @param type $value
     */
    public function offsetSet($offset, $value)
    {
        if ($this->statusClose)
        {
            ErrorHandle::Notice("La modificacion del valor de \$_SESSION[$offset] no surtira efecto ya que se cerro el archivo de session");
        }
        parent::offsetSet($offset, $value);
    }

    /**
     * @access private
     * @param type $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->statusClose)
        {
            ErrorHandle::Notice("La modificacion del valor de \$_SESSION[$offset] no surtira efecto ya que se cerro el archivo de session");
        }
        parent::offsetUnset($offset);
    }

}
