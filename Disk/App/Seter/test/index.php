<?php
use Seter\Core;

include('../I.php');			//入口代码
include('../Core/Core.php');			//入口代码



/**
 * 测试整体功能
 * 1 实例化
 */
$base = \Seter\Seter::getInstance();
//$s = new \Seter\Seter();

unset($base->Config);
unset($base->user);
unset($base->db);
unset($base->mdb);
unset($base->request);
unset($base->table);
unset($base->doc);
unset($base->jsoninfo);
unset($base->ry);


Core\Error::E();

//错误页测试
//$base->error->E('内容');

/*
$base->error->E404();
\Seter\Library\Error::E404();

$base->error->E500('发生一个错误');
$base->error->J404();
\Seter\Library\Error::J404();

$base->error->J500('发生一个错误');
json自定义输出
$base->error->J(200,'ok',array('u'=>1));
error自定义输出
$base->error->E('内容');
*/









/*
//系统 系列接口测试 \ArrayAccess, \Countable, \IteratorAggregate
//$base['s'] = 999;       //set
//$base['s2'] = 999;       //set
//foreach($base as $key => $item){//echo "{$key} => {$item} \n";//}
//var_dump(count($base));
//echo $base->count();
////unset($base['s']);      //unset
//echo isset($base['s']); //isset
//echo $base['s'] ;       //get
*/



var_dump($base->data);





echo 'mark';