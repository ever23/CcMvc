  <div align="center">
                <h2>DATOS DEL REPRESENTANTE</h2>
              
                <table width="900" cellspacing="1" border="0">
                  <tr style="background:rgba(191,191,191,1.00);">
                    <td>CI</td>
                    <td>NOMBRES</td>
                    <td>APELLIDOS</td>
                    <td>TELEFONO</td>
                    <td>OCUPACION</td>
                    <td>DIRECCION</td>
                    <td></td>
                  </tr>
                  <?php
                   

                    echo "<tr class='collactive2'>
	<td>" . $campo['ci_repr'] . "</td>
	<td>" . $campo['nomb_repr'] . "</td>
	<td>" . $campo['apel_repr'] . "</td>
	<td>" . $campo['telf_repr'] . "</td>
	<td>" . $campo['ocup_repr'] . "</td>
	<td>" . $campo['dire_repr'] . "</td>
	<td><a href='" . $ObjResponse->ROOT_HTML . "Representante/editar?ci_repr=" . $campo['ci_repr'] . "'><img alt='editar' src='" . $ObjResponse->ROOT_HTML . "src/images/edit.png'  ></a>
	</td>
	</tr>";
                    ?>
                </table>
                  <h2>DATOS DE LOS REPRESENTADOS</h2>
                <table width="900" cellspacing="2" border="0">
                    <tr style="background:rgba(191,191,191,1.00);">
                        <td>CI</td>
                        <td>NOMBRES</td>
                        <td>APELLIDOS</td>
                        <td>FECHA DE NACIMIENTO</td>
                        <td>EDAD</td>
                        <td>LUGAR DE NACIMIENTO</td>
                        <td>GRADO</td>
                        <td>SECCION</td>
                        <td>ESCOLARIDAD</td>
                        <td>PARENTESCO </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    $time= new DateTimeEx();
                    foreach($estudiante as $i=>$campo)
                    {
                        echo "<tr class='".($i%2?'collactive':'collactive2')."'>
	<td>" . $campo['cedu_estu'] . "</td>
	<td>" . $campo['nomb_estu'] . "</td>
	<td>" . $campo['apel_estu'] . "</td>
	<td>" . $campo['fena_estu'] . "</td>
	<td>" . $time->DateEdad($campo['ano_estu'], $campo['mes_estu'], $campo['dia_estu']) . "</td>
	<td>" . $campo['luga_estu'] . "</td>
	<td>" . $campo['grad_estu'] . "</td>
	<td>" . $campo['secc_estu'] . "</td>
	<td>" . $campo['esco_estu'] . "</td>
    <td>" . $campo['pare_repr'] . "</td>
	<td><a href='" . $ObjResponse->ROOT_HTML . "Estudiantes/editar?id_estu=" . $campo['id_estu'] . "'><img alt='editar' src='" . $ObjResponse->ROOT_HTML . "src/images/edit.png'  ></a>
	</td><td>
	<a href='" . $ObjResponse->ROOT_HTML . "Estudiantes/constancia?id_estu=" . $campo['id_estu'] . "'><img alt='constancia' src='" . $ObjResponse->ROOT_HTML . "src/images/pdf.gif' ></a></td>
	</tr>";
                    }
                    ?>
                </table>
            </div>