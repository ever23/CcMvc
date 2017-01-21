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

namespace Cc\DB\Drivers;

use Cc\DB\Drivers;
use Cc\DB\Exception;

include_once dirname(__FILE__) . '/MetaData.php';

/**
 * 
 * DRIVER PARA POSGRESQL 
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>
 * @package Cc
 * @subpackage DataBase  
 * @category Drivers
 */
class pgsql extends Drivers
{

    /**
     * OBJETO MANEJADOR DE BASES DE DATOS
     * @var \Cc\PDO
     */
    protected $db;
    protected $information_schema = 'SELECT * FROM information_schema.columns WHERE table_name=';
    protected $keys_activ = false;

    public function __construct(\Cc\iDataBase $db, $tabla = NULL)
    {
        parent::__construct($db, $tabla);
        $this->_escape_char = '"';
    }

    public function keys($tab)
    {
        $this->Ttipe = self::table;

        if ($RESUT = $this->db->query($this->information_schema . "'" . $tab . "' and table_catalog='" . $this->db->dbName() . "'"))
        {
            if ($this->num_rows($RESUT) == 0)
            {
                throw new Exception("LA TABLA " . $this->tabla . " NO EXISTE EN LA BASE DE DATOS");
            }
            if ($RESUT2 = $this->db->query("SELECT * FROM information_schema.key_column_usage WHERE table_name='" . $tab . "'"))
            {
                while ($campo = $this->fecth_result($RESUT2))
                {
                    if ($campo['constraint_name'] == $tab . " pkey")
                    {
                        array_push($this->primarykey, $campo['column_name']);
                    }
                }
            }
            while ($campo = $this->fecth_result($RESUT))
            {
                $this->OrderColum[$campo['ordinal_position'] - 1] = $campo['column_name'];
                if ($campo['data_type'] == 'time with time zone')
                {
                    $campo['data_type'] = 'datetime';
                }
                $extra = '';
                if (preg_match('/serial/i', $campo['data_type']) && in_array($campo['column_name'], $this->primarykey))
                {

                    $this->autoincrement = $campo['column_name'];
                    $extra = 'AUTO_INCREMENT';
                }

                $this->colum+=[$campo['column_name'] => [
                        'Type' => $campo['data_type'],
                        'TypeName' => $campo['udt_name'],
                        'KEY' => '',
                        'Extra' => $extra,
                        'Default' => NULL,
                        'Nullable' => true,
                        'Position' => $campo['ordinal_position']
                ]];
            }
        }
    }

    public function ForeingKey()
    {
        $this->contarint = []; //post_id_user_fkey

        if ($RESUT = $this->db->query("SELECT * FROM information_schema.table_constraints where table_name='" . $this->tabla() . "' and constraint_catalog='" . $this->db->dbName() . "' and constraint_type='FOREIGN KEY'"))
        {


            while ($campo = $this->fecth_result($RESUT))
            {
                $RESUT2 = $this->db->query("SELECT * FROM information_schema.referential_constraints where     constraint_name='" . $campo['constraint_name'] . "' and constraint_catalog='" . $this->db->dbName() . "'");
                /// echo ("SELECT * FROM information_schema.referential_constraints where     constraint_name='" . $campo['constraint_name'] . "' and constraint_catalog='" . $this->db->dbName() . "'\n");
                if ($this->num_rows($RESUT2) != 0)
                {
                    $campo2 = $this->fecth_result($RESUT2);
                    $RESUT3 = $this->db->query("SELECT * FROM information_schema.key_column_usage where constraint_name='" . $campo2['unique_constraint_name'] . "' and constraint_catalog='" . $this->db->dbName() . "' ");

                    $campo3 = $this->fecth_result($RESUT3);
                    $RESUT4 = $this->db->query("SELECT * FROM information_schema.key_column_usage where constraint_name='" . $campo2['constraint_name'] . "' and constraint_catalog='" . $this->db->dbName() . "' ");
                    $campo4 = $this->fecth_result($RESUT4);
                    $this->contarint+=[$campo4['column_name'] => [
                            'table' => $campo3['table_name'],
                            'colum' => $campo3['column_name'],
                            'name' => $campo['constraint_name'],
                            'DBname' => $campo['constraint_catalog'],
                            'OnUpdate' => $campo2['update_rule'],
                            'OnDelete' => $campo2['delete_rule'],
                            'Match' => $campo2['match_option']
                    ]];
                }
// $this->contarint[$campo['ORDINAL_POSITION']] = $campo['COLUMN_NAME'];REFERENCED_TABLE_NAME
            }
            return $this->contarint;
        } else
        {
            
        }
        return [];
    }

    public function CreateKeys()
    {
        parent::CreateKeys();
    }

    public function ProtectIdentifiers($item, $prefix_single = FALSE, $protect_identifiers = NULL, $field_exists = TRUE)
    {
        $item2 = $item;
        $posItem = '';
        if (preg_match('/\[\d\]/', $item))
        {

            $item = preg_replace('/\[\d\]/', '', $item);
            $posItem = substr($item2, strlen($item));
        }
        return parent::ProtectIdentifiers($item, $prefix_single, $protect_identifiers, $field_exists) . $posItem;
    }

    /**
     * 
     * @param string $ColumName
     * @param mixes $str
     * @return mixes
     * 
     */
    public function SerializeType($ColumName, $str)
    {

        $ColumName = preg_replace('/\[\d\]/', '', $ColumName);

        if (!key_exists($ColumName, $this->colum))
            return $str;

        $class_name = "\\Cc\\DB\\MetaData\\pg" . $this->colum[$ColumName]['Type'];

        if (!is_null($str) && class_exists($class_name, false))
        {
            try
            {
                $obj = new $class_name($str, $this->colum[$ColumName]['TypeName']);
                return $obj->__toString();
            } catch (\Exception $ex)
            {
                return $str;
            }
        } else
        {
            return parent::SerializeType($ColumName, $str);
        }
    }

    /**
     * 
     * @param type $ColumName
     * @param type $str
     * @return type
     */
    public function UnserizaliseType($ColumName, $str)
    {
        $ColumName = preg_replace('/\[\d\]/', '', $ColumName);

        if (!key_exists($ColumName, $this->colum))
            return $str;
        $class_name = "\\Cc\\DB\\MetaData\\pg" . $this->colum[$ColumName]['Type'];
        if (!is_null($str) && class_exists($class_name, false))
        {
            $obj = new $class_name($str, $ColumName);
            return $obj;
        } else
        {
            return parent::UnserizaliseType($ColumName, $str);
        }
    }

    public function ListTablas()
    {//where Tables_in_" . $this->db . "='" . $tabla . "'
        $tablas = [];
        $result = $this->db->query("SELECT * FROM information_schema.tables where table_catalog='" . $this->db->dbName() . "'  and table_schema='public' ");
        while ($campo = $this->fecth_result($result))
        {
            if ($campo["table_type"] == 'BASE TABLE')
                $tablas[] = $campo["table_name"];
        }
        return $tablas;
    }

    public function CreateTable($colums, $index, $unique, $primary, $ForeingKey)
    {
        $sql = 'Create table ' . $this->_escape_char . $this->tabla . $this->_escape_char . "(";
        $SqlColum = '';
        foreach ($colums as $i => $colum)
        {
            $SqlColum.=' ' . $this->_escape_char . $i . $this->_escape_char;
            if ($colum['Extra'] == 'AUTO_INCREMENT')
            {
                $SqlColum.= " " . $this->GetFullType('serial', '');
            } else
            {
                $SqlColum.= " " . $this->GetFullType($colum['type'], $colum['ParamType']);
            }


            if ($colum['NULL'])
            {
                $SqlColum.= " " . $colum['NULL'];
            }

            if ($colum['Defalut'])
            {
                $SqlColum.= "  DEFAULT " . $this->FormatVarInsert($colum['Defalut']) . "";
            }
            if ($colum['Extra'] && $colum['Extra'] != 'AUTO_INCREMENT')
            {
                $SqlColum.= " " . $colum['Extra'];
            }
            $SqlColum.=",\n";
        }
        $sql.=substr($SqlColum, 0, -2) . "\n";
        if ($primary)
        {
            $sql.= ',PRIMARY KEY (' . $this->_escape_char . implode($this->_escape_char . ',' . $this->_escape_char, $primary) . $this->_escape_char . ")\n";
        }
        if ($unique)
        {
            $sql.= ',UNIQUE (' . $this->_escape_char . implode($this->_escape_char . ',' . $this->_escape_char, $unique) . $this->_escape_char . ")\n";
        }
        if ($ForeingKey)
            foreach (array_keys($ForeingKey) as $ke)
            {
                $index[] = $ke;
            }
        if ($index)
        {
//  $sql.= ',INDEX (' . $this->_escape_char . implode($this->_escape_char . ',' . $this->_escape_char, $index) . $this->_escape_char . ")\n";
        }
        if ($ForeingKey)
        {
// $sql.=',key(' . implode(',', array_keys($this->ForeingKey)) . '),';
            /* @var $key ForeingKey */
            $Fkey = ",\n";
            foreach ($ForeingKey as $i => $key)
            {
                $Fkey .= 'FOREIGN KEY (' . $this->_escape_char . $i . $this->_escape_char . ') REFERENCES ' . $this->_escape_char . $key['reference'] . $this->_escape_char . ' '
                        . '(' . $this->_escape_char . implode($this->_escape_char . ',' . $this->_escape_char, $key['keys']) . $this->_escape_char . ")\n";
                if ($key['MATCH'])
                {
                    $Fkey.=' MATCH ' . $key['MATCH'] . " \n";
                }
                if ($key['ONDELETE'])
                {
                    $Fkey.=' ON DELETE ' . $key['ONDELETE'] . " \n";
                }
                if ($key['ONUPDATE'])
                {
                    $Fkey.=' ON UPDATE ' . $key['ONDELETE'] . " \n";
                }
                $Fkey .= ",\n";
            }
            $sql.=substr($Fkey, 0, -2);
        }
        $sql .= ');';
        return $sql;
    }

    protected function GetFullType($type, $TypeParams = [])
    {
        switch (strtolower($type))
        {
            case 'int':
            case 'integer':
                return 'integer';
            case 'longblob':
            case 'smallblob':
            case 'blob':
                return 'bytea';
            case 'datetime':
                return 'time with time zone';
            case 'enum':
            case 'varchar':
                return 'text';
            default :
                return $type;
        }
        $sql = $type;
        if ($TypeParams)
        {
            $sql.='(';
            $tipe = '';
            if (is_string($TypeParams))
            {
                $tipe.=$TypeParams;
            } else
            {
                foreach ($TypeParams as $var)
                {
                    $tipe.=$this->FormatVarInsert($var) . ',';
                }
            }

            $sql.=substr($tipe, 0, -1) . ')';
        }
        return $sql;
    }

    protected $FormatBinary = ['longblob', 'blob', 'binary', 'bytea'];

    /**
     * formatea una valiable a sql de tipo inset
     * @param mixes $var
     * @return string
     */
    public function FormatVarInsert($var, $ColumName = '')
    {
        $type = 'TEXT';
        $var = $this->SerializeType($ColumName, $var);
        if (key_exists($ColumName, $this->colum))
            $type = $this->colum[$ColumName]['Type'];


        if (preg_match("/" . implode("|", $this->FormatBinary) . "/i", $type))
        {
            $bin = '';


            if ($var instanceof \SplFileInfo && $var->isReadable())
            {

                $file = $var->openFile('r');

                foreach ($file as $línea)
                {
                    $bin.=$línea;
                }
                return "'" . pg_escape_bytea($bin) . "'";
            } elseif ($var instanceof \SplFileObject)
            {


                foreach ($var as $línea)
                {
                    $bin.=$línea;
                }
                return "'" . pg_escape_bytea($bin) . "'";
            } elseif (is_resource($var) && get_resource_type($var) == 'stream')
            {

                //stream_copy_to_stream($var, $flujo);
                while (!feof($var))
                {
                    $bin.=fgets($var);
                }
                return "'" . pg_escape_bytea($bin) . "'";
            } elseif ((is_string($var) && strncmp($var, '0x', 2) === 0))
            {

                return "'" . pg_escape_bytea(hex2bin(substr($var, 2))) . "'";
            } else
            {
                return "'" . pg_escape_bytea($var) . "'";
            }
        }
        $var = $this->FilterSqlI($var);



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

}
