<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;
use Cc\ImageGD;

/**
 * GDResponse Procesa respuestas para imagenes gif, jpg y png 
 * con la capacidad de redimencionar dinamicamente segun las variables _GET 
 * GDw ancho de la imagen 
 * GDh alto de la imagen 
 * GDc calidad de la imagen si jpg y si es png es la cantidad de compresion de la imagen
 * @author Enyerber Franco <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc
 * @subpackage Response
 */
class GDResponse implements ResponseConten
{

    /**
     *
     * @var Request
     */
    protected $request;
    protected $tmp = '';
    protected $imageSoported = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/x-xbitmap',
    ];

    public static function CtorParam()
    {
        return [true, '{name_param}'];
    }

    public function __construct($compress = true, $param = NULL)
    {
        if (!is_null($param))
        {
            if (isset(Mvc::App()->Config()->Response['ExtencionContenType'][$param]) && in_array(Mvc::App()->Config()->Response['ExtencionContenType'][$param], $this->imageSoported))
            {
                Mvc::App()->ChangeResponseConten(Mvc::App()->Config()->Response['ExtencionContenType'][$param]);
                Mvc::App()->Response = $this;
            } else
            {
                throw new Exception("LA extencion .$param no esta soportada por " . static::class);
            }
        }
        $this->request = &Mvc::App()->Request;
        Mvc::App()->Buffer->SetCompres($compress);
        $this->tmp = Mvc::App()->Config()->App['Cache'];
    }

    public function GetLayaut()
    {
        return ['Layaut' => NULL, 'Dir' => NULL];
    }

    public function ProccessConten($str)
    {
        if (isset($_GET['GDw']) || isset($_GET['GDh']) || isset($_GET['GDc']) || isset($_COOKIE['GDmaxW']))
        {
            return $this->ResampledImage($str, isset($_GET['GDw']) ? $_GET['GDw'] : NULL, isset($_GET['GDh']) ? $_GET['GDh'] : NULL, isset($_GET['GDc']) ? $_GET['GDc'] : NULL);
        }
        return $str;
    }

    protected function ResampledImage($image, $nuevo_ancho, $nuevo_alto, $calidad = NULL)
    {
        list($ancho, $alto) = getimagesizefromstring($image);

        if (is_null($nuevo_ancho) && !is_null($nuevo_alto))
        {
            $proc = ($nuevo_alto * 100) / ( $alto);
            $nuevo_ancho = ( $ancho * ($proc * 0.01));
        } elseif (is_null($nuevo_alto) && !is_null($nuevo_ancho))
        {
            $proc = ($nuevo_ancho * 100) / ($ancho);
            $nuevo_alto = ($alto * ($proc * 0.01));
        }
        if (isset($_GET['GDNoCookie']) || !$this->AppConfig->Response['OptimizeImages'])
        {
            if (is_null($nuevo_alto) && is_null($nuevo_ancho) && is_null($calidad))
                return $image;
        }
        if (is_null($nuevo_alto) && is_null($nuevo_ancho))
        {
            $nuevo_alto = $alto;
            $nuevo_ancho = $ancho;
        }
        Mvc::App()->Log("GDResponse: w=" . $nuevo_ancho . " h=" . $nuevo_alto);
        if (!isset($_GET['GDNoCookie']) && (isset($_COOKIE['GDmaxW']) && $nuevo_ancho > $_COOKIE['GDmaxW']))
        {
            $alto = $nuevo_alto;
            $ancho = $nuevo_ancho;
            $nuevo_ancho = $_COOKIE['GDmaxW'];
            $proc = ($nuevo_ancho * 100) / ($ancho);
            $nuevo_alto = ($alto * ($proc * 0.01));
            Mvc::App()->Log("GDResponse Cookie: w=" . $nuevo_ancho . " h=" . $nuevo_alto);
            ///return $this->ResampledImage($image, $_COOKIE['GDmaxW'], $nuevo_alto)
        }



        $IMG = new ImageGD($nuevo_ancho, $nuevo_alto, Mvc::App()->Content_type);
        $IMG->ImportImgFormString('img', $image);
        $IMG->PrintImg('img', 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto);
        switch (Mvc::App()->Content_type)
        {
            case 'image/x-xbitmap':
            case 'image/gif':
                return $IMG->Output(NULL, "S");
            case 'image/jpeg':
                return $IMG->Output(NULL, "S", [is_null($calidad) ? 100 : $calidad]);
            case 'image/png':
                return $IMG->Output(NULL, "S", [is_null($calidad) ? 9 : $calidad]);
            default :
                $IMG->destroy();
                return $image;
        }
    }

    public function SetLayaut($layaut, $dirLayaut = NULL)
    {
        
    }

}
