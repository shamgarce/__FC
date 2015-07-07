<?php

namespace Seter\Library;

class Table{
    public function __get($tablename) {
        //获得表明，根据表明生成一个对象返回
        return new \Seter\Library\TableObj($tablename);
    }

//    private $db = null;
//    public function __construct($chr = 'default'){
////        getInstance::
//    }
//
//    public function test(){
////        $S = \Seter\Seter::getInstance();
////        $ns = $S->table->dy_user;
////        var_dump($ns);
////        echo 'table::test';
//    }
//    public function __set($a, $b) {
//        //echo 1;
//    }
}

