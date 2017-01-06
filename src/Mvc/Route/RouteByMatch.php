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

/**
 * Compila el enrutamiento manual 
 *
 * @author Enyerber Franco 
 * @package CcMvc
 * @subpackage Router
 */
class RouteByMatch
{

    /**
     *
     * @var array 
     */
    protected $routes;

    /**
     * path de peticion
     * @var string 
     */
    protected $path;

    /**
     * path dividido 
     * @var array 
     */
    protected $PartsPath = [];

    /**
     * numero de partes del path
     * @var int 
     */
    protected $NpartsPath = 0;

    /**
     * parametros
     * @var array 
     */
    protected $params = [];

    /**
     * parametros a remplazar en el path
     * @var array 
     */
    protected $replace = [];

    /**
     * indica si es un callable
     * @var bool 
     */
    protected $isCalable = false;

    /**
     *
     * @var string 
     */
    protected $origRegex = '';

    /**
     *
     * @var string 
     */
    protected $extencion;

    /**
     * 
     * @param string $path path de la peticion
     * @param array $routes
     */
    public function __construct($path, $routes)
    {
        $this->routes = $routes;
        $this->path = $path;
    }

    /**
     * Retorna los parametros que surgen de la compilacion del path
     * @return array
     */
    public function GetParams()
    {
        return $this->params;
    }

    /**
     * 
     * @return string
     */
    public function GetOrigRegex()
    {
        return $this->origRegex;
    }

    /**
     * Verifica si el controlador es un calback
     * @return bool
     */
    public function IsCalableRoute()
    {
        return $this->isCalable;
    }

    private function DividePath($path)
    {
        $ParstPath = preg_split('/\//', $path);
        /* $popParnts = array_pop($ParstPath);
          foreach (explode('.', $popParnts) as $p)
          {
          $ParstPath[] = $p;
          } */
        return $ParstPath;
    }

    /**
     * compila el enrutamiento
     * @return boolean
     */
    public function compile()
    {
        $this->PartsPath = $this->DividePath($this->path);

        $this->NpartsPath = count($this->PartsPath);
        if (count($ex = explode('.', $this->PartsPath[$this->NpartsPath - 1])) > 1)
        {
            $this->extencion = $ex[1];
            $this->PartsPath[$this->NpartsPath - 1] = $ex[0];
        }
        $this->params = [];
        $this->replace = [];
        $this->isCalable = false;
        $this->origRegex = '';
        $v = false;
        foreach ($this->routes as $path => $contr)
        {
            list($controller, $repl, $mathvar) = $contr;

            $Rpath = substr($path, 1);
            $pathRegex = $this->DividePath($Rpath);
            $extRegex = NULL;
            $CountpathRegex = count($pathRegex);
            if (count($ex = explode('.', $pathRegex[$CountpathRegex - 1])) > 1)
            {
                $extRegex = $ex[1];
                $pathRegex[$CountpathRegex - 1] = $ex[0];
            }
            $verifi = false;
            $param = [];
            $this->replace = [];
            $this->params = [];
            if (strcasecmp($Rpath, $this->path) === 0)
            {

                return $this->ReturnedController($controller, $path);
            } elseif (count($this->PartsPath) != count($pathRegex))
            {
                continue;
            } elseif (( (is_null($this->extencion) && is_null($extRegex)) || (!is_null($this->extencion) && !is_null($extRegex))) && $this->CompileRegex($pathRegex, $controller, $mathvar))
            {
                // var_dump($controller, $path);

                if (!is_null($this->extencion) && !is_null($extRegex))
                {
                    if (preg_match('/\{.*\}$/U', $extRegex))
                    {
                        $this->replace['/' . preg_quote($extRegex, '/') . '/i'] = $this->extencion;
                    } elseif (strcasecmp($extRegex, $this->extencion) !== 0)
                    {
                        continue;
                    }
                }
                return $this->ReturnedController($controller, $path);
            }
        }
        return false;
    }

    protected function ReturnedController($controller, $path)
    {
        $this->origRegex = $path;

        if (is_callable($controller))
        {
            $this->isCalable = true;
            return $controller;
        } else
        {
            if (is_numeric($controller))
            {
                if (in_array($controller, [404, 403]))
                {
                    Mvc::App()->LoadError($controller, " Via Enrutamiento manual");
                    exit;
                }
            }

            if (preg_match('/\.\{.*\}$/U', $controller))
            {

                $ext = (new \SplFileInfo($this->path))->getExtension();
                $controller = preg_replace('/\.\{.*\}$/U', '.' . $ext, $controller);
            }

            foreach ($this->replace as $r => $p2)
            {

                $controller = preg_replace($r, $p2, $controller);
                // var_dump($p2);
            }

            //  var_dump($controller);
            // exit;
            return $controller;
        }
        return false;
    }

    /**
     * compila las expreciones
     * @param array $pathRegex
     * @param string $controller
     * @param array $mathvar
     * @return boolean
     */
    protected function CompileRegex($pathRegex, $controller, $mathvar)
    {
        foreach ($this->PartsPath as $i => $p)
        {
            if (isset($pathRegex[$i]))
            {

                if (strcasecmp($p, $pathRegex[$i]) === 0)
                {

                    continue;
                } elseif (preg_match('/(\{.*\})/U', $pathRegex[$i]))
                {

                    if ($this->EvalueRouteVars($pathRegex[$i], $p, $controller, $mathvar))
                    {

                        continue;
                    } else
                    {
                        return false;
                    }
                } else
                {
                    return false;
                }
            } else
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 
     * @param string $PathT
     * @param string $pathP
     * @param string $c
     * @param array $mathvar
     * @param array $match
     * @return boolean
     */
    private function EvalueRouteVars($PathT, $pathP, $c, $mathvar, $match = ['\{', '\}'])
    {
        $split = preg_split('/(' . $match[0] . '.*' . $match[1] . ')/Ui', $PathT, PREG_SPLIT_DELIM_CAPTURE, -1);
        $explo = '';
        foreach ($split as $j => $sp)
        {
            if ($j % 2 != 0)
            {
                $explo = preg_quote($sp[0], '/') . '|';
            }
        }
        if ($explo == '')
        {
            $Pexplo = [$pathP];
        } else
        {
            $Pexplo = preg_split('/' . substr($explo, 0, -1) . '/i', $pathP);
            $PExpAth = preg_split('/' . substr($explo, 0, -1) . '/i', $PathT);
            if (count($Pexplo) != count($PExpAth))
            {
                return false;
            }
        }
        $z = 0;

        foreach ($split as $j => $sp)
        {
            if ($j % 2 == 0)
            {

                $name = preg_replace('/' . $match[0] . '|' . $match[1] . '/i', '', $sp[0]);

                if ((isset($mathvar[$name]) && !preg_match('/' . $mathvar[$name] . '/i', $Pexplo[$z])) || trim($Pexplo[$z]) == '')
                {
                    return false;
                }

                $this->params[$name] = $Pexplo[$z];
                if (is_string($c) && preg_match('/(' . $match[0] . $name . $match[1] . ')/i', $c))
                {
                    $m = '/' . preg_quote($sp[0], '/') . '/i';
                    $this->replace[$m] = $Pexplo[$z];
                }
                $z++;
            }
        }
        return true;
    }

}
