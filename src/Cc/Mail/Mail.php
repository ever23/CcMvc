<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc;

/**
 * clase encargada de enviar mails
 *
 * @author ENYREBER FRANCO  <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com> 
 *     
 * @package Cc
 * @subpackage Mail
 * @todo Se nesesita merorar el envio de mails
 * 
 */
class Mail
{

    protected $config;
    protected $Destinatarios = [];
    protected $Title = '';
    protected $messaje = '';
    protected $header = [];
    protected $adjuntos = [];

    const NewLine = "\r\n";

    public function __construct(Config $conf)
    {
        $this->config = $conf;
        $this->Header('From', $conf->WebMaster['email']);
        $this->Header('Reply-To', $conf->WebMaster['email']);
    }

    public function Destino($mail)
    {
        if (is_array($mail))
        {
            foreach ($mail as $v)
            {
                if (filter_var($v, FILTER_VALIDATE_EMAIL))
                    $this->Destinatarios[] = $v;
            }
        } else
        {
            if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                $this->Destinatarios[] = $mail;
        }
    }

    public function AdjuntarArchivo($filename)
    {
        if (preg_match("/^((http:\/\/)|(https:\/\/))/", $filename) || file_exists($filename))
        {
            $spl = new \SplFileInfo($filename);
            $adjunto = [];
            $adjunto['ext'] = $spl->getExtension();
            $adjunto['file'] = $spl->getBasename();
            $adjunto['string'] = file_get_contents($filename);
            $this->adjuntos[] = $adjunto;
        } else
        {
            throw new Exception("el archivo " . $filename . " adjunto no exuste");
        }
    }

    public function AdjuntarArchivoString($string, $nombre, $ext)
    {
        $adjunto = [];
        $adjunto['ext'] = $ext;
        $adjunto['file'] = $nombre;
        $adjunto['string'] = $string;
        $this->adjuntos[] = $adjunto;
    }

    public function Titulo($title)
    {
        $this->Title = $title;
    }

    public function Mensaje($mensaje)
    {
        $this->messaje = $mensaje;
    }

    public function Header($name, $header)
    {
        $this->header[$name] = $header;
    }

    public function Send()
    {
        $realMensaje = '';
        $mensaje = $this->getMessaje();


        if ($this->adjuntos)
        {
            $html = false;
            if (key_exists('Content-type', $this->header) && preg_match('/text\/html/i', $this->header['Content-type']))
            {
                $html = true;
            }
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x" . $semi_rand . "x";
            $this->Header('MIME-version', '1.0');
            $this->Header('Content-type', 'multipart/mixed');
            $this->Header('boundary', $mime_boundary);
            $this->Header('Content-transfer-encoding', '7BIT');
            if ($html)
            {
                $body_top = ""; //--{$mime_boundary}" . self::NewLine;
                $body_top .= "Content-type: text/html" . self::NewLine;
                $body_top .= "Content-transfer-encoding: 7BIT" . self::NewLine;
                $body_top .= "Content-description: Mail message body" . self::NewLine . self::NewLine;
            }
            $realMensaje = $body_top . $this->messaje;

            foreach ($this->adjuntos as $fichero)
            {
                if (strlen($fichero['string']) > 0)
                {
                    $realMensaje .= self::NewLine . "--{$mime_boundary}" . self::NewLine;
                    if (isset($this->config['ExtencionContenType']) && isset($this->config['ExtencionContenType'][$fichero["ext"]]))
                    {
                        $type = $this->config['ExtencionContenType'][$fichero["ext"]];
                    } else
                    {
                        $type = 'binary';
                    }
                    $realMensaje .= "Content-type: " . $type . ";name=\"" . $fichero["file"] . "\"" . self::NewLine;

                    $realMensaje .= "Content-Transfer-Encoding: BASE64\n";
                    $realMensaje .= "Content-disposition: attachment;filename=\"" . $fichero["file"] . "\"" . self::NewLine . self::NewLine;
                    $realMensaje .= chunk_split(base64_encode($fichero['string']));
                }
            }
        } else
        {
            $realMensaje = $mensaje;
        }

        return mail($this->getDestinos(), $this->Title, $realMensaje, $this->getHeaders());
    }

    private function getMessaje()
    {
        return wordwrap($this->messaje, 70, "\r\n");
    }

    private function getHeaders()
    {
        $h = '';
        foreach ($this->header as $name => $header)
        {
            $h .= $name . ': ' . $header . "\r\n";
        }
        return $h;
    }

    private function getDestinos()
    {
        $b = '';
        foreach ($this->Destinatarios as $destino)
        {
            $b.='<' . $destino . '> ,';
        }
        return substr($b, 0, -1);
    }

}
