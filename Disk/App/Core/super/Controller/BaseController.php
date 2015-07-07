<?php

//hook

class BaseController extends Controller{

    public function __construct()
    {
        parent::__construct();
    }

    //action before
    protected function _init(){
        header("Content-Type:text/html; charset=utf-8");
    }

    protected function RDBC()
    {

    }

//  扩展内容包括
//  内容包括
/**
 * 属性 ispost
 *
 * db
 * table
 * cache
 * user
 * router
 * input
 * model
 * library
 * helper
 * log
 * trace
 * cache
 * ldb
 * debug
 * S
 */

} 
