<?php

namespace app\controller\admin;

use app\controller\match\MatchModel;
use app\controller\match\MatchQuestionModel;
use app\controller\question\Model\MeddleRecordUser;
use app\controller\question\Model\RecordModel;
use app\controller\question\Model\SolveQuestion;
use app\controller\question\Question;
use app\controller\question\TestSampleModel;
use app\Request;
use think\facade\View;

class adminController
{

    public function toAdminMenu(){
        $question = new Question();
        $easyQuestionList = $question->where("difficulty", 0)->select();
        $meddleQuestionList = $question->where("difficulty", 1)->select();
        $hardQuestionList = $question->where("difficulty", 2)->select();

        return View::fetch('adminMenu',[
            "easyQuestionList"=>$easyQuestionList,
            "meddleQuestionList"=>$meddleQuestionList,
            "hardQuestionList"=>$hardQuestionList
        ]);
    }

    public function getQuestions($page){
        $questions = Question::page($page,15)->select();
        foreach ($questions as $key=>$question){
            $question->input_sample = json_decode($question->input_sample);
            $question->output_sample = json_decode($question->output_sample);
            $question->tag = json_decode($question->tag);

        }

        $count = Question::count();

        return View::fetch('adminQuestion',['questionList' => $questions,"count" => $count] );
    }

    public function getMatch($page){
        $matchModel = new MatchModel();
        $matchList = $matchModel->page($page,10)->select();
        $count = $matchModel->count();
        return View::fetch('adminMatch',["matchList" => $matchList,"count"=>$count]);
    }

    public function deleteQuestion(Request $request){
        $qid = $request->param("qid");

        $matchQuestionModel = new MatchQuestionModel();
        $matchQuestion = $matchQuestionModel->where("qid", $qid)->select();
        foreach ($matchQuestion as $key => $match)
            $match->delete();

        $solveQuestion = new SolveQuestion();
        $solveQuestions = $solveQuestion->where("qid", $qid)->select();
        foreach ($solveQuestions as $key => $solve)
            $solve->delete();




        $testSampleModel = new TestSampleModel();
        $testSamples = $testSampleModel->where("qid", $qid)->select();
        foreach ($testSamples as $key => $testSample)
            $testSample->delete();


        $qeustion = Question::find($qid);
        $qeustion->delete();
    }

    public function deleteMatch(Request $request){

        $mid = $request->param("mid");
        $match = MatchModel::find($mid);
        $match->delete();
    }
    public function toChangeQuestion($qid){
        $question = Question::find($qid);
        return View::fetch("changeQuestionInfo", ["question" => $question]);
    }

    public function toChangeMatch($mid){
        $match = MatchModel::find($mid);
        $question = new Question();
        $easyQuestionList = $question->where("difficulty", 0)->select();
        $meddleQuestionList = $question->where("difficulty", 1)->select();
        $hardQuestionList = $question->where("difficulty", 2)->select();

        $matchQuestionModel = new MatchQuestionModel();
        $questionIds = $matchQuestionModel->where("mid", $mid)->select();
        return View::fetch("changeMatchInfo", [
            "match" => $match,
            "easyQuestionList"=>$easyQuestionList,
            "meddleQuestionList"=>$meddleQuestionList,
            "hardQuestionList"=>$hardQuestionList,
                "questionIds"=>$questionIds
        ]);
    }

}