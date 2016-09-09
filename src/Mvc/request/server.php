<?php

namespace Cc\Mvc;

use Cc\UrlManager;

/**
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package CcMvc
 * @subpackage Request
 */
class Server implements \ArrayAccess
{

    const C1 = '%5B';
    const C2 = '%5D';

    /**
     *  ENVIA UN HEADER DE TIPO Location QUE DETIENE LA EJECUCION Y REDIRECCIONA
     *  @param string $url DIRECCION URL A LA QUE SE REDIRECCIONARA
     *  @param array $get contendra las variables Get que se enviaran
     */
    public static function Redirec($url = NULL, array $get = array())
    {
        header("Location: " . UrlManager::Href($url, $get));

        exit;
    }

    /**
     *  SERIALIZA UNA ARRAY Y LO COVIERTE EN UNA CADENA Get VALIDA
     *  @param array $var contendra las variables Get
     *  @return string CADENA Get VALIDA
     */
    public static function SerializeGet(array $var)
    {
        return http_build_query($var);
        /* $conten = '';
          foreach($var as $i => $v)
          {
          if(is_array($v))
          {
          $conten.=self::SerializeGetArray($i, $v) . '&';
          } else
          $conten.=$i . '=' . $v . '&';
          }
          return substr($conten, 0, strlen($conten) - 1); */
    }

    /**
      RETORNA E TIPO DE PROTOCOLO USADO


     */
    public static function Protocol()
    {
        if(!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']))
        {
            $uri = 'https://';
        } else
        {
            $uri = 'http://';
        }
        return $uri;
    }

    public static function AcceptEncoding($encoding)
    {
        $cod = explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']);
        return in_array($encoding, $cod);
    }

    public static function HttpAccept($accept)
    {
        $h = explode(',', $_SERVER["HTTP_ACCEPT"]);
        return in_array($accept, $h);
    }

    public static function Get($ind, $filter = FILTER_DEFAULT, $option = NULL)
    {
        return filter_input(INPUT_GET, $ind, $filter, $option);
    }

    public static function Post($ind, $filter = FILTER_DEFAULT, $option = NULL)
    {
        return filter_input(INPUT_POST, $ind, $filter, $option);
    }

    protected static function SerializeGetArray($var, $array)
    {
        $conten = '';
        foreach($array as $i => $v)
        {
            if(is_array($v))
            {
                $conten.=self::SerializeGetArray($var . self::C1 . $i . self::C2, $v) . '&';
            } else
            {
                $conten.=$var . self::C1 . $i . self::C2 . '=' . $v . '&';
            }
        }
        return substr($conten, 0, strlen($conten) - 1);
    }

    public function offsetSet($offset, $value)
    {
        $_SERVER[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($_SERVER[$offset]);
    }

    public function offsetUnset($offset)
    {

        unset($_SERVER[$offset]);
    }

    public function offsetGet($offset)
    {
        return $_SERVER[$offset];
    }

}
