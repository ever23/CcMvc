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

/**
 * 
 *
 * @author Enyerber Franco
 * @package CcMvc  
 * @subpackage Template
 */
class TemplateLoad
{

    protected $Config;
    protected $evaluadores = [];
    protected $DefaultLoader = [];

    public function __construct(Config $c)
    {
        $this->Config = $c;
        if (isset($c['TemplateLoaders']))
        {
            $this->evaluadores = $c['TemplateLoaders']['Loaders'];
            $this->DefaultLoader = $c['TemplateLoaders']['Default'];
        }
    }

    public function Load(&$context, $file, array $agrs)
    {
        $splfile = new \SplFileInfo($file);
        if ((strpos($file, ':') !== false))
        {
            return $this->Evaluate($context, $splfile, $agrs);
        }
        if (!$splfile->isFile())
        {
            $splfile = new \SplFileInfo($file . '.' . $this->DefaultLoader['ext']);
            if (!$splfile->isFile())
                throw new TempleteLoaderException("El archivo " . $file . " no existe");
        }
        return $this->Evaluate($context, $splfile, $agrs);
    }

    public function Fetch(&$context, $file, array $agrs)
    {
        $splfile = new \SplFileInfo($file);
        if ((strpos($file, ':') !== false))
        {
            return $this->LoadFetch($context, $splfile, $agrs);
        }
        if (!$splfile->isFile())
        {
            $splfile = new \SplFileInfo($file . '.' . $this->DefaultLoader['ext']);
            if (!$splfile->isFile())
                throw new TempleteLoaderException("El archivo " . $file . " no existe");
        }
        return $this->LoadFetch($context, $splfile, $agrs);
    }

    protected function LoadFetch(&$context, \SplFileInfo $file, array $agrs)
    {
        $ext = $file->getExtension();

        if (isset($this->evaluadores[$ext]))
        {

            $eval = $this->FactoryLoaders($ext);
            return $eval->Fetch($context, $file->__toString(), $agrs);
        } else
        {
            $eval = $this->FactoryLoaders();
            return $this->Fetch($context, $file, $agrs);
        }
    }

    protected function Evaluate(&$context, \SplFileInfo $file, array $agrs)
    {
        $ext = $file->getExtension();

        if (isset($this->evaluadores[$ext]))
        {
            $eval = $this->FactoryLoaders($ext);
            return $eval->Load($context, $file->__toString(), $agrs);
        } else
        {
            $eval = $this->FactoryLoaders();
            return $eval->Load($context, $file->__toString(), $agrs);
        }
    }

    private function FactoryLoaders($ext = NULL)
    {
        if (!is_null($ext))
        {

            $class = $this->evaluadores[$ext]['class'];
            $param = isset($this->evaluadores[$ext]['param']) && is_array($this->evaluadores[$ext]['param']) ? $this->evaluadores[$ext]['param'] : [];
        } else
        {
            $class = $this->DefaultLoader['class'];
            $param = isset($this->DefaultLoader['param']) && is_array($this->DefaultLoader['param']) ? $this->DefaultLoader['param'] : [];
        }
        return new $class(...$param);
    }

}

class TempleteLoaderException extends Exception
{
    
}

/**
 * interface a ser implementada en las clase cargadoras de plantillas
 * @package CcMvc  
 * @subpackage view
 */
interface TemplateLoader
{

    /**
     * cargar y retorna el contenido de una plantilla
     * @param object $context
     * @param string $file
     * @param array $agrs
     */
    public function Load(&$context, $file, array $agrs);

    /**
     * carga e imprime en el buffer el contenido de una plantilla
     * @param object $context
     * @param string $file
     * @param array $agrs
     */
    public function Fetch(&$context, $file, array $agrs);
}
