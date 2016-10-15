<style>

    table th,table td{
        border: 1px solid #FFFFFF;
    }
</style>

<div>
    <div style="text-align: center; align-content: center">
        <form action="" method="POST"  id="contact-form" >
            <input type="search" name="busqueda">
            <button class="button">buscar</button>

        </form>


    </div><br><br>
    <table width="960" border="1" >
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">TITULO</th>
                <th scope="col">AÑO</th>
                <th scope="col">DURACION</th>
                <th scope="col">COSTO DE PRODUCCION</th>
                <th scope="col">GANANCIA</th>
                <th scope="col"> ESTUDIO</th>
                <th scope="col"> </th>
                <th scope="col"> </th>
            </tr></thead>
        <tbody>
            <?php
            /* @var $pelicula DBtabla */

            foreach($pelicula as $campo)
            {
                echo ' <tr>
      <td>' . $campo['Id_pelicula'] . '</td>
      <td>' . $campo['Titulo'] . '</td>
      <td>' . $campo['Anno'] . '</td>
      <td>' . $campo['Duracion'] . '</td>
      <td>' . $campo['Costoproduccion'] . '</td>
      <td>' . $campo['Ganancia'] . '</td>
      <td>' . $campo['Nombre'] . '</td>
             <td><a class="button" href="pelicula/editar?Id_pelicula=' . $campo['Id_pelicula'] . '">editar</a></td>
                  <td><a  class="button" href="pelicula/eliminar?Id_pelicula=' . $campo['Id_pelicula'] . '">eliminar</a></td>
    </tr>';
            }
            ?>
        </tbody>
    </table>   

</div>