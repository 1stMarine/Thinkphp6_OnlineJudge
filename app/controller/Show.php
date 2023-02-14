<?php

namespace app\controller;

use think\facade\View;

class Show
{
    public function index(){
        return View::fetch('block');
    }

    public function questionList(){
        return View::fetch('questionList');
    }

    public function matchList(){
        return View::fetch('matchList');
    }
}