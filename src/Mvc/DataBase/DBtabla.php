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

use Cc\Cache;
use Cc\iDataBase;
use Cc\Mvc;

/**
 * CLASE DBtabla                                                                
 * FACILITA LAS OPERACIONES BASICAS SOBRE LAS TABLAS USANDO LA CLASE DB_MySQli  
 * U OTRA CLASE QUE IMPLEMENTE LA INTERFACE iDataBase PARA CREA UN OBJETO       
 * DE ESTA CLASE                                                                
 *                                                                              
 *                                                       
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage DataBase
 * @see iDataBase
 * @see DB_MySQLi
 * @see DB_PDO  
 * @see \Cc\DBtabla ES EXTENDIDA DE ESTA CLASE 
 * @example ../examples/cine/protected/controllers/Cpelicula.php description                                                         
 */
class DBtabla extends \Cc\DBtabla implements \Serializable, ParseObjectSmartyTpl
{

    /**
     * active record
     * @var array 
     */
    protected static $activeRecord = [];

    /**
     * solo para smarty
     * @var bool 
     */
    private $each = true;

    /**
     * solo para smarty
     * @var bool 
     */
    private $eachend = true;

    /**
     * modelo de la tabla 
     * @var DBtablaModel\TablaModel
     */
    protected $model;

    /**
     *
     * @var Config 
     */
    protected $MvcConfig;

    /**
     * constructor
     * <code>
     * <?php
     * $db= new DB_PDO('sqlite:sqlite.db');
     * $mitabla=$db->Tab('mitabla');// creando un objeto DBtabla con DB_PDO
     * </code>
     * @param iDataBase &$db referencia a un objeto manejador de bases de datos que implemente iDatabase 
     * @param string $tabla nombre de la tabla que se asociara 
     * @throws Exception en caso de no existir la tabla
     */
    public function __construct(iDataBase &$db, $tabla, $useStmt = NULL)
    {
        $this->MvcConfig = Mvc::Config();
        $this->db = &$db;
        if (is_null($useStmt))
        {
            $this->useStmt = Mvc::App()->Config()->DB['UseStmt'];
        } else
        {
            $this->useStmt = Mvc::App()->Config()->DB['UseStmt'] && $useStmt;
        }

        $this->tabla = $tabla;
        $this->CacheName = get_class($db) . static::class . $tabla;

        if (Cache::IsSave($this->CacheName))
        {
            Mvc::App()->Log("Creando objeto para la tabla " . $tabla . " desde cache...");
            $Cache = Cache::Get($this->CacheName);
            static::$_CACHE[$this->CacheName] = $Cache;
            $this->unserialize($Cache);
        } elseif (($data = $this->MetadataFromModel($tabla)) != false)
        {
            static::$_CACHE[$this->CacheName] = $data;
            $this->unserialize($data);
        } else
        {
            Mvc::App()->Log("Creando objeto para la tabla " . $tabla);
            parent::__construct($db, $tabla, $this->useStmt);
            Cache::Set($this->CacheName, $this->serialize());
        }

        $this->Driver->FilterSqli = Mvc::App()->Config()->VarAceptSqlI === false;
    }

    /**
     * retorna el nombre del indice donde se almacena el cache de la tabla 
     * @return string
     */
    public function GetCacheName()
    {
        return $this->CacheName;
    }

    /**
     * 
     * @param array $serialized
     */
    public function unserialize($serialized)
    {
        if (!($this->db instanceof iDataBase))
            $this->db = Mvc::App()->DataBase();
        parent::unserialize($serialized);
    }

    /**
     * 
     * @param string $sql
     * @return \Cc\Mvc\DBtabla
     * @see \Cc\DBtabla::Query()
     */
    protected function &Query($sql)
    {
        $md5 = md5($sql);
        if (isset(self::$activeRecord[$this->Tabla()]) && isset(self::$activeRecord[$this->Tabla()][$md5]))
        {
            Mvc::App()->Log("Consulta de ActiveRecord ");
            $this->sql = $sql;
            $this->ResultAll = self::$activeRecord[$this->Tabla()][$md5]['ResultAll'];
            $this->rewind();
            // var_dump($this->ResultAll);
            $this->end_result = NULL;
            $this->num_rows = self::$activeRecord[$this->Tabla()][$md5]['num_rows'];
            $this->ActiveRow = NULL;
            return $this;
        } else
        {
            // Mvc::App()->Log("Consulta sql ");
            $r = parent::Query($sql);
            if (!isset(self::$activeRecord[$this->Tabla()]))
            {
                self::$activeRecord[$this->Tabla()] = [];
            }
            self::$activeRecord[$this->Tabla()][$md5] = [
                'ResultAll' => $this->ResultAll,
                'num_rows' => $this->num_rows
            ];
            return $r;
        }
    }

    /**
     * funcion de bloque para smarty
     * @param array $params
     * @param string $content
     * @param Smarty $smarty
     * @param bool $repeat
     * @return string
     * @internal solo para plantillas smarty
     */
    public function each($params, $content = NULL, &$smarty, &$repeat)
    {

        $repeat = true;
        if ($this->each)
        {
            if (!$this->eachend)
            {
                $this->rewind();
            }
            $fech = $this->current();
            $key = $this->key();
            $this->eachend = $this->next();

            $this->each = false;
            if (isset($params['row']))
            {
                $smarty->assign($params['row'], $fech);
            } else
            {
                $smarty->assign('row', $fech);
            }
            if (isset($params['key']))
            {
                $smarty->assign($params['key'], $key);
            } else
            {
                $smarty->assign('key', $key);
            }
        } else
        {
            $this->each = true;
            $repeat = $this->eachend;
            return $content;
            // $repeat = $this->next();
        }
    }

    /**
     * 
     * @return array
     * @see ParseObjectSmartyTpl::ParseSmaryTpl()
     * @internal Solo para plantillas smarty
     */
    public function ParseSmaryTpl()
    {
        return [
            'allowed' => ['__debugInfo', 'fetch', 'GetPrimary', 'Tabla', 'GetValuesEnum', '__call'],
            'format' => true,
            'block_methods' => ['each']
        ];
    }

    /**
     * cargar los metadatos de una clase de modelo
     * @param string $tabla
     * @return boolean
     * @throws Exception
     */
    protected function MetadataFromModel($tabla)
    {
        //return false;


        $class = '\\Cc\\Mvc\\DBtablaModel\\' . $tabla;
        if (Mvc::App()->AutoloaderLib->GetLoader('model')->autoloadCore($class))
        {
            $this->model = new $class();
            if (!($this->model instanceof DBtablaModel\TablaModel))
            {
                throw new Exception("LA CLASE " . $class . " DEBE SER EXTENDIDA DE " . DBtablaModel\TablaModel::class);
            }
            $cache = [];

            $cache['colum'] = $this->model->getMetadata();
            $cache['primary'] = [];
            $cache['autoinrement'] = '';
            foreach ($cache['colum'] as $i => $campo)
            {

                if (isset($campo['Key']) && $campo['Key'] == DBtablaModel\TablaModel::PrimaryKey && !in_array($i, $cache['primary']))
                {
                    array_push($cache['primary'], $i);
                }
                if (isset($campo['Extra']) && $campo['Extra'] === DBtablaModel\TablaModel::AutoIncrement)
                {
                    $cache['autoinrement'] = $i;
                }
            }

            $cache['Ttipe'] = self::table;
            $cache['tabla'] = $tabla;
            $cache['Ttipe'] = self::table;
            $cache['tabla'] = $tabla;
            $cache['typeDB'] = $this->GetTypeDB();
            $cache['OrderColum'] = array_keys($cache['colum']);
            return $cache;
        } else
        {
            return false;
        }
    }

}
