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

namespace Cc\Mvc;

/**
 * Envio de correos electronicos implementando os views y layauts para enviar html 
 *
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Mail
 */
class Mailer extends \PHPMailer
{

    /**
     * cargador de views
     * @var ViewController 
     */
    public $view;

    /**
     * cargador de layauts 
     * @var LayautManager 
     */
    public $layaut;

    /**
     * manejador de html 
     * @var Html 
     */
    public $html;

    /**
     * buffer para los views 
     * @var string 
     */
    private $BufferView = '';

    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
        $conf = Mvc::Config();

        $this->layaut = new LayautManager();
        $this->html = new Mailer\Html();
        $this->view = new Mailer\MailerView($conf->App['view'], $this->html);
        $this->DirLayaut = $conf->App['layauts'];
        $this->html->SetLayaut(NULL, $conf->App['layauts']);
        $this->BufferView = '';
    }

    /**
     * Crea el mensaje y lo envia 
     * Uses the sending method specified by $Mailer.
     * @throws \phpmailerException
     * @return boolean false on error - See the ErrorInfo property for details of the error.
     */
    public function send()
    {
        $layaut = $this->html->GetLayaut();
        if (isset($layaut['Layaut']) && !is_null($layaut['Layaut']))
        {
            $this->isHTML(true);
            $this->Body = $this->ExecuteLayaut();
        } else
        {
            if ($this->isHTML())
            {
                $this->Body.=$this->BufferView;
            }
        }
        parent::send();
    }

    /**
     * layaut
     * @param string $layaut
     * @param string $dir
     */
    public function SetLayaut($layaut, $dir = NULL)
    {
        $this->html->SetLayaut($layaut, $dir);
    }

    /**
     * carga una plantilla view
     * @param string $view
     * @param array $agrs
     * @return string
     */
    public function LoadView($view, $agrs = [])
    {
        $this->view->ObjResponse = $this->html;
        $b = $this->view->Fetch($view, $agrs);
        $this->BufferView.=$b;
        return $b;
    }

    private function ExecuteLayaut()
    {
        $function = \Closure::bind(function( $content, $LayautController)
                {
                    $layaut = $this->GetLayaut();

                    //$__name = ($layaut['Dir'] . $layaut['Layaut'] . '.php');
                    $__name = ($layaut['Dir'] . $layaut['Layaut']);

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

        return $function($this->BufferView, $this->layaut);
    }

}
