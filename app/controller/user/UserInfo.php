<?php

namespace app\controller\user;

use app\controller\question\Model\RecordModel;
use app\controller\question\Model\TbRecordUser;
use think\Model;

class UserInfo extends Model
{
    protected $name = "tb_user";
    protected $pk = "uid";

    public function badges(){
        return $this->belongsToMany(
            Badge::class,
            TbBadgeUser::class,
            "bid","uid");
    }

    public function submitRecord(){
        return $this->belongsToMany(
            RecordModel::class,
            TbRecordUser::class,
            "rid","uid")->limit(5)->order("id desc");
    }
}