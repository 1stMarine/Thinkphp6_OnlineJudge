<?php

namespace app\common;

class SnowFlake{
    public static function createID(){
        //假设一个机器id
        $machineId = 1234567890;

        //41bit timestamp(毫秒)
        $time = floor(microtime(true) * 1000);

        //0bit 未使用
        $suffix = 0;

        //datacenterId  添加数据的时间
        $base = decbin(pow(2,40) - 1 + $time);

        //workerId  机器ID
        $machineid = decbin(pow(2,9) - 1 + $machineId);

        //毫秒类的计数
        $random = mt_rand(1, pow(2,11)-1);

        $random = decbin(pow(2,11)-1 + $random);
        //拼装所有数据
        $base64 = $suffix.$base.$machineid.$random;
        //将二进制转换int
        $base64 = bindec($base64);

        $id = sprintf('%.0f', $base64);

        return $id;
    }
}