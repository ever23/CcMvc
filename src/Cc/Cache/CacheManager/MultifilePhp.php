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

namespace Cc\Cache;

use Cc\Config;
use Cc\AbstracCache;

/**
 * Clase manejadora de Cache que lo almacena en multiples archivos .inc en formato php
 * @author Enyerber Franco
 * @package Cc
 * @subpackage Cache
 */
class MultifilePhp extends AbstracCache
{

    public $VersionCache = '1.0.0.5';

    /**
     * configuracion de la aplicacion 
     * @var \Cc\Config 
     */
    protected $Config;

    /**
     *
     * @var string 
     */
    protected $fileRead;

    /**
     *
     * @var string 
     */
    protected $expiretime = NULL;

    /**
     *
     * @var \SplFileInfo 
     */
    protected $FileCache = NULL;

    /**
     * 
     * @param Config $conf
     */
    public function __construct(Config $conf)
    {
        $this->Config = $conf;
        $this->expiretime = $conf['Cache']['ExpireTime'];
        if (is_dir($conf['App']['app']) && !is_dir($conf['App']['Cache']))
        {
            mkdir($conf['App']['Cache']);
        }
        $this->changed = false;
        $this->FileCache = $conf['App']['Cache'] . $conf['Cache']['dir'] . '/';
        if (!is_dir($this->FileCache))
        {
            mkdir($this->FileCache);
        }
    }

    /**
     * retorna todo el contenido del cache
     * @return array
     */
    public function GetAllCache()
    {
        return $this->CAHCHE;
    }

    /**
     * 
     * @param string $name
     * @param mixes $value
     * @see ICache::Set()
     */
    public function Set($name, $value, $expire = NULL)
    {
        if (is_null($expire))
        {
            $expire = $this->expiretime;
        }
        $time = NULL;
        if ($expire)
        {
            $time = new \DateTime;
            $time->add(date_interval_create_from_date_string($expire));
            $expire = $time->getTimestamp();
            // echo $time->format('Y/m/d H:i:s'),' ',$time->,' ',time();
        }
        $this->CAHCHE[$name] = [$this->serialize($value), $expire];
        $this->WriteFile($name, $this->CAHCHE[$name], $time);
    }

    /**
     * 
     * @param string $name
     * @return mixes
     * @see ICache::Get()
     */
    public function Get($name)
    {
        if (isset($this->CAHCHE[$name]))
        {
            return parent::Get($name);
        } elseif ($this->FileExist($name))
        {
            $this->CAHCHE[$name] = $this->ReadFile($name);

            $expire = new \DateTime('now');
            if (!is_null($this->CAHCHE[$name][1]) && $expire->getTimestamp() >= $this->CAHCHE[$name][1])
            {
                $this->Delete($name);
            }
            //echo $name, date('Y/m/d H:i:s',$this->CAHCHE[$name][1]),' '. $expire->format('Y/m/d H:i:s');exit;
            return $this->CAHCHE[$name][0];
        }
    }

    /**
     * 
     * @param string $name
     * @return bool
     * @see ICache::IsSave()
     */
    public function IsSave($name)
    {
        if (isset($this->CAHCHE[$name]))
        {
            $expire = new \DateTime;
            return (is_null($this->CAHCHE[$name][1]) || $expire->getTimestamp() < $this->CAHCHE[$name][1]);
        } elseif ($this->FileExist($name))
        {
            $expire = new \DateTime;
            $this->Get($name);
            return (is_null($this->CAHCHE[$name][1]) || $expire->getTimestamp() < $this->CAHCHE[$name][1]);
        } else
        {
            return false;
        }
    }

    /**
     * 
     * @param string $name
     * @see ICache::Delete()
     */
    public function Delete($name)
    {

        unset($this->CAHCHE[$name]);
        if ($this->FileExist($name))
        {
            @unlink($this->FileName($name));
        }
    }

    /**
     * cobierte los valores en tipos simples de datos
     * @param mixes $value
     * @return array
     */
    protected function serialize($value)
    {
        if (is_object($value))
        {
            if ($value instanceof \Serializable)
            {
                return $this->serialize($value->serialize());
            } elseif (method_exists($value, '__sleep'))
            {
                return $this->serialize($value->__sleep());
            } else
            {
                return (array) $value;
            }
        } elseif (is_array($value))
        {
            foreach ($value as $i => $v)
            {
                $value[$i] = $this->serialize($v);
            }
            return $value;
        } else
        {
            return $value;
        }
    }

    /**
     * almacena el cache en un archivo
     */
    public function Save()
    {

        /* if ($this->changed)
          {
          //  echo "changed";

          $this->CAHCHE['VersionCache'] = $this->VersionCache;
          $this->CAHCHE['ModifyTime'] = date('Y-m-d H:i:s');

          $cache = $this->CAHCHE;
          $save = '<?php return ' . var_export($cache, true) . ';?>';
          @file_put_contents($this->FileCache, $save);
          } */
    }

    /**
     * limpia el cache
     */
    public function Destruct()
    {
        $this->CAHCHE = [];
        $directory = $this->FileCache;
        $dir = dir($this->FileCache);
        while ($file = $dir->read())
        {
            if ($file != '.' && $file != '..')
            {
                if (is_file($directory . $file))
                {
                    @unlink($directory . $file);
                }
            }
        }
    }

    /**
     * Escribe el archivo cache 
     * @param string $name nombre del cache
     * @param array $value valor del cache
     * @param \DateTime $ex expiracion 
     */
    protected function WriteFile($name, $value, \DateTime $ex = NULL)
    {

        $save = "<?php\n /**"
                . "\n * Cache "
                . "\n * name: " . $name;
        if (!is_null($ex))
            $save .= "\n * Expire: " . $ex->format('Y-m-d H:i:s');
        $save .= "\n*/\n"
                . "return "
                . var_export($value, true)
                . ";?>";
        // Mvc::App()->Log("Guardando en cache: ");
        @file_put_contents($this->FileName($name), $save);
    }

    /**
     * obtiene el valor en cache 
     * @param string $name
     * @return array
     */
    protected function ReadFile($name)
    {

        return include ($this->FileName($name));
    }

    /**
     * indica si un archivo de cache existe 
     * @param string $name
     * @return bool
     */
    protected function FileExist($name)
    {

        return file_exists($this->FileName($name));
    }

    /**
     * obtiene el nombre real del archivo en cache para el indice 
     * @param string $name
     * @return string
     */
    private function FileName($name)
    {
        $md5Name = md5($name);
        $n = $this->Config['Cache']['File'] . '_';
        return $this->FileCache . $n . $md5Name . '.inc';
    }

//put your code here
}
