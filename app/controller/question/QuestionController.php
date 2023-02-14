<?php

namespace app\controller\question;

use app\common\SnowFlake;
use app\controller\judge_machine\TestSample;
use app\controller\question\Model\RecordModel;
use app\controller\question\Model\SolveQuestion;
use app\Request;
use think\facade\View;
use think\Template;

class QuestionController
{
    /**
     * 添加题目
     * @param Request $request
     * @return void
     */
    public function addQuestion(Request $request){

        $question = new Question;
        $question->save([
            "qid"                   =>      SnowFlake::createID(),
            "question_name"         =>      $request->param("question_name"),
            "input_style"           =>      $request->param("input_style"),
            "output_style"          =>      $request->param("output_style"),
            "data_range"            =>      $request->param("data_range"),
            "input_sample"          =>      json_encode($request->param("input_sample")),
            "output_sample"         =>      json_encode($request->param("output_sample")),
            "difficulty"            =>      $request->param("difficulty"),
            "time_limit"            =>      $request->param("time_limit"),
            "space_limit"           =>      $request->param("space_limit"),
            "total_pass_count"      =>      $request->param("total_pass_count"),
            "total_attempt_count"   =>      $request->param("total_attempt_count"),
            "resource"              =>      $request->param("resource"),
            "tag"                   =>      json_encode($request->param("tag"),JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * 查询题目 一次20
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getQuestionList($page,$uid=null){
        $questions = Question::page($page,15)->select();
        $questionIds = [];
        foreach ($questions as $key=>$question){
            $question->input_sample = json_decode($question->input_sample);
            $question->output_sample = json_decode($question->output_sample);
            $question->tag = json_decode($question->tag);
            $questionIds[] = $question->qid;
        }

//        建立在登录后情况下
        $solveQuestionState = [];
        $solveList = [];
        if($uid != null){
            $solveQuestion = new SolveQuestion();
            $solveList = $solveQuestion->whereIn("qid", $questionIds)->where("uid",$uid)->select();
        }

        foreach ($questions as $key=>$question){
            $flag = false;
            foreach ($solveList as $key => $solve){
                if($solve->qid == $question->qid){
                    $flag = true;
                    break;
                }
            }
            $solveQuestionState[] = $flag;
        }



        $count = Question::count();
        return View::fetch('questionList',['questionList' => $questions,"count" => $count , "solveQuestionState" => $solveQuestionState,"uid"=>$uid] );
    }



    public function addTestSample(Request $request){
        dump($request->param());
        $testSampleModel = new TestSampleModel();
        $testSampleModel->save([
            "qid"               => $request->param("qid"),
            "sample_input"      => json_encode($request->param("sample_input")),
            "sample_output"     => json_encode($request->param("sample_output"))
        ]);
    }

    public static function getTestSample($qid){
        $testSampleModel = new TestSampleModel();
        $testSample = $testSampleModel->where("qid",$qid)->find();

        $testSample->sample_input   = json_decode($testSample->sample_input);
        $testSample->sample_output  = json_decode($testSample->sample_output);


        $testSamples = [];
        for($i = 0;$i < count($testSample->sample_input);$i++){
            $testSamples[] = new TestSample(
                $testSample->sample_input[$i],
                $testSample->sample_output[$i]
            );
        }
        return $testSamples;
    }

    public function changeQuestion(){

        $question = Question::find($_POST["qid"]);

        $question->question_name    = $_POST["question_name"];
        $question->description      = $_POST["description"];
        $question->input_style      = $_POST["input_style"];
        $question->output_style     = $_POST["output_style"];
        $question->data_range       = $_POST["data_range"];
        $question->difficulty       = $_POST["difficulty"];
        $question->time_limit       = $_POST["time_limit"];
        $question->space_limit      = $_POST["space_limit"];
        $question->resource         = $_POST["resource"];
        $question->tag              = $_POST["tag"];

        $question->save();

        header("Location:../admin.adminController/getQuestions?page=1");
    }

//    public function toMoreQuestionInfo($rid){
//        $record = RecordModel::find($rid);
//        return View::fetch("moreInfo",["record" => $record]);
//    }
}