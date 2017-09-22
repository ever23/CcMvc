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
 * Crea una clase model a partir de la tabla en la base de datos
 *
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Modelo
 * @category DBtablaModel
 */
class CreateModel
{

    /**
     *
     * @var string 
     */
    protected $tabla;

    /**
     *
     * @var array 
     */
    protected $data = [];

    /**
     *
     * @var array 
     */
    protected $ForeingKey = [];

    /**
     * comentario de cabecera 
     */
    const CopyRight = "
 /*
 * 
 * Powered by CcMvc 
 * 
 */\n";

    /**
     * comentario de clase 
     */
    const ClassName = " /*
 * Clase modelo para la tabla {tabla}
 *
 */\n";

    /**
     * 
     * @var Mvc\DBtabla 
     */
    protected $DBtabla;

    /**
     * 
     * @param string $tabla nombre de la tabla 
     */
    public function __construct($tabla)
    {
        $this->tabla = $tabla;
    }

    /**
     * crea un modelo a partir de una tabla en la base de datos
     * haciendo ingenieria inversa a la tabla 
     * @param bool $inserts true para agregar al modelo los datos de la tabla
     * @return string nombre del archivo creado
     */
    public function CreateFromData($inserts = false, \Cc\iDataBase $db)
    {
        $this->DBtabla = $db->Tab($this->tabla);
        $this->data = $this->DBtabla->GetCol();
        $this->ForeingKey = $this->DBtabla->Driver()->ForeingKey();
        if ($inserts)
        {
            $php = $this->php($this->CreateColumFromData(), $this->CreateInitialized($this->DBtabla));
        } else
        {
            $php = $this->php($this->CreateColumFromData());
        }

        $model = Mvc::App()->Config()->App['model'];
        $dir = $model . 'DBtablaModel';
        if (!is_dir($dir))
        {
            mkdir($dir);
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $this->tabla . ".php", $php);
        return $dir . DIRECTORY_SEPARATOR . $this->tabla . ".php";
    }

    /**
     * crea una platilla de modelo 
     * @return string nombre del archivo creado
     */
    public function Create()
    {
        $php = $this->php();
        $model = Mvc::App()->Config()->App['model'];
        $dir = $model . 'DBtablaModel';
        if (!is_dir($dir))
        {
            mkdir($dir);
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $this->tabla . ".php", $php);

        return $dir . DIRECTORY_SEPARATOR . $this->tabla . ".php";
    }

    /**
     * construlle la clase 
     * @param string $created texto del metodo Create
     * @param string $inserts texto del metodo Initialized
     * @return string
     */
    protected function php($created = "        // tu condigo aqui\n", $inserts = "        // tu condigo aqui\n")
    {
        $claset = preg_replace('/\{tabla\}/U', $this->tabla, self::ClassName);
        $class = "<?php \n" . self::CopyRight
                . "namespace Cc\\Mvc\\TablaModel;\n\n"
                . "use Cc\\Mvc\\TablaModel;\n" . $claset
                . 'class  ' . $this->tabla . " extends TablaModel\n"
                . "{\n\n"
                . "    /**\n"
                . "    * Este metod sera llamado cuando se este por crear la tabla " . $this->tabla . "\n"
                . "    * Ejemplo del codigo \n"
                . "    * <code>\n"
                . "    * <?php //Ejemplo del codigo \n"
                . "    * \$this->Colum('mi_campo')->PrimaryKey(); // UN CAMPO PARA LA TABLA\n"
                . "    * \$this->Colum('mi_otro_campo')->VARCHAR(50); // OTRO CAMPO PARA LA TABLA\n"
                . "    * \$this->ForeingKey('mi_campo')->References('mi_otra_tabla')->OnUpdate('CASCADE'); // UNA CLAVE FORANEA  \n"
                . "    * ?>\n"
                . "    * </code>\n"
                . "    */\n"
                . "    public function Create()\n"
                . "    {\n";
        $class.= $created;
        $class.= "    }\n"
                . "\n"
                . "    /**\n"
                . "    * Este metod sera llamado cuando se inicialize la tabla " . $this->tabla . "\n"
                . "    * Ejemplo del codigo \n"
                . "    * <code>\n"
                . "    * <?php //Ejemplo del codigo\n"
                . "    * \$this->Insert('hola1','hola2');//insertando usando el formato de parametros\n"
                . "    * \$this->Insert(['hola1','hola2']);//insertando usando el formato arrays simples\n"
                . "    * \$this->Insert(['campo1'=>'hola1','campo2'=>'hola2']);//insertando usando el formato arrays asociativos o diccionario\n"
                . "    * ?>\n"
                . "    * </code>\n"
                . "    */\n"
                . "    public function Initialized()\n"
                . "    {\n";
        $class.= $inserts;
        $class.= "    }\n"
                . "}\n";
        return $class;
    }

    /**
     * crea el codigo php con los metadatos de la tabla 
     * @return string
     */
    protected function CreateColumFromData()
    {
        $class = "        // Columnas de la tabla \n";
        foreach ($this->data as $i => $v)
        {
            $class.= "        \$this->Colum('" . $i . "')";
            if (preg_match('/(.*\(.*\))$/', $v['Type']))
            {
                $var = $this->GetValuesType($v['Type']);
                // $a = preg_replace('/(\n{1,} {1,})|(\r{1,}\n{1,})|\n{1,}|\t{1,}| {1,}|\r{1,}/', ' ', var_export($var, true));
                $tpeName = explode('(', $v['Type'])[0];
                if (count(explode(" ", $tpeName)) > 1)
                {
                    $class.="->type('" . $tpeName . "',[" . implode(',', $var) . "])";
                } else
                {
                    $class.="->" . strtoupper($tpeName) . "(" . implode(',', $var) . ")";
                }
            } else
            {

                if (count(explode(" ", trim($v['Type']))) > 1)
                {
                    $class.="->type('" . trim($v['Type']) . "')";
                } else
                {
                    $class.="->" . strtoupper(trim($v['Type'])) . "()";
                }
            }
            if ($v['KEY'] == 'PRI')
            {
                $class.="->PrimaryKey()";
            }
            if ($v['Extra'] == 'auto_increment')
            {
                $class.="->autoincrement()";
            }
            if (isset($v['Nullable']) && !$v['Nullable'])
            {
                $class.="->NotNull()";
            }
            if (isset($v['Default']))
            {

                $class.="->DefaultValue('" . $v['Default'] . "')";
            }


            $class.=";\n";
        }
        if ($this->ForeingKey)
            $class.="        // Claves foraneas de la tabla \n";
        foreach ($this->ForeingKey as $i => $v)
        {
            $class.="        \$this->ForeingKey('" . $i . "')";
            $class.="->References('" . $v['table'] . "','" . $v['colum'] . "')";
            if ($v['OnDelete'] != '' && $v['OnDelete'] != 'NO ACTION')
            {
                $class.="->OnDelete('" . $v['OnDelete'] . "')";
            }
            if ($v['OnUpdate'] != '' && $v['OnUpdate'] != 'NO ACTION')
            {
                $class.="->OnUpdate('" . $v['OnUpdate'] . "')";
            }
            if ($v['Match'] != '' && $v['Match'] != 'NONE')
            {
                $class.="->Match('" . $v['Match'] . "')";
            }
            $class.=";\n";
        }
        return $class;
    }

    /**
     * crea el codigo php con los datos de la tabla 
     * @param \Cc\Mvc\DBtabla $tabla
     * @return string
     */
    protected function CreateInitialized(\Cc\Mvc\DBtabla $tabla)
    {
        $class = '';
        $tabla->Select();
        /* @var $row Cc\Mvc\DBRow */
        foreach ($tabla as $row)
        {
            $class.="        \$this->Insert(";
            $campos = '';
            foreach ($row as $name => $coll)
            {
                if (is_resource($coll))
                {
                    $bin = '';
                    while (!feof($coll))
                    {
                        $bin.=fgets($coll);
                    }

                    $campos.="\n        base64_decode('" . base64_encode($bin) . "')\n        ";
                } elseif (preg_match("/" . implode("|", ['longblob', 'blob', 'binary', 'bytea']) . "/i", $tabla->GetCol($name)['Type']))
                {


                    $campos.="\n        base64_decode('" . base64_encode($coll) . "')\n        ";
                } else
                {
                    $tabla->Driver()->FilterSqli = true;
                    $r = $tabla->Driver()->FormatVarInsert($coll, $name);

                    if ((is_string($r) && strncmp($r, '0x', 2) === 0))
                    {
                        $campos.="\n        '" . $r . "'\n        ";
                    } elseif (!is_null($coll) && $coll != 'false' && $coll != 'true' && !is_numeric($coll))
                    {
                        $coll = $tabla->Driver()->FilterSqlI($coll);
                        $campos.='"' . addcslashes($coll, '$"\\') . '"';
                    } else
                    {
                        $campos.=$r;
                    }
                }

                $campos.=",";
            }

            $class.=substr($campos, 0, -1) . ");\n";
        }
        return $class ? $class : "        // tu condigo aqui\n";
    }

    /**
     * convierte los parametros de los tipos en array
     * @param string $type
     * @return array
     */
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
            //var_export($exp);
            foreach ($exp as $v)
            {
                $text = str_replace("'", "", $v);

                $ret[] = $this->DBtabla->Driver()->FormatVarInsert($text);
            }
            return $ret;
        } else
        {
            return array();
        }
    }

}
