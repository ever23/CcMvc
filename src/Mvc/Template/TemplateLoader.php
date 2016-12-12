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
 * Carga los tempaltes 
 * @author Enyerber Franco
 * @package CcMvc  
 * @subpackage Template
 */
class TemplateLoad
{

    /**
     * configuracion 
     * @var Config 
     */
    protected $Config;

    /**
     * 
     * @var array 
     */
    protected $evaluadores = [];

    /**
     * loader por defecto
     * @var array 
     */
    protected $DefaultLoader = [];

    /**
     * 
     * @param \Cc\Mvc\Config $c
     */
    public function __construct(Config $c)
    {
        $this->Config = $c;
        if (isset($c['TemplateLoaders']))
        {
            $this->evaluadores = $c['TemplateLoaders']['Loaders'];
            $this->DefaultLoader = $c['TemplateLoaders']['Default'];
        }
    }

    /**
     * cargara una plantilla 
     * @param object $context
     * @param string $file
     * @param array $agrs
     * @return bool
     * @throws TempleteLoaderException
     */
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

    /**
     * cargara una plantilla y retorna su contenido 
     * @param object $context
     * @param string $file
     * @param array $agrs
     * @return bool
     * @throws TempleteLoaderException
     */
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

    /**
     * carga una plantilla
     * @param object $context
     * @param \SplFileInfo $file
     * @param array $agrs
     * @return string
     */
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

    /**
     * carga un plantilla y retorna su contenido
     * @param Object $context
     * @param \SplFileInfo $file
     * @param array $agrs
     * @return string
     */
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

    /**
     * construlle el objeto cargador de plantillas
     * @param string $ext
     * @return \Cc\Mvc\class
     */
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

/**
 * @package CcMvc  
 * @subpackage Template
 */
class TempleteLoaderException extends Exception
{
    
}

/**
 * @package CcMvc  
 * @subpackage Template
 */
class TemplateException extends Exception
{
    
}

/**
 * interface a ser implementada en las clase cargadoras de plantillas
 * @package CcMvc  
 * @subpackage Template
 */
interface TemplateLoader
{

    /**
     * cargar y retorna el contenido de una plantilla
     * @param object $context contexto en el que sera cargado la plantilla
     * @param string $file nombre de la plantilla
     * @param array $agrs parametros de la plantilla
     */
    public function Load(&$context, $file, array $agrs);

    /**
     * carga y retorna  el contenido de una plantilla
     * @param object $context contexto en el que sera cargado la plantilla
     * @param string $file nombre de la plantilla
     * @param array $agrs parametros de la plantilla
     * @return string plantilla evaluada 
     */
    public function Fetch(&$context, $file, array $agrs);
}
