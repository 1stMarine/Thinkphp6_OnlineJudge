<?php

namespace app\controller\judge_machine;

use app\common\SnowFlake;
use app\controller\common\matchCache;
use app\controller\question\Model\MeddleRecordUser;
use app\controller\question\Model\RecordModel;
use app\controller\question\Model\SolveQuestion;
use app\controller\question\Question;
use app\controller\question\QuestionController;
use app\controller\user\UserInfo;
use app\Request;

class CreateJudge
{
    private $mainJudgeBody;

    public function __construct(){

    }



//#include<iostream>
//
//using namespace std;
//
//int main(){
//int a;
//cin >> a;
//cout << a;
//}
    /**
     * 仅当测试使用
     * @return string
     */
    public function index(){
        $codeInfo = new CodeInfo();
        $codeInfo->code = "
            #include<iostream> \n
            using namespace std; \n
            int main(){ \n
                int a; \n
                cin >> a; \n
                cout << a; \n
            } \n
        ";

        $codeInfo->language = "cpp";

        $testSamples = [];

        for($i = 0;$i < 3;$i++){
            $testSamples[] = new TestSample($i,$i);
        }

//        新建测试
        $this->mainJudgeBody = new MainJudgeBody($codeInfo);

        $this->mainJudgeBody->compile();

        $this->mainJudgeBody->running();

        return $this->mainJudgeBody->index();
    }

    public function judge(Request $request){
//          封装数据
        $testInfo = new TestInfo(
            $request->param("language"),
            $request->param("code"),
            $request->param("qid"),
            $request->param("uid"),
            \time(),
            QuestionController::getTestSample($request->param("qid"))
        );

//          新建判题机
        $this->mainJudgeBody = new MainJudgeBody($testInfo);
//          编译
        $resultInfo = null;
        $compileResult = $this->mainJudgeBody->compile();

        if($compileResult == null){
            $resultInfo = $this->mainJudgeBody->running();
        }else{
            $resultInfo = $compileResult;
        }
//          运行

//          计算通过率
        $this->passRate($resultInfo->code,$request->param("qid"),$request->param("uid"),$request->param("difficulty"));
//          编译记录
        $recordModel = new RecordModel();
        $rid = SnowFlake::createID();
        $recordModel->save([
                "rid"               =>          $rid,
                "qid"               =>          $request->param("qid"),
                "question_name"     =>          Question::find($request->param("qid"))->question_name,
                "time"              =>          $resultInfo->obj->time,
                "space"             =>          $resultInfo->obj->space,
                "submit_time"       =>          $resultInfo->obj->submit_time,
                "language"          =>          $resultInfo->obj->language,
                "inputOutput"       =>          json_encode($resultInfo->obj->inputOutput),
                "code"              =>          $request->param("code"),
                "state"             =>          $resultInfo->code == "40000"
            ]
        );

        $meddleRecordUser = new MeddleRecordUser();
        $meddleRecordUser->save(["rid" => $rid,"uid" => $request->param("uid")]);

//        如果是竞赛中，存一份到缓存中
        if($request->param("isMatch")){
            matchCache::setMatchInfo(
                $resultInfo,
                $request->param("code"),
                $request->param("mid"),
                $request->param("uid"),
                $request->param("qid")
            );
        }

        return json($resultInfo);
    }

    public function solveRecord($uid,$qid,$difficulty){
        $solveQuestion = new SolveQuestion();
        $count = $solveQuestion->where("uid", $uid)->where("qid", $qid)->count();
        if(!$count){
            $solveQuestion->save([
                "id" => SnowFlake::createID(),
                "uid"=> $uid,
                "qid"=> $qid
            ]);
            $user = UserInfo::find($uid);
            switch ($difficulty){
                case 0:
                    $user->easy_resolve++;
                    break;
                case 1:
                    $user->meddle_resolve++;
                    break;
                case 2:
                    $user->hard_resolve++;
                    break;
            }
            $user->save();
        }


    }

    public function passRate($code,$qid,$uid,$difficulty){
        $question = Question::find($qid);
        $question->total_attempt_count += 1;
        if($code == 40000){
            $question->total_pass_count += 1;
            $this->solveRecord($uid,$qid,$difficulty);
        }
        $question->save();
    }


}