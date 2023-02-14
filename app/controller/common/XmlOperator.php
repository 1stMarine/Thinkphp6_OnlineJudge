<?php

namespace app\controller\common;

use app\common\returnMessage;
use app\common\SnowFlake;
use app\controller\question\Question;
use app\controller\question\TestSampleModel;
use app\Request;

class XmlOperator
{
    public function index(){
        $fileName = $_FILES['question_file']['name'];
        $type = $_FILES['question_file']['type'];
        $tmpUrl = $_FILES['question_file']['tmp_name'];

        $xml = simplexml_load_file($tmpUrl);
        $tags = "[";
        $tagsLen = count($xml->tag->children());
//  处理标签
        for ($i = 0;$i < $tagsLen;$i++){
            $tags = $tags . "\"";
            $tags = $tags . $xml->tag->children()[$i];
            $tags = $i != $tagsLen-1 ? $tags . "\"," : $tags . "\"]";
        }

        $sample_len = count($xml->sample_input->children());
        $sampleInput = $sampleOutput = "[";
//  处理展示样例
        for ($i = 0;$i < $sample_len;$i++){
            $sampleInput = $sampleInput . "\"";
            $sampleOutput = $sampleOutput . "\"";

            $sampleInput = $sampleInput . $xml->sample_input->children()[$i];
            $sampleOutput = $sampleOutput . $xml->sample_output->children()[$i];

            $sampleInput = $i != $sample_len-1 ? $sampleInput . "\"," : $sampleInput . "\"]";
            $sampleOutput = $i != $sample_len-1 ? $sampleOutput . "\"," : $sampleOutput . "\"]";
        }
//        处理测试样本
        $testLen = count($xml->test_input->children());

        $testInput = $testOutput = "[";
        for ($i = 0;$i < $testLen;$i++){
            $testInput = $testInput . "\"";
            $testOutput = $testOutput . "\"";

            $testInput = $testInput . $xml->test_input->children()[$i];
            $testOutput = $testOutput . $xml->test_output->children()[$i];

            $testInput = $i != $testLen-1 ? $testInput . "\"," : $testInput . "\"]";
            $testOutput = $i != $testLen-1 ? $testOutput . "\"," : $testOutput . "\"]";
        }



        $qid = SnowFlake::createID();
        $question = new Question;
        $question->save([
            "qid"                   =>      $qid,
            "question_name"         =>      $xml->title,
            "input_style"           =>      $xml->input_style,
            "output_style"          =>      $xml->output_style,
            "data_range"            =>      $xml->data_range,
            "input_sample"          =>      $sampleInput,
            "output_sample"         =>      $sampleOutput,
            "difficulty"            =>      number_format(("".$xml->diffculty)),
            "time_limit"            =>      $xml->time_limit,
            "space_limit"           =>      $xml->memory_limit,
            "total_pass_count"      =>      0,
            "total_attempt_count"   =>      0,
            "resource"              =>      $xml->resource,
            "tag"                   =>      $tags,
            "description"           =>      $xml->description
        ]);

        $testSampleModel = new TestSampleModel();
        $testSampleModel->save([
            "qid"           =>  $qid,
            "sample_input"  =>  $testInput,
            "sample_output" =>  $testOutput
        ]);

    }
}