<?php
namespace Cc\Mvc;
class Cindex extends Controllers
{
    public static function index()
    {
        self::LoadView('index');
    }

    public function login(Html $html, AutenticaRQU $session, Cookie $cookie)
    {
        $html->SetLayaut("login");
        $error=NULL;
      
        IF(!empty($cookie['error']))
        {
            $error=$cookie['error'];
            unset($cookie['error']);
        }
        $session->Destroy();
        self::LoadView('login',['error'=>$error]);
    }

    public function ingresar(DBtabla $user, AutenticaRQU $session, Cookie $cookie)
    {
        if(isset($_POST['user']) && isset($_POST['pass']))
        {
            $user->Select("nomb_user='" . $_POST['user'] . "' and pass_user='" . $_POST['pass'] . "'");
            if($user->num_rows == 1)
            {
                $param = $user->fetch();

                if($session->SetParam(array('user' => $param['nomb_user'], 'pass' => $param['pass_user'])))
                {
                      $this->Redirec('index');
                }else
                {
                    
                }
              
            } else
            {
                $cookie['error'] = 'EL NOMBRE DE USUARIO O CONTRASEÑA SON INCORRECTOS';
                $this->Redirec('index/login');
            }
        } else
        {
            $cookie['error'] = 'PORFAVOR LLENE EL FORMULARIO';
            $this->Redirec('index/login');
        }
    }

}
