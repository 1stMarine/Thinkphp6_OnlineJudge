<?php

namespace app\controller;

use app\controller\question\Question;
use app\Request;
use think\facade\View;

class Editor
{
    public function index(Request $request){
        $question = Question::find($request->param("qid"));

        $question->input_sample = json_decode($question->input_sample);
        $question->output_sample = json_decode($question->output_sample);
        $question->tag = json_decode($question->tag);

        $data = ['question'  =>  $question];
        if($request->param("qid") != null){
            $data["isMatch"]    =   true;
            $data["mid"]        =   $request->param("mid");
        }

        if($request->param("state") != null){
            $data["state"] = false;
        }else{
            $data["state"] = true;
        }
        return View::fetch('editor',$data);
    }
}