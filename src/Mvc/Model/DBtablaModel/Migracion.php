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

use Cc\Mvc\DBtablaModel\CreateModel;
use Cc\Mvc;

/**
 *  Controlador de consola para la base de datos
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Modelo
 * @category DBtablaModel
 */
class Migracion extends \Cc\Mvc\AbstracConsole
{

    /**
     *
     * @var \Cc\iDataBase 
     */
    protected $DataBase;

    public function __construct()
    {
        $this->Out("Conectando con la base de datos...\n");
        $this->DataBase = Mvc::App()->ConetDataBase();
    }

    /**
     * crea una clase modelo para una tabla
     * @param string $tabla -tabla nombre de la tabla
     */
    public function Create($tabla)
    {
        $model = new CreateModel($tabla);
        $file = $model->Create();
        $this->Out('Modelo plantilla De tabla ' . $tabla . " creado en " . $file . "\n");
    }

    /**
     * crea una o mas clase modelos a partir de una base de datos
     * @param string $tabla -tabla nombre de la tabla
     * @param bool $all -all si existe seran todas las tablas de la base de datos 
     * @param string $inicializar -inicializar agrega los datos disponibles
     * 
     */
    public function CreateFromDatabase($tabla = NULL, $all = false, $inicializar = false)
    {
        if ($all)
        {

            $drivers = $this->DataBase->GetDriver();
            $this->DeleteFiles(Mvc::App()->Config()->App['model'] . 'DBtablaModel');
            foreach ($drivers->ListTablas() as $tabla)
            {

                $model = new CreateModel($tabla);
                $this->Out("Realizando Ingenieria inversa a la tabla $tabla de la base de datos ...\n");
                $file = $model->CreateFromData($inicializar, $this->DataBase);
                $this->Out('Modelo De tabla ' . $tabla . " creado en " . $file . "\n\n");
            }
        } else
        {
            if (is_null($tabla))
            {
                $this->Out("Debe proporciona el nombre de la tabla con -tabla {tablename}\n");
                return;
            }
            $this->DeleteFiles(Mvc::App()->Config()->App['model'] . 'DBtablaModel' . DIRECTORY_SEPARATOR . $tabla . 'php');
            $model = new CreateModel($tabla);
            $this->Out("Realizando Ingenieria inversa a la base de datos ...\n");
            $file = $model->CreateFromData($inicializar, $this->DataBase);
            $this->Out('Modelo De tabla ' . $tabla . " creado en " . $file . "\n");
        }
        Mvc::App()->AutoloaderLib->GetLoader('model')->Reiniciar();
    }

    /**
     * 
     * Crea las tablas y las inicializa 
     * a partir de las clases modelos existentes
     */
    public function DatabaseFromModel($force = false)
    {
        if (!$this->DataBase)
        {
            return;
        }
        $this->Out("Buscando clases modelos de tablas  ...\n");
        $clases = Mvc::App()->GetCoreClass();
        $clases = Mvc::App()->AutoloaderLib->GetLoader('model')->GetCoreClass();
        $rclases = array_keys($clases);

        $model = preg_grep("/^" . preg_quote("Cc\Mvc\TablaModel\\") . "/i", $rclases);
        $tablasClases = [];
        $sqls = [];
        $references = [];
        $order = [];
        $i = 0;
        foreach ($model as $clase)
        {
            $m = new \ReflectionClass($clase);
            if ($m->isSubclassOf(\Cc\Mvc\TablaModel::class))
            {

                $TC = explode("\\", $clase);
                $name = array_pop($TC);
                $this->Out("Creando la tabla  $name de la clase $clase ...\n");
                /* @var $modelo \Cc\Mvc\TablaModel */
                $modelo = $m->newInstance($name);
                $modelo->Create();
                $sqls[$name] = $modelo;
                $references[$name] = $modelo->GetReferences();
                $order[$name] = $i++;
            }
        }

        $new = $this->Ordenar($references, $order);
        $otro = [];
        // var_dump($new);
        foreach ($new as $i => $v)
        {
            $otro[$v] = $i;
        }
        if ($force)
            foreach (array_reverse($otro, true) as $i => $tabla)
            {
                $this->DataBase->Query("Drop table $tabla");
            }

        foreach ($otro as $i => $tabla)
        {

            $this->OutLn("\n\n Creando Tabla $tabla...\n");
            $sql = $sqls[$tabla]->CreateSQL($this->DataBase);
            $this->OutLn($sql);
            if ($this->DataBase->Query($sql))
            {
                $this->OutLn("Se creo la tabla  $tabla con exito!!...");
            } else
            {
                $this->OutLn("no se creo la tabla  $tabla de la clase  " . $this->DataBase->error() . "...");
                return;
            }
            $this->OutLn("\n\n Inicializando tabla $tabla ...");
            $sqls[$tabla]->Initialized();
            $Inserts = $sqls[$tabla]->GetInserts();
            if ($this->InicializarTablas($tabla, $Inserts))
            {
                $this->OutLn("Se inicializo la tabla $tabla con exito!!...");
            } else
            {
                $this->Out("error a inicializar la tabla  $tabla ...\n");
                return;
            }
        }

        $this->OutLn(" Finalizada la operacion ...");
    }

    private function InicializarTablas($tabla, array $Inserts)
    {
        $DBtabla = $this->DataBase->Tab($tabla);
        $DBtabla->Driver()->FilterSqli = true;
        foreach ($Inserts as $params)
        {
            //var_dump($params);
            if (!$DBtabla->Insert(...$params))
            {
                //  var_dump($params);
                return false;
            }
        }
        return true;
    }

    /**
     * ordena las tablas segun sus claves foraneas 
     * @param array $referenced lista de tablas con claves foraneas 
     * @param array $order tablas en el orden actual
     * @return array
     */
    private function Ordenar($referenced, $order)
    {
        $newOrder = [];
        $a = false;
        $tablas = array_keys($order);
        for ($i = 0; $i < count($order); $i++)
        {
            $ref = $referenced[$tablas[$i]];
            foreach ($ref as $referencia)
            {
                if ($order[$referencia] > $i)
                {
                    $otraTabla = [];
                    for ($j = 0; $j <= $i - 1; $j++)
                    {
                        $otraTabla[$tablas[$j]] = $j;
                    }
                    $otraTabla[$referencia] = $j++;
                    $otraTabla[$tablas[$i]] = $j++;
                    $z = $j;
                    for ($j = $i; $j < count($order); $j++)
                    {
                        if ($tablas[$j] == $referencia)
                        {
                            continue;
                        }
                        $otraTabla[$tablas[$j]] = $z;
                        $z++;
                    }
                    return $this->Ordenar($referenced, $otraTabla);
                }
            }
        }

        return $order;
    }

    /**
     * elimina archivos de un directorio 
     * @param string $dir
     */
    private function DeleteFiles($dir)
    {
        if (is_dir($dir))
        {
            $dir = realpath($dir);
            $d = dir($dir);
            while ($f = $d->read())
            {
                if ($f != '.' && $f != '..')
                {
                    unlink($dir . DIRECTORY_SEPARATOR . $f);
                }
            }
        } elseif (is_file($dir))
        {
            unlink($dir);
        }
    }

}
