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

namespace Cc\Mvc\Session;

use Cc\Mvc\AbstractHook;
use Cc\Mvc\Controllers;
use Cc\Mvc\SessionSaveController;

/**
 * Hook para agregar funciones de almacenado de atributos de controladores en la session 
 *
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Session 
 */
class HookSaveController extends AbstractHook
{

    /**
     * 
     * @var SessionSaveController 
     */
    protected static $sessionController;

    /**
     * 
     */
    public function InstanceController()
    {
        self::$sessionController = new SessionSaveController((Controllers::GetInstance()), ( Controllers::GetReflectionClass()));
        self::$sessionController->ParseAttrs();
    }

    /**
     * retorna el objeto de savecontroller
     * @return SessionSaveController
     */
    public static function &HookAutenticate()
    {
        return self::$sessionController;
    }

}
