<?php
namespace Cc\Mvc;
class Cindex extends Controllers
{

    public static function index(Cookie $cookie)
    {
        $cookie['galleta']='hola galleta';
        self::LoadView('index');
    }


}
