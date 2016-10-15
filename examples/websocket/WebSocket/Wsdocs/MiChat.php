<?php

namespace Cc\Ws;

class MiChat extends Event
{

    private static $time;
    private static $Ptime = NULL;

    public function OnOpen()
    {
        //  var_dump(session_decode(file_get_contents(session_save_path().'/sess_'.$this->Cookie['TEG_UNEFA'])));
        var_dump($this->GET);

        $this->Cookie['ever'] = $this->Client->Ip();
        $var = [];
        $var['type'] = 'system';
        $var['message'] = $this->Client->Ip() . ' Conectado';
        $var['color'] = '00000';
        $var['name'] = '00000';
        $this->Send($var);
    }

    public static function LoopStatic()
    {
        if (is_null(self::$Ptime))
        {
            self::$Ptime = time() + 30;
        }

        if (self::$time > self::$Ptime)
        {
            $response = new MessajeJson;
            $response['type'] = 'system';
            $response['message'] = time();
            $response['color'] = '00000';
            $response['name'] = '00000';
            foreach (static::$Clients as $cliente)
                $cliente->Send($response);
            self::$Ptime = time() + 30;
        }
        self::$time = time();
    }

    public function OnMessaje(MessajeJson $response)
    {
        $response['type'] = 'usermsg';

        $this->Send($response);
        $this->Client->Send($response);
    }

    public function OnClose()
    {
        $var = new MessajeJson;
        $var['type'] = 'system';
        $var['message'] = $this->Client->Ip() . ' Desconectado';
        $var['color'] = '00000';
        $var['name'] = '00000';
        $this->Send($var);
    }

    public function OnException(\Exception $e)
    {
        echo $e;
    }

}
