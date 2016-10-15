<div align="center">
    <form method="get" action="">
        <input type="text" name="buscar"> <input type="submit">
    </form>
    <table width="900" cellspacing="2" border="0">
        <tr style="background:rgba(191,191,191,1.00);">
            <td>CI</td>
            <td>NOMBRES</td>
            <td>APELLIDOS</td>
            <td>TELEFONO</td>
            <td>OCUPACION</td>
            <td>DIRECCION</td>
            <td></td>
            <td></td>
        </tr>
        <?php
      foreach($representante as $i=>$campo)
        {
            echo "<tr class='".($i%2?'collactive':'collactive2')."'>
	<td>" . $campo['ci_repr'] . "</td>
	<td>" . $campo['nomb_repr'] . "</td>
	<td>" . $campo['apel_repr'] . "</td>
	<td>" . $campo['telf_repr'] . "</td>
	<td>" . $campo['ocup_repr'] . "</td>
	<td>" . $campo['dire_repr'] . "</td>
    <td>
	<a href='" . $ObjResponse->ROOT_HTML . "Representante/ficha?ci_repr=" . $campo['ci_repr'] . "'>ficha</a></td>
	
	<td><a href='" . $ObjResponse->ROOT_HTML . "Representante/editar?ci_repr=" . $campo['ci_repr'] . "'><img alt='editar' src='" . $ObjResponse->ROOT_HTML . "src/images/edit.png'  ></a>
	</td></tr>";
        }
        ?>
    </table>
</div>