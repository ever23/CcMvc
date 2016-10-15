<?php

include ("/../../CcMvc.php");
$config = dirname(__FILE__) . "/protected/config/configuracion.php";
CcMvc::Start($config, "Chat WebSocket")->Run();
/**
 * para lanzar el servidor web socket se deve ejecutar en la linea de comandos el archivo WebSocket/WsServer.php
 * ejemplo
 * C:/xampp/php/php -q C:/xampp/htdocs/websocket/WebSocket/WsServer.php
 * antes de iniciar el chat
 */



