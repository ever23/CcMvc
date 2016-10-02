<?php

namespace Cc\Mvc;

use Cc\Mvc;
use Cc\Autoload\SearchClass;
use Cc\UrlManager;

/**
 * enruta controladores y archivos 
 * @autor ENYREBER FRANCO       <enyerverfranco@gmail.com> , <enyerverfranco@outlook.com>                                                    
 * @package CcMvc
 * @subpackage Request
 */
class Router extends \Cc\Router
{

    const Get = 0x2;
    const Path = 0x4;
    const NoExtContr = 'NoUse';
    const RequireExtContr = 'Require';
    const UseExtContr = 'Use';

    protected $RequestFilename;
    public static $query = NULL;

    public function __construct($Conf, $query = NULL)
    {
        parent::__construct($Conf);


        $a = explode('?', $_SERVER['REQUEST_URI']);
        $this->RequestFilename = urldecode(trim($a[0]));

        if (is_null(self::$query))
        {
            if (is_null($query))
            {
                $a2 = [];
                if (isset($_SERVER['QUERY_STRING']))
                    parse_str($_SERVER['QUERY_STRING'], $a2);
                self::$query = isset($a2[$this->config['GetControllers']]) ? $a2[$this->config['GetControllers']] : 'index';
            } else
            {
                self::$query = $query;
            }
        }
    }

    public function GetRequestFile()
    {
        return $this->RequestFilename;
    }

    /**
     * INDICARA EL PAQUETE , CONTROLADOR Y METODO QUE REQUIRIO EL CLIENTE 
     * @return array [paquete=>'',class=>'',method=>'']
     */
    public function GetController()
    {
        header("Cache-Control: no-cache");
        switch ($this->config['GetControllerFormat'])
        {
            case self::Get:
                return $this->SelectPageGet();
            case self::Path:
                $this->config['OperadorAlcance'] = '/';
                Mvc::Config()->Router = $this->config;

                return $this->SelectPagePath();
        }
    }

    /**
     * CONSTRULLE UN LINK HACIA UNCONTROLADOR 
     * @param mixes $page un string que siga la sintaxis de llamada por get o path donde se especifiqen 
     * el controlado y el metodo si es un array deve contener los indices [paquete] indicara el paquete 
     * [class] indicara el controlador [method]indicara el metodos que sera llamado
     * @param array $get variables que que tendra el link 
     * @param string $ScriptName opcional nombre del script
     * @return string link valido para usa en link html
     */
    public static function Href($page, array $get = [], $ScriptName = NULL)
    {
        $req = '';
        $conf = Mvc::App()->Config();
        $r = new self($conf['Router']);
        switch ($r->config['GetControllerFormat'])
        {
            case self::Get:
                $req = $r->CreateHref($page, $r->config['OperadorAlcance']);
                return UrlManager::BuildUrl($conf->Router['protocol'], $_SERVER['HTTP_HOST'], '', $ScriptName . '?' . Server::SerializeGet(array_merge([$r->config['GetControllers'] => $req], $get)));

            case self::Path:
                $req = $r->CreateHref($page, '/');

                $getserialise = Server::SerializeGet($get);
                return UrlManager::BuildUrl($conf->Router['protocol'], $_SERVER['HTTP_HOST'], (is_null($ScriptName) ? $r->config['DocumentRoot'] : $ScriptName) . $req, (($getserialise == '') ? '' : '?' ) . $getserialise);
            // return (is_null($ScriptName) ? $r->config['DocumentRoot'] : $ScriptName) . $req . (($getserialise == '') ? '' : '?' ) . $getserialise;
        }
    }

    protected function CreateHref($page, $alcance)
    {
        $ext = $paquete = $class = $method = '';

        if (is_array($page))
        {
            $paquete = isset($page['paquete']) && $page['paquete'] != '' ? $page['paquete'] : NULL;
            $class = isset($page['controller']) ? $page['controller'] : NULL;
            $method = isset($page['method']) ? $page['method'] : NULL;
            $ext = isset($page['extencion']) ? $page['extencion'] : NULL;
        } elseif (is_string($page))
        {
            list($paquete, $class, $method, $ext) = $this->Page($page, $alcance);
        }
        $paquete = !is_null($paquete) && !is_null($class) ? $paquete . $alcance : $paquete;
        $class = !is_null($class) && !is_null($method) ? $class . $alcance : $class;
        return $paquete . $class . $method . (!is_null($ext) ? '.' . $ext : '');
    }

    /**
     * INDICA SI EL ARCHIVO REQUERIDO EXISTE Y ES ENRUTAR CON RouterFile
     * @param string $orig_path OPCIONAL EL DOCUMENT ROOT DE LA APLIACION
     * @param strig $path OPCIONAL EL ARCHIVO REQUERIDO 
     * @return bool
     */
    public function &IsEnrutableFile($orig_path = NULL, $path = NULL)
    {
        $ret = parent::IsEnrutableFile($orig_path, is_null($path) ? $this->RequestFilename : $path);
        // $f=new \SplFileInfo;

        if ($ret && $ret->__toString() == Mvc::App()->GetExecutedFile())
        {
            $ret = NULL;
        }
        return $ret;
    }

    /**
     * carga el achivo requerido y aplica las headers correspondientes al archivo
     * @param SplFileInfo $splinfo
     */
    public function RouterFile(\SplFileInfo &$splinfo = NULL)
    {
        //DocumentBuffer::Clear();
        if (is_null($splinfo))
        {
            $splinfo = &$this->InfoFile;
        }
        if ($splinfo->getExtension() == 'php')
        {
            spl_autoload_unregister([Mvc::App(), 'autoloadCore']);
            SearchClass::StopAutoloadClass();
            self::LoadFilePhp($splinfo);
        } else
        {
            //Mvc::App()->Response->Destroy();
            //Mvc::App()->Response->SetLayaut(NULL);
            $conf = Mvc::App()->Config();
            $contenttype = Mvc::App()->Content_type;
            if (array_key_exists($splinfo->getExtension(), $conf['Response']['ExtencionContenType']))
            {
                $contenttype = $conf['Response']['ExtencionContenType'][$splinfo->getExtension()];
                if (!isset($conf['Response']['Accept'][$contenttype]['staticFile']) || !$conf['Response']['Accept'][$contenttype]['staticFile'])
                {
                    Mvc::App()->ProcessConten = false;
                }
            } else
            {
                if (!isset($conf['Response']['Accept'][$contenttype]['staticFile']) || !$conf['Response']['Accept'][$contenttype]['staticFile'])
                {
                    Mvc::App()->ProcessConten = false;
                }
            }

            if (self::HeadersReponseFiles($splinfo, $contenttype, $this->config['CacheExpiresTime'], $conf['debung']['NoReenviarFiles']))
            {

                readfile($splinfo);
            } else
            {
                DocumentBuffer::Clear();
            }
            exit;
        }
    }

    public static function HeadersReponseFiles($spl, $ContentType, $caheExpire = NULL, $reenv = false)
    {
        if (!Mvc::App()->ChangeResponseConten($ContentType))
        {
            // $contenttype = Mvc::App()->Content_type;
            Mvc::App()->ResponseContenDefault($ContentType);
        }
        return parent::HeadersReponseFiles($spl, $ContentType, $caheExpire, $reenv);
    }

    protected static function LoadFilePhp(\SplFileInfo &$spl)
    {

        $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] = Mvc::App()->Router->RequestFilename;
        $_SERVER['SCRIPT_FILENAME'] = $spl->getLinkTarget();


        foreach (headers_list() as $v)
        {
            $e = explode(':', $v);
            header_remove($e[0]);
        }
        unset($e);

        DocumentBuffer::Clear();
        Mvc::App()->ProcessConten = false;
        Mvc::App()->Buffer->SetCompres(false);
        //echo '<pre>';print_r($_SERVER);
        header("X-Powered-By: PHP/" . PHP_VERSION . " + " . get_class(Mvc::App()));
        if (class_exists('\\Runkit_Sandbox', false))
        {
            $php = new \Runkit_Sandbox();
            $php->include($spl->getLinkTarget());
            unset($php);
        } else
        {
            include $spl->getLinkTarget();
        }




        exit;
    }

    /**
     * indica si el path requerido por en cliente existe o es enruptable como controlador 
     * @return bool
     */
    public function IsNoPath()
    {

        //$script = Mvc::App()->GetExecutedFile();
        //  $dir1 = realpath($_SERVER["DOCUMENT_ROOT"] . $this->config['DocumentRoot']) . DIRECTORY_SEPARATOR;
        //$dir = str_replace(DIRECTORY_SEPARATOR, "/", preg_replace("/" . addcslashes($dir1, DIRECTORY_SEPARATOR) . "/i", "", $script));
        $path = $this->GetPath();

        if ($this->config['GetControllerFormat'] == self::Get)
        {
            if (Mvc::App()->isRouter)
            {
                $p = new \SplFileInfo(Mvc::App()->GetExecutedFile());
            } elseif (isset($_SERVER['SCRIPT_FILENAME']))
            {
                $p = new \SplFileInfo($_SERVER['SCRIPT_FILENAME']);
            }
            return $path != '' && strtolower($path) != strtolower($p->getFilename());
        } else
        {
            return count(explode('/', $path)) > 3;
        }
    }

    /**
     * retorna el path reuqerido por el cliente
     * @return type
     */
    public function GetPath()
    {

        if ($this->config['DocumentRoot'] === '/')
        {
            $dir = substr($this->RequestFilename, 1);
        } else
        {
            $dir = str_replace(strtolower($this->config['DocumentRoot']), "", strtolower($this->RequestFilename));
        }
        return $dir;
    }

    private function SelectPagePath()
    {
        $path = $this->GetPath();
        if (preg_match("/\.php/i", $path))
        {
            if (Mvc::App()->isRouter)
            {
                $p = new \SplFileInfo(Mvc::App()->GetExecutedFile());
            } elseif (isset($_SERVER['SCRIPT_FILENAME']))
            {
                $p = new \SplFileInfo($_SERVER['SCRIPT_FILENAME']);
            }
            if (strtolower($path) == strtolower($p->getFilename()))
            {
                $path = '';
            }
        }
        return $this->SelectPage($path, '/');
    }

    private function SelectPageGet()
    {
        $page = self::$query;
        if (!$page)
            $page = Mvc::App()->Config()->Controllers['DefaultControllers'];
        return $this->SelectPage($page, $this->config['OperadorAlcance']);
    }

    /**
     * indica si el controlador pasado es el que se esta ejecutando actualmente 
     * @param string $page
     * @return bool
     */
    public function is_self($page)
    {

        $p = Mvc::App()->GetController();
        if ($this->config['GetControllerFormat'] == self::Path)
        {
            $this->config['OperadorAlcance'] = '/';
        }
        if (!is_array($page))
        {
            if (filter_var($page, FILTER_VALIDATE_URL))
            {
                if (!(strcasecmp(parse_url($page, PHP_URL_HOST), $_SERVER['HTTP_HOST']) == 0))
                    return false;
                if ($this->config ['GetControllerFormat'] == self::Path)
                {

                    $url = parse_url($page, PHP_URL_PATH);

                    $page = preg_replace('/^(' . preg_quote(Mvc::Config()->Router['DocumentRoot'], '/') . ')/', '', $url);
                } else
                {
                    $url = NULL;
                    $query = parse_url($page, PHP_URL_QUERY);
                    parse_str($query, $get);
                    if (isset($get[$this->config ['GetControllers']]))
                    {
                        $page = $get[$this->config ['GetControllers']];
                    } else
                    {
                        $page = '';
                    }
                }
            }
            list($Paquete, $Controller, $Method) = $this->Page($page, $this->config['OperadorAlcance']);
        } else
        {

            $Paquete = $p['paquete'];
            $Controller = $page['controller'];
            $Method = $page['method'];
        }

        $class = $Controller == '*' ? true : strcasecmp($p['controller'], $Controller) === 0;
        $metodo = is_null($Method) || $Method == '*' ? true : strcasecmp($p['method'], $Method) === 0;
        $pakage = is_null($Paquete) || $Controller == '*' ? true : strcasecmp($p['paquete'], $Paquete) === 0;
        $pakage2 = $metodo2 = $class2 = false;
        if (is_null($Paquete))
        {
            $Paquete = $Controller != '*' ? $Controller : '';
            ;
            $Controller = $Method != '*' ? $Method : '';
            $Method = NULL;
            $class2 = $Controller == '*' ? true : strcasecmp($p['controller'], $Controller) === 0;
            $metodo2 = is_null($Method) || $Method == '*' ? true : strcasecmp($p['method'], $Method) === 0;
            $pakage2 = is_null($Paquete) || $Controller == '*' ? true : strcasecmp($p['paquete'], $Paquete) === 0;
        }


        return ($class && $metodo && $pakage) || ($class2 && $metodo2 && $pakage2);
    }

    private function Page($page, $alcance)
    {
        $extArray = explode('.', $page);
        $ext = NULL;
        if (count($extArray) == 2)
        {
            $page = $extArray[0];
            $ext = $extArray[1];
        }

        $p = explode($alcance, $page);
        $n = count($p);
        $Paquete = $Controller = $Method = NULL;
        if ($n == 2)
        {
            list($Controller, $Method) = $p;
        } elseif ($n == 3)
        {
            list($Paquete, $Controller, $Method) = $p;
        } else
        {
            $Controller = $p[0];
        }
        $Method = trim($Method) == '' ? NULL : $Method;
        $Paquete = trim($Paquete) == '' ? NULL : $Paquete;
        return [$Paquete, $Controller, $Method, $ext, $n];
    }

    private function SelectPage($page, $alcance)
    {

        if (trim($page) == '')
            $page = Mvc::App()->Config()->Controllers['DefaultControllers'];

        list($Paquete, $Controller, $Method, $ext, $count) = $this->Page($page, $alcance);


        if ($this->is_aceptable($Controller, $Method, $Paquete, $ext))
        {
            Mvc::App()->LoadError('404', $this->RouterError('Ruta invalida'));
            exit;
        }
        if (empty($Method) || $Method == '')
        {
            $Method = 'index';
        }

        return array(
            'controller' => $Controller,
            'method' => $Method,
            'paquete' => $Paquete,
            'extencion' => $ext);
    }

    public function RouterError($string)
    {
        if ($this->config['GetControllerFormat'] == self::Get)
        {
            return 'EL TEXTO QUE CONTIENE LA VARIABLE ' . $this->config['GetControllers'] . ' ES INVALIDO,'
                    . $string;
        } else
        {
            return 'EL DIRECTORIO ' . $_SERVER['REQUEST_URI'] . ' NO SE PUEDE ENRUTAR ,' . $string;
        }
    }

    public function ValidateExt($ext, array $aprovadas = [])
    {
        $config = Mvc::App()->Config();
        switch ($this->config['ExtencionController'])
        {
            case self::NoExtContr:

                if (!is_null($ext) && !in_array($ext, $aprovadas))
                {
                    return $this->RouterError(" NO SE PERMITE EXTENCIONES PARA LOS CONTROLADORES'");
                }
                break;
            case self::RequireExtContr:
                if (is_null($ext) || !key_exists($ext, $config['Response']['ExtencionContenType']))
                {
                    return $this->RouterError(" LA EXTENCION DEL CONTROLADOR ES OBLIGATORIA");
                }
                if ($aprovadas !== [] && !in_array($ext, $aprovadas))
                {
                    return $this->RouterError(',EXTENCION  ' . $ext . ' NO ADMITIDA POR EL CONTROLADOR');
                }
                break;
            case self::UseExtContr:
                if (!is_null($ext) && !key_exists($ext, $config['Response']['ExtencionContenType']))
                {
                    return $this->RouterError('NO SE ENCONTRO UN MIME TYPE PARA LA EXTENCION ' . $ext);
                }
                if ($aprovadas !== [] && !in_array($ext, $aprovadas))
                {
                    return $this->RouterError('EXTENCION  ' . $ext . ' NO ADMITIDA POR EL CONTROLADOR');
                }
                break;
        }
        if (!is_null($ext))
        {
            $Content_type = Mvc::App()->Config()->Response['ExtencionContenType'][$ext];
            if ($Content_type != Mvc::App()->Content_type)
                if (!Mvc::App()->ChangeResponseConten($Content_type))
                {
                    return $this->RouterError('EL MIME ' . $Content_type
                                    . ' NO TIENE CONFIGURADA UNA CLASE  DE RESPUESTA  ');
                }
        }
        return false;
    }

}
