<?php
namespace Cc\Mvc;
class CRepresentante extends Controllers
{

    public static function index(DBtabla $representante, $buscar = NULL)
    {
        if($buscar)
        {
            $representante->Busqueda($buscar);
        } else
        {
            $representante->Select();
        }
        self::LoadView('representante/', ['representante' => $representante]);
    }

    public function ficha(DBtabla $representante, DBtabla $estudiante, $ci_repr = 0)
    {
        $representante->Select('ci_repr=' . $ci_repr);
        $estudiante->Select([
            'estudiante.*',
            'YEAR(estudiante.fena_estu) as ano_estu',
            'MONTH(estudiante.fena_estu) as mes_estu',
            'DAY(estudiante.fena_estu) as dia_estu', 'representante.nomb_repr', 'representante.apel_repr'
                ], 'ci_repr=' . $ci_repr, ['>representante']);
        self::LoadView('representante/ficha', ['campo' => $representante->fetch(), 'estudiante' => $estudiante]);
    }

    public function insertar(DBtabla $representante)
    {
        $msj = NULL;
        if($_POST)
            if($representante->Insert($_POST))
            {
                $this->Redirec('Representante');
            } else
            {
                echo 'ocurrio un error al insertar el representante';
            }
        self::LoadView("representante/insertar", ['msj' => $msj]);
    }

    public function editar(DBtabla $representante, $ci_repr = 0)
    {
        $msj = NULL;
        if($_POST)
            if($representante->Update($_POST, "ci_repr='" . $ci_repr . "'"))
            {
                $this->Redirec('Representante');
            } else
            {
                echo 'ocurrio un error al editar el representante';
            }
        $representante->Select("ci_repr='" . $ci_repr . "'");

        self::LoadView("representante/editar", ['msj' => $msj, 'campo' => $representante->fetch()]);
    }

}
