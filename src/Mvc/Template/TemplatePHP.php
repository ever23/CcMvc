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

/**
 * cargador de plantillas php
 *
 * @author Enyerber Franco
 * @package CcMvc  
 * @subpackage Template
 */
class TemplatePHP implements TemplateLoader
{

    public function Fetch(&$context, $file, array $agrs)
    {
        ob_start();
        $this->Load($context, $file, $agrs);
        $conten = ob_get_contents();
        ob_end_clean();
        return $conten;
    }

    public function Load(&$context, $file, array $agrs)
    {
        if (!file_exists($file) && ($t = strpos($file, ':')) !== false)
        {
            $file = new \SplFileInfo(substr($file, $t + 1));
            if (!$file->isFile())
                throw new TempleteLoaderException("El archivo " . $file->__toString() . " no existe");
        }

        $function = \Closure::bind(function($__agrs, $__file)
                {
                    extract($__agrs);
                    include ($__file);
                }, $context, get_class($context));
        $function($agrs, $file);
    }

//put your code here
}
