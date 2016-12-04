<?php

namespace Cc;

/**
 * clase Cookie para administrar las cokies mas efisientemente
 * <code>
 * <?php
 * // @var $conf Config
 * $cookie= new Cookie($conf);
 * 
 * echo $cookie['micookie1'];// leyendo la cookie 
 * echo $cookie->micookie1;// leyendo la cookie 
 * 
 * $cookie->Set('micookie2','holacookie');// enviando una cookie
 * $cookie['micookie2']='holacookie';// enviando una cookie
 * $cookie->micookie2='holacookie';// enviando una cookie
 * 
 * 
 * unset($cookie['micookie1']); // eliminando la cookie 
 * ?>
 * </code>
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package Cc
 * @subpackage Request
 */
abstract class Cookie implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Path por defecto de la cookie
     * @var string 
     */
    protected $path = '/';

    /**
     * host por defecto de la cookie
     * @var string 
     */
    protected $host = NULL;

    /**
     * parametro secure por defecto de la cookie
     * @var bool 
     */
    protected $secure = false;

    /**
     * parametro httponly por defecto de la cookie
     * @var bool 
     */
    protected $httponly = false;

    /**
     * cookies
     * @var array 
     */
    protected $Cookie = [];

    /**
     * @access private
     * @return type
     */
    public function __debugInfo()
    {
        return $this->Cookie;
    }

    /**
     * ENVIA UNA COOKIE AL NAVEGADOR 
     * @param string $name
     * @param mixes $value tambien se pueden enviar array mediante cookies
     * @param int $expire
     * @param strig $path
     * @param string $dominio
     * @param bool $secure
     * @param bool $httponly
     */
    public function Set($name, $value, $expire = NULL, $path = NULL, $dominio = NULL, $secure = NULL, $httponly = NULL)
    {
        if (is_null($path))
        {
            $path = $this->path;
        }
        if (is_null($dominio))
        {
            $dominio = $this->host;
        }
        if (is_null($secure))
        {
            $secure = $this->secure;
        }
        if (is_null($httponly))
        {
            $httponly = $this->httponly;
        }


        if (is_array($value) || $value instanceof \Traversable)
        {
            foreach ($value as $i => $v)
            {
                $this->Set($name . '[' . $i . ']', $v, $expire, $path, $dominio, $secure, $httponly);
            }
        }

        $this->SaveCookie($name, $value, $expire, $path, $dominio, $secure, $httponly);
    }

    abstract protected function SaveCookie($name, $value, $expire = NULL, $path = NULL, $dominio = NULL, $secure = NULL, $httponly = NULL);

    /**
     * filtra una cookie
     * @param string $name nombre de la cookie
     * @param int $filter
     * @param mixes $option
     * @return type
     * @uses filter_var()
     */
    public function Filter($name, $filter = FILTER_DEFAULT, $option = NULL)
    {
        return filter_var($this->offsetGet($name), $filter, $option);
    }

    /**
     * modifica una cookie o envia una con los parametros por defecto de no existir
     * @param string $offset nombre de la cookie
     * @param mixes $value valor de la cookie
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value) || $value instanceof \Traversable)
        {
            foreach ($value as $i => $v)
            {
                $this->offsetSet($offset . '[' . $i . ']', $v);
            }
        } else
        {
            $this->Set($offset, $value);
            $this->Cookie[$offset] = $value;
        }
    }

    /**
     * comprueva si existe una cookie
     * @param string $offset nombre de la cookie
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->Cookie[$offset]);
    }

    /**
     * elimina una cookie
     * @param string $offset nombre de la cookie
     */
    public function offsetUnset($offset)
    {

        if (isset($this->Cookie[$offset]))
        {
            $this->CookieUnset($offset, $this->Cookie[$offset]);

            unset($this->Cookie[$offset]);
        }
    }

    /**
     * elimina una cookie de tipo array
     * @param string $name
     * @param array|string $cookie
     */
    protected function CookieUnset($name, $cookie)
    {

        if (is_array($cookie) || $cookie instanceof \Traversable)
        {

            foreach ($cookie as $i => $v)
            {
                $this->CookieUnset($name . '[' . $i . ']', $v);
            }
        } else
        {
            $this->Set($name, NULL, time() - 1000);
        }
    }

    /**
     * Obtiene el valor de una cookie
     * @param string $offset nombre de la cookie
     * @return mixes
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset))
        {
            ErrorHandle::Notice("Undefined index: " . $offset);
            return;
        }

        return $this->Cookie[$offset];
    }

    /**
     * retorna el numero de cookies que existe
     * @return int
     */
    public function count()
    {
        return count($this->Cookie);
    }

    /**
     * @internal funcion magica para acceso a cookie como atributos de la clase 
     * @param string $name
     * @param mixes $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @internal funcion magica para acceso a cookie como atributos de la clase 
     * @param string $name
     * @return mixes
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @internal funcion magica para acceso a cookie como atributos de la clase 
     * @param string $name
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * @internal funcion magica para acceso a cookie como atributos de la clase 
     * @param string $name
     */
    public function __isset($name)
    {
        $this->offsetExists($name);
    }

    /**
     *  @access private
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->Cookie);
    }

}
