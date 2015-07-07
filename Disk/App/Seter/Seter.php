<?php
/*
 * 调用
 * include('Seter/I.php');			//入口代码
 * include('Seter/Seter.php');
 * $this->Seter = new \Seter\Seter();
 * \Seter\Seter::getInstance()
 * */
namespace Seter;
!defined('FAST_PATH') && die('out of app');
class Seter extends \Seter\Core\Base
{
    /*
     * $mysqltime=date('Y-m-d H:i:s',$phptime);
     * $phptime=strtotime($mysqldate);
     * 单例调用
     * */
    private static $instance    = null;         //单例调用

//    /*
//     * trace记录
//     * 调用: \Sham\trace($info='')
//     * \Sham\gettrace()
//     * */
//    public static $trace        = array();      //trace记录
//    public  $dec        = 123;                  //trace记录

//    /*
//     * 用户信息
//     * */
//    private $identify = array();                //用户识别

    /*
     * 配置
     * */
    public $Config = array();


//    /**
//     * Key-value array of arbitrary data
//     * @var array
//     */
//    public $data = array();


    public function __construct($items = array())
    {
        //iniaction

        $this->Config = include(FAST_PATH.'\\Config\\Default.php');

        $this->singleton('sys', function ($c) {
            return new \Seter\Core\Sys();
        });
        $this->singleton('error', function ($c) {
            return new \Seter\Core\Error();
        });
        /**
         * 系统函数和参数
         */
        $this->singleton('ry', function ($c) {
            return new \Seter\Library\ServerAPI('8luwapkvufd1l','428XgqSUvxeAzr');
        });

        //=================================================
        /**
         * 注入对象
         */
        foreach($this->Config['obj'] as $key=>$value){
            $this->singleton($value['classname'], function () use ($key,$value){
                return new $this->Config['obj'][$key]['class'];
            });
        }
    }

    public static function run()
    {
        echo 'mvc模式执行';
    }

    //用于密码hash
    public static function pwdhash($str='')
    {
        return $str;
        if(empty($str)){
            return '';
        }else{
            return md5($str);
        }
    }

    /*hash函数
     * */
    public static function hash($str='')
    {
        if(empty($str)){
            return '';
        }else{
            return md5($str);
        }
    }

    public static function getInstance(){
        !(self::$instance instanceof self)&&self::$instance = new self();
        return self::$instance;
    }

    /**
     * Normalize data key
     * @param  string $key The data key
     * @return mixed       The transformed/normalized data key
     */
    protected function normalizeKey($key)
    {
        return $key;
    }





    /**
     * Ensure a value or object will remain globally unique
     * @param  string  $key   The value or object name
     * @param  Closure        The closure that defines the object
     * @return mixed
     */
    public function singleton($key, $value)
    {

        $this->set($key, function ($c) use ($value) {
            static $object;
            if (null === $object) {
                $object = $value($c);
            }
            return $object;
        });
    }

    /**
     * Protect closure from being directly invoked
     * @param  Closure $callable A closure to keep from being invoked and evaluated
     * @return Closure
     */
    public function protect(Closure $callable)
    {
        return function () use ($callable) {
            return $callable;
        };
    }

    function __call($function_name, $args)
    {
        echo "function: $function_name (<br />";
        var_dump($args);
        echo ") not exist！";
    }

    public static function autoload($className)
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
//        echo $fileName;
        if (file_exists($fileName)) {
            require $fileName;
        }
    }

    /**
     * Register Slim's PSR-0 autoloader
     */
    public static function registerAutoloader()
    {
        spl_autoload_register(__NAMESPACE__ . "\\Seter::autoload");
    }

    /*
     * 判断失败之后，失败消息存放到这里面
     * 格式 code / msg / data 可以用jsong输出
     * 这里是json失败消息机制，判断操作失败的数据存放到这里，用统一的函数进行输出
     * 也可以采用模型本身的消息，二选一，只选一
     * */
    public $jsoninfo        = array();
    public function setjsoninfo($ar = array())
    {
        $this->jsoninfo = $ar;
    }

    public function getjsoninfo()
    {
        echo json_encode($this->$json);
        exit;
    }


}

\Seter\Seter::registerAutoloader();     //PSR-0

