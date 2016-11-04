<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

/**
 * Description of FileEvaluator
 *
 * @author usuario
 */
class ViewLoader
{

    protected $Config;
    protected $evaluadores = [];

    public function __construct(Config $c)
    {
        $this->Config = $c;
        if (isset($c['ViewLoaders']))
            $this->evaluadores = $c['ViewLoaders'];
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
            $splfile = new \SplFileInfo($file . '.php');
            if (!$splfile->isFile())
                throw new Exception("El archivo " . $file . " no existe");
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
            $splfile = new \SplFileInfo($file . '.php');
            if (!$splfile->isFile())
                throw new ViewLoaderException("El archivo " . $file . " no existe");
        }
        return $this->LoadFetch($context, $splfile, $agrs);
    }

    protected function LoadFetch(&$context, \SplFileInfo $file, array $agrs)
    {
        $ext = $file->getExtension();

        if ($ext != 'php' && isset($this->evaluadores[$ext]))
        {

            $class = $this->evaluadores[$ext]['class'];
            $param = isset($this->evaluadores[$ext]['param']) && is_array($this->evaluadores[$ext]['param']) ? $this->evaluadores[$ext]['param'] : [];
            $eval = new $class(...$param);
            return $eval->Fetch($context, $file->__toString(), $agrs);
        } else
        {
            ob_start();
            $this->LoadPHP($context, $file, $agrs);
            $conten = ob_get_contents();
            ob_end_clean();
            return $conten;
        }
    }

    protected function Evaluate(&$context, \SplFileInfo $file, array $agrs)
    {
        $ext = $file->getExtension();

        if ($ext != 'php' && isset($this->evaluadores[$ext]))
        {
            $class = $this->evaluadores[$ext]['class'];
            $param = isset($this->evaluadores[$ext]['param']) && is_array($this->evaluadores[$ext]['param']) ? $this->evaluadores[$ext]['param'] : [];
            $eval = new $class(...$param);
            return $eval->Load($context, $file->__toString(), $agrs);
        } else
        {
            return $this->LoadPHP($context, $file, $agrs);
        }
    }

    protected function LoadPHP(&$context, \SplFileInfo $file, array $agrs)
    {
        if (($t = strpos($file, ':')) !== false)
        {
            $file = new \SplFileInfo(substr($file, $t + 1));
            if (!$file->isFile())
                throw new ViewLoaderException("El archivo " . $file . " no existe");
        }

        $function = \Closure::bind(function($__agrs, $__file)
                {
                    extract($__agrs);
                    include ($__file);
                }, $context, get_class($context));
        $function($agrs, $file);
    }

}

class ViewLoaderException extends Exception
{
    
}

interface ViewLoaderExt
{

    /**
     * 
     * @param object $context
     * @param string $file
     * @param file $agrs
     */
    public function Load(&$context, $file, array $agrs);

    public function Fetch(&$context, $file, array $agrs);
}
