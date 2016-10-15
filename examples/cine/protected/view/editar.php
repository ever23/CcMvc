<div style="text-align: center;">
    <h2>EDITAR</h2>
    <?php

echo ' <form method="post" action="" id="contact-form" >
                            
                            <input type="hidden" name="Id_pelicula" value="' . $campo['Id_pelicula'] . '">
                            <br> <br>
                              Titulo <br>
                            <input type="text" name="Titulo" value="' . $campo['Titulo'] . '">
                            <br> <br>
                             Año <br>
                            <input type="text" name="Anno" value="' . $campo['Anno'] . '">
                            <br> <br>
                             Duracion <br>
                            <input type="time" name="Duracion" value="' . $campo['Duracion'] . '">
                            <br> <br>
                             Consto de produccion <br>
                            <input type="text" name="Costoproduccion" value="' . $campo['Costoproduccion'] . '">
                            <br> <br>
                             Ganancia <br>
                            <input type="text" name="Ganancia" value="' . $campo['Ganancia'] . '">
                            <br> <br>
                           
                             Id Estudio <br>
 <select name="Id_estudio">';
       
            foreach($estudio as $est)
            {
                
                echo "  <option value='".$est['Id_estudio']."' ".( $campo['Id_estudio']==$est['Id_estudio']?'selected':'').">".$est['Nombre']."</option>";
            }
          echo '</select>
                          
                            <br> <br>
                            <button class="button">EDITAR</button>

                        </form> 
                                ';
?></div>
