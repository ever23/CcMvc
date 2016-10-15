<?php
namespace Cc\Mvc;
use \TablePdfIterator;
use Cc\DateTimeEx;
use \ExtendsFpdf;
class CEstudiantes extends Controllers
{

    protected $columnas = [
        'estudiante.*',
        'YEAR(estudiante.fena_estu) as ano_estu',
        'MONTH(estudiante.fena_estu) as mes_estu',
        'DAY(estudiante.fena_estu) as dia_estu', 'representante.nomb_repr', 'representante.apel_repr'
    ];

    public  function index(DBtabla $estudiante, $buscar = NULL, $id_estu = 0)
    {
 
        if($buscar)
        {
           
            $estudiante->Busqueda($buscar,['cedu_estu', 'nomb_estu', 'apel_estu', 'grad_estu', 'secc_estu'], $this->columnas, NULL, ['>representante']);
        } elseif($id_estu)
        {
            $estudiante->Select($this->columnas, 'id_estu=' . $id_estu, ['>representante']);
        } else
        {
            $estudiante->Select($this->columnas, ['>representante']);
        }
       
        self::LoadView('estudiantes/', ['estudiante' => $estudiante]);
    }

    public function insertar(DBtabla $estudiante)
    {
        $msj = NULL;
        if($_POST)
        {
            $_POST['id_estu']=$estudiante->AutoIncrement('id_estu')+1;
             if($estudiante->Insert($_POST))
            {
                // echo $estudiante->sql;
                $this->Redirec('Estudiantes');
            } else
            {
                echo 'ocurrio un error al insertar el estudiante';
            }
        }
           
        self::LoadView("estudiantes/insertar", ['msj' => $msj]);
    }

    public function editar(DBtabla $estudiante, $id_estu = 0)
    {
        $msj = NULL;
        if($_POST)
            if($estudiante->Update($_POST, "id_estu=" . $id_estu))
            {
                $this->Redirec('Estudiantes');
            } else
            {
                echo 'ocurrio un error al editar el estudiante';
            }
        $estudiante->Select("id_estu=" . $id_estu);
        self::LoadView("estudiantes/editar", ['msj' => $msj, 'campo' => $estudiante->fetch()]);
    }
    public function nomina(DBtabla $estudiante)
    {
        $grados=$estudiante->Select(['grad_estu'],NULL,NULL,'grad_estu')->FetchAll();
        $secciones=$estudiante->Select(['secc_estu'],NULL,NULL,'secc_estu')->FetchAll();
        $this->LoadView('estudiantes/nomina', ['grados'=>$grados,'secciones'=>$secciones]);
    }
    public function lista(DBtabla $estudiante,$grad_estu=NULL,$secc_estu=NULL)
    {
        $plus='';
        if($grad_estu && $secc_estu)
        {
            $plus='GRADO '.$grad_estu.' SECCION '.$secc_estu;
            $estudiante->Select($this->columnas,"grad_estu='".$grad_estu."' and secc_estu='".$secc_estu."'", ['>representante']);
        }else
        {
              $estudiante->Select($this->columnas, ['>representante']);
        }
        
        $Tab_estu = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $Tab_estu->AddCollHead('C', 'C.I', 25);
        $Tab_estu->AddCollHead('n', 'NOMBRES', 30);
        $Tab_estu->AddCollHead('a', 'APELLIDOS', 30);
        $Tab_estu->AddCollHead('f', 'FECHA DE NACIMIENTO', 25);
        $Tab_estu->AddCollHead('E', 'EDAD', 15);
        $Tab_estu->AddCollHead('L', 'LUGAR DE NACIMIENTO', 25);
        $Tab_estu->AddCollHead('G', 'GRADO', 20);
        $Tab_estu->AddCollHead('S', 'SECCION', 20);
        $Tab_estu->AddCollHead('ES', 'ESCOLARIDAD', 30);
        $Tab_estu->AddCollHead('r', 'REPRESENTANTE', 35);
        $time = new DateTimeEx();
        foreach($estudiante as $campo)
        {
            $Tab_estu->AddRow(5);
            $Tab_estu->AddCell('C', $campo['cedu_estu']);
            $Tab_estu->AddCell('n', $campo['nomb_estu']);
            $Tab_estu->AddCell('a', $campo['apel_estu']);
            $Tab_estu->AddCell('f', $campo['fena_estu']);
            $Tab_estu->AddCell('E', $time->DateEdad($campo['ano_estu'], $campo['mes_estu'], $campo['dia_estu']));
            $Tab_estu->AddCell('L', $campo['luga_estu']);
            $Tab_estu->AddCell('S', $campo['secc_estu']);
            $Tab_estu->AddCell('ES', $campo['esco_estu']);
            $Tab_estu->AddCell('r', $campo['nomb_repr'] . " " . $campo['apel_repr']);
        }
        $pdf = new ExtendsFpdf('L', 'mm', 'Letter');
        $pdf->titulo("LISTA DE ESTUDIANTES ".$plus, 120, 'arial', 'B', 18);
        $pdf->AddPage();
        $pdf->Table($Tab_estu);
        $pdf->Output("ESTUDIANTES.pdf", 'I');
    }

    public function constancia(DBtabla $estudiante, $id_estu = 0)
    {
        $estudiante->Select('id_estu=' . $id_estu);
        $campo = $estudiante->fetch();
        $mes = date('m');

        if($mes < 6)
        {
            $esc = (date('Y') - 1) . "-" . date('Y');
        } else
        {
            $esc = date('Y') . "-" . (date('Y') + 1);
        }
        $time = new DateTimeEx();
        $pdf = new ExtendsFpdf();
        $pdf->SetCompression(true);
       
        $pdf->AddPage();
        $pdf->SetFont('arial', '', 14);
        $pdf->SetX(70);
        
        $pdf->Image(dirname(__FILE__).'/../../src/images/logoministerio.jpg', 15, 4, 60, 20);
        $Y = $pdf->GetY();
         $pdf->SetY(15);
         $pdf->SetX(70);
        $pdf->MultiCell(100, 5, "ZONA EDUCATIVA TRUJILLO", 0);
        $pdf->SetFont('arial', '', 12);
        $pdf->SetXY(140, 15);
        $pdf->MultiCell(190, 5, "Division De Municipios Escolares", 0);

        $pdf->SetY(30);
        $pdf->SetFont('arial', '', 14);
        $pdf->MultiCell(190, 5,
                "REPUBLICA BOLIVARIANA DE VENEZUELA 
MINISTERIO DEL PODER POPULAR PARA LA EDUCACION 
ESCUELA BASICA NACIONAL RAFAEL QUEVEDO URBINA 
CAMPO ALEGRE-CARVAJAL
ESTADO TRUJILLO", 0, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetY(70);
        $pdf->SetFont('arial', 'U', 16);
        $pdf->MultiCell(190, 5, "CONSTANCIA DE ESTUDIO", 0, 'C');
        $pdf->SetFont('arial', '', 14);
        $pdf->Ln();
        $pdf->MultiCell(190, 7,
                "Quien suscribe. Director (a) de la E.B.N 'Rafael Quevedo Urbina' ______________ ______________ titular de la cedula de identidad  _____________ por medio de la presente hace constar que el (la) alumno " . strtoupper($campo['nomb_estu'] . " " . $campo['apel_estu']) . " titular de la cedula de identidad: " . ($campo['cedu_estu'] == '' ? 'S/N' : $campo['cedu_estu']) . " cursa en esta institucion " . $campo['grad_estu'] . " Grado De Educacion Basica periodo escolar " . $esc . " ",
                0, 'J');
        $pdf->Ln();
        $pdf->Ln();

        $pdf->MultiCell(190, 5,
                "Constancia que se expide a solucitud de la parte interesada en Campo Alegre a los " . date('d') . " dias del mes de " . $time->meses[(int) date('m')] . " del año " . date('Y') . " ", 0, 'J');

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->MultiCell(190, 5, "ATENTAMENTE", 0, 'C');
        $pdf->Line(75, 200, 135, 200);
        $pdf->Ln();
        $pdf->Ln();

        $pdf->Output("constancia de estudio.pdf", "I");
    }

}
