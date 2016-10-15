<?PHP
//require("tablefpdf.php");
define('FPDF_FONTPATH',dirname(__FILE__).'/font_fpdf/');
class ExtendsFpdf extends TableFpdf 
{
    private $titulo;
    private $xtitle;
    private $sizetitle;
    private $fonttitle;
    private $add_page;
    private $tipo;
    private $fondo_img;
    private $fondo_Image;
    private $funct_footer=array();
    private $funct_head=array();
   
    public function __construct($orientation='P', $unit='mm', $size='A4')
    {
        parent::__construct($orientation, $unit, $size);
       
        $this->SetAuthor("ENYERBER FRANCO (enyerverfranco@gmail.com)",true);
        $this->SetCreator("ExtendsFpdf ",true);
		$this->AddFont('arial','','arial.php');
		$this->AddFont('arial','B','arial.php');
        $this->fondo_img=NULL;
        $this->fondo_Image='Image';
        $this->AliasNbPages();
        $this->fonttitle='arial';
        $this->SetCompression(true);
    }
    public function FnFooter($fn_text)
    {
        array_push($this->funct_footer,$fn_text);
    }
	protected function CallFnFooter()
	{
		 foreach($this->funct_footer as $func)
        {
            $func($this);
        }
	}
    public function FnHeader($fn_text)
    {
        array_push($this->funct_head,$fn_text);
    }
	protected function CallFnHeader()
	{
		foreach($this->funct_head as $func)
        {
            $func($this);
        }
      
	}
    public function add_pagina($titulo=NULL)
    {
        if($titulo!=NULL)
        {
            $this->SetTitle($titulo,true);
            $this->titulo=$titulo;
        }
        $this->AddPage();		
    }

    public function titulo($title,$X,$fonttitle,$tipo,$fontsize)
    {
        $this->titulo=$title;
        $this->xtitle=$X;
        $this->sizetitle=$fontsize;
        $this->fonttitle=$fonttitle;
        $this->tipo=$tipo;
    }
    public function Image_fondo($imagen,$fondo_Image="png")
    {
        $this->fondo_img=$imagen;
        $this->fondo_Image=$fondo_Image; 
    }

    public function Header()
    {
	
        if($this->fondo_img!=NULL && $this->fondo_img!='')
            $this->Image($this->fondo_img,0,0,$this->w,$this->h,$this->fondo_Image);
        // Logo
        // Arial bold 15
        $this->SetFont($this->fonttitle,$this->tipo,$this->sizetitle);
        // Movernos a la derecha
        $this->SetXY($this->xtitle,8);

        $this->Cell(30,10,$this->titulo,0,0,'C');
        $this->Ln();
        $this->SetY(25);
		parent::TableFpdfHeader();
		$this->CallFnHeader();
		
       
    }
    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
	{ 
		parent::Cell($w,$h,utf8_decode($txt),$border,$ln,$align,$fill,$link);
	}
    public function Footer()
    {
        $this->SetFont('Arial','I',8);
        $this->SetXY(-10,-10);
        $this->Cell(0,10,' '.$this->PageNo().'/{nb}',0,0,'C');
        $this->CallFnFooter();
    }
}

?>