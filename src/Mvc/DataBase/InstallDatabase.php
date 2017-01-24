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

use Cc\Mvc;

/**
 * Instala la base de datos desde los modelos de tablas 
 * cuando se usa PDO
 * @author ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package CcMvc  
 * @subpackage Hook
 */
class InstallDatabase extends AbstractHook
{

    /**
     * 
     */
    public function FailConetDatabase()
    {
        $class = new \ReflectionClass(Mvc::App()->Config()->DB['class']);
        $params = Mvc::App()->Config()->DB['param'];

        if ($class->name == PDO::class || $class->isSubclassOf(\Cc\PDO::class))
        {

            $dsn = $params[0];
            if (preg_match('/(dbname=.*;)|(DATABASE=.*;)/i', $params[0], $m))
            {
                $dbname = substr(preg_replace('/(dbname=)|(DATABASE=)/i', '', $m[0]), 0, -1);
                $params[0] = preg_replace('/dbname=' . $dbname . ';|dbname=' . $dbname . '/i', '', $params[0]);
                try
                {
                    $classname = $class->name;

                    $db = new $classname(...$params);
                    $db->query("create database " . $dbname);
                    if (!$db->error())
                    {

                        ob_start();
                        Mvc::App()->ConetDataBase();
                        try
                        {
                            $console = new Console\Migracion();
                            $console->DatabaseFromModel();
                        } catch (\Exception $ex)
                        {
                            
                        }
                        if (!Mvc::App()->IsConsole())
                        {
                            ob_end_clean();
                        } else
                        {
                            ob_end_flush();
                        }
                    }
                } catch (Exception $ex)
                {
                    
                }
            }
        }
    }

}
