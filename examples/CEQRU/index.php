<?php

include ("/../../CcMvc.php");
$config = dirname(__FILE__) . "/protected/config/configuracion.php";
$name = "Rafael Quevedo Urbina";
CcMvc::Start($config, $name)->Run();



