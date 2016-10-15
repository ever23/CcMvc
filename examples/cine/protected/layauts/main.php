<?php

/* @var $content string */
/* @var $this Html */
?>


  <!DOCTYPE html>
<html lang="en">
<head>
   
    <meta charset="utf-8">
        <?php echo $this->GetContenHead() ?>
   <link rel="stylesheet" href="src/css/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="src/css/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="src/css/grid.css" type="text/css" media="screen">   
    <script src="src/js/jquery-1.7.1.min.js" type="text/javascript"></script>
    <script src="src/js/cufon-yui.js" type="text/javascript"></script>
    <script src="src/js/cufon-replace.js" type="text/javascript"></script>
	<script src="src/js/Vegur_500.font.js" type="text/javascript"></script> 
    <script src="src/js/FF-cash.js" type="text/javascript"></script>       
  
	<!--[if lt IE 8]>
    <div style=' clear: both; text-align:center; position: relative;'>
        <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
        	<img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
        </a>
    </div>
	<![endif]-->
    <!--[if lt IE 9]>
   		<script type="text/javascript" src="js/html5.js"></script>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen">
	<![endif]-->
</head>
<body id="page1">
	<div class="main-bg">
        <div class="bg">
            <!--==============================header=================================-->
            <header>
                <div class="main">
                    <div class="wrapper">
                        <h2 style="text-height: 30px; ">Cine</h2>
                        <div class="fright">
                        
                        </div>
                    </div>
                    <nav>
                        <ul class="menu">
                            <li><a class="active" href="pelicula/">INICIO</a></li>
                                <li><a  href="pelicula/insertar">INSERTAR</a></li>
                          
                        </ul>
                    </nav>
                   
                </div>
            </header>
            
            <!--==============================content================================-->
            <section id="content">
               
                <div class="main">
                 <?php
                    echo $content;
                 ?>
                </div>
            </section>
            
            <!--==============================footer=================================-->
            <footer>
                <div class="main">
                    </div>
            </footer>
        </div>
    </div>
	
</body>
</html>