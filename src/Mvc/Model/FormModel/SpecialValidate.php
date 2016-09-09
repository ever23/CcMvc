<?php

/*
 * Copyright (C) 2016 Enyerber Franco
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
  A.
 */

namespace Cc\Mvc;

class ValidFileUpload extends \Cc\ValidDependence
{

    public function __construct($value, $option = array())
    {
        $option['StrictValue'] = true;
        parent::__construct($value, $option);
    }

    public function __toString()
    {
        return $this->value;
    }

    public function Validate(&$value)
    {
        $file = NULL;
        if (isset($this->option['name']))
        {
            $file = new PostFiles($this->option['name']);
            if ($file->is_Uploaded())
            {
                if (isset($this->option['ext']))
                {
                    $ext = explode(',', $this->option['ext']);
                    if (!in_array($file->getExtension(), $ext))
                    {
                        return NULL;
                    }
                }
                return $file;
            }
        }
    }

}
