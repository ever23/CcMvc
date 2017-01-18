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

namespace Cc\Mvc\DBtablaModel;

/**
 * Description of TablaModel
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
    protected $tabla = '';

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
     * @param string $tabla
     * @internal 
     */
    public function __construct($tabla)
    {
        $this->tabla = $tabla;
        parent::__construct();
    }

    /**
     *  retorna los metadatos de una tabla 
     * @return array
     */
    public function getMetadata()
    {
        $colums = [];
        $i = 1;
        /* @var $obj ColumModel */
        foreach ($this->columnas as $name => $obj)
        {
            $this->columnas[$name] = [
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
     * retorna la tabla en sql create 
     * @return string
     */
    public function Sql()
    {

        $sql = 'CREATE TABLE ' . $this->tabla . ' (';
        $primary = [];
        $index = [];
        $unique = [];
        /* @var $obj ColumModel */
        foreach ($this->columnas as $name => $obj)
        {
            $sql.=$obj->Sql() . ',';
            if ($obj->PrimaryKey)
            {
                $primary[] = $name;
            }
            if ($obj->index)
            {
                $index[] = $name;
            }
            if ($obj->unique)
            {
                $unique[] = $name;
            }
        }
        $sql = substr($sql, 0, -1);
        if ($primary)
        {
            $sql.= ',PRIMARY KEY (' . implode(',', $primary) . ')';
        }
        if ($unique)
        {
            $sql.= ',UNIQUE (' . implode(',', $unique) . ')';
        }
        if ($index)
        {
            $sql.= ',INDEX (' . implode(',', $index) . ')';
        }
        if ($this->ForeingKey)
        {

            $sql.=',key(' . implode(',', array_keys($this->ForeingKey)) . '),';
            /* @var $key ForeingKey */
            foreach ($this->ForeingKey as $key)
            {
                $sql.=$key->Sql() . ',';
            }
        }
        $sql = substr($sql, 0, -1);
        $sql.=');';
        return $sql;
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

}
