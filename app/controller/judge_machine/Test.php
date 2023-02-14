<?php

namespace app\controller\judge_machine;

use app\common\AsyncHook;

class Test
{
    public function index(){

        $test = "3488";
//        $test = str_replace(array("\\r\\n","\\r","\\n"),'',$test);
        dump($test);
    }

    public function testFunction(): string
    {
        return "testFunction.";
    }
}