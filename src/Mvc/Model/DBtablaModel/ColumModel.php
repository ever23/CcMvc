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
 * Una instancia de esta clase representa un campo de base de datos 
 * se usa para crear capos de una tabla
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

    /**
     * nombre de la columna 
     * @var string 
     */
    protected $name;

    /**
     * tipo de dato
     * @var string 
     */
    protected $type;

    /**
     * parametros de tipo de dato
     * @var array 
     */
    protected $typeParams = NULL;

    /**
     * extra
     * @var string 
     */
    protected $extra = '';

    /**
     * indicca si el campo es Not NULL
     * @var string 
     */
    protected $NotNull = 'NOT NULL';

    /**
     * valor por defecto
     * @var string 
     */
    protected $DefaulValue = '';

    /**
     * indice
     * @var  bool
     *  @internal 
     */
    public $index = false;

    /**
     * indica si el campo es una clave primaria
     * @var bool 
     *  @internal 
     */
    public $PrimaryKey = false;

    /**
     * indica si el campo es unico
     * @var bool 
     *  @internal 
     */
    public $unique = false;

    /**
     * attributes
     * @var string 
     */
    protected $attribute = '';

    /**
     * indica si el campo es auroincrement 
     * @var bool 
     */
    protected $auroincrement = false;

    /**
     * 
     * @param string $name nombre del campo
     *  @internal 
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * establece el campo como unico
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &Unique()
    {
        $this->unique = true;
        return $this;
    }

    /**
     * estable el campo como index
     * @param array $params parametros del indes
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &index($params = true)
    {
        $this->index = $params;
        return $this;
    }

    /**
     * establece el campo como clave primaria 
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &PrimaryKey()
    {
        $this->PrimaryKey = true;
        return $this;
    }

    /**
     * establece el tipo de dato del campo
     * @param string $name nombre del tipo de dato
     * @param array|string $params parametros del tipo de dato
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
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

    /**
     * 
     * @param string $extra
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &extra($extra)
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * establece el campo como autoincrement
     * @param array $params parametros del tipo de dato INT
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
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

    /**
     * agrega un atributo al campo
     * @param string $attrs
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &Atributo($attrs)
    {
        $this->attribute = $attrs;
        return $this;
    }

    /**
     * Estable el campo como no nullo
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &NotNull()
    {
        $this->NotNull = 'NOT NULL';
        return $this;
    }

    /**
     * Establece el valor por defecto del campo como NULL
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &DefaultNull()
    {
        $this->NotNull = 'NULL';
        return $this;
    }

    /**
     * Estable el valor por defecto del campo
     * @param type $value
     * @return \Cc\Mvc\DBtablaModel\ColumModel
     */
    public function &DefaultValue($value)
    {
        $this->DefaulValue = $value;
        return $this;
    }

    /**
     * funcion magica para los tipos de datos
     * @param string $name
     * @param array $arguments
     * @return this
     * @internal
     */
    public function __call($name, $arguments)
    {
        return $this->type(str_replace('_', ' ', $name), $arguments);
    }

    /**
     * genera el sql del campo
     * @return string
     * @internal 
     */
    public function Sql()
    {
        $sql = $this->name . ' ';
        $sql.=$this->GetFullType();
        $sql.=' ' . $this->attribute;
        $sql.=' ' . $this->NotNull;
        $sql.=' ' . $this->extra;
        return $sql;
    }

    public function GetNull()
    {
        return $this->NotNull;
    }

    public function GetParamType()
    {
        return $this->typeParams;
    }

    /**
     * retorna al tipo de dato en el formato sql
     * @return string
     * @internal 
     */
    public function GetFullType()
    {
        $sql = $this->type;
        if ($this->typeParams)
        {
            $sql.='(';
            $tipe = '';
            foreach ($this->typeParams as $var)
            {
                $tipe.=$this->SqlType($var) . ',';
            }
            $sql.=substr($tipe, 0, -1) . ')';
        }
        return $sql;
    }

    private function SqlType($var)
    {

        if (is_null($var) || (is_string($var) && strtolower($var) == 'null'))
        {
            return 'NULL';
        } elseif (is_numeric($var) || is_int($var) || is_float($var) || is_double($var))
        {
            return $var;
        } elseif (is_bool($var))
        {
            return $var ? 'true' : 'false';
        } else
        {

            return "'" . $var . "'";
        }
    }

    /**
     * retorna el tipo de dato
     * @return string
     * @internal 
     */
    public function GetType()
    {
        return $this->type;
    }

    /**
     * indica si el campo es AutoIncrement
     * @return bool
     * @internal 
     */
    public function IsAutoIncrement()
    {
        return $this->auroincrement;
    }

    /**
     * retorna el valor por defecto del campo
     * @return string
     * @internal 
     */
    public function GetDefault()
    {
        return $this->DefaulValue;
    }

    /**
     * 
     * @return string
     */
    public function GetExtra()
    {
        return $this->extra;
    }

}
