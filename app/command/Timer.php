<?php
declare (strict_types = 1);

namespace app\command;

use app\controller\Index;
use app\controller\match\matchController;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class Timer extends Command
{
    protected $timer;
    protected $interval = 60;

    protected function configure()
    {
        // 指令配置
        $this->setName('timer')
            ->addArgument('status', Argument::REQUIRED, 'start/stop/reload/status/connections')
            ->addOption('d', null, Option::VALUE_NONE, 'daemon（守护进程）方式启动')
            ->addOption('i', null, Option::VALUE_OPTIONAL, '多长时间执行一次')
            ->setDescription('开启/关闭/重启 定时任务');
    }

    protected function init(Input $input, Output $output)
    {
        global $argv;
        if ($input->hasOption('i'))
            $this->interval = floatval($input->getOption('i'));
        $argv[1] = $input->getArgument('status') ?: 'start';
        if ($input->hasOption('d')) {
            $argv[2] = '-d';
        } else {
            unset($argv[2]);
        }
    }

    protected function execute(Input $input, Output $output)
    {
        $this->init($input, $output);
        //创建定时器任务  new Worker('websocket://0.0.0.0:2346');
        $task = new Worker();
        $task->count = 1;
        //每个子进程启动时都会执行$this->start方法
        $task->onWorkerStart = [$this, 'start'];
        //每个子进程连接时都会执行，浏览器127.0.0.1:2346,就能调用方法
        $task->onConnect = function ($connection) {
            echo "nihao\n";
        };
        $task->runAll();
    }

    public function stop()
    {
        //手动暂停定时器
        \Workerman\Lib\Timer::del($this->timer);
    }
    public function start()
    {
//        workerman的Timer定时器类 add ，$time_interval是多长时间执行一次
        $time_interval = 60;
        \Workerman\Lib\Timer::add($time_interval, function()
        {//  运行控制器Index的index
            echo matchController::scanMatch();
        });
//   下面是网上找的内容

        $last = time();
        $task = [6 => $last, 10 => $last, 30 => $last, 60 => $last, 180 => $last, 300 => $last];

        $this->timer = \Workerman\Lib\Timer::add($this->interval, function () use (&$task) {
            //每隔2秒执行一次
            try {
                $now = time();
                foreach ($task as $sec => $time) {
                    if ($now - $time >= $sec) {
                        //每隔$sec秒执行一次
                        $task[$sec] = $now;
                    }
                }
            } catch (\Throwable $e) {
            }
        });
    }

}