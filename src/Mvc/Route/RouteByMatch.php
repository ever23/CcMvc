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
 * Description of RouteByMatch
 *
 * @author usuario
 */
class RouteByMatch
{

    protected $routes;
    protected $path;
    protected $PartsPath = [];
    protected $NpartsPath = 0;
    protected $params = [];
    protected $replace = [];

    public function __construct($path, $routes)
    {
        $this->routes = $routes;
        $this->path = $path;
    }

    public function GetParams()
    {
        return $this->params;
    }

    public function compile()
    {
        $this->PartsPath = preg_split('/\/|\./', $this->path);
        $this->NpartsPath = count($this->PartsPath);
        $this->params = [];
        $this->replace = [];
        foreach ($this->routes as $path => $contr)
        {
            list($controller, $repl, $mathvar) = $contr;
            $pathRegex = preg_split('/\/|\./', substr($path, 1));
            $verifi = false;
            $param = [];
            $this->replace = [];
            if (count($this->PartsPath) != count($pathRegex))
            {
                continue;
            } else
            {

                $verifi = $this->CompileRegex($pathRegex, $controller);
                if ($verifi)
                {

                    Mvc::App()->DependenceInyector->SetDependenceForParamArray($param);
                    if (is_callable($c))
                    {
                        return $c;
                    } else
                    {
                        if (is_numeric($c))
                        {
                            if (in_array($c, [404, 403]))
                            {
                                Mvc::App()->LoadError($c, " Via Enrutamiento manual");
                                exit;
                            }
                        }

                        if (preg_match('/\.\{.*\}$/U', $c))
                        {

                            $ext = (new \SplFileInfo($this->path))->getExtension();
                            $c = preg_replace('/\.\{.*\}$/U', '.' . $ext, $c);
                        }
                        // var_dump($c);
                        foreach ($this->replace as $r => $p2)
                        {

                            $c = preg_replace($r, $p2, $c);
                            // var_dump($p2);
                        }

                        //  var_dump($c);
                        return $c;
                    }
                }
            }
        }
    }

    public function CompileRegex($pathRegex, $controller)
    {
        foreach ($this->PartsPath as $i => $p)
        {
            if (isset($pathRegex[$i]))
            {
                if ($p == $pathRegex[$i])
                {

                    continue;
                } elseif (preg_match('/(\{.*\})/U', $pathRegex[$i]))
                {
                    if ($this->EvalueRouteVars($pathRegex[$i], $p, $c, $mathvar))
                    {

                        continue;
                    } else
                    {
                        return false;
                    }
                } elseif (preg_match('/(\{.*\?\})/U', $pathRegex[$i]))
                {
                    if ($this->EvalueRouteVars($pathRegex[$i], $p, $controller, $mathvar, ['\{', '\?\}']))
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
    }

    private function EvalueRouteVars($PathT, $pathP, $c, $mathvar, $match = ['\{', '\}'])
    {
        $split = preg_split('/(' . $match[0] . '.*' . $match[1] . ')/U', $PathT, PREG_SPLIT_DELIM_CAPTURE, -1);
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
            $Pexplo = preg_split('/' . substr($explo, 0, -1) . '/', $pathP);
            $PExpAth = preg_split('/' . substr($explo, 0, -1) . '/', $PathT);
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

                $name = preg_replace('/' . $match[0] . '|' . $match[1] . '/', '', $sp[0]);
                //var_dump($mathvar[$name]);
                if (isset($mathvar[$name]) && !preg_match('/' . $mathvar[$name] . '/i', $Pexplo[$z]))
                {
                    return false;
                }
                if ($Pexplo[$z] == '' && $match[1] != '\?\}')
                {
                    continue;
                }
                $this->params[$name] = $Pexplo[$z];
                if (is_string($c) && preg_match('/(' . $match[0] . $name . $match[1] . ')/', $c))
                {
                    $m = '/' . preg_quote($sp[0], '/') . '/';
                    $this->replace[$m] = $Pexplo[$z];
                }
                $z++;
            }
        }
        return true;
    }

}
