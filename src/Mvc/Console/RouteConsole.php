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

namespace Cc\Mvc;

use Cc\Mvc;
use Cc\InyectorException;

/**
 * Enruta los comando de la consola y ejecuta los controladores de consola 
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Console
 */
class RouteConsole
{

    /**
     * parametros
     * @var string 
     */
    protected $argv;

    /**
     * separador entre la clase y el metodo
     * @var string 
     */
    protected $separator = ':';

    /**
     * nombre de la clase controladora de consola 
     * @var string 
     */
    protected $class;

    /**
     * nombre del metodo
     * @var string 
     */
    protected $method;

    /**
     * autocargador de controladores de consola del usuario
     * @var \Cc\Autoload 
     */
    protected $autoload;

    /**
     *
     * @var \ReflectionClass 
     */
    protected $reflectionClass;

    /**
     *
     * @var \ReflectionMethod
     */
    protected $reflectionMethod;

    /**
     *
     * @var AbstracConsole 
     */
    protected $consoleController;

    /**
     * 
     * @param array $argv parametros
     */
    public function __construct($argv)
    {
        array_shift($argv);
        $this->argv = $argv;
    }

    /**
     * Enruta el controlaodor de consola 
     */
    public function Route()
    {
        $controller = explode($this->separator, array_shift($this->argv));

        $this->class = $controller[0];
        $this->method = isset($controller[1]) ? $controller[1] : 'index';
        if (!is_dir(Mvc::App()->Config()->App['Console']))
        {
            mkdir(Mvc::App()->Config()->App['Console']);
        }
        $this->autoload = new \Cc\Autoload(Mvc::App()->Config()->App['Console'], false);
        if ($this->argv)
            $this->ReadParameters();
    }

    /**
     * obtiene los parametros de la consola para pasarlos como dependencias 
     */
    protected function ReadParameters()
    {
        $params = [];
        $indices = preg_grep('/^\-{1}([0-9a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/', $this->argv);

        foreach ($indices as $ind => $i)
        {
            $name = substr($i, 1);
            if (isset($this->argv[$ind + 1]) && !preg_match('/^\-{1}([0-9a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/', $this->argv[$ind + 1]))
            {
                $params[$name] = $this->argv[$ind + 1];
            } else
            {
                $params[$name] = true;
            }
        }
        Mvc::App()->DependenceInyector->SetDependenceForParamArray($params);
    }

    /**
     * crea las  Reflexion tanto de la clase como del metodo
     * @return bool
     */
    public function CreateReflexion()
    {
        $namespace = __NAMESPACE__;
        $class = $namespace . '\\Console\\' . $this->class;

        if (Mvc::App()->autoloadCore($class) || $this->autoload->autoloadCore($class))
        {
            $this->reflectionClass = new \ReflectionClass($class);

            if ($this->reflectionClass->isSubclassOf(AbstracConsole::class))
            {
                return $this->CreateReflexionMethod($this->reflectionClass);
            }
        }
        self::Out("\nEl comando no se encontro...\n");
        exit;
    }

    /**
     * crea la Reflexion del metodo
     * @param \ReflectionClass $class
     * @return boolean
     */
    protected function CreateReflexionMethod(\ReflectionClass $class)
    {

        try
        {
            $this->reflectionMethod = $class->getMethod($this->method);
        } catch (\ReflectionException $ex)
        {
            self::Out("\nEl comando " . $this->method . " no se reconoce...\n");
            exit;
        }
        if ($this->reflectionMethod->isConstructor() ||
                $this->reflectionMethod->isDestructor() ||
                $this->reflectionMethod->isAbstract() ||
                $this->reflectionMethod->isPrivate() ||
                $this->reflectionMethod->isProtected())
        {
            self::Out("\nEl comando " . $this->method . " no se reconoce...\n");
            exit;
        }
        return true;
    }

    /**
     * Ejecuta el controlador de consola  
     */
    public function Execute()
    {
        //Mvc::App()->DependenceInyector->SetDependenceForParamArray($this->argv);

        $this->consoleController = $this->reflectionClass->newInstanceWithoutConstructor();
        $construc = $this->reflectionClass->getConstructor();
        if ($construc)
        {
            try
            {
                $params = Mvc::App()->DependenceInyector->SetFunction($construc)->Param();
            } catch (InyectorException $ex)
            {
                switch ($ex->getCode())
                {
                    case InyectorException::NotResolveParam:
                        self::Out("El parametro -" . $ex->GetReflectionParameter()->name . " no es opcional y es obligatorio \n");
                        break;
                    case InyectorException::FaileTypeParam:
                        self::Out("El parametro -" . $ex->GetReflectionParameter()->name . " no es del tipo correcto \n");
                        break;


                    default :
                        self::Out($ex->getMessage() . "\n");
                }
                exit;
            }
            $this->consoleController->__construct(...$params);
        }
        try
        {
            $paramMethod = Mvc::App()->DependenceInyector->SetFunction($this->reflectionMethod)->Param();
        } catch (InyectorException $ex)
        {
            switch ($ex->getCode())
            {
                case InyectorException::NotResolveParam:
                    self::Out("El parametro -" . $ex->GetReflectionParameter()->name . " no es opcional y es obligatorio \n");
                    break;
                case InyectorException::FaileTypeParam:
                    self::Out("El parametro -" . $ex->GetReflectionParameter()->name . " no es del tipo correcto \n");
                    break;

                default :
                    self::Out($ex->getMessage() . "\n");
            }
            exit;
        }
        $this->consoleController->{$this->method}(...$paramMethod);
    }

    /**
     * imprime un texto en el flujo STDOUT
     * @param string $out
     */
    public static function Out($out)
    {
        fwrite(STDOUT, $out);
    }

    /**
     * obtiene una entrada de el flujo STDIN
     * @return string 
     */
    public static function In()
    {
        return fgets(STDIN);
    }

}
