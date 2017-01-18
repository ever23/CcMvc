<?php

namespace Cc\Mvc;

use Cc\Mvc;
use Cc\ValidDependence;
use Cc\ValidDefault;
use Cc\Inyectable;

/**
 * clase base para modelos de formularios html 
 * 
 * 
 * EL METODO Campos DEBE REOTRNAR UN ARRAY CON LOS NOMBRES DE LOS CAMPOS COMO INDICES  Y Y UN ARRAY COMO VALOR
 * QUE DEBE CONTENER EN EL INDICE  0 EL TIPO DE CAMPO HTML EJEMPLO PARA TEXTO NORMAL text O UN CAMPO DE CORREO email
 * EN INDICE 1 ES OPCIONAL Y DEBE CONTENER EL VALOR POR DEFECTO DEL CAMPO,
 * EL INDIICE 3 TAMBIEN ES OPCIONAL Y DEBE CONTENER LA IFORMACION PARA LA VALIDACION GENERADA CON ALGUNA DE LAS CLASES DE VALIDACION 
 * EXTENDIDAS DE {@link ValidDependence} Y EL METODO {@link ValidDependence::CreateValid}
 * EJEMPLO:
 * <code>
 * <?php
 * namespace Cc\Mvc;
 * class MyFormulario extends FormModel{
 *      protected function Campos()
 *      {
 *           $campos=[
 *                  'campo'=>[FormTypeHtml,DefultValue,typevalid],
 *                  'campo1'=>[FormTypeHtml,DefultValue,typevalid],
 *                  .       
 *                  .                                               
 *                  .
 *                  ];
 *           return $campos;
 *      }
 * }
 * </code>
 * Un ejemplo mas completo:
 * <code>
 * <?php
 * namespace Cc\Mvc;
 * class MyFormulario extends FormModel
 * {
 *      protected function Campos()
 *      {
 *              $campos=[
 *                      'nombre' => ['text','',['required'=>true]],
 *                      'apellido' => ['text','',],
 *                      'correo' => ['email'],
 *                      'telefono' => ['tel'],
 *                      'websitie'=>['text','',ValidUrl::CreateValid()],
 *                      'unaIp'=> ['text','127.0.0.1',ValidIp::CreateValid()],
 *                      'pass1' => ['password'],
 *                      'pass2' => ['password']
 *                  ];
 *          return $canpos;
 *      }
 * }
 * </code>
 * 
 * @author Enyerber Franco
 * @package CcMvc
 * @subpackage Modelo
 * @category FormModel
 * @method FormModel\Campo text() text(string $campo,string $valid = NULL)  crea un nuevo campo de tipo text
 * @method FormModel\Campo tel() tel(string $campo,string $valid = NULL)  crea un nuevo campo de tipo tel
 * @method FormModel\Campo email() email(string $campo,string $valid = NULL)  crea un nuevo campo de tipo email
 * @method FormModel\Campo number() number(string $campo,string $valid = NULL)  crea un nuevo campo de tipo number
 * @method FormModel\Campo range() range(string $campo,string $valid = NULL)  crea un nuevo campo de tipo range
 * @method FormModel\Campo url() url(string $campo,string $valid = NULL)  crea un nuevo campo de tipo url
 * @method FormModel\Campo date() date(string $campo,string $valid = NULL)  crea un nuevo campo de tipo date
 * @method FormModel\Campo datetime() datetime(string $campo,string $valid = NULL)  crea un nuevo campo de tipo datetime
 * @method FormModel\Campo week() week(string $campo,string $valid = NULL)  crea un nuevo campo de tipo week
 * @method FormModel\Campo year() year(string $campo,string $valid = NULL)  crea un nuevo campo de tipo year
 * @method FormModel\Campo month() month(string $campo,string $valid = NULL)  crea un nuevo campo de tipo month
 * @method FormModel\Campo datetime_local() datetime_local(string $campo,string $valid = NULL)  crea un nuevo campo de tipo datetime-local
 * @method FormModel\Campo file() file(string $campo,string $valid = NULL)  crea un nuevo campo de tipo file
 * 
 */
abstract class FormModel extends Model implements Inyectable, \Serializable
{

    /**
     * <code>
     * 'name'=>[type,DefultValue,typevalid]
     * </code>
     * @var array 
     */
    private $campos = [];

    /**
     * metodo de trasmision de datos 
     * @var string 
     */
    private $Method = 'POST';

    /**
     * indica si en el furmulario hay uno o mas campos tipo file
     * @var bool 
     */
    private $existFile = false;

    /**
     * nombre del formulario
     * @var string 
     */
    private $NameSubmited;

    /**
     * indica si se recibieron los datos
     * @var bool 
     */
    private $Sumited = false;

    /**
     * cantidad de instancias de la clase
     * @var int 
     */
    private static $count = 0;

    /**
     * indica si el formulario es valido
     * @var bool 
     */
    protected $valid = false;

    /**
     * url donde se enviara el formulario
     * @var string 
     */
    protected $action = '';

    /**
     * indica si solo se aceptaran datos provenientes de la misma url
     * @var bool 
     */
    protected $protected = false;

    /**
     * indice en el array de campos que indica el tipo de campo 
     */
    const TypeHtml = 0;

    /**
     * indice en el array de campos que indica el valor por defecto del campo
     */
    const DefaultConten = 1;

    /**
     * indice en el array de campos que indica la configuracion de validacion
     */
    const Validate = 2;

    /**
     * indica si el objeto contruido fue inyectado como dependencia o no
     * @var bool 
     */
    private $inyected = false;

    /**
     *
     * @var url 
     */
    protected $RequestUri = '';

    /**
     *
     * @var Request 
     */
    protected $Request;
    protected $macros = [];
    protected $alternativeCampos = [];

    public static function CtorParam()
    {
        return [NULL, 'POST', true, true];
    }

    /**
     * 
     * @param string|NULL $action indica a que url se enviara el formulario
     * @param string $method el metodo de envio por ahora solo se acepta GET o POST
     * @param bool $protected indica si solo se aceptaran datos provenientes otra url
     * @param bool $inyected indica si el objeto contruido fue inyectado como dependencia o no
     */
    public function __construct($action = NULL, $method = 'POST', $protected = true, $inyected = false)
    {
        $this->Method = $method != 'GET' && $method != 'POST' ? 'POST' : $method;
        $this->NameSubmited = 'Submited' . static::class . self::$count;
        self::$count++;
        $this->protected = $protected;
        $this->inyected = $inyected;
        $this->Request = Mvc::App()->Request;
        $this->RequestUri = Mvc::App()->Request->Uri();
        if (!is_null($action))
        {
            $this->action = $action;
        } else
        {
            $this->action = Mvc::App()->Request->Url();
        }
        if (!$inyected)
        {
            $this->Request();
        }
    }

    /**
     * serializa el objeto 
     * @return string
     * @access private
     */
    public function serialize()
    {
        $seri = [
            'campos' => $this->campos,
            'Method' => $this->Method,
            'action' => $this->action,
            'NameSubmited' => $this->NameSubmited,
            'lastPage' => $this->RequestUri,
            '_ValuesModel' => $this->_ValuesModel,
            'existFile' => $this->existFile,
        ];
        return serialize($seri);
    }

    /**
     * 
     * @param array|string $serialized
     * @access private
     */
    public function unserialize($serialized)
    {
        if (is_string($serialized))
            $serialized = unserialize($serialized);
        $this->campos = $serialized['campos'];
        $this->Method = $serialized['Method'];
        $this->action = NULL;
        if (is_null($serialized['action']))
        {
            $this->protected = true;
        } else
        {
            $hostaction = parse_url($serialized['action'], PHP_URL_HOST);
            $pathAction = parse_url($serialized['action'], PHP_URL_PATH);
            if ((!$hostaction || (strcasecmp($hostaction, $this->Request->Host()) == 0)) && (strcasecmp($pathAction, $this->Request->Path())))
            {
                $this->protected = true;
            } else
            {
                $this->protected = false;
            }
        }


        $this->inyected = false;
        $this->NameSubmited = $serialized['NameSubmited'];
        //  $this->protected = $serialized['lastPage'] == Mvc::App()->Request->Uri() || $serialized['lastPage'] != Mvc::App()->Request->Referer();
        $this->inyected = false;
        $this->_ValuesModel = $serialized['_ValuesModel'];
        $this->existFile = $serialized['existFile'];
        $this->RequestUri = Mvc::App()->Request->Uri();
        $this->Request = Mvc::App()->Request;
        ;
        $this->Request(true, true);

        //return $this;
    }

    /**
     * Establece el metodo de envio del formulario puede ser GET o POST
     * @param string $method
     * @return \Cc\Mvc\FormModel
     */
    public function &Method($method = NULL)
    {
        if (is_null($method))
        {
            $this->Method = $method != 'GET' && $method != 'POST' ? 'POST' : $method;
        }
        return $this;
    }

    /**
     * Establece donde sera enviado el formulario
     * @param string $action
     * @return \Cc\Mvc\FormModel
     */
    public function &Action($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Establece si se recibiran datos desde otra url
     * @param bool $protected
     * @return \Cc\Mvc\FormModel
     */
    public function &ProtectedUrl($protected)
    {
        if (is_null($protected))
        {
            $this->protected = $protected;
        }
        return $this;
    }

    /**
     * carga los campos y verifica si existen en get o post segun se aya establecido
     * @param bool $serialized indica si se llamo desde el metodod serialized 
     * @param bool $process indica si se procesara la entrada de datos
     */
    private function Request($serialized = false, $process = true)
    {
        $this->inyected = false;

        if (!$serialized)
            $this->LoadMetaData();

        if ($process)
        {
            $this->ProcessSubmit();
            if ($this->IsSubmited() && !$this->IsValid())
            {
                foreach ($this->campos as $i => $v)
                {
                    if ($v[self::TypeHtml] != 'password' && $this->offsetExists($i))
                        $this->campos[$i][self::DefaultConten] = $this->offsetGet($i);
                }
            }
        }
    }

    /**
     * retorna los campos del formulario tomadolos de las propiedades publicas de la clase
     * @return array
     */
    protected function campos()
    {
        $reflexion = new \ReflectionClass($this);
        $prop = $reflexion->getProperties(\ReflectionProperty::IS_PUBLIC);
        $array = [];
        /* @var $propiedad \ReflectionProperty */
        foreach ($prop as $i => $propiedad)
        {
            if ($propiedad->getDeclaringClass()->name == $reflexion->name)
            {
                $name = $propiedad->getName();
                $array[$name] = $propiedad->getValue($this);
                unset($this->{$name});
            }
        }
        return $array;
    }

    /**
     * carga los metadatos de los campos
     */
    private function LoadMetaData()
    {
        $campos = $this->campos();
        if (!is_array($campos))
        {
            $campos = $this->alternativeCampos;
        }

        foreach ($campos as $i => $v)
        {
            if ($v instanceof FormModel\Campo)
            {
                $obj = $v;
                $v = [];
                $v[self::TypeHtml] = $obj->type;
                $v[self::DefaultConten] = $obj->default;
                $v[self::Validate] = $obj->GetValid();
            } else
            {
                $this->Campo($i, isset($v[self::TypeHtml]) ? $v[self::TypeHtml] : 'text')
                        ->Validator(isset($v[self::Validate]) ? $v[self::Validate] : [])->DefaultValue(isset($v[self::DefaultConten]) ? $v[self::DefaultConten] : '');
                $v[self::Validate] = $this->alternativeCampos[$i]->GetValid();
            }
            if ($v[self::TypeHtml] == 'file')
            {
                if (!is_array($this->existFile))
                {
                    $this->existFile = [];
                }
                array_push($this->existFile, $i);
            }


            if (!isset($v[self::Validate]) || (is_array($v[self::Validate]) && !isset($v[self::Validate][ValidDependence::class])))
            {

                if (isset($v[self::Validate]) && is_array($v[self::Validate]) && (!isset($v[self::Validate][ValidDependence::class]) ))
                {
                    $options = $v[self::Validate] + ['required' => false];
                } else
                {
                    $options = ['required' => false];
                }
                if (!isset($v[self::Validate]))
                {
                    $v[self::Validate] = [];
                }
                if (preg_match('/\w\[\]/', $v[self::TypeHtml]))
                {
                    $item = $this->ParseValid([], str_replace('[]', '', $v[self::TypeHtml]), ['required' => true] + $options);
                    $v[self::Validate] = ValidArray::CreateValid(['ValidItems' => $item] + $options);
                } else
                {
                    $v[self::Validate] = $this->ParseValid($v[self::Validate], $v[self::TypeHtml], $options);
                }
            }
            $this->alternativeCampos[$i]->SetRealValid($v[self::Validate]);
            $this->campos[$i] = $v;
            $this->_ValuesModel[$i] = '';
        }
    }

    /**
     * 
     * @param array $v configuracion de validacion
     * @param string $type
     * @param array $options configuracion de validacion del usuario
     * @return array
     */
    protected function ParseValid($v, $type, $options)
    {
        switch (strtolower($type))
        {
            case 'number':
            case 'range':
                $v = ValidNumber::CreateValid($options);
                break;
            case 'email':
                $v = ValidEmail::CreateValid($options);
                break;
            case 'tel':
                $v = ValidTelf::CreateValid($options);
                break;
            case 'color':
                $v = ValidExadecimal::CreateValid($options);
                break;
            case 'date':
                $v = ValidDate::CreateValid(['format' => 'Y/m/d'] + $options);
                break;
            case 'datetime':
                $v = ValidDate::CreateValid(['format' => 'Y/m/d H:i:s'] + $options);
                break;
            case 'week':
                $v = ValidDate::CreateValid(['format' => 'Y-W'] + $options);
                break;
            case 'month':
                $v = ValidDate::CreateValid(['format' => 'Y-m'] + $options);
                break;
            case 'time':
                $v = ValidDate::CreateValid(['format' => 'H:i:s'] + $options);
                break;
            case 'year':
                $v = ValidDate::CreateValid(['format' => 'Y'] + $options);
                break;
            case 'datetime-local':
                $v = ValidDate::CreateValid(['format' => \DateTime::W3C] + $options);
                break;
            case 'url':
                $v = ValidUrl::CreateValid($options);
                break;
            case 'file':
                //case 'image':
                $v = ValidString::CreateValid(['opt_files' => $options, 'required' => false]);
                break;
            default :

                $v = ValidString::CreateValid($options);
        }
        return $v;
    }

    /**
     * Establece el valor por defecto de campo en el formulario
     * @param string $name
     * @param string $value
     */
    public function DefaultValue($name, $value = NULL)
    {
        if (is_array($name) || $name instanceof \Traversable)
        {
            foreach ($name as $i => $v)
            {
                $this->DefaultValue($i, $v);
            }
            return;
        }

        if (key_exists($name, $this->campos))
        {
            $this->campos[$name][self::DefaultConten] = $value;
        }
    }

    /**
     * obtiene el valor por defecto del campo en el formulario
     * @param string $name
     * @return mixes
     */
    public function GetDefaultValue($name)
    {
        if (isset($this->campos[$name][self::DefaultConten]))
        {
            return $this->campos[$name][self::DefaultConten];
        }
    }

    /**
     * obtiene el valor de un campo dado
     * @param string $name
     * @return mixes
     * @internal funcion magica
     */
    public function __get($name)
    {

        if (isset($this->_ValuesModel[$name]))
        {
            if ($this->_ValuesModel[$name] instanceof ValidDependence && isset($this->_ValuesModel[$name]->option['StrictValue']) && $this->_ValuesModel[$name]->option['StrictValue'] == true)
            {
                return $this->_ValuesModel[$name]->get();
            } else
            {
                return $this->_ValuesModel[$name];
            }
        } else
        {
            ErrorHandle::Notice("Propiedad '" . $name . "' no definida ");
        }
    }

    /**
     * obtiene el valor de un campo dado
     * @param string $offset
     * @return mixes
     * @internal \ArrayAccess
     */
    public function offsetGet($offset)
    {
        if (isset($this->_ValuesModel[$offset]))
        {
            if ($this->_ValuesModel[$offset] instanceof ValidDependence && isset($this->_ValuesModel[$offset]->option['StrictValue']) && $this->_ValuesModel[$offset]->option['StrictValue'] == true)
            {
                return $this->_ValuesModel[$offset]->get();
            } else
            {
                return $this->_ValuesModel[$offset];
            }
        } else
        {
            ErrorHandle::Notice("Indice '" . $offset . "' no definido ");
        }
    }

    /**
     * indica si se recibieron los datos del modelo
     * @return bool
     */
    public function IsSubmited()
    {
        if ($this->inyected)
        {
            $this->Request();
        }
        return $this->Sumited;
    }

    /**
     * indica si los datos del modelo son validos
     * @return bool
     */
    public function IsValid()
    {
        return $this->valid;
    }

    /**
     * 
     * @return boolean
     */
    private function ProcessSubmit()
    {
        if ($this->protected && !$this->VerificateReferer($this->Request->Referer()))
        {
            return false;
        }
        if ($this->Method == 'GET')
        {
            if (isset($this->Request->Get[$this->NameSubmited]))
            {
                $this->Sumited = true;
                $r = $this->ValidateValues($this->Request->Get);
            } else
            {

                return false;
            }
        } elseif ($this->Method == 'POST')
        {

            if (isset($this->Request->Post[$this->NameSubmited]))
            {
                $this->Sumited = true;

                $r = $this->ValidateValues($this->Request->Post);
            } else
            {
                return false;
            }
        } else
        {
            $r = false;
        }

        if ($this->existFile)
        {
            $this->ValidateFile();
        }



        if ($this->Sumited)
        {
            if (method_exists($this, 'OnSubmit'))
            {
                $this->OnSubmit(...Mvc::App()->DependenceInyector->SetFunction([$this, 'OnSubmit'])->Param());
            }
        }
        return $r;
    }

    private function VerificateReferer($referer)
    {

        $url = parse_url($referer, PHP_URL_PATH);
        $hostReferer = parse_url($referer, PHP_URL_HOST);
        if (!(strcasecmp($hostReferer, $this->Request->Host()) == 0))
        {
            return false;
        }
        if (!(strcasecmp($url, $this->Request->Path()) == 0))
        {
            return false;
        }
        return true;
    }

    /**
     * valida los archivos recibidos
     * @return boolean
     */
    private function ValidateFile()
    {

        foreach ($this->existFile as $v)
        {
            $this->_ValuesModel[$v] = new PostFiles($v);

            $options = $this->campos[$v][self::Validate][1][0]['opt_files'];
            if (key_exists('required', $options) && $options['required'])
            {
                if (!$this->_ValuesModel[$v]->is_Uploaded())
                {
                    $this->valid = false;
                    return false;
                }
            }
            if ($this->_ValuesModel[$v]->is_Uploaded() && isset($options['ext']))
            {
                if (!in_array($this->_ValuesModel[$v]->getExtension(), explode(',', $options['ext'])))
                {
                    $this->valid = false;
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * validacion 
     * @param array $values
     * @return boolean
     */
    protected function ValidateValues(array $values)
    {
        $types = [];
        $val = [];
        $valid = true;
        foreach ($this->campos as $i => $v)
        {
            $type = $v[self::TypeHtml];
            if (!isset($values[$i]))
            {
                $val[$i] = NULL;
            } else
            {
                $val[$i] = $values[$i];
            }
            if ($type != 'file')
                $types[$i] = $v[self::Validate];
        }

        $valuesModel = ValidDefault::Filter($val, $types, ValidDefault::DefaultValid);
        if (!$valid || !$valuesModel->IsValid())
        {

            $this->valid = false;
            $this->_ValuesModel = &$valuesModel;
            return false;
        } else
        {

            $this->_ValuesModel = &$valuesModel;
            $this->valid = true;
            return true;
        }
    }

    /**
     * imprime un imput desde el nombre de un dato en el modelo
     * @param string $name
     * @param array $attrs [attr=>valor]
     * @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function Input($name, $attrs = [], $return = false)
    {

        if (is_object($attrs) && $attrs instanceof \Smarty_Internal_Template)
        {

            $attrs = $name;
            $name = $attrs['name'];
            $return = false;
        }
        if (isset($this->campos[$name]))
        {
            if (preg_match('/\w\[\]/', $this->campos[$name][self::TypeHtml]))
            {
                $attrs['type'] = str_replace('[]', '', $this->campos[$name][self::TypeHtml]);
                $attrs['name'] = $name . '[]';
            } else
            {
                $attrs['type'] = $this->campos[$name][self::TypeHtml];
                $attrs['name'] = $name;
            }


// echo "<input type='" . $this->campos[$name][0] . "' name='" . $name . "'";
            if (isset($this->campos[$name][self::DefaultConten]))
            {
                if (is_array($this->campos[$name][self::DefaultConten]) || $this->campos[$name][self::DefaultConten] instanceof ValidArray)
                {
                    $attrs['value'] = '';
                } else
                {
                    $attrs['value'] = $this->campos[$name][self::DefaultConten];
                }
            }
            $attrValid = [ 'pattern', 'min', 'max', 'maxlength', 'size', 'accept', 'step', 'required', 'multiple', 'title', 'placeholder', 'checked'];
            if ($valid = ValidDefault::GetOptions($this->campos[$name][self::Validate]))
            {
                if ($attrs['type'] == 'file')
                    $valid = $valid['opt_files'];
                foreach ($valid as $i => $v)
                {
                    if (in_array($i, $attrValid))
                    {
                        $attrs[$i] = $v;
                    }
                }
            }


            $buff = $this->PrintMacro($attrs['type'], $attrs['name'], $attrs);
            if ($buff)
            {
                return $buff;
            } else
            {
                echo $buff;
            }
        } else
        {
            ErrorHandle::Notice('EL CAMPO ' . $name . ' NO EXISTE');
        }
    }

    /**
     * imprime un textarea desde el nombre de un dato en el modelo
     * @param string $name
     * @param array $attrs [attr=>valor]
     *  @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function TextArea($name, $attrs = [], $return = false)
    {
        if (is_object($attrs) && $attrs instanceof \Smarty_Internal_Template)
        {

            $attrs = $name;
            $name = $attrs['name'];
            $return = false;
        }
        if (isset($this->campos[$name]))
        {
            if (preg_match('/\w\[\]/', $this->campos[$name][self::TypeHtml]))
            {
                $attrs['type'] = str_replace('[]', '', $this->campos[$name][self::TypeHtml]);
                $attrs['name'] = $name . '[]';
            } else
            {
                $attrs['type'] = $this->campos[$name][self::TypeHtml];
                $attrs['name'] = $name;
            }
            $value = '';
            if (isset($this->campos[$name][self::DefaultConten]))
            {
                $value = $this->campos[$name][self::DefaultConten];
            }
            $attrValid = [ 'pattern', 'min', 'max', 'maxlength', 'size', 'accept', 'placeholder', 'required'];
            if (isset($this->campos[$name][self::Validate]) && $valid = ValidDefault::GetOptions($this->campos[$name][self::Validate]))
            {
                foreach ($valid as $i => $v)
                {
                    if (in_array($i, $attrValid))
                    {
                        $attrs[$i] = $v;
                    }
                }
            }
            $type = $attrs['type'];
            unset($attrs['type']);

            $buff = $this->PrintMacro($type, $attrs['name'], $attrs, $value);
            if ($return)
            {
                return $buff;
            } else
            {
                echo $buff;
            }
        } else
        {
            ErrorHandle::Notice('EL CAMPO ' . $name . ' NO EXISTE');
        }
    }

    /**
     * imprime un select desde el nombre de un dato en el modelo
     * @param type $name
     * @param array|\Traversable $options [opcion=>sttr]
     * @param array $attrs [attr=>valor]
     *  @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function Select($name, $options = [], array $attrs = [], $return = false)
    {
        if (is_object($options) && $options instanceof \Smarty_Internal_Template)
        {

            $attrs = $name;
            $name = $attrs['name'];
            $options = [];



            $return = false;
        }
        if (isset($this->campos[$name]))
        {
            if (isset($this->campos[$name][self::DefaultConten]))
            {
                $attrs['value'] = $this->campos[$name][self::DefaultConten];
            }
            if (isset($this->campos[$name][self::Validate]) && $valid = ValidDefault::GetOptions($this->campos[$name][self::Validate]))
            {
                if ($options === [] && isset($valid['options']) && (is_array($valid['options']) || $valid['options'] instanceof \Traversable))
                {
                    $options = $valid['options'];
                }
                // var_dump($valid['options']);
            }


            $attrs['name'] = $name;
            $buff = $this->PrintMacro('select', $attrs['name'], $attrs, $options);
            if ($return)
            {
                return $buff;
            } else
            {
                return $buff;
            }
        } else
        {
            ErrorHandle::Notice('EL CAMPO ' . $name . ' NO EXISTE');
        }
    }

    /**
     * inicia el formulario
     * @param array $attrs
     *  @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function BeginForm($attrs = [], $return = false)
    {
        if ($this->existFile)
        {
            $attrs['ENCTYPE'] = "multipart/form-data";
        }
        $attrs['action'] = $this->action;
        $attrs['method'] = $this->Method;

        if ($return)
        {
            return Html::OpenTang('form', $attrs);
        } else
        {
            echo Html::OpenTang('form', $attrs);
        }
    }

    /**
     * imprime el boton de enviar el formulario
     * @param string $value
     * @param array $attrs
     *  @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function ButtonSubmit($value = '', $attrs = [], $return = false)
    {
        if (is_object($attrs) && $attrs instanceof \Smarty_Internal_Template)
        {
            $attrs = $value;
            $value = $attrs['value'];
            $return = false;
        }
        $attrs['value'] = 1;
        $attrs['name'] = $this->NameSubmited;
        $buff = $this->PrintMacro('button', 'submit', $attrs, $value);
        if ($return)
        {
            return $buff;
        } else
        {
            //   echo Html::input(['type' => 'hidden', 'name' => $attrs['name'], 'value' => 1]);
            return $buff;
        }
    }

    /**
     * finaliza el formulario
     *  @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function EndForm($return = false)
    {
        $r = Html::input(['type' => 'hidden', 'name' => $this->NameSubmited, 'value' => 1]);
        $r .= Html::CloseTang('form');
        if ($return)
        {
            return $r;
        } else
        {
            echo $r;
        }
    }

    /**
     *  imprime el formulario completo 
     * @param array $attrs atrubutos de la etiqueta de formulario
     * @param array $campos atributos de los campos
     * @param array $submit stributos de el boton submit
     *  @param bool $return indica si en contenido sera impreso en el buffer o retornado
     */
    public function PrintForm($attrs, array $campos = [], $submit = [], $return = false)
    {
        $specialTang = ['select', 'textarea', 'hidden'];
        $buff = '';
        $buff.=$this->BeginForm($attrs, true);

        $buff.= Html::OpenTang('ul', ['class' => 'FormList']);
        foreach ($this->campos as $i => $v)
        {
            $typeHtml = $v[self::TypeHtml];
            $name = isset($campos[$i]['text']) ? $campos[$i]['text'] : '';
            $attr = isset($campos[$i]['attr']) ? $campos[$i]['attr'] : [];
            if ($typeHtml == 'hidden')
            {
                $buff.=$this->Input($i, $attr, true);
                continue;
            }
            $buff.= Html::OpenTang('li', ['class' => 'FormRow']) . Html::label($name, ['from' => $i]);
            if (!in_array($typeHtml, $specialTang))
            {
                if (isset($campos[$i]['ListValue']))
                {
                    foreach ($campos[$i]['ListValue'] as $val)
                    {
                        $buff.= $this->Input($i, $attr + ['value' => $val], true);
                    }
                } else
                {
                    $buff.=$this->Input($i, $attr, true);
                }
            } else
            {
                switch ($typeHtml)
                {
                    case 'textarea':
                    case 'textarea[]':
                        $buff.=$this->TextArea($i, $attr, true);
                        break;
                    case 'select':

                        if (isset($campos[$i]) && isset($campos[$i]['option']) && (is_array($campos[$i]['option']) || $campos[$i]['option'] instanceof \Traversable))
                        {

                            $buff.=$this->Select($i, $campos[$i]['option'], $attr, true);
                        } else
                        {

                            $buff.=$this->Select($i, [], $attr, true);
                        }
                        break;
                }
            }
            $error = $this->GetError($i);
            if ($error)
            {
                $buff.= Html::br() . Html::span($error, ['style' => 'color:red;', 'class' => 'FormError']);
            }

            $buff.= Html::CloseTang('li');
        }

        $buff.= Html::OpenTang('li', ['class' => 'FormRow']);
        $buff.=$this->ButtonSubmit(isset($submit['value']) ? $submit['value'] : 'ENVIAR', $submit, true);
        $buff.= Html::CloseTang('li');
        $buff.= Html::CloseTang('ul');
        $buff.=$this->EndForm(true);
        if ($return)
        {
            return $buff;
        } else
        {
            echo $buff;
        }
    }

    /**
     * funcion de bloque para el motor de templetes smarty para imprimir el formulario 
     * @param array $params
     * @param string|NULL $content
     * @param \Smarty &$smarty
     * @param bool &$repeat
     * @return string
     * @internal soopara efectos de plantillas smarty
     */
    public function Form($params, $content, &$smarty, &$repeat)
    {
        if (!isset($content))
        {
            $content = $this->BeginForm($params, true);
        } else
        {
            $content = $content . $this->EndForm(true);
        }
        return $content;
    }

    /**
     * registra el objeto para las funciones smarty
     * @return string
     * @internal soopara efectos de plantillas smarty
     */
    public function ParseSmaryTpl()
    {
        $smarty = parent::ParseSmaryTpl();
        $smarty['allowed'] = [];
        $smarty['block_methods'][] = 'Form';
        return $smarty;
    }

    /**
     * Registra un Macro para imprimir el html de un campo 
     * ejemplo:
     * <code>
     * <?php
     * $this->RegisterMacros('campo1',function($attrs)
     * {
     *      return '<input type="text" name="campo1">';
     * });
     * </code>
     * @param type $name
     * @param \Cc\Mvc\callable $callback
     */
    protected function RegisterMacros($name, callable $callback)
    {
        $this->macros[$name] = $callback;
    }

    /**
     * imprime el macro 
     * @param type $type
     * @param type $name
     * @param type $attrs
     * @param type $options
     * @return type
     */
    private function PrintMacro($type, $name, $attrs, $options = [])
    {
        if (isset($this->macros[$name]))
        {
            return $this->macros[$name]($attrs);
        } else
        {
            $buff = '';
            switch ($type)
            {
                case 'textarea':

                    $buff = Html::TextArea($options, $attrs);
                    break;
                case 'select':
                    $buff = Html::select($attrs, $options);
                    break;
                case 'button':
                    $buff = Html::button($options, $attrs);
                    break;
                default :
                    $buff = Html::input($attrs);
            }
            return $buff;
        }
    }

    /**
     * Agrega un campo en el formulario 
     * @param string $name nombre del campo
     * @param string $type tipo de campo 
     * @param array $valid validacion
     * @param mixes $defaul valor por defecto
     * @return FormModel\Campo campo
     */
    protected function &Campo($name, $type = 'text')
    {
        $this->alternativeCampos[$name] = new FormModel\Campo($name, $type);
        return $this->alternativeCampos[$name];
    }

    public function &__call($name, $arguments)
    {

        $otros = [
            'telefono' => 'tel',
            'texto' => 'text',
            'datetime_local' => 'datetime-local',
        ];
        if (isset($otros[$name]))
        {
            $name = $otros[$name];
        }
        if (isset($arguments[1]))
        {
            return $this->Campo($arguments[0], $name)->Validator($arguments[1]);
        }
        return $this->Campo($arguments[0], $name);
    }

    /**
     * copia la configuracion y validacion de un campo a otro
     * @param string $copy
     * @param string $to
     * @return FormModel\Campo campo
     */
    protected function &CopyCampo($copy, $to)
    {
        if (isset($this->alternativeCampos[$copy]))
        {
            $this->alternativeCampos[$to] = clone $this->alternativeCampos[$copy];
            $this->alternativeCampos[$to]->name = $to;
        }
        return $this->alternativeCampos[$to];
    }

    /**
     * optiene el error de validacion de un campo
     * @param string $offset
     * @param \Smarty_Internal_Template $options
     * @param type $attrs
     * @return boolean
     */
    public function GetError($offset, $options = NULL, $attrs = [])
    {
        if (is_object($options) && $options instanceof \Smarty_Internal_Template)
        {
            $offset = $attrs['name'];
        }

        if ($this->offsetExists($offset))
        {
            if ($this->_ValuesModel[$offset] instanceof ValidDependence && !$this->_ValuesModel[$offset]->IsValid())
            {
                return $this->alternativeCampos[$offset]->GetError($this->_ValuesModel[$offset]);
            } else
            {
                return false;
            }
        } else
        {
            ErrorHandle::Notice("Indice '" . $offset . "' no definido ");
        }
    }

}
