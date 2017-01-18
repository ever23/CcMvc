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

namespace Cc\Mvc\DBtablaModel;

use Cc\Mvc;

/**
 * Description of CreateModel
 *
 * @author enyerber
 */
class CreateModel
{

    /**
     *
     * @var type 
     */
    protected $tabla;

    /**
     *
     * @var type 
     */
    protected $data = [];
    protected $ForeingKey = [];

    const CopyRight = "
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
 */\n";
    const ClassName = " /*
 * Clase modelo para la tabla {tabla}
 *
 */\n";

    /**
     *
     * @var Mvc\DBtabla 
     */
    protected $DBtabla;

    public function __construct($tabla)
    {
        $this->tabla = $tabla;
        $this->DBtabla = Mvc::App()->DataBase()->Tab($tabla);
    }

    public function CreateFromData()
    {
        $this->data = $this->DBtabla->GetCol();
        $this->ForeingKey = $this->DBtabla->Driver()->ForeingKey();
        $php = $this->php();
        $model = Mvc::App()->Config()->App['model'];
        $dir = $model . 'Database';
        if (!is_dir($dir))
        {
            mkdir($dir);
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $this->tabla . ".php", $php);
    }

    public function php()
    {
        $claset = preg_replace('/\{tabla\}/U', $this->tabla, self::ClassName);
        $class = "<?php \n" . self::CopyRight
                . "namespace " . __NAMESPACE__ . ";\n\n" . $claset
                . 'class  ' . $this->tabla . " extends " . __NAMESPACE__ . "\n"
                . "{\n"
                . "    public function Campos()\n"
                . "    {\n";
        foreach ($this->data as $i => $v)
        {

            $class.="        \$this->Colum('" . $i . "')";
            if (preg_match('/(.*\(.*\))$/', $v['Type']))
            {
                $var = $this->GetValuesType($v['Type']);
                $a = preg_replace('/(\n{1,} {1,})|(\r{1,}\n{1,})|\n{1,}|\t{1,}| {1,}|\r{1,}/', ' ', var_export($var, true));

                $class.="->type('" . explode('(', $v['Type'])[0] . "'," . $a . ")";
            } else
            {
                $class.="->type('" . trim($v['Type']) . "')";
            }
            if ($v['KEY'] == 'PRI')
            {
                $class.="->PrimaryKey()";
            }
            if ($v['Extra'] == 'auto_increment')
            {
                $class.="->autoincrement()";
            }
            if (isset($v['Default']))
                if ($v['Default'] == 'NULL')
                {
                    $class.="->DefaultNull()";
                } elseif ($v['Default'] == 'NOT NULL')
                {
                    $class.="->NotNull()";
                } elseif ($v['Default'] != '')
                {
                    $class.="->DefaultNull('" . $v['Default'] . "')";
                }
            $class.=";\n";
        }
        foreach ($this->ForeingKey as $i => $v)
        {
            $class.="        \$this->ForeingKey('" . $i . "')";
            $class.="->References('" . $v['table'] . "','" . $v['colum'] . "')";
            if ($v['OnDelete'] != '')
            {
                $class.="->OnDelete('" . $v['OnDelete'] . "')";
            }
            if ($v['OnUpdate'] != '')
            {
                $class.="->OnUpdate('" . $v['OnUpdate'] . "')";
            }
            if ($v['Match'] != '')
            {
                $class.="->Match('" . $v['Match'] . "')";
            }
        }
        $class.= "     }\n"
                . "}\n";
        return $class;
    }

    public function GetValuesType($type)
    {
        $a = [];
        if (preg_match("/.*\(.*\)/", $type, $a))
        {
            $expo = explode('(', $a[0]);
            unset($expo[0]);
            $a = implode('(', $expo);
            $exp = explode(",", substr($a, 0, -1));
            $ret = [];
            var_export($exp);
            foreach ($exp as $v)
            {
                $ret[] = str_replace("'", "", $v);
            }
            return $ret;
        } else
        {
            return array();
        }
    }

}
