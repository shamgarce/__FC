<?php  if ( ! defined('SHAM_PATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| the active record class
*/

$config = [
    'RDBC'=>'',
    'debug'=>true,
    'route' =>[
        'style' => 'mix',       //混合模式  //path //query
    ],

    'mysql'=>[
        'default' =>[
            "hostname"  =>  '127.0.0.1',
            "username"  =>  'ns',
            "password"  =>  'nsgd012003',
            "database"  =>  'ns',
            "charset"   =>  'utf8',
            "pconnect"  =>  '0',
            "quiet"     =>  '0'
        ],
    ],

    'mongodb'=>[
        'user'		=> 'sa',
        'pwd'		=> 'sa012003',
        'host'		=> '127.0.0.1',
        'port'		=> '27017',
        'database'	=> 'v1',
    ],

    //依赖注入的组件
    'obj'=>[
        [
            'classname'=>'db',
            'class'=>'\Seter\Library\SDb',
            'parm'=>'',
        ],
        [
            'classname'=>'table',
            'class'=>'\Seter\Library\Table',
            'parm'=>'',
        ],
        [
            'classname'=>'model',
            'class'=>'\Seter\Library\Model',
            'parm'=>'',
        ],
        [
            'classname'=>'request',
            'class'=>'\Seter\Library\Request',
            'parm'=>'',
        ],
        [
            'classname'=>'user',
            'class'=>'\Seter\Library\User',
            'parm'=>'',
        ],


//        [
//            'classname'=>'doc',
//            'class'=>'\Seter\Library\Doc',
//            'parm'=>'',
//        ],
//        [
//        [
//            'classname'=>'mdb',
//            'class'=>'\Seter\Library\Mdb',
//            'parm'=>'',
//        ],
//        [
//            'classname'=>'router',
//            'class'=>'\Seter\Library\Router',
//            'parm'=>'',
//        ],

    ]
];

return $config;
