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

/**
 * Un objeto instanciado de esta clase representa una clave foranea de una tabla
 *
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Modelo
 * @category DBtablaModel
 */
class ForeingKey
{

    /**
     * Nombre de la columna 
     * @var string 
     */
    protected $colum = '';

    /**
     * Nombre de la tabla donde se encuentra la clave foranea 
     * @var string 
     */
    protected $references = '';

    /**
     * claves foraneas 
     * @var array 
     */
    protected $referencesId = [];

    /**
     * Opciones para ON DELETE
     * @var string 
     */
    protected $OnDelete = 'NO ACTION';

    /**
     * Opciones para ON UPDATE
     * @var string 
     */
    protected $OnUpdate = 'NO ACTION';

    /**
     * Opciones para MATCH
     * @var string 
     */
    protected $match = '';

    /**
     * 
     * @param string $colum nombre de la columna 
     * @internal 
     */
    public function __construct($colum)
    {
        $this->colum = $colum;
    }

    /**
     * Establece la clave foranea
     * @param string $tabla tabla donde esta la clave foranea 
     * @param array $campos campos de la tabla que se usaran para asociar
     * @return \Cc\Mvc\DBtablaModel\ForeingKey
     */
    public function &References($tabla, $campos = NULL)
    {
        $this->references = $tabla;
        if (is_null($campos))
        {
            $campos = [$this->colum];
        }
        if (!is_array($campos))
        {
            $campos = [$campos];
        }
        $this->referencesId = $campos;
        return $this;
    }

    /**
     * Establece la sentencia ON DELETE 
     * 
     * @param string $valor  pueden ser RESTRICT, CASCADE, SET NULL, SET NULL o  SET DEFAULT
     * @return \Cc\Mvc\DBtablaModel\ForeingKey
     */
    public function &OnDelete($valor)
    {
        $this->OnDelete = $valor;
        return $this;
    }

    /**
     * Establece la sentencia ON UPDATE 
     * 
     * @param STRING $valor pueden ser RESTRICT, CASCADE, SET NULL, SET NULL o  SET DEFAULT
     * @return \Cc\Mvc\DBtablaModel\ForeingKey
     */
    public function &OnUpdate($valor)
    {
        $this->OnUpdate = $valor;
        return $this;
    }

    /**
     * Estable la seletcia match
     * @param type $math puede ser FULL, PARTIAL o SIMPLE
     * @return \Cc\Mvc\DBtablaModel\ForeingKey
     */
    public function &Match($math)
    {
        $this->match = $math;
        return $this;
    }

    /**
     * crea el sql de la clave foranea 
     * @return string
     * @internal 
     */
    public function Sql()
    {
        $sql = 'FOREIGN KEY (' . $this->colum . ') REFERENCES ' . $this->references . ' (' . implode(',', $this->referencesId) . ')';
        if ($this->match)
        {
            $sql.=' MATCH ' . $this->match . ' ';
        }
        if ($this->OnDelete)
        {
            $sql.=' ON DELETE ' . $this->OnDelete . ' ';
        }
        if ($this->OnUpdate)
        {
            $sql.=' ON UPDATE ' . $this->OnUpdate . ' ';
        }
        return $sql;
    }

    public function GetData()
    {
        return [
            'reference' => $this->references,
            'keys' => $this->referencesId,
            'MATCH' => $this->match,
            'ONDELETE' => $this->OnDelete,
            'ONUPDATE' => $this->OnUpdate
        ];
    }

}
