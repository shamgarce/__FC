<?php

namespace Seter\Library;

class Request{
    public static $instance;

    public function __construct(){}
    public function post(){
        return $_POST;
    }
    public function get(){
        $router = C('router');
        $parms = $router['params'];
        return $parms;
    }

    public function cookie(){
        return $_COOKIE;
    }

    public function __get($name){
        return $this->$name();
    }

    //=======================================
    //=======================================
}

