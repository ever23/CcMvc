<?php

/**
 * controlador pelicula 
 * 
 */

namespace Cc\Mvc;

class Cpelicula extends Controllers
{

    /**
     * muestra la pagina principal
     * @param DBtabla $pelicula
     * @param type $busqueda
     */
    public function index(DBtabla $pelicula, $busqueda = NULL)
    {


        if ($busqueda)
        {
            $colBusqueda = ['Id_pelicula', 'Titulo', 'Anno', 'Nombre'];
            $colMostrados = ['pelicula.*', 'estudio.Nombre'];
            $join = ['>estudio'];
            $pelicula->Busqueda($busqueda, $colBusqueda, $colMostrados, NULL, $join, 'Id_pelicula');
        } else
        {
            $pelicula->Select(['*'], NULL, ['>estudio']);
        }
        //echo var_dump($pelicula->errno);
        self::LoadView('index', ['pelicula' => $pelicula]);
//se pasa el objeto pelicula al view index   este es el archivo protected/view/index.php
    }

    /**
     * muestra el formulario de insertar 
     * @param DBtabla $pelicula
     * @param DBtabla $estudio
     */
    public function insertar(DBtabla $pelicula, DBtabla $estudio)
    {
        if ($_POST)
        {
            if ($pelicula->Insert($_POST))
            {

                $this->Redirec('index/index');
            } else
            {
                echo $pelicula->error;
            }
        }
        $estudio->Select();
        $this->LoadView('insertar', ['estudio' => $estudio]);
    }

    public function editar(DBtabla $pelicula, DBtabla $estudio, $Id_pelicula)
    {
        if ($_POST)
        {
            if ($pelicula->Update($_POST, "Id_pelicula='" . $Id_pelicula . "'"))
            {
                $this->Redirec('index/index');
            } else
            {
                echo $pelicula->error;
            }
        }
        $pelicula->Select("Id_pelicula='" . $Id_pelicula . "'");
        $campo = $pelicula->fetch();
        $estudio->Select();
        $this->LoadView('editar', ['campo' => $campo, 'estudio' => $estudio]);
    }

    public function eliminar(DBtabla $pelicula, $Id_pelicula)
    {
        if ($pelicula->Delete("Id_pelicula='" . $Id_pelicula . "'"))
        {
            $this->Redirec('index/index');
        } else
        {
            echo $pelicula->error;
        }
    }

}
