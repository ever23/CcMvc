<?PHP
require_once("fpdf17.php");
include_once("TablePdfIterator.php");
/*******************************************************************************
* <h1>TableFpdf </h1>                                                          *
* EXTENCION DE FPDF 1.7 DISEÑADA PARA FACILITAR LA CREACION DE TABLAS PDF      *
* NOTA:SI DESEA EXTENDER ESTA CLASE Y REDEFINIR EL METODO Header RECUERDE      * 
* LLAMAR A  parent::TableFpdfHeader()  DENTRO DE ESTE PARA EFECTOS DE SALTOS   *
* DE PAGINA DE LAS TABLAS                                                      *
*                                                                              *
* Version: 1.0                                                                 *
* Fecha:  2015-04-11                                                           *
* Autor:  ENYREBER FRANCO                                                      *
* Email:  enyerverfranco@gmail.com , enyerverfranco@outlook.com                *
*******************************************************************************/
class TableFpdf extends FPDF 
{
    protected $Thead=array();
    protected $conf_thead=array();
    protected  $page_thead=false;
    protected $TableW=0;
    public function __construct($orientation='P', $unit='mm', $size='A4')
    {
        parent::__construct($orientation, $unit, $size);
		 $this->conf_thead=['hcol'=>5,'sizefont'=>10,'font'=>'arial','filcolor'=>[224,235,255]];
		 
    }
    public function Header()
    {
		$this->TableFpdfHeader();
    }
	
	public function TableFpdfHeader()
	{
		 if($this->page_thead)
        {
            $this->TableHead();
        }
	}
   
    /**
* CALCULA LA CANTIDAD DE SALTOS DE LINEA QUE REALIZARA MultiCell
* CON CON LOS PARAMETROS 
* @param TODOS LOS PARAMETROS DEL METODO MultiCell
*/
    public function CalcMultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
    {
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $b = 0;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                continue;
            }
            if($c==' ')
            {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];

            if($l>$wmax)
            {
                // Automatic line break
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                {
                    $i = $sep+1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl-1;
    }
    public function GetTableW()
    {
        return $this->TableW;	
    }
    /**
*	ESTABLECE LA CABECERA DE UNA TABLA 
*   TableHead($header=NULL,$hcol=5, $sizefont=10,$font='',$FillColor=array(201,199,190))
*	@param array $header ARRAY CON CON LOS NOMBRES DE LOS CAMPOS DEVE SER DEFINIDO DE LA SIGUIENTE MANERA
*   array('IDcolumna1'=>'DESCRIPCION DE LA COLUMNA1','IDcolumnaN'=>'DESCRIPCION DE LA COLUMNAN',);
*	@param int $hcol ALTURA DE LAS COLUMNAS 
*	@param int $sizefont TAMAÑO DE LA FUENTE 
*	@param string $font FUENTE DE TEXTO 
*	@param array $FillColor ARRAY CON TRES VALORES PARA EL COLOR DE LA FILA RGB
* 	
*/
    public function TableHead($header=NULL,$hcol=5, $sizefont=10,$font='',$FillColor=array(201,199,190))
    {
        if($header!=NULL)
        {
            $this->conf_thead['hcol']=$hcol;
            $this->conf_thead['sizefont']=$sizefont;
            $this->conf_thead['font']=$font;
            $this->conf_thead['filcolor']=$FillColor;
        }else
        {
            $header=$this->Thead;
        }

        $this->SetFillColor($this->conf_thead['filcolor'][0],$this->conf_thead['filcolor'][1],$this->conf_thead['filcolor'][2]);
        $this->SetTextColor(0);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont($this->conf_thead['font'],'B');
        //$font=$sizefont+2;
        $this->Thead=$header;
        $header=array();;
        $TableW=0;
        foreach($this->Thead as $i=>$col)
        {
            $TableW+=$col[1];
            if(substr($col[0],0,5)!='Image')
            {
                if(!empty($col['dir']))
                {
                    $header+=array($i=>array('dir'=>$col['dir'],'ext'=>$col['ext']));
                }else
                {
                    $header+=array($i=>$col[0]);
                }

            }else
            {
                $header+=array($i=>$col[0]);
            }

        }
        $this->TableW=$TableW;
        //print_r($header);
        $this->TableRow($header,$this->conf_thead['hcol'],10,true);
    }

    /**
*	INSERTA UNA FILA EN LA TABLA (SE TIENE QUE ESTABLECER LA CABECERA ANTES ANTES)
*   TableRow($row,$hcol,$sizefont,$fill)
*	@param array $row ARRAY CON CON LOS VALORES DE LOS CAMPOS DEVE SER DEFINIDO DE LA SIGUENTE MANERA
*    array('IDcolumna1'=>'DESCRIPCION DEL CAMPO1','IDcolumnaN'=>'DESCRIPCION DEL CAMPO N')
*	@param int $hcol ALTURA DE LAS COLUMNAS 
*	@param int $sizefont TAMAÑO DE LA FUENTE 
*	@param bool $fill INDICA SI SE PINTARA LA FILA CON EL COLOR ESTABLECIDO CON SetFillColor
* 	@return int ANCHO DE LA FILA 
*/
    public function TableRow($row,$hcol,$sizefont,$fill)
    {
        $this->SetFontSize($sizefont);
        $hcolini=$hcol;
        $aling='';
        $mas=0;
        $ColMultiCell=array();
        $a=array(0);
 		$ANCHO=0;
        foreach($this->Thead as $namecol=>$col)
        {
			if(empty($row[$namecol]))
			$row[$namecol]='';
           $ANCHO+=$col[1];
                if(!is_array($row[$namecol]))
                    $n=$this->CalcMultiCell($col[1],$hcolini,$row[$namecol],"TRL",$aling,true);
                if($n>0 && ((count($a)>0 && max($a)<=$n)))
                {
                    array_push($ColMultiCell,$namecol);
                    $mas=(($n)*$hcolini);
                }
                array_push($a,$n);
        }
        $y=$this->y;
		if($mas+$this->y+$hcolini>$this->PageBreakTrigger)
		{
			
			//echo $mas+$this->y,'-',$this->PageBreakTrigger,'<br>';
			$this->Line($this->x,$this->y,$this->x+$ANCHO,$this->y);
			$this->AddPage();
		}
		//echo $this->PageBreakTrigger.'-',$mas+$this->y,'<br>';
        foreach($this->Thead as $namecol=>$col)
        {
			if(empty($row[$namecol]))
			$row[$namecol]='';
                
                $x=$this->x;
                if(isset($col[2]))
                    $aling=$col[2];
                if(is_array($row[$namecol]))
                {
                    $y=$this->y;
                    //echo $row[$namecol];
                    $this->Cell($col[1],$hcolini+$mas,"",0,0,$aling,$fill,'');
                    $this->SetXY($x,$y);
                    $this->Image($row[$namecol]['dir'],$x,$y,$col[1],$hcolini+$mas,$row[$namecol]['ext']);
                    $this->Cell($col[1],$hcolini+$mas,"","TRL",0,$aling,0,'');
					
                }else
                {
                    $x=$this->x;
                    if(in_array($namecol,$ColMultiCell))
                    {
                        $y=$this->y;
                        $x1=$this->x;
                        $this->Cell($col[1],$hcolini+$mas,"","TRL",0,$aling,$fill,'');
                        $this->x=$x1;
                        $this->MultiCell($col[1],$hcolini,$row[$namecol],"TRL",$aling,$fill);
                        $this->SetY($y);
                        $this->x=$x+$col[1];
                    }else
                    {
                        $this->Cell($col[1],$hcolini+$mas,$row[$namecol],"TRL",0,$aling,$fill,'');
                    }
                }
                $aling='';
        }
		//$this->Line($this->x,$this->y,$this->x+$ANCHO,$this->y);
        $this->Ln($hcolini+$mas);

    }
	/**
	CREA EL CUERPO DE UNA TABLA EN PDF 
	@param array $tabla ARRAY CON LOS CAMPOS DE LA TABLA ORDENADA CON LA SIGUIENTE ESTRUCTURA
	array(
	0=>array('IDcolumna1'=>'DESCRIPCION DEL CAMPO1')
	)
	@param int $sizeefont TAMAÑO DEL TEXTO
	@param int $hcol  altura de la columna
	@param array $fillcolor array con el color array(r,g,b) de la tabla
	@return AUTOREFERENCIA
	*/
    public function TableBody(array $tabla,$sizefont=NULL,$hcol=5,$FillColor=array(255,255,255))
    {
        $this->page_thead=true;
        $fill=false;
        $this->SetFontSize($sizefont);
        $this->SetFont('','');
        $this->SetFillColor($FillColor[0],$FillColor[1],$FillColor[2]);
        //$this->SetFontSize($sizefont);
        $cambio=false;
        $hcolini=$hcol;
        $aling='';
        $ANCHO=0;
        foreach($tabla as $ide=>$campo)
        {
            $ANCHO=$this->TableRow($campo,$hcolini,$sizefont,$fill);
            $fill = !$fill;		
        }
		$this->Line($this->x,$this->y,$this->x+$this->TableW,$this->y);
       // $this->Cell($this->TableW,5,"","T",1,$aling,false,'');
        $this->page_thead=false;
    }
	/**
	CREA UNA TABLA EN PDF 
	@param mixes $Thead SI ES UN OBJETO DEVE SER INSTANCEADO DE TABLEPDFITERATOR SI ES UN ARRAY SERA EL CONTENIDO DE LA CABECREA DE LA TABLA
	@param array $tabla ARRAY CON LOS CAMPOS DEL CUERPO DE LA TABLA ORDENADA CON LA SIGUIENTE ESTRUCTURA
	array(
	0=>array('IDcolumna1'=>'DESCRIPCION DEL CAMPO1')
	)
	@param int $sizeefont TAMAÑO DEL TEXTO
	@param int $hcol  altura de la columna
	@param array $fillcolorH array con el color array(r,g,b) de la CABECERA DE LA TABLA
	@param array $fillcolorB array con el color array(r,g,b) del CUERPO DE LA TABLA
	@return AUTOREFERENCIA
	*/
    public function &Table($Thead,$Tbody=NULL,$sizefont=NULL,$hcol=5,$font='',$FillColorH=array(201,199,190),$FillColorB=array(255,255,255))
    {
        if(is_object($Thead))
        {
            $this->TableIterator($Thead);
        }else
        {
            $this->TableHead($Thead,$hcol+2,$sizefont+2,$font,$FillColorH);
            $this->TableBody($Tbody,$sizefont,$hcol,$FillColorB);
        }
		return $this;
    }
    protected function TableIterator(TablePdfIterator &$table)
    {
        $conf_head=$table->GetConfHead();
        $conf_body=$table->GetConfigBody();
        $this->TableHead($table->GetThead(),$conf_head['h'],$conf_head['sizefont'],$conf_head['font'],$conf_head['fillcolor']);
        $this->TableBodyIterator($table);
    }
    private function TableBodyIterator(TablePdfIterator &$table)
    {
        $conf=$table->GetConfigBody();
        $body=$table->GetTbody();
        $this->page_thead=true;
        //$this->SetFontSize($sizefont);
        foreach($body as $i=>$campo)
        {
            $this->SetFont($conf[$i]['font'],'');
            $this->SetFontSize($conf[$i]['sizefont']);
            $this->SetFillColor($conf[$i]['fillcolor'][0],$conf[$i]['fillcolor'][1],$conf[$i]['fillcolor'][2]);
            $this->TableRow($campo,$conf[$i]['h'],$conf[$i]['sizefont'],$conf[$i]['fill']);	
        }

        $this->Line($this->x,$this->y,$this->x+$this->TableW,$this->y);
        $this->page_thead=false;
    }
}

