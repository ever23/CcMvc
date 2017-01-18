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
 * Description of ColumModel
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Modelo
 * @category DBtablaModel
 * @method ColumModel CHAR() CHAR(int $num)  tipo sql CHAR
 * @method ColumModel VARCHAR() VARCHAR(int $num)  tipo sql VARCHAR
 * @method ColumModel TINYINT() TINYINT(int $num)  tipo sql TINYINT
 * @method ColumModel SMALLINT() SMALLINT(int $num)  tipo sql SMALLINT
 * @method ColumModel MEDIUMINT() MEDIUMINT(int $num)  tipo sql MEDIUMINT
 * @method ColumModel INT() INT(int $num = null)  tipo sql INT
 * @method ColumModel INTEGER() INTEGER(int $num = null)  tipo sql INTEGER
 * @method ColumModel BIGINT() BIGINT(int $num = null)  tipo sql BIGINT
 * @method ColumModel FLOAT() FLOAT(int $precision = null, int $decimales = NULL)  tipo sql FLOAT
 * @method ColumModel DOUBLE() DOUBLE(int $precision = null, int $decimales = NULL)  tipo sql DOUBLE
 * @method ColumModel REAL() REAL(int $precision = null, int $decimales = NULL)  tipo sql REAL
 * @method ColumModel DECIMAL() DECIMAL(int $precision = null, int $decimales = NULL)  tipo sql DECIMAL
 * @method ColumModel DATE() DATE()  tipo sql DATE
 * @method ColumModel DATETIME() DATETIME()  tipo sql DATETIME
 * @method ColumModel TIMESTAMP() TIMESTAMP(string $timestamp = NULL)  tipo sql TIMESTAMP
 * @method ColumModel TIME() TIME()  tipo sql TIME
 * @method ColumModel YEAR() YEAR(int $formato = 4)  tipo sql YEAR
 * @method ColumModel TINYBLOB() TINYBLOB()  tipo sql TINYBLOB
 * @method ColumModel TINYTEXT() TINYTEXT()  tipo sql TINYTEXT
 * @method ColumModel BLOB() BLOB()  tipo sql BLOB
 * @method ColumModel TEXT() TEXT()  tipo sql TEXT
 * @method ColumModel MEDIUMBLOB() MEDIUMBLOB()  tipo sql MEDIUMBLOB
 * @method ColumModel MEDIUMTEXT() MEDIUMTEXT()  tipo sql MEDIUMTEXT
 * @method ColumModel LONGBLOB() LONGBLOB()  tipo sql MEDIUMTEXT
 * @method ColumModel LONGTEXT() LONGTEXT()  tipo sql LONGTEXT
 * @method ColumModel ENUM() ENUM(...$conjunto)  tipo sql ENUM
 * @method ColumModel SET() SET(...$conjunto)  tipo sql SET
 * @method ColumModel JSON() JSON()  tipo sql JSON solo para posgres
 * @method ColumModel XML() XML()  tipo sql XML solo para posgres
 */
class ColumModel
{

    protected $name;
    protected $type;
    protected $typeParams = NULL;
    protected $extra = '';
    protected $NotNull = '';
    protected $DefaulValue = '';
    public $index = false;
    public $PrimaryKey = false;
    public $unique = false;
    protected $attribute = '';
    protected $auroincrement = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function &Unique()
    {
        $this->unique = true;
        return $this;
    }

    public function &index()
    {
        $this->index = true;
        return $this;
    }

    public function &PrimaryKey()
    {
        $this->PrimaryKey = true;
        return $this;
    }

    public function &type($name, $params = NULL)
    {
        $this->type = $name;
        if (!is_array($params))
        {
            $params = [$params];
        }
        $this->typeParams = $params;
        return $this;
    }

    public function &extra($extra)
    {
        $this->extra = $extra;
        return $this;
    }

    public function &autoincrement($params = [])
    {
        $this->extra = 'AUTO_INCREMENT';
        $this->auroincrement = true;
        if (!$this->type)
        {
            $this->type('INT', $params)->Atributo('UNSIGNED');
        }
        return $this;
    }

    public function &Atributo($attrs)
    {
        $this->attribute = $attrs;
        return $this;
    }

    public function &NotNull()
    {
        $this->NotNull = 'NOT NULL';
        return $this;
    }

    public function &DefaultNull()
    {
        $this->NotNull = 'NULL';
        return $this;
    }

    public function &DefaultValue($value)
    {
        $this->DefaulValue = $value;
        return $this;
    }

    public function __call($name, $arguments)
    {
        return $this->type(str_replace('_', ' ', $name), $arguments);
    }

    public function Sql()
    {
        $sql = $this->name . ' ';
        $sql.=$this->GetFullType();
        $sql.=' ' . $this->attribute;
        $sql.=' ' . $this->NotNull;
        $sql.=' ' . $this->extra;
        return $sql;
    }

    public function GetFullType()
    {
        $sql = $this->type;
        if ($this->typeParams)
        {
            $sql.='(' . implode(',', $this->typeParams) . ')';
        }
        return $sql;
    }

    public function GetType()
    {
        return $this->type;
    }

    public function IsAutoIncrement()
    {
        return $this->auroincrement;
    }

    public function GetDefault()
    {
        return $this->DefaulValue;
    }

}
