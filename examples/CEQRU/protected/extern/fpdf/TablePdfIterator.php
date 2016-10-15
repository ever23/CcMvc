<?php
/*******************************************************************************
* <h1>TablePdfIterator </h1>                                                   *
* FACILITA LA CREACION DE TABLAS PARA TableFpdf extencion de FPDF              *
*                                                                              *
*                                                                              *
* Version: 1.0                                                                 *
* Fecha:  2015-04-11                                                           *
* Autor:  ENYREBER FRANCO                                                      *
* Email:  enyerverfranco@gmail.com , enyerverfranco@outlook.com                *
*******************************************************************************/

class TablePdfIterator 
{
    private $Tbody=array();
    private $Thead=array();
    private $conf_thead=array();
    private $conf_tbody=array();
	/**
	CONSTRUCTOR DE LA CLASE
	*/
    public function __construct($h_head=NULL, $filcolor=NULL,$font=NULL,$sizefont=NULL)
    {
		if(is_object($h_head))
		{
			$this->Copy($h_head);
		}else
		{
			if(is_null($filcolor))
			{
				$filcolor=array(255,255,255);
			}
      		 $this->IniHead($h_head,$filcolor,$font,$sizefont);
	  		 $this->IniBody();
		}
		
    }
	public function IniBody()
	{
		$this->conf_tbody=$this->Tbody=array();
	}
	public function IniHead($h_head,array $filcolor,$font,$sizefont)
	{
		 $this->conf_thead=array('h'=>$h_head,'fillcolor'=>$filcolor,'font'=>$font,'sizefont'=>$sizefont);
		 $this->Thead=array();
	}
	public function Copy(TablePdfIterator &$table)
	{
		$this->Thead=$table->Thead;
		$this->conf_thead=$table->conf_thead;
		$this->Tbody=$table->conf_tbody;
		$this->conf_tbody=$table->conf_tbody;
	}
	public function GetCellBody($coll,int $nrow)
	{
		return $this->Tbody[$nrow][$coll];
	}
	public function GetRowBody(int $nrow)
	{
		return $this->Tbody[$nrow];
	}
	public function GetCollBody($coll)
	{
		$columna=array();
		foreach($this->GetTbody() as $body)
		{
			array_push($columna,$body[$coll]);
		}
		return $columna;
	}
    public function &SetRowHeah(array $a)
    {
        $this->Thead=$a;
        return $this;
    }
    public function &AddCollHead($namecoll,$value,$w,$aling=NULL)
    {
        $array=array($namecoll=>array(0=>$value,1=>$w,2=>$aling));
        $this->Thead=array_merge($this->Thead,$array);
        return $this;
    }
    public function &AddCell($namecoll,$value,$row=NULL)
    {
        if(is_null($row))
        {
            $row=count($this->Tbody)-1;
        }
        $this->Tbody[$row][$namecoll]=$value;
        return $this;

    }
    public function &addCellImg($namecoll,$img,$ext,$row=NULL)
    {
        if(is_null($row))
        {
            $row=count($this->Tbody)-1;
        }
        $this->Tbody[$row][$namecoll]=array('dir'=>$img,'ext'=>$ext);
        return $this;
    }
	public function &AddRowArray(array $array,$h=NULL,$font=NULL,$sizefont=NULL,$fill=NULL,array $filcolor=NULL)
	{
		$this->AddRow($h,$font,$sizefont,$fill, $filcolor);
		foreach($array as $i=>$v)
		{
			$this->AddCell($i,$v);	
		}
		return $this;
	}
    public function &AddRow($h=NULL,$font=NULL,$sizefont=NULL,$fill=NULL,array $filcolor=NULL)
    {
        $n=count($this->conf_tbody)-1;
        if($n<0)
        {
			$h=is_null($h)?$this->conf_thead['h']:$h;
            $font=is_null($font)?$this->conf_thead['font']:$font;
            $sizefont=is_null($sizefont)?$this->conf_thead['sizefont']:$sizefont;
            $fill=is_null($fill)?false:$fill;
            $filcolor=is_null($filcolor)?$this->conf_thead['fillcolor']:$filcolor;
        }else
        {
            $h=is_null($h)?$this->conf_tbody[$n]['h']:$h;
            $font=is_null($font)?$this->conf_tbody[$n]['font']:$font;
            $sizefont=is_null($sizefont)?$this->conf_tbody[$n]['sizefont']:$sizefont;
            $fill=is_null($fill)?$this->conf_tbody[$n]['fill']:$fill;
            $filcolor=is_null($filcolor)?$this->conf_tbody[$n]['fillcolor']:$filcolor;
        }


        array_push($this->Tbody,array());
        array_push($this->conf_tbody,array('h'=>$h,'font'=>$font,'sizefont'=>$sizefont,'fill'=>$fill,'fillcolor'=>$filcolor));
        return $this;

    }
    public function GetConfHead()
    {
        return $this->conf_thead;
    }
    public function GetConfigBody()
    {
        return $this->conf_tbody;
    }
    public function GetTbody()
    {
        return $this->Tbody;
    }
    public function GetThead()
    {
        return $this->Thead;
    }
	public function NumRows()
	{
		return count($this->Tbody);	
	}

}