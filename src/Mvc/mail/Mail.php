<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Description of Mail
 *
 * @author Equipo
 */
class Mail extends \Cc\Mail
{

    protected $namelayaut;
    protected $DirLayaut;

    /**
     *
     * @var ViewController 
     */
    public $view;

    /**
     *
     * @var type 
     */
    public $layaut;

    /**
     *
     * @var Html 
     */
    public $html;
    private $BufferView = '';

    public function __construct()
    {
        $conf = Mvc::Config();
        parent::__construct(Mvc::Config());
        $this->view = new ViewController($conf->App['view']);
        $this->layaut = new LayautManager();
        $class = $conf->Response['Accept']['text/html']['class'];
        $param = $conf->Response['Accept']['text/html']['param'];
        $this->html = new $class(...$param);
        $this->html->SetLayaut('mail', $conf->App['layauts']);
        $this->BufferView = '';
    }

    public function Titulo($title)
    {
        $this->html->titulo = $title;
        parent::Titulo($title);
    }

    public function SetLayaut($layaut, $dir = NULL)
    {
        $this->html->SetLayaut($layaut, $dir);
    }

    public function LoadView($view, $agrs = [])
    {
        ob_start();
        $this->view->ObjResponse = $this->html;
        $this->view->Load($view, $agrs);
        $b = ob_get_contents();
        ob_end_clean();
        $this->BufferView.=$b;
        return $b;
    }

    public function SendHtml()
    {

        $function = \Closure::bind(function( $content, $LayautController)
                {
                    $layaut = $this->GetLayaut();

                    $__name = ($layaut['Dir'] . $layaut['Layaut'] . '.php');
                    if (is_null($layaut['Layaut']) || $layaut['Layaut'] == '')
                        return $content;
                    if (is_file($__name))
                    {

                        try
                        {
                            ob_start();
                            if (isset($layaut['params']))
                            {
                                extract($layaut['params']);
                            }
                            extract($LayautController->jsonSerialize());
                            $this->conten = &$content;


                            include($__name);
                            $buffer = ob_get_contents();

                            ob_end_clean();


                            return $buffer;
                        } catch (LayautException $ex)
                        {
                            throw $ex;
                        }
                    } else
                    {
                        throw new LayautException("EL LAYAUT " . $__name . " NO EXISTE ");
                    }
                }, $this->html, get_class($this->html));

        $text = $function($this->BufferView, $this->layaut);

        $this->Header('MIME-Version', '1.0');
        $this->Header("Content-type", 'text/html; charset=utf-8');
        $this->Mensaje($text);

        return $this->Send();
    }

}
