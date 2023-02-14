<?php

namespace app\controller\user;

use app\controller\question\Question;
use think\facade\View;

class User
{
    public function index(){

    }

    public function login(){
        return View::fetch('login');
    }

    public function register(){
        return View::fetch('register');
    }

    public function personInfo($uid){
        $user = UserInfo::find($uid);
        $user->tag = json_decode($user->tag);
        $badges = $user->badges;

        $record = $user->submitRecord;

        $easy_count = Question::where('difficulty', 0)->count();
        $meddle_count = Question::where('difficulty', 1)->count();
        $hard_count = Question::where('difficulty', 2)->count();
//        foreach ($record as $key => $badge){
//            dump($badge->question_name);
//        }

        return View::fetch('personInfo', [
            "userInfo"          =>  $user,
            "badges"            =>  $badges,
            "records"           =>  $record,
            "recordLen"         =>  count($record),
            "easy_count"      =>  $easy_count,
            "meddle_count"    =>  $meddle_count,
            "hard_count"      =>  $hard_count
        ]);
    }
}