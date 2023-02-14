<?php

namespace app\controller;

use app\controller\question\Question;
use app\Request;
use think\facade\View;

class Editor
{
    public function index(Request $request){
        $question = Question::find($request->param());

        $question->input_sample = json_decode($question->input_sample);
        $question->output_sample = json_decode($question->output_sample);
        $question->tag = json_decode($question->tag);

        return View::fetch('editor',['question' => $question]);
    }
}