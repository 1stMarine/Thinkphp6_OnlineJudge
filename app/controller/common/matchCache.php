<?php

namespace app\controller\common;

use app\common\SnowFlake;
use app\controller\match\MatchQuestionModel;
use app\controller\match\Model\Rank;
use app\controller\match\Model\MatchRecord;
use app\controller\match\Model\QuestionCache;
use app\controller\match\Model\SubmitRecordsCache;
use think\facade\Cache;

class matchCache
{
//
    public static function setMatchInfo($resultInfo,$code,$mid,$uid,$qid){

        $redis_id = $mid . "_" . $qid;

        $submitRecordsCache = new SubmitRecordsCache(
            $qid,
            $uid,
            $mid,
            $resultInfo->obj->time,
            $resultInfo->obj->space,
            $resultInfo->obj->submit_time,
            $code,
            json_encode($resultInfo->obj->inputOutput),
            $resultInfo->obj->language,
            $resultInfo->obj->code
        );
        Cache::push($redis_id,json_encode($submitRecordsCache));

    }

    public static function getMatchInfo($mid,array $qidList){

        foreach($qidList as $key => $qid){
            $redis_id = $mid . "_" . $qid;

//            拿到某个竞赛中某个题的提交记录
            $submitRecordsCaches = Cache::get($redis_id);

            if($submitRecordsCaches == null){
                return;
            }
//            循环提交记录
            foreach ($submitRecordsCaches as $key => $submitRecordsCache){

                $submitRecord = json_decode($submitRecordsCache);
                dump($submitRecord);
                $matchRecord = new MatchRecord();
                $matchRecord->save([
                    "rid"           =>  SnowFlake::createID(),
                    "qid"           =>  $submitRecord->qid,
                    "uid"           =>  $submitRecord->uid,
                    "mid"           =>  $submitRecord->mid,
                    "time"          =>  $submitRecord->time,
                    "space"         =>  $submitRecord->space,
                    "submit_time"   =>  $submitRecord->submitTime,
                    "code"          =>  $submitRecord->code,
                    "input_output"  =>  $submitRecord->inputOutput,
                    "language"      =>  $submitRecord->language,
                    "state"         =>  $submitRecord->state,
                ]);
            }
            Cache::delete($redis_id);
        }


    }

    public static function test(){
        dump(Cache::get("24417750679388981563490304_24414715981591820460097536"));
    }
}