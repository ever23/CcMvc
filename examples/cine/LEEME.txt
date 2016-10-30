INTEGRANTES:


CI: 23.781.625, Enyerber Franco

porfravor use php 5.6 o mayor con una version menor no funcionara
teniendo en cuenta que php ya tiene una version 7 no deveria ser un problema

el ejercisio fue desarrollado haciendo uso del Framework CcMvc http://ccmvc.890m.com
se utiliza un formato de base de datos sqlite portable por lo que no sera nesesario cargar la base de 
datos en mysql por lo que para ver el funcionamiento del ejercisio solo devera pegar los 
archivos en un directorio del servidor y ejecutarlos desde el navegador

si fuese nesesario utilizar mysql devera modificar el archivo protected/conf/configuracion.php
y sustituir el indice DB por 
'DB' =>
[
    class' => 'DB_MySQLi',
    'param' =>['localhost','tuUser','tuClave','cine']
]
luego cargar el archivo cine.sql en mysql esto no deveria causar problemas ya que CcMvc maneja las base de datos de una
forma muy abstracta  

si desea revisar el codigo fuente se encuentra de el directorio 
protected/

el directorio CcMvc/ es el framework