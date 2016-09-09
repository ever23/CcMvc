<?php

namespace Cc\Mvc;

use Cc\Mvc;
use Cc\ValidDependence;
use Cc\ValidDefault;
use Cc\Cache;

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
 */
abstract class FormModel extends Model
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
    private $existFile = false;
    private $NameSubmited;
    private $Sumited = false;
    private static $count = 0;
    protected $valid = false;
    protected $action = '';
    protected $protected = false;
    private $UseCache = true;

    const TypeHtml = 0;
    const DefaultConten = 1;
    const Validate = 2;

    /**
     * methodo que se utilizara para trasmitir los datos puede ser _POST o _GET
     * @param string $method
     */
    public function __construct($action = NULL, $method = 'POST', $protected = true)
    {
        $this->Method = $method;

        $this->NameSubmited = 'Submited' . static::class . self::$count;
        self::$count++;

        $this->protected = $protected;
        if (!is_null($action))
        {

            $this->action = $action;
        }
        if ($this->UseCache && Cache::IsSave('Form.' . static::class))
        {
            $cache = Cache::Get('Form.' . static::class);
            $this->campos = $cache['campos'];
            $this->_ValuesModel = $cache['_ValuesModel'];
            $this->existFile = $cache['existFile'];
        } else
        {
            $this->LoadMetaData();
            Cache::Set('Form.' . static::class, ['campos' => $this->campos, '_ValuesModel' => $this->_ValuesModel, 'existFile' => $this->existFile]);
        }
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

    private function LoadMetaData()
    {
        foreach ($this->campos() as $i => $v)
        {
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
                switch ($v[self::TypeHtml])
                {
                    case 'Number':
                    case 'range':
                        $v[self::Validate] = ValidNumber::CreateValid($options);
                        break;
                    case 'email':
                        $v[self::Validate] = ValidEmail::CreateValid($options);
                        break;
                    case 'tel':
                        $v[self::Validate] = ValidTelf::CreateValid($options);
                        break;
                    case 'color':
                        $v[self::Validate] = ValidExadecimal::CreateValid($options);
                        break;
                    case 'date':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => 'Y/m/d'] + $options);
                        break;
                    case 'datetime':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => 'Y/m/d H:i:s'] + $options);
                        break;
                    case 'week':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => 'Y-W'] + $options);
                        break;
                    case 'month':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => 'Y-m'] + $options);
                        break;
                    case 'time':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => 'H:i:s'] + $options);
                        break;
                    case 'year':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => 'Y'] + $options);
                        break;
                    case 'datetime-local':
                        $v[self::Validate] = ValidDate::CreateValid(['format' => \DateTime::W3C] + $options);
                        break;
                    case 'url':
                        $v[self::Validate] = ValidUrl::CreateValid($options);
                        break;
                    case 'file':
                        //case 'image':
                        $v[self::Validate] = ValidString::CreateValid(['opt_files' => $options, 'required' => false]);
                        break;
                    default :

                        $v[self::Validate] = ValidString::CreateValid($options);
                }
            }

            $this->campos[$i] = $v;
            $this->_ValuesModel[$i] = '';
        }
    }

    /**
     * Establece el valor por defecto de campo en el formulario
     * @param string $name
     * @param string $value
     */
    public function DefaultValue($name, $value = NULL)
    {
        if (is_array($name))
        {
            foreach ($name as $i => $v)
            {
                $this->DefaultValue($i, $v);
            }
            return;
        }
        if (isset($this->campos[$name][self::DefaultConten]))
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

    //  abstract protected function Campos();

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

        if ($this->Method == 'GET')
        {
            if (isset($_GET[$this->NameSubmited]))
            {
                $this->Sumited = true;
                $r = $this->ValidateValues($_GET);
            } else
            {

                return false;
            }
        } elseif ($this->Method == 'POST')
        {
            if (isset($_POST[$this->NameSubmited]))
            {
                $this->Sumited = true;
                $r = $this->ValidateValues($_POST);
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
            $this->valid = $this->ValidateFile();
        }
        if ($this->protected)
        {

            if (!Mvc::App()->Router->is_self(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''))
            {
                $r = false;
            }
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
                    return false;
                }
            }
            if ($this->_ValuesModel[$v]->is_Uploaded() && isset($options['ext']))
            {
                if (!in_array($this->_ValuesModel[$v]->getExtension(), explode(',', $options['ext'])))
                {
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
     */
    public function Input($name, array $attrs = [])
    {

        if (isset($this->campos[$name]))
        {
            $attrs['type'] = $this->campos[$name][self::TypeHtml];
            $attrs['name'] = $name;

            // echo "<input type='" . $this->campos[$name][0] . "' name='" . $name . "'";
            if (isset($this->campos[$name][self::DefaultConten]))
            {
                $attrs['value'] = $this->campos[$name][self::DefaultConten];
            }
            $attrValid = [ 'pattern', 'min', 'max', 'maxlength', 'size', 'accept', 'step', 'required', 'multiple', 'title', 'placeholder'];
            if ($valid = ValidDefault::GetOptions($this->campos[$name][self::Validate]))
            {
                //  var_dump($this->campos[$name][2]);
                foreach ($valid as $i => $v)
                {
                    if (in_array($i, $attrValid))
                    {
                        $attrs[$i] = $v;
                    }
                }
            }
            echo Html::input($attrs);
        } else
        {
            ErrorHandle::Notice('EL CAMPO ' . $name . ' NO EXISTE');
        }
    }

    /**
     * imprime un textarea desde el nombre de un dato en el modelo
     * @param string $name
     * @param array $attrs [attr=>valor]
     */
    public function TextArea($name, array $attrs = [])
    {
        if (isset($this->campos[$name]))
        {
            $attrs['name'] = $name;
            $attrs['type'] = $this->campos[$name][self::TypeHtml];
            $value = '';
            if (isset($this->campos[$name][self::DefaultConten]))
            {
                $value = $this->campos[$name][self::DefaultConten];
            }
            $attrValid = [ 'pattern', 'min', 'max', 'maxlength', 'size', 'accept', 'placeholder'];
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
            echo Html::textarea($value, $attrs);
        } else
        {
            ErrorHandle::Notice('EL CAMPO ' . $name . ' NO EXISTE');
        }
    }

    /**
     * imprime un select desde el nombre de un dato en el modelo
     * @param type $name
     * @param array $options [opcion=>sttr]
     * @param array $attrs [attr=>valor]
     */
    public function Select($name, array $options = [], array $attrs = [])
    {
        if (isset($this->campos[$name]))
        {
            if (isset($this->campos[$name][self::DefaultConten]))
            {
                $attrs['value'] = $this->campos[$name][self::DefaultConten];
            }
            if (isset($this->campos[$name][self::Validate]) && $valid = ValidDefault::GetOptions($this->campos[$name][self::Validate]))
            {
                if ($options === [] && isset($valid['options']) && is_array($valid['options']))
                {
                    $options = $valid['options'];
                }
            }
            $attrs['name'] = $name;

            echo Html::select($attrs, $options);
        } else
        {
            ErrorHandle::Notice('EL CAMPO ' . $name . ' NO EXISTE');
        }
    }

    /**
     * inicia el formulario
     * @param array $attrs
     */
    public function BeginForm($attrs = [])
    {
        if ($this->existFile)
        {
            $attrs['ENCTYPE'] = "multipart/form-data";
        }
        $attrs['action'] = $this->action;
        $attrs['method'] = $this->Method;
        echo Html::OpenTang('form', $attrs);
    }

    /**
     * imprime el boton de enviar el formulario
     * @param string $value
     * @param array $attrs
     */
    public function ButtonSubmid($value = '', array $attrs = [])
    {
        $attrs['value'] = 1;
        $attrs['name'] = $this->NameSubmited;
        echo Html::button($value, $attrs);
    }

    /**
     * finaliza el formulario
     */
    public function EndForm()
    {
        echo Html::CloseTang('form');
    }

    public function PrintForm($attrs, array $campos = [], $submit = [])
    {
        $specialTang = ['select', 'textarea', 'hidden'];
        $this->BeginForm($attrs);
        foreach ($this->campos as $i => $v)
        {
            $typeHtml = $v[self::TypeHtml];
            $name = isset($campos[$i]['text']) ? $campos[$i]['text'] : '';
            $attr = isset($campos[$i]['attr']) ? $campos[$i]['attr'] : [];
            if ($typeHtml == 'hidden')
            {
                $this->Input($i, $attr);
                continue;
            }
            echo Html::OpenTang('div', ['class' => 'FormRow']) . Html::label($name, ['from' => $i]);
            if (!in_array($typeHtml, $specialTang))
            {
                if (isset($campos[$i]['ListValue']))
                {
                    foreach ($campos[$i]['ListValue'] as $val)
                    {
                        $this->Input($i, $attr + ['value' => $val]);
                    }
                } else
                {
                    $this->Input($i, $attr);
                }
            } else
            {
                switch ($typeHtml)
                {
                    case 'textarea':
                        $this->TextArea($i, $attr);
                        break;
                    case 'select':
                        if (isset($campos[$i]) && isset($campos[$i]['option']) && is_array($campos[$i]['option']))
                        {
                            $this->Select($i, $campos[$i]['option'], $attr);
                        } else
                        {
                            $this->Select($i, [], $attr);
                        }
                        break;
                }
            }
            echo Html::CloseTang('div');
        }

        echo Html::OpenTang('div', ['class' => 'FormRow']);
        $this->ButtonSubmid(isset($submit['value']) ? $submit['value'] : 'ENVIAR', $submit);
        echo Html::CloseTang('div');
        $this->EndForm();
    }

}
