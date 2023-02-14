<?php

namespace app\controller\match\Model;

class Rank
{
    public $mid;
    public $uid;
    public $user_name;
    public $score;
    public $states = [];

    /**
     * @param $mid
     * @param $uid
     * @param $user_name
     * @param $score
     * @param array $states
     */
    public function __construct($mid, $uid, $user_name, $score, array $states)
    {
        $this->mid = $mid;
        $this->uid = $uid;
        $this->user_name = $user_name;
        $this->score = $score;
        $this->states = $states;
    }


}