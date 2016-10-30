<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Description of ResponseCSS
 *
 * @author usuario
 */
class ResponseCSS extends Response
{

    public function ProccessConten($conten)
    {

        if ($this->min && !Mvc::App()->IsDebung())
        {
            if (Mvc::App()->Router->InfoFile instanceof \SplFileInfo)
            {
                $name = Mvc::App()->Router->InfoFile->getBasename('.css');

                if (substr($name, -4, 4) != '.min')
                {
                    Mvc::App()->Buffer->SetAutoMin(true);
                    Mvc::App()->Buffer->SetTypeMin($this->typeMin);
                }
            } else
            {
                Mvc::App()->Buffer->SetAutoMin(true);
                Mvc::App()->Buffer->SetTypeMin($this->typeMin);
            }
        }

        return $conten;
    }

}
