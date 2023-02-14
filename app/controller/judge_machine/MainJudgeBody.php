<?php

namespace app\controller\judge_machine;

use app\common\AsyncHook;
use app\common\returnMessage;
use app\controller\question\Question;
use Cassandra\Time;

class MainJudgeBody
{
// 是否在windows
    private $__WIN__ = true;
// linux下存储目录
    private $root = "/home/judge_machine";
// WIN代码目录
    private $codeDirWIN;
// Linux代码目录
    private $codeDirLinux;
// 容器id
    private $containerId;
// shell命令
    private $testSamples = [];

    private $compileCommand;

    private $runningCommand;

    private $deleteCommand;

    private $testInfo;

    public function index(){
        return "MainJudgeBody.   " . $this->codeDirWIN;
    }

//    初始化
    public function  __construct(TestInfo $codeInfo){
        $this->testInfo = $codeInfo;
        $this->testSamples = $codeInfo->TestSamples;
//      设置根目录文件
        $this->root = $this->__WIN__ ? "D:/docker_windows/" : "/home/judge_machine";

//        创建容器
        $this->createContainer($codeInfo->language);

//        创建文件夹
        $this->codeDirWIN = $this->root . $codeInfo->submitTime . "/";
        $this->codeDirLinux = "/home/judge_machine/" . $codeInfo->submitTime;
        mkdir($this->codeDirWIN);
        mkdir($this->codeDirWIN . "out");
        mkdir($this->codeDirWIN . "time");
        mkdir($this->codeDirWIN . "in");

        $this->setCommand($codeInfo);
    }
    /**
     * 设置Docker命令
     * @param $language
     * @return void
     */
    public function setCommand($codeInfo){
        $codeFile = null;
        $shellFile = null;
        $this->runningCommand = "docker exec -i " . $this->containerId . " /bin/sh -c \"cd " . $this->codeDirLinux . " && chmod 777 do.sh&&./do.sh\" 2>&1";
        $codeFileName = null;
        $compileFileName = null;
        $runningFileName = null;

        switch ($codeInfo->language){
            case "c_cpp":
                $codeFileName = "test.cpp";
                $compileFileName = "g++ test.cpp";
                $runningFileName = "./a.out";
                break;
            case "java":
                $codeFileName = "Main.java";
                $compileFileName = "javac Main.java";
                $runningFileName = "java Main";
                break;
            case "python":
                $codeFileName = "test.python";
                $runningFileName = "python test.python";
                break;
            case "go":

                break;
        }

        //        写入代码文件
        $codeFile = fopen(($this->codeDirWIN . $codeFileName),"w") or die("无法写入代码文件!");

        // 设置编译、运行命令
        $this->compileCommand = "docker exec " . $this->containerId . " /bin/sh -c \"cd " . $this->codeDirLinux . " && ".$compileFileName."\" 2>&1";
        //        写入shell命令 、 测试样例
        for ($i = 0;$i < count($this->testSamples);$i++){
            $shellFile = fopen(($this->codeDirWIN) . "do.sh","a") or die("无法写入shell命令");
            fwrite($shellFile,"timeout 2s time -ao time/time".$i.".txt -f %U♥%M ".$runningFileName." < in/in".$i.".in > out/out".$i.".out \n");
            //          写入测试样例文件
            $inFile = fopen(($this->codeDirWIN . "/in/in" . $i . ".in"),"a") or die("无法写入测试样例");
            fwrite($inFile,$this->testSamples[$i]->input);
            fclose($inFile);
        }

        fwrite($codeFile,$codeInfo->code);
        fclose($codeFile);
        fclose($shellFile);
    }
//      编译
    public function compile(){
        exec($this->compileCommand,$compileResult);
    }

//      运行
    public function running(){

        exec($this->runningCommand,$runningResult);
//        $this->check();
        $this->deleteContainer();

        return $this->check();



    }

    public function deleteContainer(){
        exec("docker stop ".$this->containerId);
        exec("docker rm ".$this->containerId);
    }

    /**
     * 创建容器
     * @param $language
     * @return void
     */
    public function createContainer($language){
        $createCommand = null;
        switch ($language) {
            case "c_cpp":
                $createCommand = "docker run -id -v " . Docker::__WINDOWS__PATH__ . ":" . Docker::__LINUX__PATH__ . " echocen/gcc:v1 2>&1";
                break;
            case "java":
                $createCommand = "docker run -id -v " . Docker::__WINDOWS__PATH__ . ":" . Docker::__LINUX__PATH__ . " echocen/openjdk:v1 2>&1";
                break;
            case "python":

                break;
            case "go":

                break;
        }
        $containerId = null;
        exec($createCommand,$containerId);
        $this->containerId = $containerId[0];
    }

    public function check(){
        $outFile = null;
        $totalTime = 0.0;
        $totalSpace = 0;
//        返回的测试数据初始化
        $resultInfo = new ResultInfo(
            $this->testInfo->code,
            date("Y-m-d H:i",$this->testInfo->submitTime),
            $this->testInfo->language
        );

        for($i = 0;$i < count($this->testSamples);$i++){
            $outFile = fopen($this->codeDirWIN."out/out".$i.".out","r");
            $timeFile = fopen($this->codeDirWIN."time/time".$i.".txt","r");

            $userOut = fread($outFile,filesize($this->codeDirWIN."out/out".$i.".out"));

            if($userOut[strlen($userOut)-1] == "\n"){
                $userOut = substr($userOut,0,strlen($userOut) - 1);
            }

            $timeAndSpace = explode("♥",fread($timeFile,filesize($this->codeDirWIN."time/time".$i.".txt")));
            $timeAndSpace[1] = substr($timeAndSpace[1],0,strlen($timeAndSpace[1]) - 1);


            $totalSpace += (int)$timeAndSpace[1];
            $totalTime  += (int)$timeAndSpace[0];


//            超出时间限制
            if($userOut == ""){
                $resultInfo->setCode(40003);
                $resultInfo->setTime(($totalTime / (count($this->testSamples) - $i)));
                $resultInfo->setSpace($totalSpace / (count($this->testSamples) - $i));
                $resultInfo->setMessage("超出时间/空间限制");
                return new returnMessage(
                    40001,
                    "答案错误",
                    $resultInfo
                );
            }

//                 答案错误
            if($this->testSamples[$i]->output != $userOut){
                $resultInfo->setCode(40001);
                $resultInfo->setTime(($totalTime / (count($this->testSamples) - $i)));
                $resultInfo->setSpace($totalSpace / (count($this->testSamples) - $i));
                $resultInfo->setMessage("答案错误");
                $this->testSamples[$i]->userOutput = $userOut;
                $resultInfo->setInputOutput($this->testSamples[$i]);
                 return new returnMessage(
                     40001,
                     "答案错误",
                     $resultInfo
                 );
             }

//            代码报错
            if(strpos($userOut,"Exception") !== false ||
                strpos($userOut,"error") !== false  ||
                strpos($userOut,"Error") !== false
            ){
                $resultInfo->setCode(40004);
                $resultInfo->setTime(($totalTime / (count($this->testSamples) - $i)));
                $resultInfo->setSpace($totalSpace / (count($this->testSamples) - $i));
                $resultInfo->setMessage("代码报错");
                return new returnMessage(
                    40004,
                    "代码报错",
                    $resultInfo
                );
            }

        }

        $resultInfo->setCode(40000);
        $resultInfo->setTime(($totalTime / count($this->testSamples)));
        $resultInfo->setSpace($totalSpace / count($this->testSamples));
        $resultInfo->setMessage("代码通过");

        fclose($outFile);

        return new returnMessage(
            40000,
            "程序通过",
            $resultInfo
        );
    }

}