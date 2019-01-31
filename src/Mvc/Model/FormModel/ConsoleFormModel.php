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

use Cc\Mvc;

/**
 * Controlador de consola para crear clases modelos de formularios
 *
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Modelo
 * @category FormModel
 */
class FormModel extends \Cc\Mvc\AbstracConsole
{

    /**
     * crea una clase de formulario
     * @param string $name -name nombre de la clase
     */
    public function Create($name)
    {
        $this->OutLn("\nCreando Modelo de formulario $name\n");
        $php = "<?php\n\n"
                . ""
                . "namespace Cc\\Mvc;\n\n"
                . "/**\n"
                . " * Modelo de formulario $name\n"
                . " *\n"
                . " */\n"
                . "class $name extends FormModel\n"
                . "{\n"
                . "    /**\n"
                . "     * Usa este metodo para definir los capos que tendra el formulario\n"
                . "     *<code>\n"
                . "     * <?php\n"
                . "     * \$this->email('email_contac')->Validator('max:200|required|placeholder:Email');\n"
                . "     * \$this->tel('telf_contac')->Validator('max:200|required|placeholder:Telefono');\n"
                . "     * \$this->string('texto')->Validator('max:600|required|placeholder:tu texto');\n"
                . "     *</code>\n"
                . "     *\n"
                . "     */\n"
                . "    protected function Campos()\n"
                . "    {\n"
                . "        //tu codigo aqui\n"
                . "    }\n\n"
                . "    /**\n"
                . "     * Este metodo se ejecutara cuando se reciban datos del formulario\n"
                . "     *\n"
                . "     */\n"
                . "    protected function OnSubmit()\n"
                . "    {\n"
                . "        //tu codigo aqui\n"
                . "    }\n"
                . "}\n";

        $file = Mvc::App()->Config()->App['model'] . 'FormModel' . DIRECTORY_SEPARATOR . $name . ".php";
        if (!is_dir(Mvc::App()->Config()->App['model'] . 'FormModel'))
        {
            mkdir(Mvc::App()->Config()->App['model'] . 'FormModel');
        }
        file_put_contents($file, $php);
        $this->OutLn(" Modelo de formulario $name creado en $file\n");
        Mvc::App()->AutoloaderLib->GetLoader('model')->Reiniciar();
    }

}
