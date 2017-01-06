<?php

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * clase Cookie para administrar las cokies mas efisientemente
 * <code>
 * <?php
 * 
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
 * @package CcMvc
 * @subpackage Request
 */
class Cookie extends \Cc\Cookie
{

    /**
     * contrctor de la clase
     * @param Config $conf
     */
    public function __construct(Config $conf = NULL)
    {
        if (is_null($conf))
        {
            $conf = \Cc\Mvc::Config();
        }
        $this->Cookie = &$_COOKIE;
        if (isset($conf['Autenticate']) && isset($conf['Autenticate']['SessionCookie']))
        {
            $this->path = $conf['Autenticate']['SessionCookie']['path'];
            $this->host = $conf['Autenticate']['SessionCookie']['dominio'];
        }
        if (isset($conf['protocol']))
            $this->secure = $conf['protocol'] == 'https';
        $this->httponly = false;
    }

    /**
     * 
     * @param string $name
     * @param mixes $value
     * @param string $expire
     * @param string $path
     * @param string $dominio
     * @param bool $secure
     * @param bool $httponly
     * @see \Cc\Cookie::SaveCookie()
     */
    protected function SaveCookie($name, $value, $expire = NULL, $path = NULL, $dominio = NULL, $secure = NULL, $httponly = NULL)
    {
        setcookie($name, $value, $expire, $path, $dominio, $secure, $httponly);
    }

}
