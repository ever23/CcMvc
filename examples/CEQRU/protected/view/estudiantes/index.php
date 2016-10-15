<div align="center">
    <form method="get" action="">
        <input type="text" name="buscar"> <input type="submit">
    </form>
    <table width="900" cellspacing="2" border="0">
        <thead>  <tr style="background:rgba(191,191,191,1.00);">
            <td>CI</td>
            <td>NOMBRES</td>
            <td>APELLIDOS</td>
            <td>FECHA DE NACIMIENTO</td>
            <td>EDAD</td>
            <td>LUGAR DE NACIMIENTO</td>
            <td>GRADO</td>
            <td>SECCION</td>
            <td>ESCOLARIDAD</td>
            <td>REPRESENTANTE</td>
            <td></td >


            </tr></thead><tbody>
        <?php
        $time = new Cc\DateTimeEx();

        foreach($estudiante as $i=>$campo)
        {
            echo "<tr class='".($i%2?'collactive':'collactive2')."'>
	<td>" . $campo['cedu_estu'] . "</td>
	<td>" . $campo['nomb_estu'] . "</td>
	<td>" . $campo['apel_estu'] . "</td>
	<td>" . $campo['fena_estu'] ."</td>
	<td>" . $time->DateEdad($campo['ano_estu'], $campo['mes_estu'], $campo['dia_estu']) . "</td>
	<td>" . $campo['luga_estu'] . "</td>
	<td>" . $campo['grad_estu'] . "</td>
	<td>" . $campo['secc_estu'] . "</td>
	<td>" . $campo['esco_estu'] . "</td>
	<td><a href='" . $ObjResponse->ROOT_HTML . "Representante/ficha?ci_repr=" . $campo['ci_repr'] . "'>" . $campo['nomb_repr'] . " " . $campo['apel_repr'] . "</a>
	</td>
	<td><a href='" . $ObjResponse->ROOT_HTML . "estudiantes/editar?id_estu=" . $campo['id_estu'] . "' ><img alt='editar' src='" . $ObjResponse->ROOT_HTML . "src/images/edit.png'  ></a>
	
	<a href='" . $ObjResponse->ROOT_HTML . "estudiantes/constancia?id_estu=" . $campo['id_estu'] . "' ><img alt='constancia' src='" . $ObjResponse->ROOT_HTML . "src/images/pdf.gif' ></a></td>
	</tr>";
        }
        ?></tbody>
    </table>
</div>