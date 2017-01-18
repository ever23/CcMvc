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

use Cc\Mvc;
use Cc\Mvc\Autenticate;
use Cc\Mvc\Exception;
use Cc\Mvc\AbstractHook;

/**
 * Description of HookSession
 *
 * @author enyerber
 */
class HookAutenticate extends AbstractHook
{

    public function PreSessionStart()
    {
        $conf = Mvc::Config();
        if (!empty($conf['Autenticate']['class']))
        {
            $class_name = $conf['Autenticate']['class'];
            if (!class_exists($class_name, true))
            {
                throw new Exception("LA CLASE " . $class_name . " MANEJADORA DE AUTENTIFICACION NO EXISTE ");
            }
            Mvc::App()->Session = new $class_name(...$conf['Autenticate']['param']);
        } else
        {
            Mvc::App()->Session = [];
        }
    }

    public function PostSessionStart()
    {
        if (Mvc::App()->Session instanceof Autenticate)
        {
            Mvc::App()->Session->SetDependenceInyector(Mvc::App()->DependenceInyector);

            Mvc::App()->Session->Start(Mvc::App()->GetInternalSession());
            Mvc::App()->Log("Autenticando....");
            Mvc::App()->Session->Auth();
        }
    }

}
