<?php

namespace app\controller\judge_machine;

class TestSample
{

    public $input;

    public $output;

    public $userOutput;

    public function __construct($input,$output){
        $this->input = $input;
        $this->output = $output;
    }

}