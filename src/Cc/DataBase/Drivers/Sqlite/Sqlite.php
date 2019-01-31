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

/**
 *
 * DRIVER PARA SQLITER
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>
 * @package Cc
 * @subpackage DataBase  
 * @category Drivers
 * 
 */
class sqlite extends Drivers
{

    protected $keys_activ = false;
    protected $SqliteMaster = "select * from sqlite_master where  name=";
    protected $ForeingKey = [];

    public function __construct(\Cc\iDataBase &$db, $tabla = NULL)
    {
        if ($db instanceof \SQLite3)
        {
            $db->createFunction('hex2bin', function($data)
            {
                return hex2bin($data);
            }, 1);
        }
        if ($db instanceof \PDO && $db->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
        {
            $db->sqliteCreateFunction('hex2bin', function($data)
            {
                return hex2bin($data);
            });
        }
        parent::__construct($db, $tabla);
    }

    /**
     * 
     * @param string $tab
     * @return boolean
     * @throws \Exception
     * 
     */
    public function keys($tab)
    {
        if ($this->keys_activ && $tab == $this->Tabla())
            return TRUE;
        $this->keys_activ = TRUE;
        if ($r = $this->db->query($this->SqliteMaster . "'" . $tab . "'"))
        {

            $result = $this->fecth_result($r);

            if (!$result)
            {
                throw new Exception("LA TABLA " . $this->tabla . " NO EXISTE EN LA BASE DE DATOS");
            }

            if ($result['type'] == 'table')
            {
                $this->Ttipe = self::table;
            } else
            {
                return true;
            }
            // echo '<br>' . nl2br($result['sql']);
            $sql = substr(substr($result['sql'], strlen('create table ' . $tab) + 2), 0, -1);

            $sql = preg_replace('/create table.*\(/Ui', '', $result['sql']);

            $rows = preg_split("/(\(.*[,]+.*\))?[,]/", trim($sql));
            $i = 0;
            $order = 1;

            foreach ($rows as $v)
            {

                if (!preg_match('/FOREIGN KEY|PRIMARY KEY.*\(.*\)/iU', $v))
                {
                    $fil = explode(" ", trim($v));
                    if (strtolower($fil[0]) != 'primary' && (isset($fil[1]) && strtolower($fil[1]) != 'key'))
                    {
                        $Default = NULL;
                        $NULL = '';
                        $KEY = '';
                        if (preg_match("/(DEFAULT.*('.*'))/iU", $v, $def))
                        {
                            $Default = substr($def[2], 1, -1);
                        }
                        if (preg_match('/^NULL$/i', $v))
                        {
                            $Default = NULL;
                            $NULL = 'NULL';
                        }
                        if (preg_match('/NOT NULL/i', $v, $def))
                        {
                            $NULL = 'NOT NULL';
                        }
                        $fil[0] = preg_replace("/[\"|\'|`]/", '', $fil[0]);
                        if (preg_match('/PRIMARY KEY/i', $v))
                        {
                            $KEY = 'PRI';
                            if (!in_array($fil[0], $this->primarykey))
                                array_push($this->primarykey, $fil[0]);
                        }

                        //return $v;



                        $this->OrderColum[$order++] = $fil[0];
                        $this->colum+=[$fil[0] => [
                                'Type' => $fil[1],
                                'KEY' => $KEY,
                                'TypeName' => '',
                                'Extra' => NULL,
                                'Default' => $Default,
                                'Nullable' => $NULL == 'NOT NULL' ? false : true,
                                'Position' => $i
                        ]];
                        $i++;
                    }
                } else
                {
                    $v = trim($v);
                    if (preg_match('/PRIMARY KEY.*\(.*\).*FOREIGN KEY|PRIMARY KEY.*\(.*\)/iU', $v, $f))
                    {
                        $pk = preg_replace('/PRIMARY KEY.*\(|\).*FOREIGN KEY|`|\)/iU', '', $f[0]);
                        foreach (explode(',', $pk) as $p)
                        {
                            if (isset($this->colum[trim($p)]))
                            {
                                $this->colum[trim($p)]['KEY'] = 'PRI';
                                if (!in_array(trim($p), $this->primarykey))
                                    array_push($this->primarykey, trim($p));
                            }
                        }
                    }

                    if (preg_match('/FOREIGN KEY.*/s', $v, $F))
                    {

                        if (preg_match('/(FOREIGN KEY\ {1,10}\(.*\).*REFERENCES)/is ', $v, $F2))
                        {
                            $fk = trim(preg_replace('/(FOREIGN KEY)|(REFERENCES)|\(|\)|`/i', '', $F2[0]));
                            $this->contarint[$fk] = [];
                            if (preg_match('/(REFERENCES.*\(.*\))/is ', $v, $F3))
                            {
                                $this->contarint[$fk]['table'] = trim(preg_replace('/(REFERENCES|\(.*\).*|`)/is', '', $F3[0]));
                                $this->contarint[$fk]['colum'] = trim(preg_replace('/(REFERENCES.*' . $this->contarint[$fk]['table'] . '.*\(|\).*|`)/is', '', $F3[0]));
                            }
                            $this->contarint[$fk]['OnUpdate'] = 'NO ACTION';
                            if (preg_match('/ON DELETE.*(RESTRICT|CASCADE|SET NULL|SET NULL|SET DEFAULT|NO ACTION)/isU', $v, $F4))
                            {
                                $this->contarint[$fk]['OnDelete'] = $F4[1];
                            }
                            $this->contarint[$fk]['OnUpdate'] = 'NO ACTION';
                            if (preg_match('/ON UPDATE.*(RESTRICT|CASCADE|SET NULL|SET NULL|SET DEFAULT|NO ACTION)/isU', $v, $F4))
                            {
                                $this->contarint[$fk]['OnUpdate'] = $F4[1];
                            }
                            $this->contarint[$fk]['Match'] = '';
                            if (preg_match('/MATCH.*(FULL|PARTIAL|SIMPLE)/isU', $v, $F4))
                            {
                                $this->contarint[$fk]['Match'] = $F4[1];
                            }
                        }
                    }
                }
            }
            /* if (preg_match('/primary key \(.*\)/i', $sql, $m))
              {
              $primary = preg_replace('/(primary key \()|(\))/i', '', $m[0]);
              $keis = explode(',', $primary);
              foreach ($keis as $v)
              {
              if (isset($this->colum[trim($v)]))
              {
              $this->colum[trim($v)]['KEY'] = 'PRI';
              if (!in_array(trim($v), $this->primarykey))
              array_push($this->primarykey, trim($v));
              }
              }
              } */
            return true;
        } else
        {

            return false;
        }
    }

    public function ForeingKey()
    {
        return $this->contarint;
    }

    public function ListTablas()
    {
        $tablas = [];
        if ($r = $this->db->query("select * from sqlite_master"))
        {

            while ($result = $this->fecth_result($r))
            {
                if ($result['type'] == 'table')
                {
                    $tablas[] = $result['name'];
                }
            }
            return $tablas;
        }
    }

    public function CreateTable($colums, $index, $unique, $primary, $ForeingKey)
    {
        $sql = 'CREATE TABLE  `' . $this->tabla . "` (";
        $SqlColum = '';
        foreach ($colums as $i => $colum)
        {
            $SqlColum.=' `' . $i . "` "
                    . $this->GetFullType($colum['type'], $colum['ParamType']);

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

        $sql.=substr($SqlColum, 0, -2);
        if ($primary)
        {
            $sql.= "\n,PRIMARY KEY (`" . implode('`,`', $primary) . '`)';
        }
        if ($unique)
        {
            if (!$primary && !$unique)
            {
                $sql.="\n,";
            }
            $sql.= "UNIQUE (' " . implode(',', $unique) . ')';
        }
        if ($ForeingKey)
            foreach (array_keys($ForeingKey) as $ke)
            {
                $index[] = $ke;
            }

        if ($ForeingKey)
        {

            // $sql.=',key(' . implode(',', array_keys($this->ForeingKey)) . '),';
            /* @var $key ForeingKey */
            $Fkey = "";
            if (!$primary && !$index && !$unique)
            {
                $Fkey = "\n,";
            }

            foreach ($ForeingKey as $i => $key)
            {
                $Fkey .= ' FOREIGN KEY (' . $i . ') REFERENCES ' . $key['reference'] . ' (' . implode(',', $key['keys']) . ")\n";
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

    /**
     * enera los tipos de datos
     * @param string $type
     * @param array $TypeParams
     * @return string
     */
    protected function GetFullType($type, $TypeParams = [])
    {
        $sql = $type;
        switch (strtolower($type))
        {
            case 'enum':
                return 'varchar(100)';
        }

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

                return "hex2bin('" . bin2hex($bin) . "')";
            } elseif ($var instanceof \SplFileObject)
            {

                foreach ($var as $línea)
                {
                    $bin.=$línea;
                }
                return "hex2bin('" . bin2hex($bin) . "')";
            } elseif (is_resource($var) && get_resource_type($var) == 'stream')
            {

                while (!feof($var))
                {
                    $bin.=fgets($var);
                }
                return "hex2bin('" . bin2hex($bin) . "')";
            } elseif ((is_string($var) && strncmp($var, '0x', 2) === 0))
            {
                return "hex2bin('" . substr($var, 2) . "')";
            } else
            {
                return "hex2bin('" . bin2hex($var) . "')";
                ;
            }
        }
        $var = $this->FilterSqlI($var);
        if (is_null($var) || (is_string($var) && strtolower($var) == 'null'))
        {
            return 'NULL';
        } elseif (is_int($var) || is_float($var) || is_double($var) || is_numeric($var))
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

//put your code here
}
