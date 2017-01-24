<?php

/*
 * Copyright (C) 2017 Enyerber Franco
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Envio de correos electronicos implementando os views y layauts para enviar html 
 *
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Mail
 */
class Mailer extends \PHPMailer
{

    /**
     * cargador de views
     * @var ViewController 
     */
    public $view;

    /**
     * cargador de layauts 
     * @var LayautManager 
     */
    public $layaut;

    /**
     * manejador de html 
     * @var Html 
     */
    public $html;

    /**
     * buffer para los views 
     * @var string 
     */
    private $BufferView = '';

    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
        $conf = Mvc::Config();

        $this->layaut = new LayautManager();
        $this->html = new Mailer\Html();
        $this->view = new Mailer\MailerView($conf->App['view'], $this->html);
        $this->DirLayaut = $conf->App['layauts'];
        $this->html->SetLayaut(NULL, $conf->App['layauts']);
        $this->BufferView = '';
    }

    /**
     * Crea el mensaje y lo envia 
     * Uses the sending method specified by $Mailer.
     * @throws \phpmailerException
     * @return boolean false on error - See the ErrorInfo property for details of the error.
     */
    public function send()
    {
        $layaut = $this->html->GetLayaut();
        if (isset($layaut['Layaut']) && !is_null($layaut['Layaut']))
        {
            $this->isHTML(true);
            $this->msgHTML($this->ExecuteLayaut(), $this->html->BasePath);
        } else
        {
            if ($this->isHTML())
            {
                $this->Body.=$this->BufferView;
            }
        }
        parent::send();
    }

    /**
     * layaut
     * @param string $layaut
     * @param string $dir
     */
    public function SetLayaut($layaut, $dir = NULL)
    {
        $this->html->SetLayaut($layaut, $dir);
    }

    /**
     * carga una plantilla view
     * @param string $view
     * @param array $agrs
     * @return string
     */
    public function LoadView($view, $agrs = [])
    {
        $this->view->ObjResponse = $this->html;
        $b = $this->view->Fetch($view, $agrs);
        $this->BufferView.=$b;
        return $b;
    }

    private function ExecuteLayaut()
    {
        $function = \Closure::bind(function( $content, $LayautController)
                {
                    $layaut = $this->GetLayaut();

                    //$__name = ($layaut['Dir'] . $layaut['Layaut'] . '.php');
                    $__name = ($layaut['Dir'] . $layaut['Layaut']);

                    if ((strpos($layaut['Layaut'], ':') !== false))
                    {
                        $__name.=$layaut['Layaut'];
                    }
                    if (is_null($layaut['Layaut']) || $layaut['Layaut'] == '')
                        return $content;

                    try
                    {
                        $param = ['content' => $content] + $LayautController->jsonSerialize();

                        if (isset($layaut['params']))
                        {
                            $param+=$layaut['params'];
                        }
                        $loader = new TemplateLoad(Mvc::App()->Config());

                        return $loader->Fetch($this, $__name, $param);
                    } catch (LayautException $ex)
                    {
                        throw $ex;
                    } catch (TemplateException $ex)
                    {
                        throw new LayautException("EL LAYAUT " . $__name . " NO EXISTE ");
                    } catch (Exception $ex)
                    {
                        throw $ex;
                    }
                }, $this->html, get_class($this->html));

        return $function($this->BufferView, $this->layaut);
    }

    /**
     * Add an embedded (inline) attachment from a file.
     * This can include images, sounds, and just about any other document type.
     * These differ from 'regular' attachments in that they are intended to be
     * displayed inline with the message, not just attached for download.
     * This is used in HTML messages that embed the images
     * the HTML refers to using the $cid value.
     * Never use a user-supplied path to a file!
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment; Use this to reference
     *        the content when using an embedded image in HTML.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File MIME type.
     * @param string $disposition Disposition to use
     * @return boolean True on successfully adding an attachment
     */
    public function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = '', $disposition = 'inline')
    {
        if (!@is_file($path))
        {
            // $this->setError($this->lang('file_access') . $path);
            return false;
        }

        // If a MIME type is not specified, try to work it out from the file name
        if ($type == '')
        {
            $type = self::filenameToType($path);
        }

        $filename = basename($path);
        if ($name == '')
        {
            $name = $filename;
        }

        // Append to $attachment array
        $this->attachment[] = array(
            0 => $path,
            1 => $filename,
            2 => $name,
            3 => $encoding,
            4 => $type,
            5 => false, // isStringAttachment
            6 => $disposition,
            7 => $cid
        );
        return true;
    }

    /**
     * Create a message body from an HTML string.
     * Automatically inlines images and creates a plain-text version by converting the HTML,
     * overwriting any existing values in Body and AltBody.
     * Do not source $message content from user input!
     * $basedir is prepended when handling relative URLs, e.g. <img src="/images/a.png"> and must not be empty
     * will look for an image file in $basedir/images/a.png and convert it to inline.
     * If you don't provide a $basedir, relative paths will be left untouched (and thus probably break in email)
     * If you don't want to apply these transformations to your HTML, just set Body and AltBody directly.
     * @access public
     * @param string $message HTML message string
     * @param string $basedir Absolute path to a base directory to prepend to relative paths to images
     * @param boolean|callable $advanced Whether to use the internal HTML to text converter
     *    or your own custom converter @see PHPMailer::html2text()
     * @return string $message The transformed message Body
     */
    public function msgHTML($message, $basedir = '', $advanced = false)
    {
        preg_match_all('/(src|background)=["\'](.*)["\']/Ui', $message, $images);
        if (array_key_exists(2, $images))
        {
            if (strlen($basedir) > 1 && substr($basedir, -1) != '/')
            {
                // Ensure $basedir has a trailing /
                $basedir .= '/';
            }
            foreach ($images[2] as $imgindex => $url)
            {
                // Convert data URIs into embedded images
                if (preg_match('#^data:(image[^;,]*)(;base64)?,#', $url, $match))
                {
                    $data = substr($url, strpos($url, ','));
                    if ($match[2])
                    {
                        $data = base64_decode($data);
                    } else
                    {
                        $data = rawurldecode($data);
                    }
                    $cid = md5($url) . '@phpmailer.0'; // RFC2392 S 2
                    if ($this->addStringEmbeddedImage($data, $cid, 'embed' . $imgindex, 'base64', $match[1]))
                    {
                        $message = str_replace(
                                $images[0][$imgindex], $images[1][$imgindex] . '="cid:' . $cid . '"', $message
                        );
                    }
                    continue;
                }
                if (
                // Only process relative URLs if a basedir is provided (i.e. no absolute local paths)
                        !empty($basedir)
                        // Ignore URLs containing parent dir traversal (..)
                        && (strpos($url, '..') === false)
                        // Do not change urls that are already inline images
                        && substr($url, 0, 4) !== 'cid:'
                        // Do not change absolute URLs, including anonymous protocol
                        && !preg_match('#^[a-z][a-z0-9+.-]*:?//#i', $url)
                )
                {
                    $filename = basename($url);
                    $directory = dirname($url);
                    if ($directory == '.')
                    {
                        $directory = '';
                    }
                    $cid = md5($url) . '@phpmailer.0'; // RFC2392 S 2
                    if (strlen($directory) > 1 && substr($directory, -1) != '/')
                    {
                        $directory .= '/';
                    }
                    if ($this->addEmbeddedImage(
                                    $basedir . $directory . $filename, $cid, $filename, 'base64', self::_mime_types((string) self::mb_pathinfo($filename, PATHINFO_EXTENSION))
                            )
                    )
                    {
                        $message = preg_replace(
                                '/' . $images[1][$imgindex] . '=["\']' . preg_quote($url, '/') . '["\']/Ui', $images[1][$imgindex] . '="cid:' . $cid . '"', $message
                        );
                    }
                }
            }
        }
        $this->isHTML(true);
        // Convert all message body line breaks to CRLF, makes quoted-printable encoding work much better
        $this->Body = $this->normalizeBreaks($message);
        $this->AltBody = $this->normalizeBreaks($this->html2text($message, $advanced));
        if (!$this->alternativeExists())
        {
            $this->AltBody = 'To view this email message, open it in a program that understands HTML!' .
                    self::CRLF . self::CRLF;
        }
        return $this->Body;
    }

}
