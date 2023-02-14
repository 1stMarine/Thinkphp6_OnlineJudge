<?php

namespace app\controller\question\Model;

use think\model\Pivot;

/**
 * 编译记录 -> 用户  中间表
 */
class TbRecordUser extends Pivot
{
    protected $autoWriteTimestamp = true;
}