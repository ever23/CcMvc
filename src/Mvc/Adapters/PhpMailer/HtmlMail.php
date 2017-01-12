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

namespace Cc\Mvc\Mailer;

/**
 * Manejador de html para Miler 
 *
 * @author Enyerber Franco 
 * @package CcMvc
 * @subpackage Mail
 */
class Html extends \Cc\Mvc\Html
{

    /**
     * 
     * @param bool $compress indica si se comprimira el resultado
     * @param bool $min indica si se procesara con MinScript
     */
    public function __construct()
    {

        $this->foother = false;
        $this->errores = '';
        $this->AppConfig = Mvc::App()->Config();
        $this->ROOT_HTML = $this->AppConfig['Router']['DocumentRoot'];
        if ($this->ROOT_HTML[0] != '/')
        {
            $this->ROOT_HTML = '/' . $this->ROOT_HTML;
        }
        $this->titulo = &Mvc::App()->Name;
        $this->SetSrc("{root}", $this->ROOT_HTML);
        $this->SetSrc("{src}", 'src/');
        $this->script_error = "";
        $this->BasePath = UrlManager::BuildUrl($this->AppConfig['Router']['protocol'], $_SERVER['HTTP_HOST'], $this->ROOT_HTML);
        $this->MetaTang = $this->AppConfig->SEO['MetaTang'];
        $this->KeyWords = $this->AppConfig->SEO['keywords'];
        $this->http_equiv = $this->AppConfig->SEO['HttpEquiv'] + ['Content-Type' => 'text/html; charset=UTF-8'];
        $this->layaut = NULL;
        $this->DirLayaut = NULL;
    }

}
