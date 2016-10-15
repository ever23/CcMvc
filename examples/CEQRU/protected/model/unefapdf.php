<?php
//include "../controllers/extendsfpdf.php";
namespace Cc\Mvc;
class UnefaPdf extends \ExtendsFpdf 
{
	public function Header()
	{
		parent::Header();
		$this->Image(dirname(__FILE__)."/../../img/logo_unefa.png",15,4,17,17);
		$this->SetCompression(true);
	}
  

}
