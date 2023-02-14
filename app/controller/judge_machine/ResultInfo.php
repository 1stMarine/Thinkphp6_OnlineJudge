<?php

namespace app\controller\judge_machine;

class ResultInfo
{


    public $time = 0.0;

    public $space = 0;

    public $code;

    public $state = 40002;

    public $message = "未知错误";

    public $submit_time;

    public $language;

    public $inputOutput;

    /**
     * @param $time
     * @param $space
     * @param $code
     * @param $state
     * @param $message
     * @param $submit_time
     */
    public function __construct($code, $submit_time,$language)
    {
        $this->code = $code;
        $this->submit_time = $submit_time;
        $this->language = $language;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @param float $time
     */
    public function setTime(float $time): void
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getSpace(): int
    {
        return $this->space;
    }

    /**
     * @param int $space
     */
    public function setSpace(int $space): void
    {
        $this->space = $space;
    }

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
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getSubmitTime()
    {
        return $this->submit_time;
    }

    /**
     * @param mixed $submit_time
     */
    public function setSubmitTime($submit_time): void
    {
        $this->submit_time = $submit_time;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getInputOutput()
    {
        return $this->inputOutput;
    }

    /**
     * @param mixed $inputOutput
     */
    public function setInputOutput($inputOutput): void
    {
        $this->inputOutput = $inputOutput;
    }


}