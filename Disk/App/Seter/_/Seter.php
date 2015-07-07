<?php
namespace Seter;

use Seter\Sham\Object;
use \Sham\Sham;             //可以直接访问sham命名空间

include(__DIR__.'\Sham.php');
include(__DIR__.'\Sham\SeterBase.php');

class Seter extends \Seter\Sham\SeterBase{

    public function __construct($items = array())
    {
        // $this->replace($items);
//        $this->singleton('db', function ($c) {
//            return new Sham_Db();
//        });
////        $this->singleton('mdb', function ($c) {
////            return new Sham_Mdb();
////        });
//        $this->singleton('logmon', function ($c) {
//            return new Sham_Logmon();
//        });
//        $this->singleton('PHPExcel', function ($c) {
//            return new PHPExcel();
//        });
        $this->singleton('set', function ($c) {
            return new \Seter\Model\Set();
        });
        $this->ini();
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



}

\Seter\Seter::registerAutoloader();     //PSR-0

/*
 * //use \Sham\BaseYii as Sham;
Sham::test();
 * */
//Sham::test();
//$seter = new Seter();
//var_dump($seter->set);

//说明
//Sham是可以访问的静态方法空间



//include(__DIR__.'\Interface\ActiveRecordInterface.php');
//include(__DIR__.'\Interface\Arrayable.php');
//include(__DIR__.'\Interface\ArrayableTrait.php');
//include(__DIR__.'\Sham\Object.php');
//include(__DIR__.'\Sham\Component.php');
//include(__DIR__.'\Sham\Event.php');
//include(__DIR__.'\Sham\Model.php');
//include(__DIR__.'\Base\BaseActiveRecord.php');
//include(__DIR__.'\Base\ActiveRecord.php');
//include(__DIR__.'\Model\User.php');



//Model
//

//$user = new \Seter\Base\ActiveRecord();
//$user = new \Seter\Model\User();

//$user = \Seter\Model\User::findOne(42);
//$user->test();
//var_dump($user);
//
//Object::className();
//
//
//
//











echo 'mark';
exit;