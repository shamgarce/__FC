<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Fast Demo',

//	// preloading 'log' component
//	'preload'=>array('log'),
//
//	// autoloading model and component classes
//	'import'=>array(
//		'application.models.*',
//		'application.components.*',
//	),
	'defaultController'=>'site',
	'modules' => [
		//'gii' => 'yii\gii\Module',
		'admin'=>array(
			//'postPerPage'=>20,
		),
	],

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

//		'db'=>array(
//			'connectionString' => 'sqlite:protected/data/blog.db',
//			'tablePrefix' => 'tbl_',
//		),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=blog',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),
		*/

//		'errorHandler'=>array(
//			// use 'site/error' action to display errors
//			'errorAction'=>'site/error',
//		),
//
//		'urlManager'=>array(
//			'urlFormat'=>'path',
//			'showScriptName' => false,//隐藏index.php
//			'rules'=>array(
//			),
//		),
//
//		'log'=>array(
//			'class'=>'CLogRouter',
//			'routes'=>array(
//				array(
//					'class'=>'CFileLogRoute',
//					'levels'=>'error, warning',
//				),
//				// uncomment the following to show log messages on web pages
//				/*
//				array(
//					'class'=>'CWebLogRoute',
//				),
//				*/
//			),
//		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	//'params'=>require(dirname(__FILE__).'/params.php'),
);