<?php

/**
 * Class Ms
 * hook : _init
 */
class Ms extends Model
{
    public function load($res = null)
    {
//        $this->res = $res;
    }
    public function go()
    {
        echo '模型';
        D($this->get);
        D($this->post);
        D($this->cookie);
    }

    /**
     * 重写
     */
//    public function _init()
//    {
//        $this->get = \Seter\Seter::getInstance()->request->get;
//        $this->post = \Seter\Seter::getInstance()->request->post;
//        $this->cookie = \Seter\Seter::getInstance()->request->cookie;
//    }


}
