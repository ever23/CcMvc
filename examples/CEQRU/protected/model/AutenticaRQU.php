<?php

//require_once('../controllers/Html.php')

namespace Cc\Mvc;

use Cc\Mvc;

class AutenticaRQU extends Autenticate
{

    protected function Autentica(DBtabla &$user)
    {
        $user->Select("nomb_user='" . $this['user'] . "' and pass_user='" . $this['pass'] . "'");
        if ($user->num_rows == 1)
        {
            $param = $user->fetch();
            return array('user' => $param['nomb_user'], 'pass' => $param['pass_user']);
        }
        return [];
    }

    protected function OnFailed()
    {
        if ($this->IsFailed() != self::NoAuth)
        {
            $this->Destroy();
            Mvc::Redirec('index/login');
        }
    }

    protected function OnSuccess()
    {
        
    }

}
