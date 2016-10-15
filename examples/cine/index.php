<?php

/**
 * si tiene dudas sobre el codigo visite {@link  http:/ccmvc.890m.com}
 * el sitio del framework CcMvc Aun se encuentra en construccion
 * 
 */
include ("/../../CcMvc.php");
$config = dirname(__FILE__) . "/protected/config/configuracion.php";
CcMvc::Start($config, "Cine")->Run();

