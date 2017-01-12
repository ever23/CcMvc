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
 * Clase Abstracta para los Hooks 
 *  
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Hook
 * @property ViewController $view Controlador de vistas   
 * @property LayautManager $Layaut Controlador de layauts    
 * @method void AppRun() AppRun(mixes ...$_)  Se ejecuta una vez se ha configurado e inicializado la aplicacion
 * @method void Route() Route(mixes ...$_)  Se ejecuta despues del enrutamiento              
 * @method void LoadController() LoadController(mixes ...$_) Se ejecuta una vez se ha cargado la clase controladora y antes de crear la coneccion con la base de datos 
 * @method void ConetDatabase() ConetDatabase(mixes ...$_)  se ejecuta al conectar con la base de datos solo cuando esta es exitosa
 * @method void PreSessionStart() PreSessionStart(mixes ...$_) se ejecuta justo antes de iniciar la session 
 * @method void PostSessionStart() PostSessionStart(mixes ...$_) se ejecuta al establecer la session 
 * @method void PreConstrucController() PreConstrucController(mixes ...$_) Se ejecuta antes de instancial el controlador y ejecutar su constructor
 * @method void PostConstrucController() PostConstrucController(mixes ...$_) Se ejecuta despues de ejecutar el constructor del controlador
 * @method void AppEnd() AppEnd(mixes ...$_) Se ejecuta al finalizar la aplicacion 
 * @method void LayautController() LayautController(mixes ...$_) 
 */
abstract class AbstractHook
{

    /**
     *
     * @var ViewController 
     */
    public $View;

    /**
     *
     * @var LayautManager 
     */
    public $Layaut;
    public $events = [];

    public final function &__get($name)
    {
        $NULL = NULL;
        if (strtolower($name) == 'view')
        {
            return static::$View;
        } elseif (strtolower($name) == 'layaut')
        {
            return static::$Layaut;
        } else
        {
            ErrorHandle::Notice("EL ATRIBUTO " . static::class . '::$' . $name . " NO ESTA DEFINIDO ");
            return $NULL;
        }
    }

}
