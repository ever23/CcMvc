<?php
/* @var $content string */
/* @var $this Html */

$this->addlink_css(['{src}style.css'], 'U');
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset='utf-8' >
        <?php echo $this->GetContenHead() ?>

    </head>
    <body  text="black">
        <div class="container">
            <header>
                <b>CHAT WEBSOCKET  EN TIEMPO REAL</b>
            </header>
            
            
            <div class="conten">
                <?php echo $content ?>
            </div>

        </div>
    </body>

</html>
