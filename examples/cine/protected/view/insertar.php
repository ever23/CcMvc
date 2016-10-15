<h2 style="text-align: center">INSERTAR PELICULA</h2>
<div style="align-content: center; padding-left: auto;padding-right: auto; text-align: center ">
    <form method="post" action="" id="contact-form" style="float: none;">
        <label>Id</label> <br>
        <input type="text" name="Id_pelicula">
        <br> <br>
        Titulo <br>
        <input type="text" name="Titulo">
        <br> <br>
        Año <br>
        <input type="text" name="Anno">
        <br> <br>
        Duracion <br>
        <input type="time" name="Duracion">
        <br> <br>
        Consto de produccion <br>
        <input type="text" name="Costoproduccion">
        <br> <br>
        Ganancia <br>
        <input type="text" name="Ganancia">
        <br> <br>

        Estudio <br>
        <select name="Id_estudio">
            <?php
            foreach($estudio as $campo)
            {
                echo "  <option value='".$campo['Id_estudio']."'>".$campo['Nombre']."</option>";
            }
            ?>
          
        </select>
        
        <br> <br>
        <button class="button">INSERTAR</button>

    </form>
</div>