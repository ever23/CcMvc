<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;

if (!class_exists("\\Smarty"))
{
    throw new Exception("Se requieren de la libreria Smarty para cargar archivos .tpl");
}

/**
 * Description of ViewSmartyTpl
 *
 * @author usuario
 */
class ViewSmartyTpl
{

    protected $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();
        $this->smarty->debugging = Mvc::App()->IsDebung();
        // $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 120;

        $cache = Mvc::App()->Config()->App['Cache'] . 'Smarty';

        $this->smarty->setCacheDir($cache . '/Cache');
        $this->smarty->setCompileDir($cache . '/Compile');
    }

    public function LoadTpl($dir, $name, ...$agrs)
    {
        $this->smarty->setTemplateDir($dir);
        foreach ($agrs as $v)
        {
            foreach ($v as $i => $var)
                $this->smarty->assign($i, $var);
        }
        $this->smarty->display($name);
    }

}
