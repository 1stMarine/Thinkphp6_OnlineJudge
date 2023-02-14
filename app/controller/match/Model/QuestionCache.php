<?php

namespace app\controller\match\Model;

class QuestionCache
{
    public $qid;

    public $questionName;

    public $submitRecords = [];

    /**
     * @param $qid
     * @param $questionName
     * @param array $submitRecords
     */
    public function __construct($qid, $questionName, array $submitRecords)
    {
        $this->qid = $qid;
        $this->questionName = $questionName;
        $this->submitRecords = $submitRecords;
    }


}