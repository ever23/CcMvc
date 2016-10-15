<center><br>
    <br>
    <br>
    <br>

    <form action="" method="POST">
        <input type="hidden" name="ci_repr" value="<?php echo $campo['ci_repr'] ?>">
        <label>C.I:</label>
        <?php echo $campo['ci_repr'] ?>
        <br><br>
        <label> Nombre: </label>
        <INPUT NAME="nomb_repr" value="<?php echo $campo['nomb_repr'] ?>">
        <br><br>
        <label> Apellido:</label>
        <INPUT NAME="apel_repr" value="<?php echo $campo['apel_repr'] ?>">
        <br><br>
        <label> TELEFONO:</label>
        <INPUT name="telf_repr" value="<?php echo $campo['telf_repr'] ?>">

        <br><br>
        <label> OCUPACION:</label>
        <INPUT NAME="ocup_repr" value="<?php echo $campo['ocup_repr'] ?>">
        <br><br>

        <label> DIRECCION:</label>
        <INPUT NAME="dire_repr" value="<?php echo $campo['dire_repr'] ?>">
        <br><br>
        <input type="submit">

    </form>
</center>