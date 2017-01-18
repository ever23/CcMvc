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

namespace Cc\Mvc\Console;

use Cc\Mvc\Config;
use Cc\Mvc\AbstracConsole;

/**
 * creaa controladores de comsola para el usuario
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Console
 */
class Console extends AbstracConsole
{

    /**
     * creaa un controlador de comsola para el usuario
     * @param Config $conf
     * @param string $class -class nombre de la clase
     * @param string $method -method nombre del metodo
     */
    public function create(Config $conf, $class, $method)
    {

        if (trim($class) == '')
        {
            $this->Out("Debe proporcionar el nombre de la clase ");
        }

        $controller = "<?php\n"
                . "\n";


        $controller.="namespace " . __NAMESPACE__ . ";\n\n";
        $controller.="use " . AbstracConsole::class . ";\n\n";

        $name = $class;
        $controller.= "class " . $name . " extends AbstracConsole\n"
                . "{\n";
        $nameController = $name;
        if ($method)
        {
            $nameController.=':' . $method;
            $controller.= "    public function " . $method . "()\n"
                    . "    {\n"
                    . "        // tu codigo\n"
                    . "    }\n";
        } else
        {
            $controller.= "    /**\n"
                    . "     * Tus Comando de consola\n"
                    . "     */\n";
        }

        $controller.= "}\n";



        $file = $conf->App['Console'] . $class . '.php';
        file_put_contents($file, $controller);
        unlink($conf->App['Console'] . \Cc\Autoload\FileCore);

        $this->Out("El Comando de consola $nameController fue creado en " . $file . "\n");
    }

}
