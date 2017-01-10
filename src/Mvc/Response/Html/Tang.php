<?php

/*
 * Copyright (C) 2017 Enyerber Franco
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
 */

namespace Cc\Mvc\Html;

/**
 * Description of Tang
 *
 * @author Enyerber Franco
 */
trait Tang
{

    /**
     * CODIFICA LOS UNA CADENA HTML
     * @param string $text
     * @return string
     */
    public static function Encode($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'utf-8');
    }

    /**
     * DECODIFICA UNA CADENA HTML
     * @param string $text
     * @return string
     */
    public static function Decode($text)
    {
        return htmlspecialchars_decode($text, ENT_QUOTES, 'utf-8');
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    public static function EncodeArray($data)
    {
        $d = array();
        foreach ($data as $key => $value)
        {
            if (is_string($key))
                $key = htmlspecialchars($key, ENT_QUOTES);
            if (is_string($value))
                $value = htmlspecialchars($value, ENT_QUOTES);
            elseif (is_array($value))
                $value = self::EncodeArray($value);
            $d[$key] = $value;
        }
        return $d;
    }

    /**
     * FUNCION MAGICA SE EJECUTA CADA VEZ QUE ES LLAMADO UN METODO ESTATICO 
     * QUE NO ESTE DEFINIDO 
     * EN ESTA CLASE ESTA DEFINIDO PARA CREAR ETIQUETAS HTML 
     * <code><?php
     * echo Html::h1('hola mundo');// <h1>hola mundo</h1>
     * 
     * echo Html::div('un texto',['class'=>'texto']);// <div class='texto'>un texto</h1> 
     * 
     * </code>
     * @param string $name NOMBRE DE LA ETIQUETA 
     * @param array $arguments ARGUMENTOS $text y $attrs DE {@link Tang} 
     * @return string
     * 
     */
    public static function __callStatic($name, array $arguments)
    {
        $arguments[0] = isset($arguments[0]) ? $arguments[0] : '';
        $arguments[1] = isset($arguments[1]) ? $arguments[1] : [];

        return self::Tang($name, $arguments[0], $arguments[1]);
    }

    /**
     * CREA UNA ETIQUETA HTML 
     * @param string $name NOMBRE DE LA ETIQUETA 
     * @param string|array $text TEXTO DE LA ETIQUETA SI ES UN ARRAY SE TOMA CON EL PARAMETRO $attrs
     * @param array $attrs ATRIBUTOS DE LA ETIQUETA [attr=>value]
     * @return string
     */
    public static function Tang($name, $text = '', $attrs = NULL)
    {
        if (is_array($text))
        {
            return self::OpenTang($name, $text, true);
        } else
        {
            return self::OpenTang($name, $attrs) . $text . self::CloseTang($name);
        }
    }

    /**
     * CREA UNA ETIQUETA DE APERTURA HTML
     * @param string $name NOMBRE DE LA ETIQUETA 
     * @param array $attrs ATRIBUTOS DE LA ETIQUETA [attr=>value]
     * @param bool $close INDICA SI LA ETIQUETA ES DE AUTOCERRADO EJEMPLO <imput />,<img />,<link /> true SI ES UNA ETIQUETA DE AUTOCERRADO Y false SI NO
     * @return string
     */
    public static function OpenTang($name, array $attrs = [], $close = false)
    {
        return "<" . $name . " " . self::ConvertAttrs($attrs) . ($close ? '/' : '') . ">";
    }

    /**
     * CREA UNA ETIQUETA DE CIERRE 
     * @param string $name NOMBRE DE LA ETIQUETA 
     * @return string
     *
     */
    public static function CloseTang($name)
    {
        return "</" . $name . ">";
    }

    /**
     * CONVIERTE UN ARRAY EN ATRIBUTOS HTML
     * @param array $attrs
     * @return string
     *  
     */
    private static function ConvertAttrs(array $attrs)
    {
        $specialAttributes = [
            'autofocus' => 1,
            'autoplay' => 1,
            'async' => 1,
            'checked' => 1,
            'controls' => 1,
            'declare' => 1,
            'default' => 1,
            'defer' => 1,
            'disabled' => 1,
            'formnovalidate' => 1,
            'hidden' => 1,
            'ismap' => 1,
            'itemscope' => 1,
            'loop' => 1,
            'multiple' => 1,
            'muted' => 1,
            'nohref' => 1,
            'noresize' => 1,
            'novalidate' => 1,
            'open' => 1,
            'readonly' => 1,
            'required' => 1,
            'reversed' => 1,
            'scoped' => 1,
            'seamless' => 1,
            'selected' => 1,
            'typemustmatch' => 1,
        ];
        $buffer = '';
        $encode = true;
        if (isset($attrs['encode']))
        {
            $encode = $attrs['encode'];
            unset($attrs['encode']);
        }
        foreach ($attrs as $i => $v)
        {
            if (isset($specialAttributes[$i]))
            {
                if ($v === false && $i === 'async')
                {
                    $buffer .= ' ' . $i . '="false"';
                } elseif ($v)
                {
                    $buffer .= ' ' . $i;
                }
            } elseif ($v !== null)
            {

                if ($encode && $i != 'src' && $i != 'href')
                {
                    $buffer .= " " . $i . "='" . self::Encode($v) . "'";
                } else
                {
                    $buffer .= " " . $i . "='" . $v . "'";
                }
            }
        }
        return $buffer;
    }

    /**
     * ETIQUETAS ESPECIALES 
     */

    /**
     * CREA UN ETIQUETA SELECT 
     * 
     * @param array $attrs ATRIBUTOS DE LA ETIQUETA SELECT [attr=>value]
     * @param array|\Traversable $options ATRIBUTOS DE LAS ETIQUETAS OPTION  
     * <code>
     * [
     *      0=>[attr=>value,..],
     *      1=>[attr=>value,..],
     *      .
     *      .
     *      N=>[attr=>value,..]
     * ]</code>
     * @return string
     */
    public static function select($attrs = [], $options = [])
    {

        $tang = self::OpenTang('select', $attrs);

        foreach ($options as $i => $v)
        {
            $text = '';
            if (is_array($v) || $v instanceof \Traversable)
            {
                $atr = [];
                foreach ($v as $i => $v)
                    $atr[$i] = $v;
                if (isset($atr['text']))
                {
                    $text = $atr['text'];
                    unset($atr['text']);
                }
                if (!isset($atr['value']))
                    $atr['value'] = $i;
                if (isset($attrs['value']) && $attrs['value'] == $atr['value'])
                {
                    $atr['selected'] = true;
                }

                $tang.=self::Tang('option', $text, $atr);
            } elseif (is_numeric($i))
            {
                $atr = ['value' => $v];
                if (isset($attrs['value']) && $attrs['value'] == $atr['value'])
                {
                    $atr['selected'] = true;
                }
                $tang.=self::Tang('option', $v, $atr);
            } else
            {
                $atr = ['value' => $i];
                if (isset($attrs['value']) && $attrs['value'] == $atr['value'])
                {
                    $atr['selected'] = true;
                }
                $tang.=self::Tang('option', $i, $atr);
            }
        }
        $tang.=self::CloseTang('select');
        return $tang;
    }

    /**
     * CREA UNA ETIQUETA SCRIPT
     * @param array $text 
     * @param array $attrs
     * @return string
     *
     */
    public static function script($text = '', $attrs = [], $noCDATA = true)
    {
        $attrs = $attrs + ['type' => 'text/javascript'];
        if (is_string($text) && $text != '')
        {
            if ($noCDATA)
            {
                return self::Tang('script', self::cdata($text, true), $attrs);
            } else
            {
                return self::Tang('script', $text, $attrs);
            }
        } elseif (is_array($text))
        {
            return self::Tang('script', "", $text + ['type' => 'text/javascript']);
        } else
        {
            return self::Tang('script', $text, $attrs);
        }
    }

    /**
     * CREA UNA ETIQUETA STYLE
     * @param array $text 
     * @param array $attrs
     * @return string
     */
    public static function style($text = '', $attrs = [])
    {
        $attrs = $attrs + ['type' => 'text/css'];
        if (isset($attrs['src']))
        {
            $attrs+=[ 'rel' => "stylesheet"];
        }
        if (is_string($text) && $text != '')
        {
            return self::Tang('style', self::cdata($text, true), $attrs);
        } else
        {
            return self::Tang('style', $text, $attrs);
        }
    }

    /**
     * CREA UNA SECCION CDATA
     * @param string $text 
     * @param bool $comment INDICA SI SE COMENTARA 
     * @return string
     */
    public static function cdata($text, $comment = false)
    {
        if ($comment)
        {
            return '/*<![CDATA[*/' . $text . '/*]]>*/';
        } else
        {
            return '<![CDATA[' . $text . ']]>';
        }
    }

    public static function meta($attrs)
    {
        return self::OpenTang('meta', $attrs);
    }

}
