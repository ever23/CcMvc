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

use Cc\Mvc;
use Cc\Cache;

/**
 * Clase manejadora de respuesta css y javascript 
 *
 * @author Enyerber Franco 
 * @package CcMvc
 * @subpackage Response
 */
class ResponseJsCss extends Response
{

    /**
     * nombre del archivo para cache
     * @var string 
     */
    protected $filecache = '';

    /**
     * tipo de archivo puede ser css o js
     * @var string 
     */
    protected $type = '';

    /**
     * 
     * @param bool $compres indica si se activa la compresion 
     * @param bool $min inica si de minifiara 
     */
    public function __construct($compres = false, $min = false)
    {
        if (Mvc::App()->Content_type == 'text/css')
        {
            $this->type = 'css';
            Cache::AutoClearCacheFile(Mvc::App()->Config()->App['Cache'] . 'Min' . $this->type . DIRECTORY_SEPARATOR);
            parent::__construct($compres, $min, 'css');
        } elseif (Mvc::App()->Content_type == 'text/javascript' || Mvc::App()->Content_type == 'application/javascript')
        {
            $this->type = 'js';
            Cache::AutoClearCacheFile(Mvc::App()->Config()->App['Cache'] . 'Min' . $this->type . DIRECTORY_SEPARATOR);
            parent::__construct($compres, $min, 'js');
        }
    }

    /**
     * procesa la respuesta 
     * @param string $conten
     * @return string
     */
    public function ProccessConten($conten)
    {

        if ($this->min && !Mvc::App()->IsDebung())
        {

            if (Mvc::App()->Router->InfoFile instanceof \SplFileInfo)
            {
                $name = Mvc::App()->Router->InfoFile->getBasename('.' . $this->type);

                if (substr($name, -4, 4) != '.min')
                {
                    try
                    {
                        Mvc::App()->Buffer->SetAutoMin(false);
                        return $this->CacheMin(Mvc::App()->Router->InfoFile, $conten);
                    } catch (Exception $ex)
                    {
                        return $conten;
                    }
                }
            } else
            {
                Mvc::App()->Buffer->SetAutoMin(true);
                Mvc::App()->Buffer->SetTypeMin($this->typeMin);
            }
        }

        return $conten;
    }

    /**
     * crea y almacena el cache del minifiado
     * @param \SplFileInfo $file
     * @param string $conten
     * @return string
     */
    public function CacheMin(\SplFileInfo $file, $conten)
    {
        $cache = Mvc::App()->Config()->App['Cache'] . 'Min' . $this->type . DIRECTORY_SEPARATOR;
        $f = dirname(Mvc::App()->GetExecutedFile()) . DIRECTORY_SEPARATOR;
        if (!is_dir($cache))
            mkdir($cache);
        $name = preg_replace("/^(" . preg_quote($f, '/') . ")/i", "", $file->__toString());

        $name = str_replace(DIRECTORY_SEPARATOR, '.', $name);


        $this->fileCache = new \SplFileInfo($cache . $name);
        $cache = [];
//return Mvc::App()->GetExecutedFile();
        $cache['type'] = 'file';
        $cache['Controller'] = $file->__toString();
        $cache['RealFile'] = $this->fileCache->__toString();
        $min = new MinScript();
        $min->file = $file;
        $conten2 = $min->Min($conten, $this->type);
        $f = fopen($this->fileCache, 'w');
        fwrite($f, $conten2);
        fclose($f);

        Cache::Set(Mvc::App()->GetNameStaticCacheRouter(), $cache);
        Router::HeadersReponseFiles($this->fileCache, Mvc::App()->Content_type, Mvc::App()->Config()->Router['CacheExpiresTime']);
        return "/*create cache " . $name . "*/\n" . $conten2;
    }

}
