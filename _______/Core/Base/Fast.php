<?php

namespace Fast;
! defined('IN_FAST_APP') && exit('No direct script access allowed');

/*
 * -------------------------------------------------------------------
 *  FAST_BASE core path
 * -------------------------------------------------------------------
 */
define('FAST_BASE', pathinfo(__FILE__, PATHINFO_DIRNAME));        //当前的路径

class Fast
{
    private static $instance    = null;         //单例调用

    public function __construct(){
    }

    public static  function Run()
    {
        /*
         * 根据路由执行相对应的controllers
         * */
//        $router =


    }

    /**
     * @return Fast
     * 自身实例化
     */
    public static function getInstance(){
        !(self::$instance instanceof self)&&self::$instance = new self();
        return self::$instance;
    }

    /**
     * Fast PSR-0 autoloader
     */
    public static  function autoload($className)
    {
        $thisClass = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);
        $baseDir = __DIR__;
        if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
            $baseDir = substr($baseDir, 0, -strlen($thisClass));
        }
        $className = ltrim($className, '\\');
        $fileName = $baseDir;
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        if (file_exists($fileName)) {
            require $fileName;
        }
    }

    /**
     * Register Fast's PSR-0 autoloader
     */
    public static  function registerAutoloader()
    {
        spl_autoload_register("\\Fast\\Fast::autoload");
    }
}


//\Fast\Fast::autoload('Fast\Test\SL');
\Fast\Fast::registerAutoloader();

$md = Fast::getInstance();
var_dump($md);
//基本的调用规则 mca



//
///*
// * 执行模式一 router
// * */
//Fast::loadconfig()->router()->moduleconfig()->eventment()->router()->Run();
//
//
///*
// *  * 执行模式二 不需要router
// * */
//Fast::loadconfig()->router()->moduleconfig()->eventment()->Run();



//执行步骤
/*
1 ： 加载配置
配置文件中设置的信息


2 :  根据配置计算环境变量
post get cookie
router
其他各项参数


3 ：


*/






/*测试
$sl = new \Fast\Test\SL();
$sl->test();
*/









