<?php

/*
 * 调用
入口程序
 * */

define('FAST_PATH',__DIR__);

//use Seter\RedBeanPHP;
define('SHAM_PATH',__DIR__);
include(__DIR__.'\Fun.php');

!empty($_GET)   && define('ISGET',TRUE);
!empty($_POST)  && define('ISPOST',TRUE);
!defined('ISGET')   && define('ISGET',false);
!defined('ISPOST')  && define('ISPOST',false);
!defined('BTIME')  && define('BTIME', \Sham::T());

include 'Core/Base.php';                    //基类
include 'Seter.php';                        //基类

/**
 * 目录设定
 * 1 基础类 不能直接使用，需要被继承
 * Fast
 * 2 基础功能类 提供稳定的函数和变量 可以直接使用
 * Base
 * 3 功能类 提供系列功能
 * Library
 * 4 模型 Model
 * 解决问题的模型
 * 5 modules模块
 */
