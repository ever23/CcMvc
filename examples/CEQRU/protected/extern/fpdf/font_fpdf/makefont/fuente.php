<?
require("makefont.php");
if(!empty($enviar))
{

	$f1= fopen($_FILES['font']['tmp_name'],"rb");
	$fuente = fread($f1,$_FILES['font']['size']);
	fclose($f1);
	$tem_name="../../../temp/".$name_font;
	$f2= fopen($tem_name,"wb");
	fwrite($f2,$fuente);
	fclose($f2);
     MakeFont($tem_name,'ISO-8859-1',TRUE,'../');
	
			
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Documento sin título</title>
</head>
<? echo $mensaje.$error ?>
<body>

 <form action="" name="conf_img" method="post"  ENCTYPE="multipart/form-data">
<input type="file" name="font"><BR>
<input type="text" name="name_font"><BR>
<button name="enviar" value="true">CARGAR</button>
</form>
</body>
</html>