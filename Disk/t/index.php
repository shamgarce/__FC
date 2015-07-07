<?php


/**
 * https://github.com/leo108/SinglePHP
 */
include '../../SinglePHP.class.php';
$config = ['APP_PATH'    => '../App/'];
SinglePHP::getInstance($config)->run();
//Traits

/**
model
library
helper
 widget
 * 这四个会检查hmvc目录，如果不存在则递归到根目录上
 * */

