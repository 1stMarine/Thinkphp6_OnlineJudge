<?php

namespace app\controller\question;

use think\Model;

class Question extends Model
{
    protected $name = "tb_question_list";
    protected $pk = "qid";
}