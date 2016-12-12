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

namespace Cc;

/**
 * ES CAPAS DE REALIZARLE INGENIERIA INVERSA A LA FUNCION O METODO INDICADO 
 * PARA OBTENER LOS PARAMETROS QUE ESTE REQUIERE Y PROPOCIONARLOS 
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package Cc
 * @subpackage Dependencias
 */
class DependenceInyector
{

    /**
     * funcion en la que se inyectaran las dependencias 
     * @var callable|\Closure 
     */
    protected $calable;

    /**
     * Objeto de reflexion para ma funcion o metodo
     * @var \ReflectionFunctionAbstract 
     */
    protected $function;

    /**
     * lista de clases que podran ser instanceadas e inyectadas al momento de ser requeridas como dependencia
     * @var array 
     */
    protected $InyectDependence = [];

    /**
     * objetos que seran inyectados como dependencias 
     * @var array 
     */
    protected $DependenceDefault = [];

    /**
     * lista de parametros que seran inyectados en las funciones que los requirean 
     * @var array 
     */
    protected $DependenceForParam = [0 => []];

    /**
     * 
     * @param mixes $function si es un string deve ser el nombre de una funcion y si es un array el primer indice deve ser el objeto o nombre de la clase y el segundo el nombre del metodo tambien puede ser un objeto ReflectionFunctionAbstract  
     * 
     */
    public function __construct($function = NULL)
    {
        if (!is_null($function))
        {
            $this->SetFunction($function);
        }
        $this->AddDependence('{--DependenceInyector--}', $this);
    }

    /**
     * llama e inyecta los parametros de la funcion pasada
     * @param callable $function
     * @param array $params
     * @return mixes
     */
    public function Call(callable $function, $params = [])
    {
        $this->SetFunction($function);
        return $function(...$this->Param($params));
    }

    /**
     * ESTABLECE LA FUNCION QUE SERA ANALIZADA
     * @param mixes $function si es un string deve ser el nombre de una funcion y si es un array el primer indice deve ser el objeto o nombre de la clase y el segundo el nombre del metodo tambien puede ser un objeto ReflectionFunctionAbstract
     * @return DependenceInyector
     * @throws InyectorException
     */
    public function &SetFunction($function)
    {
        if ($function instanceof \ReflectionFunctionAbstract)
        {
            $this->function = &$function;
        } elseif (is_array($function))
        {

            list($class, $method) = $function;

            try
            {

                $c = new \ReflectionClass($class);
                $this->function = $c->getMethod($method);
            } catch (\Exception $ex)
            {
                throw new InyectorException($ex->getMessage(), InyectorException::LoadReflection, $ex);
            }
        } else
        {
            try
            {
                $this->function = new \ReflectionFunction($function);
            } catch (\Exception $ex)
            {
                throw new InyectorException($ex->getMessage(), InyectorException::LoadReflection, $ex);
            }
        }
        return $this;
    }

    /**
     * agrega dependencias que seran instanciadas al momento de ser inyectadas 
     * el formato deve ser [class=>[...params],..]
     * @param array $dependence
     */
    public function AddDependenceInstanciable(array $dependence)
    {
        $this->InyectDependence = $dependence;
    }

    /**
     * agrega objetos asociados a un alias que seran inyectado cuando se requiera segun el tipo o alias 
     * @param string $name alias 
     * @param mixes $param
     */
    public function AddDependence($name, &$param)
    {
        $this->DependenceDefault[$name] = &$param;
    }

    /**
     * agrega depencias que seran inyectadas segun el nombre del parametro
     * @param string $param nombre del parametro
     * @param mixes $dependenc
     */
    public function AddDependenceForParam($param, &$dependenc)
    {
        if (!isset($this->DependenceForParam[0]))
        {
            $this->DependenceForParam[0] = [];
        }
        $this->DependenceForParam[0][$param] = &$dependenc;
    }

    /**
     * elimia todas las dependencias por parametro
     */
    public function LimpiarDependenceForParam()
    {
        $this->DependenceForParam = [0 => []];
    }

    /**
     * agrega una serie de depencias que seran inyectadas segun el nombre del parametro
     * @param array|Traversable $array
     */
    public function SetDependenceForParamArray(&$array)
    {
        $this->DependenceForParam[] = &$array;
    }

    /**
     * objtiene los parametros de la funcion actual
     * @param array $params lista de parametros que seran remplazados por los que ya se han preparados
     * @return array
     * @throws InyectorException si ocurrio un error
     */
    public function &Param($params = [])
    {
        try
        {
            $p = $this->InyectDependenceParam($this->function);
            foreach ($params as $i => $v)
            {
                $p[$i] = $v;
            }
            return $p;
        } catch (\Exception $ex)
        {
            throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * obtiene los parametros a ser inyectado de una funcion dada 
     * @param mixes $function si es un string deve ser el nombre de una funcion y si es un array el primer indice deve ser el objeto o nombre de la clase y el segundo el nombre del metodo tambien puede ser un objeto ReflectionFunctionAbstract 
     * @return array
     */
    public static function &ParamFunction($function)
    {
        $p = new self($function);
        return $p->Param();
    }

    /**
     * 
     * @param ReflectionFunctionAbstract $method
     * @return array
     * @throws InyectorException
     */
    protected function &InyectDependenceParam(\ReflectionFunctionAbstract &$method)
    {
        $param = $method->getParameters();

        $parametros = array();
        $class = null;
        if (is_array($param))
        {
            /* @var $p ReflectionParameter */
            foreach ($param as $i => $p)
            {
                try
                {
                    $class = $p->getClass();
                } catch (\Exception $ex)
                {
                    throw new InyectorException($ex->getMessage(), InyectorException::NotFoundClassParam, $ex);
                }
                if (is_object($class))
                {
                    //$OBJ=NULL;
                    $OBJ = &$this->DefaultDependence($class);
                    if (!is_object($OBJ))
                    {
                        $OBJ = $this->InstanceDependence($p, $class);
                    }
                    if (!is_object($OBJ))
                    {
                        $mensaje = "NO SE PUDO RESOLVER EL PARAMETRO NUMERO " . ($i + 1) . " " . $class->name . " $" . $p->name . "  LA CLASE " . $class->name . " DEVE IMPLEMENTAR LA INTERFACE Inyectable ";
                        throw new InyectorException($mensaje, InyectorException::NotResolveParamObjec);
                    }
                    $parametros[] = &$OBJ;
                } else
                {
                    if (method_exists($p, 'getType') && $p->getType())
                    {
                        $t = $this->DependenceRequest($p, $p->getType());
                        if (gettype($t) != $p->getType())
                        {
                            $mensaje = "EL PARAMETRO NUMERO " . ($i + 1) . " " . $p->getType() . " $" . $p->name . "  DEBE SER DE TIPO " . $p->getType();

                            throw new InyectorException($mensaje, InyectorException::FaileTypeParam);
                        }
                        $parametros[] = $t;
                    } elseif ($p->isArray())
                    {
                        $t = $this->DependenceRequest($p, 'array');
                        if (gettype($t) != 'array')
                        {
                            $mensaje = "EL PARAMETRO NUMERO " . ($i + 1) . " array $" . $p->name . " DEBE SER DE UN ARRAY VALIDO";


                            throw new InyectorException($mensaje, InyectorException::FaileTypeParam);
                        }
                        $parametros[] = &$t;
                    } else
                    {
                        $t = $this->DependenceRequest($p);
                        if (!$p->isDefaultValueAvailable() && is_null($t))
                        {
                            $mensaje = "EL PARAMETRO NUMERO " . ($i + 1) . " $" . $p->name . " NO SE PUDO RESOLVER Y ES OBLIGATORIO";


                            throw new InyectorException($mensaje, InyectorException::NotResolveParam);
                        } else
                        {
                            $parametros[] = $this->DependenceRequest($p);
                        }
                    }
                }
                unset($class);
            }
        }
        return $parametros;
    }

    protected function &DefaultDependence(\ReflectionClass &$class)
    {
        foreach ($this->DependenceDefault as &$v)
        {
            if (is_a($v, $class->name))
            {
                return $v;
            }
        }
        $n = NULL;
        return $n;
    }

    protected function InstanceDependence(\ReflectionParameter &$param, \ReflectionClass &$class)
    {
        if (isset($this->InyectDependence[$class->name]))
        {
            try
            {
                $clase = $class->name;
                return new $clase(...$this->RemplaceParam($this->InyectDependence[$class->name], $param->name));
            } catch (\Exception $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            } catch (\Error $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            }
        } elseif (isset($this->InyectDependence['\\' . $class->name]))
        {
            try
            {
                $clase = '\\' . $class->name;
                return new $clase(...$this->RemplaceParam($this->InyectDependence['\\' . $class->name], $param->name));
            } catch (\Exception $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            } catch (\Error $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            }
        } elseif ($class->implementsInterface(Inyectable::class))
        {
            $clase = $class->name;

            try
            {

                $name_param = $param->name;
                $GLOBALS['name_param'] = $name_param;
                $p = $clase::CtorParam();
                if ($p instanceof $class->name)
                {
                    return $p;
                }

                return new $clase(...$this->RemplaceParam($p, $name_param));
            } catch (\Exception $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            } catch (\Error $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            }
        } elseif ($class->isSubclassOf(ValidDependence::class))
        {
            try
            {
                $clase = '\\' . $class->name;
                return new $clase($this->DependenceRequest($param));
            } catch (\Exception $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            } catch (\Error $ex)
            {
                throw new InyectorException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        $var = NULL;
        return $var;
    }

    protected function DependenceRequest(\ReflectionParameter &$param, $type = NULL)
    {
        $ret = NULL;
        $filter = FILTER_DEFAULT;
        $op = $param->isDefaultValueAvailable();
        $def = NULL;
        if (is_null($type))
        {
            if ($op)
            {
                $type = gettype($param->getDefaultValue());
                $def = $param->getDefaultValue();
            }
        }

        if (!is_null($type))
            switch (strtolower($type))
            {
                case 'array':
                    $filter = FILTER_FORCE_ARRAY;

                    break;
                case 'int':
                case 'integer':

                    $filter = FILTER_VALIDATE_INT;
                    break;
                case 'float':
                case 'double':
                    $filter = FILTER_VALIDATE_FLOAT;
                    break;
                case 'string':
                    $filter = FILTER_DEFAULT;
                    break;
                case 'bool':
                case 'boolean':
                    $filter = FILTER_VALIDATE_BOOLEAN;
                    break;


                default:
                    $filter = FILTER_DEFAULT;
            }
        foreach ($this->DependenceForParam as &$p)
        {
            if (isset($p[$param->name]))
            {

                if (is_array($p[$param->name]) && ($filter === FILTER_DEFAULT || $filter === FILTER_FORCE_ARRAY))
                {
                    return $p[$param->name];
                } elseif ($filter === FILTER_DEFAULT)
                {
                    return $p[$param->name];
                } else
                {
                    $ret = filter_var($p[$param->name], $filter);
                }
                break;
            }
        }
        if (is_bool($ret) && !$ret && $filter != FILTER_VALIDATE_BOOLEAN)
        {
            $ret = NULL;
        }
        if ($op && empty($ret))
        {

            $ret = $def;
        }
        return $ret;
    }

    protected function &RemplaceParam(array $param, $name_param)
    {
        $rest = array();
        $this->DependenceDefault['{name_param}'] = $name_param;
        foreach ($param as $i => $v)
        {
            if (is_string($v) && isset($this->DependenceDefault[$v]))
            {
                $rest[$i] = &$this->DependenceDefault[$v];
            } else
            {
                $rest[$i] = $v;
            }
        }
        return $rest;
    }

}

/**
 * Ecxepciones de Inyeccion
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package Cc
 * @subpackage Dependencias
 */
class InyectorException extends Exception
{

    /**
     * Codigo de error cuando se genera una Exception al crear el objeto de refection para la funcion 
     */
    const LoadReflection = 0x1;

    /**
     * Codigo de error indica que no existe una clase que define el tipo de un parametro
     */
    const NotFoundClassParam = 0x2;

    /**
     * Codigo de error indica que el parametro definido con una clase no se ha podido resolver
     */
    const NotResolveParamObjec = 0x3;

    /**
     * Codigo de error indica que el parametro no se ha podido resolver
     */
    const NotResolveParam = 0x4;

    /**
     * Codigo de error indica que el tipo del parametro encontrado no es el mismo que el definido en
     * la funcion 
     */
    const FaileTypeParam = 0x5;

}
