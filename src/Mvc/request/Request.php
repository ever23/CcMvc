<?php

namespace Cc\Mvc;

use Cc\FilterXss;

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

    public function __construct()
    {
        $this->Get = &$_GET;
        $this->Post = & $_POST;
        //$_POST=  $this->Post;
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && !$_POST && !is_array($_POST))
        {
            $this->ReadPost();
        } else
        {
            $this->Post = FilterXss::FilterXssArray($_POST, FilterXss::FilterXssPost);
        }
        $this->Get = FilterXss::FilterXssArray($_GET, FilterXss::FilterXssGet);
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
                    $Json = new \Cc\Json(file_get_contents('php://input'));
                    $Json->CreateJson(FilterXss::FilterXssArray($Json->Get(), FilterXss::FilterXssPost));
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
