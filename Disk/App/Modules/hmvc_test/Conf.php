<?php
/**
 * modules 可以在这里设置，并且覆盖前面的设置
 */
$config = [
    'error_manage'      =>'',
//    'application_folder' => dirname(__FILE__),
    //入口系统模块 - hmvc必须
    'modules'=>[
        'testm2' => 'hmvc_testm',
        'm2'    => 'hmvc_m2'
    ],
    'error_manage2222222222'      =>'123',
];
unset($config['modules']);      //拒绝

return $config;
