<?php




class Sys {

    public function __get($str)
    {
        return self::$str();
//        echo  '收到一个函数请求';
//        //首先判断 通用函数中是否存在该函数，否则 运用_分割，加载不同的函数库，不存在则返回错误信息
    }

    /**
     * @param string $version
     * @return bool
     * php版本监测
     * 调用：is_php(5.3)
     */
    public static function is_php($version = '5.0.0') {
        $version = (string) $version;
        return (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
    }

    /**
     * @return string
     * 获取地址栏uri信息
     */
    public static function pathinfo_query()
    {
        $pathinfo = @parse_url($_SERVER['REQUEST_URI']);
        if (empty($pathinfo)) {
            die('request parse error:' . $_SERVER['REQUEST_URI']);
        }

        /**
         * http://192.168.1.200:70/?c=1&d=123&e=123&
         * http://192.168.1.200:70/asdf/asf?c=1&d=123&e=123
        Array
        (
        [path] => /index.php/asdf/asf
        [query] => c=1&d=123&e=123
        )
         */
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
            //$pq[] = implode('=', $p);
            if(!empty($p[0]) || !empty($p[1]))  $pq[] = implode('=', $p);
        }
        $pathinfo_query = implode('&', $pq);
        return $pathinfo_query;
    }


}














$sys = new Sys();
$m =  $sys->pathinfo_query;
print_r($m);

exit;


/**
 * @return array
 * 模块列表 from 配置文件
 */
function Modulelist()
{
    return [
        'm1',
        'm2',
        'm3',
        'm4'
    ];
}

//print_r(Modulelist());      //模块列表


//------------
$pathinfo_query         = pathinfo_query();
//like dec/&m=asdf&c=asdf&a=12

//$m = getparmstyle($pathinfo_query);             //ok 获得参数模式下的路由

//$m = getpathstyle($pathinfo_query);                 //路径模式


//$m = getmodule($pathinfo_query);
//$m = getclassmothed($pathinfo_query);


print_r($pathinfo_query);
exit;



/**
 * @param $pathinfo_query
 * 两种模式，
 * m 或 c 存在 则参数模式
 * mc 不存在， 则path模式   path模式下，可以用m c a 替换模式参数
 */

/**
 * @param $pathinfo_query
 * 参数模式
 */
function getpathstyle($pathinfo_query)
{
    $modulelist = Modulelist();

    $_module = current(explode('&', $pathinfo_query));
    $_module = explode('/', $_module);

    //判断第一个是否有效参数
    //检查m是否有效
    if(in_array( $_module[0],$modulelist)){
        //第一个是模块
        is_YZ($_module[1]) && $route['c'] = $_module[1];
        is_YZ($_module[2]) && $route['a'] = $_module[2];
    }{
        is_YZ($_module[1]) && $route['c'] = $_module[1];
        is_YZ($_module[2]) && $route['a'] = $_module[2];
    }

//    is_YZ($_module[0]) && $route['m'] = $_module[0];
//    is_YZ($_module[1]) && $route['c'] = $_module[1];
//    is_YZ($_module[2]) && $route['a'] = $_module[2];
//


    //这个模式下，多解析一个参数

    print_r($route);

//    if(in_array($_module,$modulelist)){
//        return $_module;       //找到，返回
//    }else{
//        return '';
//    }
}

/**
 * @param string $str
 * @return bool
 * 是否字母开头
 */
function is_zm($str ='')
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
 * @param $pathinfo_query
 * path模式
 * 模式?m=m.c.a&c=cc&a=aa
 */
function getparmstyle($pathinfo_query)
{
    $modulelist = Modulelist();
    $list = explode('&', $pathinfo_query);
    if(!empty($list)){
        foreach($list as $value){
            $p = explode('=', $value);
            if(!empty($p[0]) && !empty($p[1]) ){
                $_pq[$p[0]] = $p[1];
            }
        }
    }
    //原始参数 获得 mca =>$_pq

    //过滤掉不是字母开头的数据
    foreach($_pq as $key=>$value){
        if(!is_YZ($value))unset($_pq[$key]);
    }

    //$m中分析mca
    $__m = explode('.', $_pq['m']);
    is_YZ($__m[0]) && $_m['m'] = $__m[0];
    is_YZ($__m[1]) && $_m['c'] = $__m[1];
    is_YZ($__m[2]) && $_m['a'] = $__m[2];

    //数据复写
    $route['m'] = !empty($_m['m'])?$_m['m']:$_pq['m'];
    $route['c'] = !empty($_m['c'])?$_m['c']:$_pq['c'];
    $route['a'] = !empty($_m['a'])?$_m['a']:$_pq['a'];

    //检查m是否有效
    if(!in_array( $route['m'],$modulelist)){
        unset($route['m']);
    }
    return $route;
}

/**
 * @param $pathinfo_query
 * 获取有效模块
 * 模块，先监测参数m 然后在检查地址栏路径，没有返回空
 */
function getmodule($pathinfo_query){
    $modulelist = Modulelist();

    $list = explode('&', $pathinfo_query);
    if(!empty($list)){
        foreach($list as $value){
            $p = explode('=', $value);
            if(!empty($p[0]) && !empty($p[1]) ){
                $_pq[$p[0]] = $p[1];
            }
        }
    }

    //对m进行分析，其中有没有点
    $_module = current(explode('.', $_pq['m']));
    if(in_array($_module,$modulelist)){
        return $_module;       //找到，返回
    }

    //上面参数模式没有通过，监测路径中是否有模块信息
    $_module = current(explode('&', $pathinfo_query));
    $_module = current(explode('/', $_module));

    if(in_array($_module,$modulelist)){
        return $_module;       //找到，返回
    }else{
        return '';
    }
}


//$_module = current(explode('&', $pathinfo_query));
//$_module = current(explode('/', $_module));
//$_system = systemInfo();
//if (isset($_system['hmvc_modules'][$_module])) {
//    return $_module;
//} else {
//    return '';
//}

//print_r(pathinfo_query_parameters(pathinfo_query()));




exit;



//function getmca($pathinfo_query_arr = array())
//{
//    $res = array();
//    !empty($pathinfo_query_arr['m']) && $res['m'] = is_YZ($pathinfo_query_arr['m'])?$pathinfo_query_arr['m']:'';
//    !empty($pathinfo_query_arr['c']) && $res['c'] = is_YZ($pathinfo_query_arr['c'])?$pathinfo_query_arr['c']:'';
//    !empty($pathinfo_query_arr['a']) && $res['a'] = is_YZ($pathinfo_query_arr['a'])?$pathinfo_query_arr['a']:'';
//    return $res;
//}


///**
// * 从info_query中获取mca信息
// */
//function getr($pathinfo_query_arr = array())
//{
//    $res = array();
//    if(!empty($pathinfo_query_arr['r'])){
//        $arr = explode('.', $pathinfo_query_arr['r']);
//        !empty($arr[0]) && $res['m'] = is_YZ($arr[0])?$arr[0]:'';
//        !empty($arr[1]) && $res['c'] = is_YZ($arr[1])?$arr[1]:'';
//        !empty($arr[2]) && $res['a'] = is_YZ($arr[2])?$arr[2]:'';
//    }
//    return $res;
//}








//解析后

//    [module] =>
//    [query] =>
//    [mpath] => welcome.index
//    [m] => index
//    [c] => welcome
//    [prefix] => do
//    [cpath] => welcome
//    [folder] =>
//    [file] => E:\www\Faster_Center\Disk/application/controllers\welcome.php
//    [class] => Welcome
//    [method] => doIndex
//    [parameters] => Array
//(
//)







