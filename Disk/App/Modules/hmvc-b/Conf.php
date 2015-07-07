<?php
/**
 * modules 可以在这里设置，并且覆盖前面的设置
 */
$config = [

    'CONF_FILE'    => 'Conf.php',

    'Default_Controller'=>'welcome',

    'Default_Action'=>'index',

    'Default_Action_Prefix'=>'do',

    'default_timezone'  => 'PRC',

    'charset'           => 'utf-8',

    'error_manage'=>'',

    'debug' => true,

//    'application_folder' => dirname(__FILE__),
    //入口系统模块 - hmvc必须


    'modules'=>[
        'testm' => 'hmvc_testm',
        'm2'    => 'hmvc_m2'
    ],
];
return $config;
