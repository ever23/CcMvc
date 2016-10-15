<center><br>
    <br>
    <br>
    <br>

    <form action="" method="POST">
        <input type="hidden" name="id_estu" value="<?php echo $campo['id_estu'] ?>">

        <label>C.I:</label>
        <INPUT NAME="cedu" value="<?php echo $campo['cedu_estu'] ?>">
        <br><br>
        <label> Nombre:</label>
        <INPUT NAME="nomb" value="<?php echo $campo['nomb_estu'] ?>">
        <br><br>
        <label> Apellido:</label>
        <INPUT NAME="apel" value="<?php echo $campo['apel_estu'] ?>">
        <br><br>
        <label> Fecha de Nacimiento: </label>


        <input type="date" name="fena"  value="<?php echo $campo['fena_estu'] ?>">
        <br><br>
        <label> Lugar de Nacimiento:</label>
        <INPUT NAME="luga" value="<?php echo $campo['luga_estu'] ?>">
        <br><br>
        <label> Grado:</label>
        <INPUT NAME="grad" value="<?php echo $campo['grad_estu'] ?>">
        <br><br>
        <label> Sección:</label>
        <INPUT NAME="secc" value="<?php echo $campo['secc_estu'] ?>">
        <br><br>
        <label> Escolaridad:</label>
        <INPUT NAME="esco" value="<?php echo $campo['esco_estu'] ?>">
        <br><br>

        <label> Codigo de Canaima:</label>
        <INPUT NAME="codi_cana" value="<?php echo $campo['codi_cana'] ?>">
        <br><br>
        <label>C.i Del Representante:</label>
        <INPUT NAME="ci_repr" value="<?php echo $campo['ci_repr'] ?>">
        <br><br>
        <label> Parentesco Con el Representante:</label>
        <INPUT NAME="pare_repr" value="<?php echo $campo['pare_repr'] ?>">
        <br><br>
        <input type="submit" value="Registrar" name="ingresar">

    </form>
</center>