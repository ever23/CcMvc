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
 * Crea clases de autenticacion 
 *
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package CcMvc
 * @subpackage Session 
 */
class autenticate extends \Cc\Mvc\AbstracConsole
{

    /**
     * crea una clase de autenticacion
     * @param string $name -name nombre de la clase
     */
    public function Create($name)
    {
        $this->OutLn("\n $name\n");

        $php = "<?php\n\n"
                . ""
                . "namespace Cc\\Mvc;\n\n"
                . "/**\n"
                . " * Modelo de Autenticacion $name\n"
                . " *\n"
                . " */\n"
                . "class $name extends AuteticateUserDB\n"
                . "{\n"
                . "    /**\n"
                . "     * Usa este metodo para definir los metadatos para la auteticacion \n"
                . "     *\n"
                . "     */\n"
                . "    protected function InfoUserDB()\n"
                . "    {\n"
                . "        //\$this->TablaDeUsuarios('usuarios')// aqui el nombre de la tabla de usuarios\n"
                . "        //\$this->ColUserName('user_name')// aqui el campo del nombre de usuario email o nick\n"
                . "        //\$this->ColPassword('user_hash')// aqui el campo que almacena el hash de contraseña\n"
                . "        //\$this->ColUserType('permiso')// aqui el campo que almacena los permisos del usuario\n\n"
                . "    }\n\n"
                . "    /**\n"
                . "     * Este metodo se ejecutara cuando sea registrada una nueva session\n"
                . "     *\n"
                . "     */\n"
                . "    protected function OnSessionRegister()\n"
                . "    {\n"
                . "        //tu codigo aqui\n"
                . "    }\n\n"
                . "    /**\n"
                . "     * Este metodo se ejecutara cuando la autenticacion falle \n"
                . "     *\n"
                . "     */\n"
                . "    protected function OnFailed()\n"
                . "    {\n"
                . "        switch (\$this->IsFailed())\n"
                . "        {\n"
                . "             case self::FailedAuth:\n"
                . "                 //usa esta opcion para definir acciones para cuando no este la session iniciada y los controladores la requieran \n"
                . "             case self::DenyAccessForUser:\n"
                . "                 //usa esta opcion para definir acciones para cuando se le niega explicitamente el acceso a un determinado tipo de usuario\n"
                . "             case self::FailedDataBase:\n"
                . "                 //usa esta opcion para definir acciones para cuando ocurre algun error en la base de datos\n"
                . "             default:\n"
                . "                 //usa esta opcion para definir acciones para cuando ocurre no hay session y los controladores no la requiren \n"
                . "        }\n"
                . "    }\n\n"
                . "    /**\n"
                . "     * Este metodo se ejecutara la autenticacion sea exitosa\n"
                . "     *\n"
                . "     */\n"
                . "    protected function OnSuccess()\n"
                . "    {\n"
                . "        //tu codigo aqui\n"
                . "    }\n"
                . "}\n";

        $file = Mvc::App()->Config()->App['model'] . $name . ".php";

        if (file_exists($file))
        {
            $this->OutLn("El archivo $file ya existe porfavor indique otro nombre ");
            return;
        }

        file_put_contents($file, $php);
        $this->OutLn(" Modelo de Autenticacion $name creado en $file\n");
        Mvc::App()->AutoloaderLib->GetLoader('model')->Reiniciar();
    }

}
