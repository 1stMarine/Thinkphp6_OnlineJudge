<?php

namespace app\controller\match;

use app\common\SnowFlake;
use app\controller\admin\adminController;
use app\controller\match\Model\MatchUserModel;
use app\controller\question\Question;
use app\Request;
use think\facade\View;


class matchController
{
    public function index(){
        return View::fetch('matchList');
    }

    /**
     * @Author kaiwei cen
     * @Time  2023-02-08.
     * @param Request $request
     * @return void
     */
    public function addMatch(Request $request){
        $matchModel = new MatchModel();
        $matchModel->save([
            "mid"                   =>      SnowFlake::createID(),
            "match_name"            =>   $request->param("match_name"),
            "match_description"     =>   $request->param("match_description"),
            "creat_time"            =>   date("Y-m-d H:i",time()),
            "start_time"            =>   $request->param("start_time"),
            "persistent_time"       =>   $request->param("persistent_time"),
            "participation_count"   =>   0,
            "match_type"            =>   $request->param("match_type"),
            "img_url"               =>   $request->param("img_url"),
        ]);
    }

    public function addMatchWithForm(Request $request){

        $matchModel = new MatchModel();
        $mid = SnowFlake::createID();
        $matchModel->save([
            "mid"                   =>   $mid,
            "match_name"            =>   $request->param("match_name"),
            "match_description"     =>   $request->param("match_description"),
            "creat_time"            =>   date("Y-m-d H:i",time()),
            "start_time"            =>   $request->param("start_time"),
            "persistent_time"       =>   $request->param("persistent_time"),
            "participation_count"   =>   0,
            "match_type"            =>   $request->param("match_type"),
            "img_url"               =>   $request->param("img_url"),
        ]);

        $qids = $request->param("question_choice");

        foreach ($qids as $key => $qid){
            $matchQuestionModel = new MatchQuestionModel();
            $save = $matchQuestionModel->save([
                "id" => null,
                "mid" => $mid,
                "qid" => $qid
            ]);


        }
        header("Location:../admin.adminController/toAdminMenu");
    }

    public function getMatchList($uid){
        $matchModel = new MatchModel();
        $matchList = $matchModel->select();

        $matchUserModel = new MatchUserModel();
        $participateMatch = $matchUserModel->where("uid", $uid)->select();

        $participateList = [];
        foreach ($matchList as $key => $match){
            $flag = false;
            foreach ($participateMatch as $key => $participate){
                if($match->mid == $participate->mid){
                    $flag = true;
                    break;
                }
            }
            $participateList[] = $flag;
        }

        return View::fetch('matchList',["matchList" => $matchList,"uid"=>$uid,"participateList" => $participateList]);
    }

    public function matchDetail($uid,$mid){

        $matchModel = new MatchModel();
        $match = $matchModel->find($mid);


        $matchQuestionModel = new MatchQuestionModel();
        $questionsId = $matchQuestionModel->where("mid",$mid)->select();
        $ids = [];
        foreach ($questionsId as $key => $id){
            $ids[] = $id->qid;
        }
        $questions = Question::select($ids);

        $matchUserModel = new MatchUserModel();

        $count = $matchUserModel->where("mid", $mid)->where("uid", $uid)->count();
        $state = $count == 1;

        return View::fetch("matchDetail",["match" => $match,"questionList" => $questions,"state"=>$state]);
    }

    public function changeMatchInfo(Request  $request){
        $mid = $_POST["mid"];
        $match = MatchModel::find($mid);
        $match->match_name          = $_POST["match_name"];
        $match->match_description   = $_POST["match_description"];
        $match->start_time          = $_POST["start_time"];
        $match->match_type          = $_POST["match_type"];
        $match->persistent_time     = $_POST["persistent_time"];
        $match->img_url             = $_POST["img_url"];

        $match->save();

        MatchQuestionModel::where("mid",'=',$mid)->delete();



        $qids = $_POST["question_choice"];

        foreach ($qids as $key => $qid){
            $matchModel = new MatchQuestionModel();
            $matchModel->save([
                "mid" => $mid,
                "qid" => $qid
            ]);

        }
        header("Location:../admin.adminController/getMatch?page=1");
    }

    public function participateMatch($uid,$mid){
        $matchUserModel = new MatchUserModel();
        $matchUserModel->save([
           "uid"    =>      $uid,
           "mid"    =>      $mid
        ]);

        $matchModel = new MatchModel();
        $match = $matchModel->where("mid", $mid)->find();
        $match->participation_count ++;
        $match->save();
        header("Location:../match.matchController/getMatchList?uid=" . $uid);
    }
}