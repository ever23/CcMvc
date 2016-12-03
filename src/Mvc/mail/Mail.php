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

use Cc\Mvc;

/**
 * @package CcMvc
 * @subpackage Validacion
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

                    //$__name = ($layaut['Dir'] . $layaut['Layaut'] . '.php');
                    $__name = ($layaut['Dir'] . $layaut['Layaut']);
                    if (!file_exists($__name))
                    {
                        $__name.='.php';
                    }
                    if ((strpos($layaut['Layaut'], ':') !== false))
                    {
                        $__name.=$layaut['Layaut'];
                    }
                    if (is_null($layaut['Layaut']) || $layaut['Layaut'] == '')
                        return $content;

                    try
                    {
                        $param = ['content' => $content] + $LayautController->jsonSerialize();
                        if (isset($layaut['params']))
                        {
                            $param+=$layaut['params'];
                        }
                        $loader = new TemplateLoad(Mvc::App()->Config());
                        return $loader->Fetch($this, $__name, $param);
                    } catch (LayautException $ex)
                    {
                        throw $ex;
                    } catch (TemplateException $ex)
                    {
                        throw new LayautException("EL LAYAUT " . $__name . " NO EXISTE ");
                    } catch (Exception $ex)
                    {
                        throw $ex;
                    }
                }, $this->html, get_class($this->html));

        $text = $function($this->BufferView, $this->layaut);

        $this->Header('MIME-Version', '1.0');
        $this->Header("Content-type", 'text/html; charset=utf-8');
        $this->Mensaje($text);

        return $this->Send();
    }

}
