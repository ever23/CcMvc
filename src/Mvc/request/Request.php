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
 */

namespace Cc\Mvc;

use Cc\FilterXss;
use Cc\Mvc;
use Cc\UrlManager;

/**
 * procesa las variables _GET y _POST ademas recibe contenido post en formato json y lo procesa 
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package CcMvc
 * @subpackage Request
 */
class Request implements \ArrayAccess, \Countable, \IteratorAggregate
{

    public $Get = [];
    public $Post = [];
    public $Files = [];
    public $Cookie = [];
    protected $uri = '';
    protected $OrigGET = [];

    public function __construct()
    {
        $this->OrigGET = $_GET;
        $this->Get = &$_GET;
        $this->Post = & $_POST;
        //$this->Cookie = &$_COOKIE;
        $this->uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
//$_POST=  $this->Post;
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $this->ReadPost();
        } else
        {
            $this->Post = FilterXss::FilterXssArray($_POST, FilterXss::FilterXssPost);
        }
        $this->Get = FilterXss::FilterXssArray($_GET, FilterXss::FilterXssGet);
        $this->Cookie = new Cookie(Mvc::Config());
    }

    /**
     *  @access private
     * @return type
     */
    public function __debugInfo()
    {
        return ['Get' => $this->Get, 'Post' => $this->Post];
    }

    /**
     * 
     */
    protected function ReadPost()
    {
        if (isset($_SERVER['CONTENT_TYPE']))
        {

            $ex = explode(";", $_SERVER['CONTENT_TYPE']);

            switch (trim($ex[0]))
            {
                case 'application/x-www-form-urlencoded':
                case 'multipart/form-data':
                    $this->Post = FilterXss::FilterXssArray($_POST, FilterXss::FilterXssPost);
                    break;
                case 'application/json':
                    $Json = new \Cc\Json();
                    //var_dump($Json->Get());
                    // exit;

                    $Json->CreateJson(FilterXss::FilterXssArray(json_decode(file_get_contents('php://input'), true), FilterXss::FilterXssPost));
                    $this->Post = &$Json;
                    break;

                default :
                    $post = [];

                    parse_str(urldecode(file_get_contents('php://input')), $post);
                    $this->Post = FilterXss::FilterXssArray($post, FilterXss::FilterXssPost);
            }
        } else
        {
            $this->Post = FilterXss::FilterXssArray($_POST, FilterXss::FilterXssPost);
        }
    }

    public function ContenType()
    {
        return isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : NULL;
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function Path()
    {
        return Mvc::App()->Config()->Router['DocumentRoot'] . Mvc::App()->Router->GetPath();
    }

    public function BasePath()
    {
        return Mvc::App()->Router->GetPath();
    }

    public function Base()
    {
        return UrlManager::BuildUrl($this->Protocolo(), $this->Host(), Mvc::App()->Config()->Router['DocumentRoot'], '');
    }

    public function RouteUrl($id, $params)
    {
        $url = RouteByMatch::ResolveUrl($id, $params);
        if ($url == false)
        {
            return Controllers::Href($id, $params);
        }
        return UrlManager::BuildUrl($this->Protocolo(), $this->Host(), Mvc::App()->Config()->Router['DocumentRoot'], $url);
    }

    public function Query()
    {
        return parse_url($this->uri, PHP_URL_QUERY);
    }

    public function Protocolo()
    {
        return Mvc::APP()->Config()->Router['protocol'];
    }

    public function AcceptEncoding($encoding = NULL)
    {
        if (is_null($encoding))
        {
            return isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : NULL;
        }
        $cod = explode(',', sset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '');
        return in_array($encoding, $cod);
    }

    public function HttpAccept($accept = NULL)
    {
        if (is_null($accept))
        {
            return isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : NULL;
        }
        $h = explode(',', isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '');
        return in_array($accept, $h);
    }

    public function url()
    {
        $url = UrlManager::BuildUrl($this->Protocolo(), $this->Host(), Mvc::App()->Config()->Router['DocumentRoot'], Mvc::App()->Router->GetPath());
        return UrlManager::Href($url, $this->OrigGET);
    }

    public function Uri()
    {
        return $this->uri;
    }

    public function Referer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
    }

    public function Host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function Fragment()
    {
        return parse_url($this->uri, PHP_URL_FRAGMENT);
    }

    public function Get($ind, $filter = FILTER_DEFAULT, $option = NULL)
    {
        return filter_input(INPUT_GET, $ind, $filter, $option);
    }

    public function Post($ind, $filter = FILTER_DEFAULT, $option = NULL)
    {
        return filter_input(INPUT_POST, $ind, $filter, $option);
    }

    /**
     * filtra una variable get o post
     * @param string $offset
     * @param int $type
     * @return mixes
     * @uses filter_var()
     */
    public function Filter($offset, $type = FILTER_DEFAULT)
    {
        return filter_var($this->offsetGet($offset), $type);
    }

    /**
     * @access private
     * @param type $offset
     * @return type
     */
    public function offsetExists($offset)
    {
        return isset($this->Post[$offset]) || isset($this->Get[$offset]);
    }

    /**
     * @access private
     * 
     * @param type $offset
     * @return type
     */
    public function offsetGet($offset)
    {
        if (isset($this->Post[$offset]))
        {
            return $this->Post[$offset];
        } elseif (isset($this->Get[$offset]))
        {
            return $this->Get[$offset];
        } else
        {
            ErrorHandle::Notice(" Undefined index: " . $offset);
        }
    }

    /**
     * @access private
     * 
     * @param type $offset
     * @param type $value
     */
    public function offsetSet($offset, $value)
    {

        $this->Post[$offset] = $value;

        $this->Get[$offset] = $value;
    }

    /**
     * @access private
     * 
     * @param type $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->Get[$offset]);
        unset($this->Post[$offset]);
    }

    /**
     * @access private
     * 
     * @return type
     */
    public function count()
    {
        return count(array_merge($this->Get, $this->Post));
    }

    /**
     * @access private
     * 
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_merge($this->Get, $this->Post));
    }

}
