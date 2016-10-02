<?php

namespace Cc;

/**
 * Manipulacion de imagenes
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package Cc
 * @subpackage GD 
 */
class ImageGD
{

    protected $img;
    public $w;
    public $h;
    protected $tipo;
    protected $fondo;
    protected $fuente;
    protected $colores;
    protected $img_import;
    protected $mask_color;
    protected $hader;

    public function __construct($ancho, $alto, $tipo)
    {
        $this->w = $ancho;
        $this->h = $alto;
        $this->img = imagecreatetruecolor($ancho, $alto);
        $this->fuente = NULL;
        $this->colores = array();
        $this->img_import = array();
        $this->fuente = array();

        $this->hader = $tipo;
        if ($tipo == "image/x-png" || $tipo == "image/png")
        {

            $this->tipo = 'png';
        }
        if ($tipo == "image/pjpeg" || $tipo == "image/jpeg")
        {

            $this->tipo = 'jpeg';
        }
        if ($tipo == "image/gif" || $tipo == "image/gif")
        {
            $this->tipo = 'gif';
        }



        $this->fondo = imagecolorallocate($this->img, 255, 255, 255);
        $this->mask_color = imagecolorallocate($this->img, 255, 0, 255);
        //$this->rectangulo_ex(0,0,$this->w,$this->h,imagecolorallocate ($this->img, 255, 0, 255));
        imagefill($this->img, 0, 0, $this->fondo);
        //imagecolortransparent($this->img ,$this->fondo);
    }

    public function Output($name = "", $ouput = "I", $options = [])
    {

        $image = "image";
        $image.=$this->tipo;
        switch ($ouput)
        {
            case "I":
                {
                    header("Content-type: " . $this->hader);
                    header('Content-Disposition: inline; filename="' . $name . '"');
                    header('Pragma: public');
                    $image($this->img, NULL, ...$options);
                } break;
            case "F":
                {
                    $image($this->img, $name . "." . $this->tipo, ...$options);
                    return $name . "." . $this->tipo;
                }break;
            case 'S':
                $name = tempnam(sys_get_temp_dir(), 'ImageGD');
                $image($this->img, $name, ...$options);
                $str = file_get_contents($name);
                unlink($name);
                return $str;
        }
    }

    public function __destruct()
    {
        $this->destroy();
    }

    public function destroy()
    {

        imagedestroy($this->img);
    }

    public function ColorMask($color)
    {
        $this->mask_color = $this->colores[$color];
        imagecolortransparent($this->img, $this->mask_color);
    }

    public function LoadTtf($name, $file_ttf)
    {
        if ($file_ttf != '')
        {
            $this->fuente+=array($name => $file_ttf);
            return 0;
        }
        return 1;
    }

    public function SaveColor($name, $R, $G, $B, $A = 0)
    {
        $this->colores+=array($name => imagecolorallocatealpha($this->img, $R, $G, $B, (int) ($A)));
        return $this->colores[$name];
    }

    public function Rgba($R, $G, $B, $A = 0)
    {
        return imagecolorallocatealpha($this->img, $R, $G, $B, (int) ($A));
    }

    public function Fill($x, $y, $rgb_color)
    {
        imagefill($this->img, $x, $y, $this->colores[$rgb_color]);
    }

    public function Linea($x, $y, $w, $h, $rgb_color)
    {
        imageline($this->img, $x, $y, $w, $h, $this->colores[$rgb_color]);
    }

    public function Rectangulo($x, $y, $w, $h, $rgb_color)
    {
        imagerectangle($this->img, $x, $y, $w, $h, $this->colores[$rgb_color]);
    }

    public function FilleRectangulo($x, $y, $w, $h, $rgb_color)
    {
        imagefilledrectangle($this->img, $x, $y, $w, $h, $this->colores[$rgb_color]);
    }

    public function PrintText($cadena, $tam, $angulo, $x, $y, $rgb_color, $fuente = NULL)
    {
        if (is_null($fuente))
        {
            imagestring($this->img, $tam, $x, $y, $cadena, $this->colores[$rgb_color]);
        } else
        {
            imagettftext($this->img, $tam, $angulo, $x, $y, $this->colores[$rgb_color], $this->fuente[$fuente], $cadena);
        }
    }

    public function PrintImageGD(ImageGD $img_class, $x, $y, $x_img, $y_img, $ancho_img, $alto_img)
    {
        imagecopyresampled($this->img, $img_class->img, $x, $y, $x_img, $y_img, $ancho_img, $alto_img, $img_class->w, $img_class->h);
    }

    public function ImportImg($name, $filename, $tip = '')
    {
        $f = new \SplFileInfo($filename);
        $tipo = $f->getExtension();



        $this->img_import[$name] = array("img" => $filename, "tipo" => 'string');
    }

    public function ImportImgFormString($name, $string)
    {

        $this->img_import[$name] = array("img" => $string, "tipo" => 'string');
    }

    public function PrintImg($name_img, $x, $y, $x_img, $y_img, $ancho_img, $alto_img, $color_trasparen = NULL)
    {
        $tipo = $this->img_import[$name_img]['tipo'];

        $original = $this->img_import[$name_img]['img'];


        $imagecreatefrom = "imagecreatefrom";

        $imagecreatefrom.=$tipo;

        if ($tipo == 'string')
        {
            $importada = imagecreatefromstring($this->img_import[$name_img]['img']);
        } else
        {
            $importada = $imagecreatefrom($original);
        }


        if ($tipo == "png" OR $tipo == "gif")
        {
            imagefill($this->img, 0, 0, $this->fondo);
        }
        if ($imagecreatefrom == 'imagecreatefromstring')
        {
            $tamano = getimagesizefromstring($original);
        } else
        {
            $tamano = getimagesize($original);
        }

        //  imagetruecolortopalette($importada, false, 255);
        $orig_Ancho = $tamano[0];

        $orig_Alto = $tamano[1];
        if ($color_trasparen != NULL)
        {
            imagecolortransparent($importada, $this->colores[$color_trasparen]);
        }
        //imagecolortransparent($this->img, IMG_COLOR_BRUSHED);
        imagecopyresampled($this->img, $importada, $x, $y, $x_img, $y_img, $ancho_img, $alto_img, $orig_Ancho, $orig_Alto);
        imagedestroy($importada);
    }

    public function PrintImgAlpha($name_img, $x, $y, $ancho_img, $alto_img, $alpha)
    {
        $tipo = $this->img_import[$name_img]['tipo'];

        $original = $this->img_import[$name_img]['img'];
        ;

        $imagecreatefrom = "imagecreatefrom";
        if ($tipo == 'php')
            $tipo = 'png';

        $imagecreatefrom.=$tipo;

        $importada = $imagecreatefrom($original);

        if ($tipo == "png" OR $tipo == "gif")
        {
            imagefill($this->img, 0, 0, $this->fondo);
        }

        if ($imagecreatefrom == 'imagecreatefromstring')
        {
            $tamano = getimagesizefromstring($original);
        } else
        {
            $tamano = getimagesize($original);
        }

        $orig_Ancho = $tamano[0];

        $orig_Alto = $tamano[1];
        $im_truco = imagecreatetruecolor($orig_Ancho, $orig_Alto);
        $fondo1 = imagecolorallocate($im_truco, 255, 255, 255);
        imagefill($im_truco, 0, 0, $fondo1);
        imagecolortransparent($im_truco, $fondo1);
        imagecopy($im_truco, $importada, 0, 0, 0, 0, $orig_Ancho, $orig_Alto);
        imagecopymerge($this->img, $im_truco, $x, $y, $ancho_img, $alto_img, $orig_Ancho, $orig_Alto, $alpha);
        imagedestroy($importada);
    }

}

/*
  $imagen=new IMG(900,1140,"image/png");
  $imagen->create_color('negro',255, 255, 255);
  $imagen->create_color('amarillo',255, 255,0);
  $imagen->linea(0,0,200,200,"negro");

  $imagen->create_color('alpha',255, 255,0,100);//crear solo antes de utilizar
  $imagen->rectangulo_ex(150, 150, 300, 300, 'alpha');
  $imagen->load_ttf('font','airstrike.ttf');
  $imagen->text_print_ttf(40,20,300,300,'negro','font'," esta es la cadena");
  $imagen->importar_img('2013','../img/2013.png');
  $imagen->print_img_import('2013',300,300,0,0,100,100);
  $imagen->print_img_import_alpha('2013',25,25,0,0,80);
 */
?>