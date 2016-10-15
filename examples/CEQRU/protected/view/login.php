<?php
/*
  @var $ObjResponse Html
 */
?><h2 style="text-align: center;">INGRESAR</h2>
<?php echo $error?>
<form method="post" action="<?php echo $ObjResponse->ROOT_HTML?>index/ingresar">
    <input type="text" name="user" placeholder="NOMBRE">
    <br><input type="password" name="pass" placeholder="CLAVE"><br>
    <input type="submit" value="INGRESAR">
</form>