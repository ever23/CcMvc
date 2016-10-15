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
                <b> Unidad Educativa <br>Rafael Quevedo Urbina </b>
            </header>
            <div class="menu"  align="center">
                <ul>
                    <a href="<?php echo $this->ROOT_HTML ?>"><li>inicio</li></a>
                    <li>Estudiantes
                        <ul>
                            <a href="<?php echo $this->ROOT_HTML ?>Estudiantes/insertar"><li>INSERTAR</li></a>
                            <a href="<?php echo $this->ROOT_HTML ?>Estudiantes"><li>CONSULTAR</li></a>
                             <a href="<?php echo $this->ROOT_HTML ?>Estudiantes/nomina"><li>NOMINA</li></a>
                        </ul>
                    <li>Representantes
                        <ul>
                            <a href="<?php echo $this->ROOT_HTML ?>Representante/insertar"><li>INSERTAR</li></a>
                            <a href="<?php echo $this->ROOT_HTML ?>Representante"><li>CONSULTAR</li></a>
                        </ul>

                        <a href="<?php echo $this->ROOT_HTML ?>usuario" style="color: #000000;"><li>Insertar Usuario</li></a>
                        <a href="<?php echo $this->ROOT_HTML ?>index/login">   <li>Salir</li></a>
                </ul>
            </div>
            <div class="conten">
                <?php echo $content ?>
            </div>

        </div>
    </body>

</html>
