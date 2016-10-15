<?php

$dir=  dirname(__FILE__);
include $dir.'/../../../CcMvc/CcWS.php';
CcWS::Start($dir.'/config/configuracion.php')->Run();