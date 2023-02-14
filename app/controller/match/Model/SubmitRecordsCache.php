<?php

namespace app\controller\match\Model;

class SubmitRecordsCache
{
    public $qid;
    public $uid;
    public $mid;
    public $time;
    public $space;
    public $submitTime;
    public $code;
    public $inputOutput;
    public $language;
    public $state;

    /**
     * @param $qid
     * @param $uid
     * @param $mid
     * @param $time
     * @param $space
     * @param $submitTime
     * @param $code
     * @param $inputOutput
     * @param $language
     * @param $state
     */
    public function __construct($qid, $uid, $mid, $time, $space, $submitTime, $code, $inputOutput, $language, $state)
    {
        $this->qid = $qid;
        $this->uid = $uid;
        $this->mid = $mid;
        $this->time = $time;
        $this->space = $space;
        $this->submitTime = $submitTime;
        $this->code = $code;
        $this->inputOutput = $inputOutput;
        $this->language = $language;
        $this->state = $state;
    }


}