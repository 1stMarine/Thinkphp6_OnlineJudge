<?php

namespace app\controller\judge_machine;

class TestInfo
{



    public $language;

    public $code;

    public $qid;

    public $uid;

    public $submitTime;

    public $TestSamples;

    /**
     * @param $language
     * @param $code
     * @param $qid
     * @param $uid
     * @param $submitTime
     * @param $createCommand
     * @param $compileCommand
     * @param $runningCommand
     * @param $deleteCommand
     * @param $containerId
     * @param $TestSamples
     */
    public function __construct($language, $code, $qid, $uid, $submitTime, $TestSamples)
    {
        $this->language = $language;
        $this->code = $code;
        $this->qid = $qid;
        $this->uid = $uid;
        $this->submitTime = $submitTime;
        $this->TestSamples = $TestSamples;
    }
}