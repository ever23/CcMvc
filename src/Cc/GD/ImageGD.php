<?php

/*
 * Copyright (C) 2016 Enyerber Franco
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  
 */

namespace Cc;

/**
 * Manipulacion de imagenes
 * @autor ENYREBER FRANCO <http://enyerberfranco.com.ve>                                                  
 * @package Cc
 * @subpackage GD 
 * @todo falta mejorar, implementar todos las funciones GD
 */
class ImageGD
{

    /**
     * identificador de imagen
     * @var resourece 
     */
    protected $img;

    /**
     * ancho de la imagen
     * @var  int
     */
    public $w;

    /**
     * alto de la imagen
     * @var int  
     */
    public $h;

    /**
     * extencion de la imagen
     * @var string 
     */
    protected $tipo;

    /**
     * indice de color de fondo de la imagen
     * @var int 
     */
    protected $fondo;

    /**
     * fuentes
     * @var array 
     */
    protected $fuente;

    /**
     * colores
     * @var array 
     */
    protected $colores;

    /**
     *
     * @var array 
     */
    protected $img_import;

    /**
     * color de mascara
     * @var int 
     */
    protected $mask_color;

    /**
     * mime type de la imagen 
     * @var string 
     */
    protected $header;

    /**
     * 
     * @var array 
     */
    public static $heaers;

    /**
     *
     * @var bool 
     */
    public static $Capture = false;

    /**
     * 
     * @param string|int $ancho si es int es el ancho de la imagen que se creara, si es un binario de imagen se cargara la misma, si es el nombre de el archivo de una imagen se cargara
     * @param int $alto alto de la imagen
     * @param string $tipo tipo de imagen
     */
    public function __construct($ancho = NULL, $alto = NULL, $tipo = NULL)
    {
        $this->fuente = NULL;
        $this->colores = array();
        $this->img_import = array();
        $this->fuente = array();
        if (!is_null($ancho))
        {
            if (is_numeric($ancho))
            {
                $this->Create($ancho, $alto, $tipo);
            } elseif (strlen($ancho) <= 300 && (is_file($ancho) || is_readable($ancho)))
            {
                $this->LoadFile($ancho);
            } elseif (is_string($ancho))
            {
                $this->LoadString($ancho);
            }
        }
    }

    /**
     * crea una imagen en blanco
     * @param int $ancho ancho de la imagen
     * @param int $alto alto de la imagen
     * @param string $tipo mime-type de la imagen
     */
    public function Create($ancho, $alto, $tipo)
    {
        $this->w = $ancho;
        $this->h = $alto;

        $this->fuente = NULL;
        $this->colores = array();
        $this->img_import = array();
        $this->fuente = array();

        $this->header = $tipo;
        if ($tipo == "image/x-png" || $tipo == "image/png")
        {
            $this->img = imagecreatetruecolor($ancho, $alto);
            $this->tipo = 'png';
        }
        if ($tipo == "image/pjpeg" || $tipo == "image/jpeg" || $tipo == "image/jpg")
        {
            $this->tipo = 'jpeg';
            $this->img = imagecreatetruecolor($ancho, $alto);
        }
        if ($tipo == "image/gif" || $tipo == "image/gif")
        {
            $this->tipo = 'gif';
            $this->img = imagecreatetruecolor($ancho, $alto);
        }

        $this->fondo = imagecolorallocate($this->img, 255, 255, 255);
        $this->mask_color = imagecolorallocate($this->img, 255, 255, 255);
        //$this->rectangulo_ex(0,0,$this->w,$this->h,imagecolorallocate ($this->img, 255, 0, 255));
        imagefill($this->img, 0, 0, $this->fondo);
        //imagecolortransparent($this->img ,$this->fondo);
    }

    /**
     * Carga una imagen de un archivo
     * @param string $filename nombre del archivo
     * @throws Exception si el archivo no existe o si ocurre un error en la lectura
     */
    public function LoadFile($filename)
    {
        //$tam = getimagesize($filename);
        $data = file_get_contents($filename);
        if ($data !== false)
        {
            $this->LoadString($data);
        } else
        {
            throw new Exception("El archivo '" . $filename . "' no existe");
        }
    }

    /**
     * cargar una imagen de un string
     * @param binary $string imagen
     */
    public function LoadString($string)
    {
        $tam = getimagesizefromstring($string);
        list( $this->w, $this->h ) = $tam;
        $type = $tam['mime'];
        // return var_export($tam, true);
        $this->img = imagecreatefromstring($string);
        $this->header = $type;
        switch ($type)
        {
            case 'image/png':
                $this->tipo = 'png';

                break;
            case 'image/jpg':
            case 'image/jpeg':

                $this->tipo = 'jpeg';
                break;
            case 'image/gif':

                $this->tipo = 'gif';
                break;
        }
        $this->Ini();
    }

    /**
     * inicializacion
     */
    protected function Ini()
    {
        $this->fuente = NULL;
        $this->colores = array();
        $this->img_import = array();
        $this->fuente = array();

        if ($this->tipo == 'png' || $this->tipo == 'gif')
        {
            imagealphablending($this->img, false);
            imagesavealpha($this->img, true);
        }
        $this->fondo = imagecolorallocate($this->img, 255, 255, 255);
        $istransparent = imagecolortransparent($this->img);
        // throw new Exception($istransparent);
        if ($istransparent !== -1)
        {
            $tp = imagecolorsforindex($this->img, $istransparent);
            $this->mask_color = imagecolorallocatealpha($this->img, $tp['red'], $tp['green'], $tp['blue'], $tp['alpha']);
        } else
        {
            $this->mask_color = imagecolorallocatealpha($this->img, 255, 255, 255, 127);
            imagecolortransparent($this->img, $this->mask_color);
        }
        imagealphablending($this->img, true);
    }

    /**
     * Cambia el tipo de imagen
     * @param string $tipo
     */
    public function To($tipo)
    {
        $this->header = $tipo;
        if ($tipo == "image/x-png" || $tipo == "image/png")
        {
            //  $this->img = imagecreatetruecolor($ancho, $alto);
            $this->tipo = 'png';
        }
        if ($tipo == "image/pjpeg" || $tipo == "image/jpeg" || $tipo == "image/jpg")
        {
            $this->tipo = 'jpeg';
            // $this->img = imagecreatetruecolor($ancho, $alto);
        }
        if ($tipo == "image/gif" || $tipo == "image/gif")
        {
            $this->tipo = 'gif';
            //  $this->img = imagecreatetruecolor($ancho, $alto);
        }
    }

    /**
     * Guarda o retorn la imagen
     * @param string $name nombre de laimagen
     * @param string $ouput indica la manera que se retornara la imagen "I" para vaciarla en el buffer de salida, "F" para almacenar la imagen el un archivo, "S" para retornar el binario de la imagen
     * @param array $options parametros de opciones para las funciones gd de salida
     * @return string si ocurre un error retorna false  si $ouput es 'F' retorna el nombre del archivo donde se guardo la imagen, si es 'S' retorna el bunario de la imagen
     */
    public function Output($name = "", $ouput = "I", ...$options)
    {
        //  imageinterlace($this->img, true);
        $image = "image";
        $image.=$this->tipo;

        switch ($ouput)
        {
            case "I":
                {
                    header("Content-type: " . $this->header);
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

    /**
     * Retorna la imaegen codificada a base 64
     * @param $uri indica si retornara con la imaformacion de la imagen lista para ser utilizada en una etiqueta <img/>
     * @param $options opciones segun el tipo de imagen
     * @return string imagen codificada
     */
    public function OutputBase64($uri = false, ...$options)
    {
        if ($uri)
        {
            return 'data:' . $this->header . ';base64,' . base64_encode($this->Output("", 'S', ...$options));
        } else
        {
            return base64_encode($this->Output("", 'S', ...$options));
        }
    }

    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * destrulle la imagen
     */
    public function destroy()
    {

        imagedestroy($this->img);
    }

    /**
     * redimenciona la imagen
     * @param int $nuevo_ancho ancho de la imagen
     * @param int $nuevo_alto alto de la imagen
     */
    public function Resize($nuevo_ancho = NULL, $nuevo_alto = NULL)
    {
        if (is_null($nuevo_ancho) && !is_null($nuevo_alto))
        {
            $proc = ($nuevo_alto * 100) / ( $this->h);
            $nuevo_ancho = ( $this->h * ($proc * 0.01));
        } elseif (is_null($nuevo_alto) && !is_null($nuevo_ancho))
        {
            $proc = ($nuevo_ancho * 100) / ($this->w);
            $nuevo_alto = ($this->w * ($proc * 0.01));
        }
        $temp = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        //imagealphablending($temp, true);
        // imagecolormatch($this->img, $temp);

        if ($this->tipo == 'png' || $this->tipo == 'gif')
        {
            imagealphablending($temp, false);
            imagesavealpha($temp, true);
            imagefill($temp, 0, 0, $this->mask_color);
            imagecolortransparent($temp, $this->mask_color);
        }
        imagecopyresampled($temp, $this->img, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $this->w, $this->h);
        $istransparent = imagecolortransparent($temp);
        if ($istransparent !== -1)
        {
            $tp = imagecolorsforindex($temp, $istransparent);
            $this->mask_color = imagecolorallocatealpha($temp, $tp['red'], $tp['green'], $tp['blue'], $tp['alpha']);
        }
        imagedestroy($this->img);
        unset($this->img);
        $this->img = $temp;
        unset($temp);
        $this->w = $nuevo_ancho;
        $this->h = $nuevo_alto;
    }

    /**
     * establece el color de mascara 
     * @param int|array $color 
     */
    public function ColorMask($color)
    {
        $this->mask_color = $this->Color($color);
        imagecolortransparent($this->img, $this->mask_color);
    }

    /**
     * caraga un archivo ttf para las fuente de texto
     * @param string $name
     * @param string $file_ttf
     * @return bool 
     */
    public function LoadTtf($name, $file_ttf)
    {
        if ($file_ttf != '')
        {
            $this->fuente+=array($name => $file_ttf);
            return true;
        }
        return false;
    }

    /**
     * almacena un  color bajo un nombre
     * @param string $name nombre
     * @param int $R rojo
     * @param int $G verde
     * @param int $B azul
     * @param int $A alpha
     * @return int indice de color
     */
    public function SaveColor($name, $R, $G, $B, $A = 0)
    {
        $this->colores+=array($name => imagecolorallocatealpha($this->img, $R, $G, $B, (int) ($A)));
        return $this->colores[$name];
    }

    /**
     * crea un indice de color 
     * @param int $R rojo
     * @param int $G verde
     * @param int $B azul
     * @param int $A alpha
     * @return int indice de color
     */
    public function Rgba($R, $G, $B, $A = 0)
    {
        return imagecolorallocatealpha($this->img, $R, $G, $B, (int) ($A));
    }

    /**
     * Lleva a cabo un relleno comenzando en la coordenada dada (superior izquierda es 0, 0), con el color dado
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int|array|string $rgb_color color
     */
    public function Fill($x, $y, $rgb_color)
    {
        imagefill($this->img, $x, $y, $this->Color($rgb_color));
    }

    /**
     * Dibuja una línea entre dos puntos dados
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int $w ancho 
     * @param int $h alto
     * @param int|array|string $rgb_color
     */
    public function Linea($x, $y, $w, $h, $rgb_color)
    {
        imageline($this->img, $x, $y, $w, $h, $this->Color($rgb_color));
    }

    /**
     * Dibuja un rectángulo
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int $w ancho 
     * @param int $h alto
     * @param int|array|string $rgb_color
     */
    public function Rectangulo($x, $y, $w, $h, $rgb_color)
    {
        imagerectangle($this->img, $x, $y, $w, $h, $this->Color($rgb_color));
    }

    /**
     * Dibuja un rectangulo con relleno
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int $w ancho 
     * @param int $h alto
     * @param int|array|string $rgb_color
     */
    public function FilleRectangulo($x, $y, $w, $h, $rgb_color)
    {
        imagefilledrectangle($this->img, $x, $y, $w, $h, $this->Color($rgb_color));
    }

    /**
     * imprime un texto en la imagen 
     * @param string $cadena texto a imprimir
     * @param int $tam tamano del texto
     * @param int $angulo angulo
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int|array|string $rgb_color
     * @param string $fuente
     */
    public function TextTtf($cadena, $tam, $angulo, $x, $y, $rgb_color, $fuente = false)
    {
        imagettftext($this->img, $tam, $angulo, $x, $y, $this->Color($rgb_color), $this->Font($fuente), $cadena);
    }

    /**
     * imprime un texto en la imagen 
     * @param string $cadena texto a imprimir
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int|array|string $rgb_color
     * @param string $fuente
     */
    public function Text($cadena, $x, $y, $rgb_color, $fuente = NULL)
    {
        imagestring($this->img, $fuente, $x, $y, $cadena, $this->Color($rgb_color));
    }

    /**
     * Copia y cambia el tamaño de parte de una imagen redimensionándola
     * @param \Cc\ImageGD $img_class objeto de imagen enlace a imagen original
     * @param int $dst_x Coordenada x del punto de la iamgen
     * @param int $dst_y Coordenada y del punto de la iamgen
     * @param int $src_x Coordenada x del punto de origen
     * @param int $src_y Coordenada y del punto de origen
     * @param int $dst_w Ancho del destino
     * @param int $dst_h Alto del destino
     * @param int $src_w Ancho original
     * @param int $src_h Altura original
     * @return bool Devuelve TRUE en caso de éxito o FALSE en caso de error.
     */
    public function CopyResample(ImageGD $img_class, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w = NULL, $src_h = NULL)
    {
        return imagecopyresampled($this->img, $img_class->img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w ? $src_w : $img_class->w, $src_h ? $src_h : $img_class->h);
    }

    /**
     *  Copiar parte de una imagen
     * 
     * @param \Cc\ImageGD $src_im imagen original
     * @param int $dst_x Coordenada x del punto de destino
     * @param int $dst_y Coordenada y del punto de destino
     * @param int $src_x Coordenada x del punto de origen
     * @param int $src_y Coordenada y del punto de origen
     * @param int $src_w Ancho original
     * @param int $src_h Alto original
     * @return bool Devuelve TRUE en caso de éxito o FALSE en caso de error.
     */
    public function Copy(ImageGD $src_im, $dst_x, $dst_y, $src_x = 0, $src_y = 0, $src_w = NULL, $src_h = NULL)
    {
        return imagecopy($this->img, $src_im->img, $dst_x, $dst_y, $src_x, $src_y, $src_w ? $src_w : $src_im->w, $src_h ? $src_h : $src_im->h);
    }

    /**
     * Copia y cambia el tamaño de parte de una imagen
     * @param \Cc\ImageGD $src_image objeto de imagen enlace a imagen original
     * @param int $dst_x Coordenada x del punto de la iamgen
     * @param int $dst_y Coordenada y del punto de la iamgen
     * @param int $src_x Coordenada x del punto de origen
     * @param int $src_y Coordenada y del punto de origen
     * @param int $dst_w Ancho del destino
     * @param int $dst_h Alto del destino
     * @param int $src_w Ancho original
     * @param int $src_h Altura original
     * @return bool Devuelve TRUE en caso de éxito o FALSE en caso de error.
     */
    public function CopyResized(ImageGD $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w = NULL, $src_h = NULL)
    {
        return imagecopyresized($this->img, $src_image->img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w ? $src_w : $src_image->w, $src_h ? $src_h : $src_image->h);
    }

    /**
     * Copiar y fusionar parte de una imagen
     * @param \Cc\ImageGD $src_im objeto de imagen enlace a imagen original
     * @param int $dst_x Coordenada x del punto de la iamgen
     * @param int $dst_y Coordenada y del punto de la iamgen
     * @param int $src_x Coordenada x del punto de origen
     * @param int $src_y Coordenada y del punto de origen
     * @param int $src_w Ancho original
     * @param int $src_h Altura original
     * @param int $pct Las dos imágenes serán fusionadas según pct, cuyo valor puede estar entre 0 y 100. Cuando pct = 0, no se realiza ninguna acción; cuando es 100, esta función se comportará de forma idéntica a imagecopy() para imágenes de paleta, excepto para ignorar componentes alfa, mientras que implementa transparencia alfa para imágenes de color verdadero
     * @return bool Devuelve TRUE en caso de éxito o FALSE en caso de error.
     */
    public function CopyMerge(ImageGD $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        return imagecopymerge($this->img, $src_im->img, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
    }

    /**
     * crea un color y le asigna un nombre
     * @param array $rgb_color debe tener como minimo 3 elementos 0=>rojo 1=>verder 3=>azul
     * @return int
     */
    private function Color($rgb_color)
    {
        if (is_array($rgb_color))
        {
            if (count($rgb_color) == 3)
            {
                list($red, $green, $blue) = $rgb_color;
                return imagecolorallocate($this->img, $red, $green, $blue);
            } else if (count($rgb_color) == 3)
            {
                list($red, $green, $blue, $alpha) = $rgb_color;
                return imagecolorallocatealpha($this->img, $red, $green, $blue, $alpha);
            }
        } elseif (is_int($rgb_color))
        {
            /// $exad = dechex($rgb_color);
            $r = ($rgb_color >> 16) & 0xFF;
            $g = ($rgb_color >> 8) & 0xFF;
            $b = $rgb_color & 0xFF;
            return imagecolorallocate($this->img, $r, $g, $b);
        } elseif (isset($this->colores[$rgb_color]))
        {
            return $this->colores[$rgb_color];
        }
    }

    /**
     * 
     * @param string $fuente
     * @return int
     */
    private function Font($fuente)
    {
        if (isset($this->fuente[$fuente]))
        {
            return $this->fuente[$fuente];
        } else
        {
            return $fuente;
        }
    }

    /**
     * retorna el color de un pixel en rgba
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @return array color rgba 0=>rojo 1=>verder 3=>azul 4=>alpha
     */
    public function GetPixel($x, $y)
    {
        $rgb = imagecolorat($this->img, $x, $y);
        $color = imagecolorsforindex($this->img, $rgb);

        return [$color['red'], $color['green'], $color['blue'], $color['alpha']] + $color;
    }

    /**
     * Dibuja un pixel
     * @param int $x posicion en x
     * @param int $y posicion en y
     * @param int|array|string $color color
     * @return bool 
     */
    public function SetPixel($x, $y, $color)
    {
        return imagesetpixel($this->img, $x, $y, $this->Color($color));
    }

    /**
     * retorna el recurso de la imagen
     * @return Resource
     */
    public function &GetResource()
    {
        return $this->img;
    }

    public function __call($name, $arguments)
    {

        $ImageCallback = 'image' . $name;
        if (isset($arguments[0]) && $arguments[0] instanceof ImageGD)
        {
            $arguments[0] = $arguments[0]->img;
            if (function_exists($ImageCallback))
            {
                return $ImageCallback($this->img, ...$arguments);
            } else
            {
                throw \Exception("La funcion $ImageCallback no existe");
            }
        } else
        {
            if (function_exists($ImageCallback))
            {
                return $ImageCallback(...$arguments);
            } else
            {
                throw \Exception("La funcion $ImageCallback no existe");
            }
        }
    }

    /**
     * inicia el capturado de una imagen en el buffer
     */
    public static function BeginCaptured()
    {
        self::$heaers = headers_list();
        self::$Capture = true;
        ob_start();
    }

    /**
     * captura una imagen desde el buffer y crea un objeto ImageGD
     * @return \Cc\ImageGD
     * @throws \Exception
     */
    public static function EndCaptured()
    {
        if (!self::$Capture)
        {
            throw \Exception("EL capturado no se ha iniciado");
        }
        self::$Capture = false;

        foreach (headers_list() as $h)
        {
            $ex = explode(':', $h);
            header_remove(trim($ex[0]));
        }
        foreach (self::$heaers as $h)
        {
            header($h);
        }
        $img = ob_get_contents();
        ob_end_clean();
        return new ImageGD($img);
    }

    public function PrintBinary(array $frame, $pixelPerPoint, $imgX, $imgY, $imgW, $imgH, $color = [0, 0, 0], $fondo = [255, 255, 255])
    {
        $len = count($frame);
        foreach ($frame as &$frameLine)
        {

            for ($i = 0; $i < $len; $i++)
            {
                $frameLine[$i] = (ord($frameLine[$i]) & 1) ? '1' : '0';
            }
        }
        $h = count($frame);
        $w = strlen($frame[0]);

        //$imgW = $w + 2 * $outerFrame;
        //$imgH = $h + 2 * $outerFrame;

        $base_image = new ImageGD($h + 6, $w + 6, 'image/png');

        if (is_null($fondo))
        {
            $base_image->ColorMask([255, 255, 255]);
            $base_image->Fill(0, 0, [255, 255, 255]);
        } else
        {
            $base_image->Fill(0, 0, $fondo);
        }
        /* $col[0] = ImageColorAllocate($base_image, 255, 255, 255);
          $col[1] = ImageColorAllocate($base_image, 0, 0, 0);

          imagefill($base_image, 0, 0, $col[0]); */

        for ($y = 0; $y < $h; $y++)
        {
            for ($x = 0; $x < $w; $x++)
            {
                $f = $frame[$y];

                if ($frame[$y][$x] == '1')
                {
                    // var_dump($f[$x]);
                    $base_image->SetPixel($x + 4, $y + 4, $color);
                    //ImageSetPixel($base_image, $x + $outerFrame, $y + $outerFrame, $col[1]);
                }
            }
        }
        //  $base_image->Output('n.png', "F");
        // var_dump($imgW * $pixelPerPoint);
        $Qr = new ImageGD($imgW * $pixelPerPoint, $imgH * $pixelPerPoint, 'image/png');
        $Qr->CopyResized($base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $w + 6, $h + 6);
        //$Qr->Output('n.png', "F");

        $this->CopyResized($Qr, $imgX, $imgY, 0, 0, $imgW, $imgH);
        unset($Qr, $base_image);

        // $this->Copy($base_image, $imgX, $imgY, 0, 0, $imgW, $imgH);
    }

}
