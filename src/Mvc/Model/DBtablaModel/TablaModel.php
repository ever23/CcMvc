<?php

/*
 * Copyright (C) 2016 Enyerber Franco
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

use Cc\Mvc\DBtablaModel\ColumModel;
use Cc\Mvc\DBtablaModel\ForeingKey;

/**
 * 
 * ESTA CLASE PROPORCIONA UNA INTERFACE ABSTRACTA  DE DEFINICION PARA 
 * PROPORCIONAR A LA CLASE {@link \Cc\Mvc\DBtablaModel} LOS METADATOS DE UNA TABLA EN LA BASE DE DATOS 
 * LA CLASE EXTENDIDA DE ESTA DEBE TENER EL MISMO NOMBRE QUE LA TABLA EN LA BASE DE DATOS A LA 
 * QUE SE REFIERE 
 * SE DEBE DEFINIR EL UNICO METODO ABSTRACTO QUE TIENE EL CUAL ES Campos 
 * QUE DEBERA RETORNAR UN ARRAY CON LOS METADATOS DE LA TABLA EN EL SIGUIENTE FORMATO 
 * <code>
 * return [
 *      'columna1'=>[
 *                  'Type'=>'tipo de dato' // el tipo de dato debe ser igual que en la tabla de la base de datos 
 *                  'KEY'=>'tipo de indice ' // es opcional, pero si la columna es una columna
 *                                           // primaria debe ser obligatorio y debe contener el valor PRI
 *                  'Default' => ''  // opcional, EL VALOR POR DEFECTO
 *                  'Extra' => self::AutoIncrement //opcional, puede ser usado para indica que una columna es auto_increment 
 *                  ],
 *      'columna2'=>[...],
 *      'columna2'=>[...],
 *      .
 *      .
 *      .
 *      'columnaN'=>[...]
 * 
 * ];
 * <ocde>
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Modelo
 * @category DBtablaModel
 */
abstract class TablaModel extends \Cc\Mvc\Model
{

    /**
     * primary key
     */
    const PrimaryKey = 'PRI';

    /**
     * AutoIncrement
     */
    const AutoIncrement = 'auto_increment';

    /**
     * nombre de la tabla 
     * @var string
     */
    private $tabla = '';

    /**
     * columnas 
     * @var array 
     */
    private $columnas = [];

    /**
     *
     * @var claves foraneas 
     */
    private $ForeingKey = [];

    /**
     *
     * @var array 
     */
    private $Inserts = [];

    /**
     * 
     * @param string $tabla
     * @internal 
     */
    public function __construct($tabla)
    {
        $this->tabla = $tabla;
        parent::__construct();
    }

    /**
     * @ignore
     */
    public function Campos()
    {
        ;
    }

    /**
     *  retorna los metadatos de una tabla 
     * @return array
     * @internal 
     */
    public function getMetadata()
    {
        $colums = [];
        $i = 1;
        /* @var $obj ColumModel */
        foreach ($this->columnas as $name => $obj)
        {
            $colums[$name] = [
                'Type' => $obj->GetFullType(),
                'TypeName' => $obj->GetType(),
                'KEY' => $obj->PrimaryKey ? self::PrimaryKey : '',
                'Extra' => $obj->IsAutoIncrement() ? self::AutoIncrement : '',
                'Default' => $obj->GetDefault(),
                'Position' => $i++
            ];
        }
        return $colums;
    }

    /**
     * crea el sql para crear la tabla 
     * @param \Cc\iDataBase $db manejador de bases de datos
     * @return string
     * @internal 
     */
    public function CreateSQL(\Cc\iDataBase $db)
    {
        $primary = [];
        $columnas = [];
        $index = [];
        $indexParams = [];
        $unique = [];
        /* @var $obj ColumModel */
        foreach ($this->columnas as $name => $obj)
        {
            $columnas[$name] = [
                'type' => $obj->GetType(),
                'ParamType' => $obj->GetParamType(),
                'atributo' => $obj->GetAtributos(),
                'Defalut' => $obj->GetDefault(),
                'NULL' => $obj->GetNull(),
                'Extra' => $obj->GetExtra(),
            ];

            if ($obj->PrimaryKey)
            {
                $primary[] = $name;
            }
            if ($obj->index)
            {
                $index[] = $name;
                if (!$obj->index === true)
                    $indexParams[$name] = $obj->index;
            }
            if ($obj->unique)
            {
                $unique[] = $name;
            }
        }
//        $sql = substr($sql, 0, -1);

        if ($index)
        {
            foreach ($index as $i => $v)
            {
                if (isset($indexParams[$v]))
                {
                    $index[$i].='(' . $indexParams[$v] . ')';
                }
            }
        }
        $ForeingKey = [];
        if ($this->ForeingKey)
        {


            /* @var $key ForeingKey */
            foreach ($this->ForeingKey as $i => $key)
            {
                $ForeingKey[$i] = $key->GetData();
            }
        }
        $driver = $db->GetDriver();
        $driver->SetTabla($this->tabla);
        return $driver->CreateTable($columnas, $index, $unique, $primary, $ForeingKey);
    }

    /**
     * retorna las claves foraneas de la tabla
     * @return array
     * @internal 
     */
    public function GetReferences()
    {
        $forge = [];
        /* @var $key ForeingKey */
        foreach ($this->ForeingKey as $i => $key)
        {
            $key = $key->GetData();
            $forge[] = $key['reference'];
        }
        return $forge;
    }

    /**
     * crea una columna 
     * @param string $name
     * @return ColumModel
     */
    protected function Colum($name)
    {
        return $this->columnas[$name] = new ColumModel($name);
    }

    /**
     * CREA CLAVES FORANEAS 
     * @param string $colum nombre de la columna 
     * @return ForeingKey
     */
    protected function ForeingKey($colum)
    {
        return $this->ForeingKey[$colum] = new ForeingKey($colum);
    }

    /**
     * Este metodo se ejecutara cuando se inicialize la tabla 
     */
    public abstract function Initialized();

    /**
     * Este metodo se ejecutara cuando se nesesite obtener los metadatos de la tabla
     */
    public abstract function Create();

    /**
     *  INSERTA UNA FILA EN LA TABLA
     * <code>
     * <?php
     * $this->Insert("hola1","ejemplo");//insertando
     * </code>
     * <code>
     * <?php
     * $this->Insert(["hola1","ejemplo"]);//insertando
     * </code>
     *  <code>
     * <?php
     * $this->Insert(["campo1"=>"hola1","campo2"=>"ejemplo"]);//insertando
     * </code>
     *  @param ...$param LA FILA QUE SE INSERTARA SI ES UN ARRAY CON INDICES NUMERICOS INSERTAR POR ORDEN NUMERICO
     *  SI ES UN ARRAY DE INDICE ALFANUMERICO SE INSERTAR CON EL ORDEN QUE TENGA LA TABLA EN LA BASE DE DATOS
     *  TAMBIEN PUEDE SER UN OBJETO DBRow
     *  
     * @see DBtabla::Insert()
     * 
     */
    protected function Insert(...$params)
    {
        $this->Inserts[] = $params;
    }

    /**
     * Retorna los datos para inicializar la tabla
     * @return array
     * @internal 
     */
    public function GetInserts()
    {
        return $this->Inserts;
    }

}
