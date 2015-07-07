<?php

namespace Seter\Core;

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
     * @param string $str
     * @return bool
     * 是否字母开头
     */
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
            if(!empty($p[0]) || !empty($p[1]))  $pq[] = implode('=', $p);
        }
        $pathinfo_query = implode('&', $pq);
        return $pathinfo_query;
    }


}