<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;
use Cc\ImageGD;
use Cc\Cache;

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
    protected $RpImage = ['GDw', 'GDh', 'GDc', 'GDNoCookie', ''];
    protected $fileCache;

    /**
     *
     * @var  ImageGD
     */
    public $BufferGD = NULL;

    public static function CtorParam()
    {
        return [true, '{name_param}'];
    }

    /**
     * 
     * @param bool $compress
     * @param bool $param
     * @throws Exception
     */
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

    public function CreateImageGd($w, $h, $ContenType)
    {
        $this->BufferGD = new ImageGD($w, $h, $ContenType);
    }

    /**
     * 
     * @param string $file
     * @throws Exception
     */
    public function CreateImageGdFormFile($file)
    {
        if (!file_exists($file))
        {
            throw new Exception("el fichero " . $file . " no existe ");
        }

        $this->BufferGD = new ImageGD($file);
    }

    public function CreateImageGdFormString($string)
    {
        $this->BufferGD = new ImageGD($string);
    }

    public function GetLayaut()
    {
        return ['Layaut' => NULL, 'Dir' => NULL];
    }

    public function ProccessConten($str)
    {
        if (isset($_GET['GDw']) || isset($_GET['GDh']) || isset($_GET['GDc']) || isset($_COOKIE['GDmaxW']) || isset($_GET['GDtp']))
        {
            return $this->ResampledImage($str, isset($_GET['GDw']) ? $_GET['GDw'] : NULL, isset($_GET['GDh']) ? $_GET['GDh'] : NULL, isset($_GET['GDc']) ? $_GET['GDc'] : NULL, isset($_GET['GDtp']) ? $_GET['GDtp'] : [255, 254, 255]);
        }
        return $str;
    }

    protected function ResampledImage($image, $nuevo_ancho, $nuevo_alto, $calidad = NULL, $fondo = NULL)
    {

        //return $image;
        if (is_string($fondo))
        {
            if ($fondo[0] == '#')
            {
                $fondo = substr($fondo, 1);
            }
            $c[0] = hexdec(substr($fondo, 0, 2));
            $c[1] = hexdec(substr($fondo, 2, 2));
            $c[2] = hexdec(substr($fondo, 4, 2));
            $fondo = $c;
        }
        list($ancho, $alto) = getimagesizefromstring($image);


        if (substr($nuevo_ancho, -1, 1) == '%' && isset($_COOKIE['GDmaxW']))
        {
            $porcent = (int) substr($nuevo_ancho, 0, -1);

            $nuevo_ancho = ( $_COOKIE['GDmaxW'] * ($porcent * 0.01));
        }


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
            unset($_GET['GDNoCookie']);
            //if (is_null($nuevo_alto) && is_null($nuevo_ancho) && is_null($calidad))
            // return $image;
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
        if ($alto == $nuevo_alto && $ancho == $nuevo_ancho)
            return $image;
        //  return var_export(Mvc::App()->Router->InfoFile, true);
        if (Mvc::App()->Router->InfoFile instanceof \SplFileInfo)
        {
            $c = $this->CacheImg($nuevo_ancho, $nuevo_alto, $calidad, $fondo);

            if ($c === true)
            {
                return file_get_contents($this->fileCache);
            } elseif (is_null($c))
            {
                return '';
            }
        }

// return 1;
//  imagecropauto($image, $mode, $threshold, $color)



        $IMG = new ImageGD($image);

        $IMG->ColorMask($fondo);
//return $IMG->LoadString($image);
        $IMG->Resize($nuevo_ancho, $nuevo_alto);
//$IMG->ImportImgFormString('img', $image);
//   $IMG->PrintImg('img', 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto);
        switch (trim(Mvc::App()->Content_type))
        {
            case 'image/x-xbitmap':
            case 'image/gif':
                $out = $IMG->Output(NULL, "S");
                break;
            case 'image/jpeg':
                $out = $IMG->Output(NULL, "S", [is_null($calidad) ? 100 : $calidad]);
                break;
            case 'image/png':
                $out = $IMG->Output(NULL, "S", [is_null($calidad) ? 9 : $calidad]);
                break;
            default :
                $IMG->destroy();
                $out = $image;
                break;
        }
        if (!Mvc::App()->IsDebung() && Mvc::App()->Router->InfoFile instanceof \SplFileInfo)
        {
            $cache = [];
            $cache['type'] = 'file';
            $cache['Controller'] = Mvc::App()->Router->InfoFile->__toString();
            $cache['RealFile'] = $this->fileCache->__toString();
            $cookie = '';
            if (isset($_COOKIE['GDmaxW']))
            {
                $cookie = 'COOKIE';
                $cookie.= $cache['GDmaxW'] = $_COOKIE['GDmaxW'];
            }

            Cache::Set(Mvc::App()->GetNameCacheRouter() . $cookie, $cache);
            Cache::Delete(Mvc::App()->GetNameCacheRouter());

            $f = fopen($this->fileCache, 'w');
            fwrite($f, $out);
            fclose($f);
            Router::HeadersReponseFiles($this->fileCache, Mvc::App()->Content_type, Mvc::App()->Config()->Router['CacheExpiresTime']);
        }

        return $out;
    }

    protected function CacheImg($w, $h, $c, $f)
    {
        $name = Mvc::App()->Router->InfoFile->getBasename(Mvc::App()->Router->InfoFile->getExtension());
        $name .= 'w' . ((int) $w) . 'h' . ((int) $h) . 'c' . $c;
        $cache = Mvc::App()->Config()->App['Cache'] . 'ImageGD' . DIRECTORY_SEPARATOR;
        if (!is_dir($cache))
            mkdir($cache);
        $file = $cache . $name . '.' . Mvc::App()->Router->InfoFile->getExtension();

        $this->fileCache = new \SplFileInfo($file);
        if (!Mvc::App()->IsDebung() && file_exists($this->fileCache) && $this->fileCache->getMTime() >= Mvc::App()->Router->InfoFile->getMTime())
        {
            if (Router::HeadersReponseFiles($this->fileCache, Mvc::App()->Content_type, Mvc::App()->Config()->Router['CacheExpiresTime'], true))
            {
                return true;
            } else
            {
                return NULL;
            }
        }
        return FALSE;
    }

    public function SetLayaut($layaut, $dirLayaut = NULL)
    {
        
    }

}

class GDexception extends Exception
{
    
}
