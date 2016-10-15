<div align="center">
    <h1>NOMINA</h1><BR>
    
    <form action="<?php echo $ObjResponse->ROOT_HTML?>Estudiantes/lista" method="POST">
        <label>GRADO:</label>
        <select name="grad_estu">
            <?php
            foreach($grados as $g)
            {
                echo " <option value='".$g['grad_estu']."'>".$g['grad_estu']."</option>";
            }
            ?>
        </select>&emsp;
        <label>SECCION</label>
        <select name="secc_estu">
            <?php
            foreach($secciones as $g)
            {
                echo " <option value='".$g['secc_estu']."'>".$g['secc_estu']."</option>";
            }
            ?>
        </select><br><br>
        <input type="submit" value="Nomina">
    </form>
</div>