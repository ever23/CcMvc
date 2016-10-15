<?php
namespace Cc\Mvc;
class CUsuario extends Controllers
{

    public static function index(DBtabla $user)
    {
        if($_POST && $_POST['pass']==$_POST['pass2'])
            if(!$user->Insert($_POST))
            {
               echo 'ocurrio un error al insertar un usuario'; 
            }
        self::LoadView('usuario/');
    }
 


}
