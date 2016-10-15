<?php
/* @var $content string */
/* @var $this Html */

$this->addlink_css(['{src}style.css', '{src}menu.css'], 'U');
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
                <b> Unidad Educativa "Rafael Quevedo Urbina" </b>
            </header>
            
            
            <div class="conten">
                <?php echo $content ?>
            </div>

        </div>
    </body>

</html>
