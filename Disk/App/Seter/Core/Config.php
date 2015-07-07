<?php

namespace Seter\Core;


class Config {
	public $config 	= array();
	public $configobj 	= null;

	public $obj 	= array();

	public function __construct()
	{
//		//$this->config 	= include(FAST_PATH.'\\Config\\Default.php');
//		var_dump($mdr);
//
//
//		//$configobj[$mdr[0]['classname']] =new $mdr[0]['class']($mdr[0]['parm']);
////		$mdr =new $mdr[0]['class']($mdr[0]['parm']);		//这里能获得对象生成的入口，再做一个循环依赖翻转就行了
////
////		$mdr->E404();
//
//		var_dump($configobj);
////		$this->singleton('config', function ($c) {
////			return new \Seter\Core\Config();
////		});
//
//
//		//$this->obj['error']->E404();
//		//print_r($m);
//		exit;
	}

	public function getconfig()
	{

	}


}





