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

namespace Cc;

/**
 * Esta interface puede ser implementadas por clases que nesiten ser inyectadas automaticamente
 * @autor ENYREBER FRANCO <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>  
 * @package Cc
 * @subpackage Dependencias
 * 
 */
interface Inyectable
{

    /**
     * 
     * DEVE RETORNAR UN ARRAY CON LOS VALORES DE LOS PARAMETROS DEL CONSTRUCTOR
     * SE PUEDEN PASAR LOS SIGUIENTES ALIAS
     * POR DEFECTO:
     * {--DependenceInyector--} sera sustituido por el objeto {@link DependenceInyector} que los instancio 
     * {name_param} sera reemplazado por el nombre del parametro que lo requiere 
     * <pre>
     * EN CASO DE QUE LA CLASE SEA INSTANCEADA EN EL CONTEXTO DE UNA APLICACION CcMvc LOS ALIAS DISPONIBLES SERAN:
     * {DB} sera sustituido por una referencia al objeto de bases de datos {@link iDataBase}
     * {Response} sera reemplazado por una referencia al objeto manejador de contenido {@link ResponseConten}
     * {Autenticate} sera reemplazado por una instancia de el objeto de autenticacion {@link Autenticate}
     * {config} sera reemplazado por una referencia de el objeto de configuracion {@link Config}
     * {SelectorControllers} una referencia al objeto Selector Controller que instanceo el controlador {@link SelectorControllers}
     * 
     * EN CASO DE QUE LA CLASE SEA INSTANCEADA EN EL CONTEXTO DE UNA APLICACION CcWs LOS ALIAS DISPONIBLES SERAN:
     * {DB} si un parámetro contiene este seudónimo será reemplazado por el objeto creado para manejar bases de datos {@link iDataBase} 
     * {config} este parametro sera reemplazado por el array de configuracion {@link Config}
     * {cliente} el objeto WsClient relacionado con el cliente que se esta procesando actualmente {@link WsClient}
     * {messaje} solo estará disponible cuando se inyecte el el método OnMessaje contendrá el mensaje recibido desde el cliente
     * {messajeLenght} solo estará disponible cuando se inyecte el el método OnMessaje contendrá el tamaño de bytes del mensaje
     * {binary} solo estará disponible cuando se inyecte el el método OnMessaje contendrá un valor booleano que indicara si el contenido del mensaje es binario o  no
     * </pre>
     * <code>
     * class miInyectable implements Inyectable
     * {
     *      public static function CtorParam()
     *      {
     *          return ['{DB}','{Config}','{name_param}'];
     *      }
     *      public function __construct(iDataBase $b,Config $conf,$param)
     *      {   
     *          echo $param;
     *          /// mas codigo ....
     *          .
     *          .
     *          .
     *                  
     *      }
     * } 
     * </code>
     * @global string $name_param nombre de paramentro
     * @return array 
     */
    public static function CtorParam();
}
