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
 * Description of ForeingKey
 *
 * @author enyerber
 */
class ForeingKey
{

    protected $colum = '';
    protected $references = '';
    protected $referencesId = [];
    protected $OnDelete = 'NO ACTION';
    protected $OnUpdate = 'NO ACTION';
    protected $match = '';

    public function __construct($colum)
    {
        $this->colum = $colum;
    }

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

    public function &OnDelete($valor)
    {
        $this->OnDelete = $valor;
        return $this;
    }

    public function &OnUpdate($valor)
    {
        $this->OnUpdate = $valor;
        return $this;
    }

    public function &Match($math)
    {
        $this->match = $math;
        return $this;
    }

    public function Sql()
    {
        $sql = 'FOREIGN KEY (' . $this->colum . ') REFERENCES ' . $this->references . ' (' . implode(',', $this->referencesId) . ')';
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

}
