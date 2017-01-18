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

/**
 * Controlador de consola para crear Controladores Webs 
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Console
 */
class Controller extends \Cc\Mvc\AbstracConsole
{

    /**
     * crea una clase controladora web 
     * @param Config $conf
     * @param string $class -class nombre de la clase
     * @param string $paquete -paquete nombre del paquete (opcional)
     */
    public function create(Config $conf, $class, $paquete = NULL)
    {

        if (trim($class) == '')
        {
            $this->Out("Debe proporcionar el nombre de la clase ");
        }
        $Prefijo = $conf->Controllers['Prefijo'];
        $controller = "<?php\n"
                . "\n";
        if ($paquete)
        {
            $controller.="namespace Cc\\Mvc\\" . $paquete . ";\n\n";
            $controller.="use Cc\\Mvc\\Controller;\n\n";
        } else
        {
            $controller.="namespace Cc\\Mvc;\n\n";
        }
        $name = $Prefijo . $class;
        $controller.= "class " . $name . " extends Controller\n"
                . "{\n"
                . "    public function index()\n"
                . "    {\n"
                . "        // tu codigo\n"
                . "    }\n"
                . "}\n";


        if (!$paquete)
        {
            $nameController = $class . $conf->Router['OperadorAlcance'] . "index";
            $file = $conf->App['controllers'] . $name . '.php';
        } else
        {
            $nameController = $class . $conf->Router['OperadorAlcance'] . $paquete . $conf->Router['OperadorAlcance'] . "index";
            if (!is_dir($conf->App['controllers'] . $paquete))
            {
                mkdir($conf->App['controllers'] . $paquete);
            }
            $file = $conf->App['controllers'] . $paquete . DIRECTORY_SEPARATOR . $name . '.php';
        }
        file_put_contents($file, $controller);
        $this->Out("El Controlador $nameController fue creado en " . $file . "\n");
    }

}
