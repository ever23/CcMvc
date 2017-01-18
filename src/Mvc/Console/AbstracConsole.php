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
 * Clases abstracta para la creacion de controladores de consola
 *
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Console
 */
abstract class AbstracConsole
{

    /**
     * imprime un texto en el flujo STDOUT
     * @param string $out
     */
    protected function Out($out)
    {
        RouteConsole::Out($out);
    }

    /**
     * obtiene una entrada de el flujo STDIN
     * @return string 
     */
    protected function In()
    {
        return RouteConsole::In();
    }

}
