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

use Cc\Mvc\ViewController;

/**
 * Cargador de vistas para mailer 
 *
 * @author Enyerber Franco 
 * @package CcMvc
 * @subpackage Mail
 */
class MailerView extends ViewController
{

    protected $htmlMail;

    public function __construct($dir = NULL, HtmlMail &$htmlMail)
    {
        parent::__construct($dir);
        $this->htmlMail = &$htmlMail;
    }

    /**
     * 
     */
    private function LoadView($dir, $view, array ...$agrs)
    {
        // $view = ValidFilename::ValidName($view, true);

        foreach ($agrs as &$_________agrs)
        {
            foreach ($_________agrs as $_i => &$_v)
            {
                $this->ViewVars[$_i] = &$_v;
            }
        }
        unset($_________agrs, $_i, $_v);
        if (!isset($this->ViewVars['ObjResponse']))
            $this->ViewVars['ObjResponse'] = &$this->htmlMail;

        if (preg_match('/\.\.\//', $view))
        {
            throw new TemplateException("EL NOMBRE DEL VIEW " . $view . " NO ES VALIDO");
        }
        if ((strpos($view, ':') !== false))
        {
            $this->_include($view);
        } elseif (file_exists($dir . $view . '.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext']))
        {
            $this->ViewVars['ViewName'] = $view;
            $this->_include($dir . $view . '.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext']);
        } elseif (is_dir($dir . $view) && file_exists($dir . $view . 'index.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext']))
        {
            $this->ViewVars['ViewName'] = $view . 'index';
            $this->_include($dir . $view . 'index.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext']);
        } else
        {
            $this->_include($dir . $view);
        }
    }

    /**
     * 
     */
    private function FetchView($dir, $view, array ...$agrs)
    {
        // $view = ValidFilename::ValidName($view, true);

        foreach ($agrs as &$_________agrs)
        {
            foreach ($_________agrs as $_i => &$_v)
            {
                $this->ViewVars[$_i] = &$_v;
            }
        }
        unset($_________agrs, $_i, $_v);
        if (!isset($this->ViewVars['ObjResponse']))
            $this->ViewVars['ObjResponse'] = &$this->htmlMail;

        if (preg_match('/\.\.\//', $view))
        {
            throw new TemplateException("EL NOMBRE DEL VIEW " . $view . " NO ES VALIDO");
        }
        if ((strpos($view, ':') !== false))
        {
            return $this->_include($view, true);
        } elseif (file_exists($dir . $view . '.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext']))
        {
            $this->ViewVars['ViewName'] = $view;
            return $this->_include($dir . $view . '.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext'], true);
        } elseif (is_dir($dir . $view) && file_exists($dir . $view . 'index.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext']))
        {
            $this->ViewVars['ViewName'] = $view . 'index';
            return $this->_include($dir . $view . 'index.' . Mvc::App()->Config()->TemplateLoaders['Default']['ext'], true);
        } else
        {
            return $this->_include($dir . $view, true);
        }
    }

}
