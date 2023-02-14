<?php

namespace app\controller\user;

use app\common\returnMessage;
use app\common\SnowFlake;
use app\Request;

class UserController
{
    public function index(){

    }

    public function addUser(Request $request){
        $userInfo = new UserInfo();
        $userInfo->save([
            "uid"                           =>          SnowFlake::createID(),
            "user_name"                     =>          $request->param("user_name"),
            "user_email"                    =>          $request->param("user_email"),
            "user_password"                 =>          $request->param("user_password"),
            "tag"                           =>          "[]",
            "url"                           =>          "https://tse4-mm.cn.bing.net/th/id/OIP-C.adLtVT7WjGjvPUdw7CwpkAAAAA?w=188&h=188&c=7&r=0&o=5&pid=1.7"
            ]);
    }

    public function login(Request $request){


        $userInfo = new UserInfo();
        $user = $userInfo->where('user_email', $request->param('user_email'))->findOrEmpty();
        if(!$user->isEmpty()){
            return json(new returnMessage(10001,"登陆成功",$user));
        }else{
            return json(new returnMessage(10000,"登陆失败，用户名或密码错误",[]));
        }
    }

    public function changeInfo(Request $request){

        $user = UserInfo::find($_POST['uid']);
        $user->url              = $_POST['url'];
        $user->user_name        = $_POST['user_name'];
        $user->user_email       = $_POST['user_email'];
        $user->user_password    = $_POST['user_password'];
        $user->location         = $_POST['location'];
        $user->school           = $_POST['school'];
        $user->gender           = $_POST['gender'];
        $tagStr = $_POST['tag'];
        $tagArr = explode(',',$tagStr);
        $tag = "[";
        for($i = 0;$i < count($tagArr);$i++){
            $tag = $tag . "\"" .$tagArr[$i] . "\"";

            if($i != count($tagArr)-1){
                $tag = $tag . ",";
            }
        }
        $tag = $tag . "]";
        dump($tag);
        $user->tag = $tag;

        $user->save();
        header(("Location:../user.user/personInfo?uid=" . $_POST['uid']));
    }
}