<?php

namespace app\common;

class returnMessage
{

    public function __construct($code,$message,$obj){
        $this->code = $code;
        $this->message = $message;
        $this->obj = $obj;
    }


    public $code;

    public $obj;

    public $message;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getObj()
    {
        return $this->obj;
    }

    /**
     * @param mixed $obj
     */
    public function setObj($obj): void
    {
        $this->obj = $obj;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }


}