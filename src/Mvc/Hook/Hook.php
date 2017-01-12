<?php

/**
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
 *
 *  
 */

namespace Cc\Mvc;

/**
 * Hook                                                       
 * se usa para exetender el framework            
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Hook
 *
 */
class BaseHook extends AbstractHook
{

    /**
     * Se ejecuta al ocurrir un error http 400
     * cuando la peticion del cliente es erronea y no puede ser procesada
     * @param string $mensaje
     */
    public function Error400($mensaje)
    {
        $this->View->LoadInternalView('Error400.php');
    }

    /**
     * Se ejecuta al ocurrir un error http 401
     * cuando la autenticacion  falla y no se captura 
     * @param string $mensaje
     */
    public function Error401($mensaje)
    {
        $this->View->LoadInternalView('Error401.php');
    }

    /**
     * Se ejecuta al ocurrir un error http 403
     * cuando al framework niega el acceso a un url 
     * @param string $mensaje
     */
    public function Error403($mensaje)
    {
        $this->View->LoadInternalView('Error403.php');
    }

    /**
     * Se ejecuta al ocurrir un error http 404
     * cuando una url no existe 
     * @param string $mensaje
     */
    public function Error404($mensaje)
    {
        $this->View->LoadInternalView('Error404.php');
    }

    /**
     * Se ejecuta al ocurrir un error http 500
     * cuando ocurre un erro en el framework tambien en caso exception no capturada por el usuario, o en caso de ser php 7 en algun error de sintaxis  
     * @param string $mensaje
     */
    public function Error500($mensaje)
    {

        $this->View->LoadInternalView('Error500.php');
    }

}
