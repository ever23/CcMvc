<?php

/*
 * Copyright (C) 2017 Enyerber Franco
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

namespace Cc\Mvc\Console;

use Cc\Mvc;
use Cc\Mvc\AbstracConsole;
use Cc\Mvc\RouteConsole;

/**
 * Muestra la informacion de ayuda de otros controladores 
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Console
 */
class Help extends AbstracConsole
{

    protected $infomethod;
    protected $params = [];
    protected $console;
    protected $infoClass = '';
    protected $listMethod = [];

    public function __construct()
    {
        
    }

    /**
     * Muestra la informacion de ayuda de otros controladores 
     * @param string $list nombre del controlador 
     */
    public function index(RouteConsole $r, $list = '')
    {
        $parameters = $r->GetParams();
        $c = trim(array_shift($parameters));

        if ($list != '')
        {
            $this->console = new Mvc\RouteConsole(["CcMvc", '']);
            $this->console->Route();
            if (!$this->ListMethods($list))
            {
                $this->Out("No se ha obtenido informacion...");
            }
            $this->Out("\n");
            $this->Out(trim($this->infoClass) . "\n\n");
            foreach ($this->listMethod as $m)
            {
                $this->console = new Mvc\RouteConsole(["CcMvc", "$list::$m"]);
                $this->console->Route();

                if ($m == 'index')
                {
                    $this->Out(" $list \n");
                } else
                {
                    $this->Out(" $list:$m \n");
                }



                if ($this->infoMethod($list, $m))
                {
                    $this->Out("    " . $this->infomethod . "\n");
                    foreach ($this->params as $name => $p)
                    {
                        $this->Out("       -" . $name . " $p \n");
                    }
                }
                $this->Out("\n\n");
            }
        } elseif ($c != '')
        {

            $this->console = new Mvc\RouteConsole(["CcMvc", $c]);
            $this->console->Route();
            echo $this->console->getMethod();
            if (!$this->infoMethod($this->console->getClass(), $this->console->getMethod()))
            {
                $this->Out("No se ha obtenido informacion...");
            }
            $this->Out("\n");
            $this->Out($this->infomethod . "\n\n");
            foreach ($this->params as $name => $p)
            {
                $this->Out(" -" . $name . " $p \n");
            }
        } else
        {
            $list = [];
            $clases = Mvc::App()->GetCoreClass();
            foreach ($clases as $class => $file)
            {
                if (substr($class, 0, strlen('Cc\\Mvc\\Console\\')) == 'Cc\\Mvc\\Console\\')
                {
                    $list[] = substr($class, strlen('Cc\\Mvc\\Console\\'));
                }
            }
            $this->console = new Mvc\RouteConsole(["CcMvc", '']);
            $this->console->Route();
            foreach ($list as $v)
            {

                if ($this->ListMethods($v))
                {
                    $this->Out(" $v \n " . trim($this->infoClass) . "\n\n");
                }
            }
        }
    }

    /**
     * Obtiene la informacion de un metodo de una clase controladora de consola 
     * @param string $class nombre de las clase 
     * @param string $method nombre del metodo
     * @return boolean
     */
    private function infoMethod($class, $method)
    {
        $namespace = 'Cc\\Mvc\\Console\\';

        if ($this->console->Autoload($namespace . $class))
        {

            $ReflectionClass = new \ReflectionClass($namespace . $class);
            if (!$ReflectionClass->isSubclassOf(AbstracConsole::class))
            {

                return false;
            }
            if (!$ReflectionClass->hasMethod($method))
            {

                return false;
            }
            $ReflectionMethod = $ReflectionClass->getMethod($method);



            $MethodComment = $this->sanitizeComent($ReflectionMethod->getDocComment());
            $arr = explode("\n", $MethodComment);

            $MethodInfo = '';
            foreach ($arr as $i => $line)
            {
                $l = $line;
                if (substr($l, 0, 2) == ' @')
                {
                    break;
                }


                $MethodInfo.= trim($l) . "\n";
            }
            $params = [];

            for ($i; $i < count($arr); $i++)
            {
                if (substr($arr[$i], 0, 7) == ' @param')
                {
                    if (preg_match('/\$(\w+)/', $arr[$i], $m, PREG_OFFSET_CAPTURE) && preg_match('/\int \$(\w+)|string \$(\w+)|float \$(\w+)|bool \$(\w+)/', $arr[$i]))
                    {
                        $params[$m[1][0]] = trim(substr($arr[$i], $m[1][1] + strlen($m[1][0])));
                    }
                }
            }
            $this->infomethod = $MethodInfo;
            $this->params = $params;
            return true;
            //echo $MethodComment;
        }
        return false;
    }

    /**
     * obtiene la lista de metodos de una clase de consola
     * @param string $class
     * @return boolean
     */
    public function ListMethods($class)
    {
        $namespace = 'Cc\\Mvc\\Console\\';
        if ($this->console->Autoload($namespace . $class))
        {
            $ReflectionClass = new \ReflectionClass($namespace . $class);
            if (!$ReflectionClass->isSubclassOf(AbstracConsole::class))
            {
                return false;
            }
            $ClassComent = $ReflectionClass->getDocComment();
            $ClassComent = $this->sanitizeComent($ClassComent);
            $arr = explode("\n", $ClassComent);
            $ClassInfo = '';
            foreach ($arr as $i => $line)
            {
                if (substr($line, 0, 2) == ' @')
                {
                    break;
                }
                $ClassInfo.=$line;
            }
            $this->infoClass = $ClassInfo;
            foreach ($ReflectionClass->getMethods() as $m)
            {
                if ($m->isConstructor() ||
                        $m->isDestructor() ||
                        $m->isAbstract() ||
                        $m->isPrivate() ||
                        $m->isProtected())
                {
                    continue;
                }
                $this->listMethod[] = $m->name;
            }
            return true;
            // $ClassInfo = preg_replace("/(\\n @\s)/", " ", $ClassComent);
        }
        return false;
    }

    /**
     * Elimina los ateriscos de comentario   
     * @param string $coment
     * @return string
     */
    private function sanitizeComent($coment)
    {
        $coment = preg_replace("/(\/\*\*)|(\*\/)/", " ", $coment);
        return preg_replace("/(\\n {1,}\*)/", "\n", $coment);
    }

}

/**
 * Muestra la informacion de ayuda de otros controladores 
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Console
 */
class h extends Help
{
    
}
