<?php
/**
 * modules 可以在这里设置，并且覆盖前面的设置
 */

$config = [

    'error_manage' => '',
    'usertablename' => 'dy_user',
    'Cacheroot' => C('APP_PATH').'cache'.C('WDS'),
//    'application_folder' => dirname(__FILE__),
    //入口系统模块 - hmvc必须


    'modules' => [
        'test' => 'hmvc_test',
        'm2'   => 'hmvc_m2'
    ],


    'mysql'=>[
        'default'=>[
            "hostname"  =>  '127.0.0.1',
            "username"  =>  'nsv1',
            "password"  =>  'nsgd012003',
            "database"  =>  'nsv1',

            "charset"   =>  'utf8',
            "pconnect"  =>  0,
            "quiet"     =>  0
        ]
    ]
];
return $config;


