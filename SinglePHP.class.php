<?php
/**
 * 获取和设置配置参数 支持批量定义
 * 如果$key是关联型数组，则会按K-V的形式写入配置
 * 如果$key是数字索引数组，则返回对应的配置数组
 * @param string|array $key 配置变量
 * @param array|null $value 配置值
 * @return array|null
 */
function G($filename){
    if(file_exists($filename)){
        return include $filename;
    }
    return [];
}

function D($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}


function C($key,$value=null){
    static $_config = array();
    $args = func_num_args();
    if($args == 1){
        if($key == 'G') {  //如果传入的G 命令行
            return $_config;
        }
        if(is_string($key)){  //如果传入的key是字符串
            return isset($_config[$key])?$_config[$key]:null;
        }
        if(is_array($key)){
            if(array_keys($key) !== range(0, count($key) - 1)){  //如果传入的key是关联数组
                $_config = array_merge($_config, $key);
            }else{
                $ret = array();
                foreach ($key as $k) {
                    $ret[$k] = isset($_config[$k])?$_config[$k]:null;
                }
                return $ret;
            }
        }
    }else{
        if(is_string($key)){
            $_config[$key] = $value;
        }else{
            halt('传入参数不正确');
        }
    }
    return null;
}

/**
 * 调用Widget
 * @param string $name widget名
 * @param array $data 传递给widget的变量列表，key为变量名，value为变量值
 * @return void
 */
function W($name, $data = array()){
    $fullName = $name.'Widget';
    if(!class_exists($fullName)){
        halt('Widget '.$name.'不存在');
    }
    $widget = new $fullName();
    $widget->invoke($data);
}

/**
 * 终止程序运行
 * @param string $str 终止原因
 * @param bool $display 是否显示调用栈，默认不显示
 * @return void
 */
function halt($str, $display=false){
    Log::fatal($str.' debug_backtrace:'.var_export(debug_backtrace(), true));
    header("Content-Type:text/html; charset=utf-8");
    if($display){
        echo "<pre>";
        debug_print_backtrace();
        echo "</pre>";
    }
    echo $str;
    exit;
}

/**
 * 获取数据库实例
 * @return DB
 */
function M(){
    $dbConf = C(array('DB_HOST','DB_PORT','DB_USER','DB_PWD','DB_NAME','DB_CHARSET'));
    return DB::getInstance($dbConf);
}

/**
 * 如果文件存在就include进来
 * @param string $path 文件路径
 * @return void
 */
function includeIfExist($path){
    if(file_exists($path)){
        include $path;
    }
}

/**
 * 总控类
 */
class SinglePHP {

    /**
     * 模块
     * @var
     */
    private $m;
    /**
     * 控制器
     * @var string
     */
    private $c;
    /**
     * Action
     * @var string
     */
    private $a;
    /**
     * 单例
     * @var SinglePHP
     */
    private static $_instance;
    private static $_conf;

    /**
     * 构造函数，初始化配置
     * @param array $conf
     */
    private function __construct($conf){
        C($conf);
        $conf['CONF_FILE'] = isset($conf['CONF_FILE'])?$conf['CONF_FILE']:'Conf.php';
        $conf = G($conf['APP_PATH'].$conf['CONF_FILE']);
        if(isset($conf['APP_PATH'])) unset($conf['APP_PATH']);
        $conf['modules']['super'] = 'hmvc_s';               //内置 debug
        $conf = array_merge(self::loadAppDefaultConfig(),$conf);
        C($conf);
    }

    private function __clone(){}

    /**
     * 获取单例
     * @param array $conf
     * @return SinglePHP
     */
    public static function getInstance($conf){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self($conf);
        }
        return self::$_instance;
    }

    /**
     * 运行应用实例
     * @access public
     * @return void
     */
    public function run(){
        if(C('USE_SESSION') == true){
            session_start();
        }
        $router = new router();
        $conf['router'] = $router();        //这个是路由
        C($conf);                           //路由进配置

        $this->loadAppConfig();             //覆盖hmvc配置
        C('APP_FULL_PATH', getcwd().C('WDS').rtrim(C('APP_PATH'),C('WDS')).C('WDS'));
        C('BASE_FULL_PATH', getcwd().C('WDS').rtrim(C('APP_BASE'),C('WDS')).C('WDS'));
        //除controllers外，都需要检测根下有没有相对应的组件
        //======================================================================
        //ok ok 下一步
        //判断ca是否存在，存在，则执行
        //否则报错
        $router = C('router');
        $controllerfile2 = C('APP_FULL_PATH').C('controller_folder').'BaseController'.C('controller_file_subfix');
        includeIfExist($controllerfile2);
        $controllerfile = C('APP_FULL_PATH').C('controller_folder').$router['Controller'].C('controller_file_subfix');
        includeIfExist($controllerfile);


        if(!class_exists($router['Controller'])){
            halt('控制器'.$router['Controller'].'不存在');
        }
        $controllerClass = $router['Controller'];
        $controller = new $controllerClass();
        $method = 'do'.ucfirst($router['Action']);
        if(!method_exists($controller, $method)){
            halt('方法'.$method.'不存在');
        }

        spl_autoload_register(array('SinglePHP', 'autoload'));              //psr-0
        $router['params'] = isset($router['params'])?$router['params']:[];
        $params = $router['params'];
        if(count($router['params']) ==1 ){
            $nr = current(array_values($router['params']));
            if(empty($nr)){
                $params = current(array_keys($router['params']));
            }
        }
        call_user_func(array($controller,$method),$params);
    }


    /**
     * 路由已经准备好了
     * 根据路由获取hmvc的配置信息
     */
    public function loadAppConfig()
    {
        $router = C('router');
        $modules = C('modules');

        if($router['Module']){
            if($router['Module'] == 'super'){
                C('APP_PATH',C('APP_PATH').'Core'.C('WDS').'super'.C('WDS'));       //hmvc路径
            }else{
                C('APP_PATH',C('APP_PATH').'Modules'.C('WDS').$modules[$router['Module']].C('WDS'));
            }
           // echo C('APP_HMVC_PATH').C('CONF_FILE');
            $conf = G(C('APP_PATH').C('CONF_FILE'));
            /**
             * 去除掉屏蔽的设置
             */
            if(isset($conf['APP_PATH']))    unset($conf['APP_PATH']);
            if(isset($conf['modules']))     unset($conf['modules']);
            if(isset($conf['router']))      unset($conf['router']);
            C($conf);
        }
        /**
         * 补全route信息
         */
        $router = C('router');
        if(empty($router['Controller']))$router['Controller'] = C('default_controller');
        if(empty($router['Action']))$router['Action'] = C('default_controller_method');
        C('router',$router);
        return true;
    }

    /**
     * 默认配置
     * @return array
     */
    public static function loadAppDefaultConfig(){
        return [
            'APP_BASE'          => C('APP_PATH'),
            'WDS'               => DIRECTORY_SEPARATOR,
            'CONF_FILE'         => 'Conf.php',
            'default_timezone'  => 'PRC',
            'charset'           => 'utf-8',
            'CONF_FILE'         => 'Conf.php',

            'error_page_404'    => C('APP_PATH').'error'.DIRECTORY_SEPARATOR.'error_404.php',
            'error_page_50x'    => C('APP_PATH').'error'.DIRECTORY_SEPARATOR.'error_50x.php',
            'error_page_db'     => C('APP_PATH').'error'.DIRECTORY_SEPARATOR.'error_db.php',

            'message_page_view' => C('APP_PATH').'error'.DIRECTORY_SEPARATOR.'error_view.php',


            //相对路径
            'controller_folder' => 'controller'.DIRECTORY_SEPARATOR,
            'model_folder'      => 'models'.DIRECTORY_SEPARATOR,
            'view_folder'       => 'views'.DIRECTORY_SEPARATOR,
            'library_folder'    => 'library'.DIRECTORY_SEPARATOR,
            'helper_folder'     => 'helper'.DIRECTORY_SEPARATOR,
            //相对路径

            'default_controller'        => 'home',
            'default_controller_method' => 'index',
            'controller_method_prefix'  => 'do',

            //扩展名
            'controller_file_subfix'    => '.php',
            'model_file_subfix'         => '.php',
            'view_file_subfix'          => '.php',
            'library_file_subfix'       => '.php',
            'helper_file_subfix'        => '.php',

            'debug' => true,
        ];
    }



    /**
     * 自动加载函数
     * @param string $class 类名
     */
    public static function autoload($class){
        if(substr($class,-6)=='Widget'){
            includeIfExist(C('APP_FULL_PATH').'/Widget/'.$class.'.class.php');
        }else{
            //首先检查在应用目录中是否存在该类，存在加载，不存在，则到根下寻找
            includeIfExist(C('APP_FULL_PATH').'/Lib/'.$class.'.class.php');
            if(!class_exists($class)){
                includeIfExist(C('BASE_FULL_PATH').'/Lib/'.$class.'.class.php');
            }
            if(!class_exists($class)){
                includeIfExist(C('APP_FULL_PATH').'/Models/'.$class.'.model.php');
            }
            if(!class_exists($class)){
                includeIfExist(C('BASE_FULL_PATH').'/Models/'.$class.'.model.php');
            }
        }
    }
}

/**
 * 控制器类
 */
class Controller {
    /**
     * 视图实例
     * @var View
     */
    private $_view;
    public $router;
    public $env;
    public $data = [];

    /**
     * 构造函数，初始化视图实例，调用hook
     */
    public function __construct(){



        $this->router = C('router');
        isset( $this->router['params']) &&  $this->params = $this->router['params'];
        $this->env['bt'] = $_SERVER['REQUEST_TIME_FLOAT'];
        $this->env['ip'] = '';
        $this->env['mem'] = memory_get_usage();

        // 依赖注入
        $this->singleton('S', function ($c) {
            includeIfExist(C('BASE_FULL_PATH').'Seter/I.php');
            //echo C('BASE_FULL_PATH').'Seter/I.php';
            return \Seter\Seter::getInstance();
        });

        /**
         * 无依赖或者只抵赖底层的 route属于最底层，可以在conf中进行变量的配置
         */
        $this->singleton('db', function ($c) {
            return $this->S->db;
        });
        $this->singleton('table', function ($c) {
            return $this->S->table;
        });
        $this->singleton('model', function ($c) {
            return $this->S->model;
        });
        $this->singleton('request', function ($c) {
            return $this->S->request;
        });
        $this->singleton('user', function ($c) {
            return $this->S->user;
        });


//        $this->singleton('error', function ($c) {
//            return $this->S->error;
//        });
//        $this->singleton('cache', function ($c) {
//            return $this->S->cache;
//        });
//        $this->singleton('input', function ($c) {
//            return $this->S->input;
//        });

//        $this->singleton('helper', function ($c) {
//            return new Test();
//        });
//        $this->singleton('input', function ($c) {
//            return new Test();
//        });


//        /**
//         * 依赖db组件
//         */
//        $this->singleton('table', function ($c) {
//            return new Test();
//        });
//        $this->singleton('user', function ($c) {
//            return new Test();
//        });
//
//        $this->singleton('debug', function ($c) {
//            return new Test();
//        });
//
//        $this->singleton('rbac', function ($c) {
//            return new Test();
//        });


        $this->_view = new View();
        $this->_init();
    }

//开始依赖注入
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
     * Set data key to value
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        $this->data[$this->normalizeKey($key)] = $value;
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $isInvokable = is_object($this->data[$this->normalizeKey($key)]) && method_exists($this->data[$this->normalizeKey($key)], '__invoke');
            return $isInvokable ? $this->data[$this->normalizeKey($key)]($this) : $this->data[$this->normalizeKey($key)];
        }
        return $default;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    protected function normalizeKey($key)
    {
        return $key;
    }
    public function has($key)
    {
        return array_key_exists($this->normalizeKey($key), $this->data);
    }
//结束依赖注入

    /**
     * 前置hook
     */
    protected function _init(){
        header("Content-Type:text/html; charset=utf-8");
    }

    /**
     * 渲染模板并输出
     * @param null|string $tpl 模板文件路径
     * 参数为相对于App/View/文件的相对路径，不包含后缀名，例如index/index
     * 如果参数为空，则默认使用$controller/$action.php
     * 如果参数不包含"/"，则默认使用$controller/$tpl
     * @return void
     */
    protected function display($tpl='',$data = []){
        if($tpl === ''){
            $router = C('router');
            $tpl = $router['Action'];
        }
        $data['router'] = $this->router;
        $data['env']    = $this->env;

        $this->_view->display($tpl,$data);
    }

    protected function fetch($tpl='',$data = []){
        if($tpl === ''){
            $router = C('router');
            $tpl = $router['Action'];
        }
        $data['router'] = $this->router;
        $data['env']    = $this->env;
        return $this->_view->fetch($tpl,$data);
    }

    /**
     * 为视图引擎设置一个模板变量
     * @param string $name 要在模板中使用的变量名
     * @param mixed $value 模板中该变量名对应的值
     * @return void
     */
//    protected function assign($name,$value){
//        $this->_view->assign($name,$value);
//    }
    /**
     * 将数据用json格式输出至浏览器，并停止执行代码
     * @param array $data 要输出的数据
     */
    protected function ajaxReturn($data){
        echo json_encode($data);
        exit;
    }
    /**
     * 重定向至指定url
     * @param string $url 要跳转的url
     * @param void
     */
    protected function redirect($url){
        header("Location: $url");
        exit;
    }
}

/**
 * 视图类
 */
class View {
    /**
     * 视图文件目录
     * @var string
     */
    private $_tplDir;
    /**
     * 视图文件路径
     * @var string
     */
    private $_viewPath;
    /**
     * 视图变量列表
     * @var array
     */
    private $_data = array();
    /**
     * 给tplInclude用的变量列表
     * @var array
     */
    private static $tmpData;

    /**
     * @param string $tplDir
     */
    public function __construct($tplDir=''){
        if($tplDir == ''){
            $this->_tplDir = './'.C('APP_PATH').'/View';
        }else{
            $this->_tplDir = $tplDir;
        }

    }
    /**
     * 为视图引擎设置一个模板变量
     * @param string $key 要在模板中使用的变量名
     * @param mixed $value 模板中该变量名对应的值
     * @return void
     */
//    public function assign($key, $value) {
//        $this->_data[$key] = $value;
//    }

    public function fetch($tplFile,$data)
    {

        ob_start(); //开启缓冲区
        $this->_data = $data;
        $router = C('router');
        $this->_viewPath = $this->_tplDir .'/'.$router['Controller'].'/'. $tplFile . '.php';
//$router = C('router');
////            D($router);        echo  $this->_viewPath ;
        unset($tplFile);
        extract($this->_data);
        include $this->_viewPath;

        $str=ob_get_contents();
        ob_end_clean();

        return $str;
    }

    /**
     * 渲染模板并输出
     * @param null|string $tplFile 模板文件路径，相对于App/View/文件的相对路径，不包含后缀名，例如index/index
     * @return void
     */
    public function display($tplFile,$data) {
        $this->_data = $data;
        $router = C('router');
        $this->_viewPath = $this->_tplDir .'/'.$router['Controller'].'/'. $tplFile . '.php';
//$router = C('router');
////            D($router);        echo  $this->_viewPath ;
        unset($tplFile);
        extract($this->_data);
        include $this->_viewPath;
    }
    /**
     * 用于在模板文件中包含其他模板
     * @param string $path 相对于View目录的路径
     * @param array $data 传递给子模板的变量列表，key为变量名，value为变量值
     * @return void
     */
    public static function tplInclude($path, $data=array()){
        self::$tmpData = array(
            'path' => C('APP_FULL_PATH') . '/View/' . $path . '.php',
            'data' => $data,
        );
        unset($path);
        unset($data);
        extract(self::$tmpData['data']);
        include self::$tmpData['path'];
    }
}

/**
 * Widget类
 * 使用时需继承此类，重写invoke方法，并在invoke方法中调用display
 */
class Widget {
    /**
     * 视图实例
     * @var View
     */
    protected $_view;
    /**
     * Widget名
     * @var string
     */
    protected $_widgetName;

    /**
     * 构造函数，初始化视图实例
     */
    public function __construct(){
        $this->_widgetName = get_class($this);
        $dir = C('APP_FULL_PATH') . '/Widget/Tpl/';
        $this->_view = new View($dir);
    }

    /**
     * 处理逻辑
     * @param mixed $data 参数
     */
    public function invoke($data){}

    /**
     * 渲染模板
     * @param string $tpl 模板路径，如果为空则用类名作为模板名
     */
    protected function display($tpl='',$data = []){
        if($tpl == ''){
            $tpl = $this->_widgetName;
        }
        $tpl = '../'.$tpl;

        $this->_view->display($tpl,$data);
    }

    /**
     * 为视图引擎设置一个模板变量
     * @param string $name 要在模板中使用的变量名
     * @param mixed $value 模板中该变量名对应的值
     * @return void
     */
    protected function assign($name,$value){
        $this->_view->assign($name,$value);
    }
}

/**
 * 数据库操作类
 * 使用方法：
 * DB::getInstance($conf)->query('select * from table');
 * 其中$conf是一个关联数组，需要包含以下key：
 * DB_HOST DB_USER DB_PWD DB_NAME
 * 可以用DB_PORT和DB_CHARSET来指定端口和编码，默认3306和utf8
 */
class DB {
    /**
     * 数据库链接
     * @var resource
     */
    private $_db;
    /**
     * 保存最后一条sql
     * @var string
     */
    private $_lastSql;
    /**
     * 上次sql语句影响的行数
     * @var int
     */
    private $_rows;
    /**
     * 上次sql执行的错误
     * @var string
     */
    private $_error;
    /**
     * 实例数组
     * @var array
     */
    private static $_instance = array();

    /**
     * 构造函数
     * @param array $dbConf 配置数组
     */
    private function __construct($dbConf){
        if(!isset($dbConf['DB_CHARSET'])){
            $dbConf['DB_CHARSET'] = 'utf8';
        }
        $this->_db = mysql_connect($dbConf['DB_HOST'].':'.$dbConf['DB_PORT'],$dbConf['DB_USER'],$dbConf['DB_PWD']);
        if($this->_db === false){
            halt(mysql_error());
        }
        $selectDb = mysql_select_db($dbConf['DB_NAME'],$this->_db);
        if($selectDb === false){
            halt(mysql_error());
        }
        mysql_set_charset($dbConf['DB_CHARSET']);
    }
    private function __clone(){}

    /**
     * 获取DB类
     * @param array $dbConf 配置数组
     * @return DB
     */
    static public function getInstance($dbConf){
        if(!isset($dbConf['DB_PORT'])){
            $dbConf['DB_PORT'] = '3306';
        }
        $key = $dbConf['DB_HOST'].':'.$dbConf['DB_PORT'];
        if(!isset(self::$_instance[$key]) || !(self::$_instance[$key] instanceof self)){
            self::$_instance[$key] = new self($dbConf);
        }
        return self::$_instance[$key];
    }
    /**
     * 转义字符串
     * @param string $str 要转义的字符串
     * @return string 转义后的字符串
     */
    public function escape($str){
        return mysql_real_escape_string($str, $this->_db);
    }
    /**
     * 查询，用于select语句
     * @param string $sql 要查询的sql
     * @return bool|array 查询成功返回对应数组，失败返回false
     */
    public function query($sql){
        $this->_rows = 0;
        $this->_error = '';
        $this->_lastSql = $sql;
        $this->logSql();
        $res = mysql_query($sql,$this->_db);
        if($res === false){
            $this->_error = mysql_error($this->_db);
            $this->logError();
            return false;
        }else{
            $this->_rows = mysql_num_rows($res);
            $result = array();
            if($this->_rows >0) {
                while($row = mysql_fetch_array($res, MYSQL_ASSOC)){
                    $result[]   =   $row;
                }
                mysql_data_seek($res,0);
            }
            return $result;
        }
    }
    /**
     * 查询，用于insert/update/delete语句
     * @param string $sql 要查询的sql
     * @return bool|int 查询成功返回影响的记录数量，失败返回false
     */
    public function execute($sql) {
        $this->_rows = 0;
        $this->_error = '';
        $this->_lastSql = $sql;
        $this->logSql();
        $result =   mysql_query($sql, $this->_db) ;
        if ( false === $result) {
            $this->_error = mysql_error($this->_db);
            $this->logError();
            return false;
        } else {
            $this->_rows = mysql_affected_rows($this->_db);
            return $this->_rows;
        }
    }
    /**
     * 获取上一次查询影响的记录数量
     * @return int 影响的记录数量
     */
    public function getRows(){
        return $this->_rows;
    }
    /**
     * 获取上一次insert后生成的自增id
     * @return int 自增ID
     */
    public function getInsertId() {
        return mysql_insert_id($this->_db);
    }
    /**
     * 获取上一次查询的sql
     * @return string sql
     */
    public function getLastSql(){
        return $this->_lastSql;
    }
    /**
     * 获取上一次查询的错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->_error;
    }

    /**
     * 记录sql到文件
     */
    private function logSql(){
        Log::sql($this->_lastSql);
    }

    /**
     * 记录错误日志到文件
     */
    private function logError(){
        $str = '[SQL ERR]'.$this->_error.' SQL:'.$this->_lastSql;
        Log::warn($str);
    }
}

/**
 * 日志类
 * 使用方法：Log::fatal('error msg');
 * 保存路径为 App/Log，按天存放
 * fatal和warning会记录在.log.wf文件中
 */
class Log{
    /**
     * 打日志，支持SAE环境
     * @param string $msg 日志内容
     * @param string $level 日志等级
     * @param bool $wf 是否为错误日志
     */
    public static function write($msg, $level='DEBUG', $wf=false){
        if(function_exists('sae_debug')){ //如果是SAE，则使用sae_debug函数打日志
            $msg = "[{$level}]".$msg;
            sae_set_display_errors(false);
            sae_debug(trim($msg));
            sae_set_display_errors(true);
        }else{
            $msg = date('[ Y-m-d H:i:s ]')."[{$level}]".$msg."\r\n";
            $logPath = C('APP_FULL_PATH').'/Log/'.date('Ymd').'.log';
            if($wf){
                $logPath .= '.wf';
            }
            file_put_contents($logPath, $msg, FILE_APPEND);
        }
    }

    /**
     * 打印fatal日志
     * @param string $msg 日志信息
     */
    public static function fatal($msg){
        self::write($msg, 'FATAL', true);
    }

    /**
     * 打印warning日志
     * @param string $msg 日志信息
     */
    public static function warn($msg){
        self::write($msg, 'WARN', true);
    }

    /**
     * 打印notice日志
     * @param string $msg 日志信息
     */
    public static function notice($msg){
        self::write($msg, 'NOTICE');
    }

    /**
     * 打印debug日志
     * @param string $msg 日志信息
     */
    public static function debug($msg){
        self::write($msg, 'DEBUG');
    }

    /**
     * 打印sql日志
     * @param string $msg 日志信息
     */
    public static function sql($msg){
        self::write($msg, 'SQL');
    }
}

/**
 * ExtException类，记录额外的异常信息
 */
class ExtException extends Exception{
    /**
     * @var array
     */
    protected $extra;

    /**
     * @param string $message
     * @param array $extra
     * @param int $code
     * @param null $previous
     */
    public function __construct($message = "", $extra = array(), $code = 0, $previous = null){
        $this->extra = $extra;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取额外的异常信息
     * @return array
     */
    public function getExtra(){
        return $this->extra;
    }
}

class Model
{
    public $get     = array();
    public $post    = array();
    public $cookie  = array();
    public $res     = array();

    public function __construct(){
        $this->_init();
    }

//    //hook
    public function _init()
    {
        $this->get = \Seter\Seter::getInstance()->request->get;
        $this->post = \Seter\Seter::getInstance()->request->post;
        $this->cookie = \Seter\Seter::getInstance()->request->cookie;
    }

}
class Router
{
    public $router      = [];
    public $moduleslist = [];
    //返回route值
    public function __invoke(){
        $this->moduleslist = array_keys(C('modules'));              //模块列表索引
        $this->routerini()->routerpath()->routermcapathmix();
      // print_r($this->router);
//exit;
        return $this->router;
    }

    /**
     * 参数部分mix
     */
    public function routermcapathmix()
    {
        //mix 获得m c a params
        /**
        [Module] =>
        [Controller] =>
        [Action] =>
         */
        if(!in_array($this->router['_params']['__mm'],$this->moduleslist)) $this->router['_params']['__mm'] = '';
        if(!in_array($this->router['_params']['m'],$this->moduleslist))    $this->router['_params']['m'] = '';

        if(!$this->is_zm($this->router['_params']['c']))    $this->router['_params']['c'] = '';
        if(!$this->is_zm($this->router['_params']['__c']))  $this->router['_params']['__c'] = '';
        if(!$this->is_zm($this->router['_params']['__cc'])) $this->router['_params']['__cc'] = '';

        $this->router['Module']     = $this->router['_params']['m']?:$this->router['_params']['__mm'];
        $this->router['Controller'] = $this->router['_params']['c']?:$this->router['_params']['__c']?:$this->router['_params']['__cc'];
        $this->router['Action']     = $this->router['_params']['a']?:$this->router['_params']['__a']?:$this->router['_params']['__aa'];
        $this->router['Controller'] = $this->router['Controller']?:'';
        $this->router['Action']     = $this->router['Action']?:'';

        //mix参数
        unset($this->router['_params']['m']);
        unset($this->router['_params']['__mm']);
        unset($this->router['_params']['__cc']);
        unset($this->router['_params']['__aa']);
        unset($this->router['_params']['__c']);
        unset($this->router['_params']['__a']);
        unset($this->router['_params']['c']);
        unset($this->router['_params']['a']);

//        $this->router['params'] = array_merge($this->router['_params'], $this->router['_paramspath']);
        foreach($this->router['_params'] as $key=>$value){
            $this->router['params'][$key] = $value;
        }
        foreach($this->router['_paramspath'] as $key=>$value){
            $this->router['params'][$key] = $value;
        }
        unset($this->router['_params']);
        unset($this->router['_paramspath']);
        return $this;
    }


    public function routerpath()
    {
        //获取参数中的module
        //获取_pathmca] => [_paramsmca
        //标记path的中间变量
        $p = isset($this->router['_path'])?explode('/', $this->router['_path']):[];
        $m = current($p);
        if(in_array($m,$this->moduleslist)){      //模块
            array_shift($p);
            $this->router['_params']['__mm']     = $m;
            //$this->router['_pathm']['m']= $m;
        }
        $this->router['_params']['__cc'] = array_shift($p)?:'';
        $this->router['_params']['__aa'] = array_shift($p)?:'';

        $_params = [];
        $count = ceil(count($p) / 2);
        for($i=0;$i<$count;$i++){
            $ii = $i*2;
            $_params[$p[$ii]] = isset($p[$ii+1])?$p[$ii+1]:'';
        }
        $this->router['_paramspath'] = $_params;            //这个是path后面的参数

        return $this;
    }

    //原始解析获取到的数据
    public function routerini()
    {
        $query = $this->pathinfo_query();
        $query = strtolower($query);
        //这里包含两部分 path 和params
        $p = explode('&', $query);
        $router =  [
            'Module'        => '',
            'Controller'    => '',
            'Action'        => '',
            //'ActionPrefix'  => C('Default_Action_Prefix'),
        ];
        $params['m'] = $params['__mm'] = $params['__cc'] = $params['__aa'] = $params['__c'] = $params['__a'] = $params['a'] = $params['c'] = '';
        if(!isset(explode('=',  current($p))[1])){
            $router['_path'] = trim(array_shift($p),'/');
        }
        foreach($p as $value){
            $v = explode('=',$value);
            $params[strtolower($v[0])] = isset($v[1])?$v[1]:'';
        }
        //解码修正 mix 和 mca 修正
        if(isset($params['m'])){
            $ar = explode('.',$params['m']);
            //判断第一个是c还是m 值有两种形式 c.a 或者 m.c.a
            if(count($ar)==2){
                $params['m'] = '';
                $params['__c'] = array_shift($ar);
                $params['__a'] = array_shift($ar);
            }
            if(count($ar)==3){
                $params['m'] = array_shift($ar);
                $params['__c'] = array_shift($ar);
                $params['__a'] = array_shift($ar);
            }
        }
        $router['_params'] = $params;
        $this->router = $router;
        return $this;
    }

    //是否首字母开头
    public static function is_zm($str ='')
    {
        $str = substr( $str, 0, 1 );
        if (preg_match('/^[a-zA-Z]+$/',$str))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return string
     * 获取地址栏uri信息
     */
    public static function pathinfo_query( )
    {
        $pathinfo = @parse_url($_SERVER['REQUEST_URI']);
        if (empty($pathinfo)) {
            die('request parse error:' . $_SERVER['REQUEST_URI']);
        }
        //pathinfo模式下有?,那么$pathinfo['query']也是非空的，这个时候查询字符串是PATH_INFO和query
        $query_str = empty($pathinfo['query']) ? '' : $pathinfo['query'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '');
//    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['REDIRECT_PATH_INFO']) ? $_SERVER['REDIRECT_PATH_INFO'] : '');
        $pathinfo_query = empty($path_info) ? $query_str : $path_info . '&' . $query_str;
        if ($pathinfo_query) {
            $pathinfo_query = trim($pathinfo_query, '/&');
        }
        //urldecode 解码所有的参数名，解决get表单会编码参数名称的问题
        $pq = $_pq = array();
        $_pq = explode('&', $pathinfo_query);
        foreach ($_pq as $value) {
            $p = explode('=', $value);
            if (isset($p[0])) {
                $p[0] = urldecode($p[0]);
            }
            if(!empty($p[0]) || !empty($p[1]))  $pq[] = implode('=', $p);
        }
        $pathinfo_query = implode('&', $pq);
        return $pathinfo_query;
    }


}