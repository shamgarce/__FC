<?php
/**
调用
 * \Sham::T()
 */

class Sham
{

    public static function timetodate($time = 0,$bo = true)
    {
        if(empty($time)){
            return '';
        }else{
            $time = intval($time);
            if($bo){
                return date('y-m-d H:i:s',$time);
            }else{
                return date('y-m-d',$time);
            }
        }
    }
    /*
     * 签名计算
     * */
    public static function signnature($str)
    {
        return MD5(MD5(MD5(MD5($str))));
    }
//
    //setcookie
    //设置cookie
    //7*24*60*60 = 604800
    public static function setcookie($cookiename,$cookievalue='',$cookietime=604800)
    {
        $cookiename = "Seter_$cookiename";
        $tm = time()+$cookietime;
        setcookie($cookiename,$cookievalue,$tm);
        return true;
    }
//
//    //getcookie
//    //获取cookie
    public static function getcookie($cookiename)
    {
        $cookiename = "Seter_$cookiename";
        return $_COOKIE[$cookiename];
    }


    //记录当前的信息
    public static function trace($info='')
    {
        array_push(\Seter\Seter::$trace,$info);
    }

    public static function gettrace()
    {
        return \Seter\Seter::$trace;
    }

    public static function R($url, $time=0, $msg='') {
        $url = str_replace(array("\n", "\r"), '', $url);
        if (empty($msg))
            $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
        if (!headers_sent()) {
            // redirect
            if (0 === $time) {
                header('Location: ' . $url);
            } else {
                header("refresh:{$time};url={$url}");
                echo($msg);
            }
            exit();
        } else {
            $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0)
                $str .= $msg;
            exit($str);
        }
    }

    //----------------------------------------------------------
    //函数群 public static
    /*
    +----------------------------------------------------------
    * 获得时间戳
    +----------------------------------------------------------
    * 参数:无
    +----------------------------------------------------------
    */
    public static function T(){
        list($usec, $sec) = explode(" ",microtime());
        $num = ((float)$usec + (float)$sec);
        return $num;
    }

    public static function U($str){
        if (empty($str)) return array();
        $arr = unserialize($str);
        $arr = !empty($arr)?$arr:array();
        return $arr;
    }

    /*
    +----------------------------------------------------------
    * 字符转化为数组
    +----------------------------------------------------------
    * 参数:$str 需要转化的字符串 $flit 是否排重 $bl 分割字符
    +----------------------------------------------------------
    */
    public static function getarr($str,$flit='0',$bl = "\r\n"){
        $arr = array();
        if(empty($str)) return $arr;
        //+----------------------------------------------------------
        $arr_ = explode($bl,$str);
        if($flit) $arr_ = array_unique($arr_);
        foreach($arr_ as $key=>$value){
            if(!empty($value)) $arr[] = trim($value);
        }
        return $arr;
    }

    /*
    +----------------------------------------------------------
    * 数组转化为数组
    +----------------------------------------------------------
    * 参数:$arr 需要转化的数组 $flit 是否排重 $bl 分割字符
    +----------------------------------------------------------
    */
    public static function getstr($arr,$flit='0',$bl = "\r\n"){
        if(empty($arr)) return '';
        //+----------------------------------------------------------
        foreach($arr as $key=>$value){
            if(!empty($value)) $arr_[] = trim($value);
        }
        if(!empty($arr_)){
            if($flit) $arr_ = array_unique($arr_);
            $str = implode($bl,$arr_);
        }else{
            $str = '';
        }
        return $str;
    }

    /**
    +----------------------------------------------------------
     * // 保存文件
    +----------------------------------------------------------
     * 参数:filename 路径文件名 / text:内容
    +----------------------------------------------------------
     */
    public static function Fs($fileName, $text) {
        if( ! $fileName ) return false;
        if( $fp = @fopen( $fileName, "wb" ) ) {
            if( @fwrite( $fp, $text ) ) {
                fclose($fp);
                return true;
            }else {
                fclose($fp);
                return false;
            }
        }
        return false;
    }

    /**
    +----------------------------------------------------------
     * // 读取文件
    +----------------------------------------------------------
     * 参数:filename 路径文件名
    +----------------------------------------------------------
     */
    public static function Fr($filename){
        if( is_file( $filename ) ){
            $cn = file_get_contents( $filename );
            return $cn;
        }
    }

    /**
    +----------------------------------------------------------
     * // 魔术转义
    +----------------------------------------------------------
     * 参数:string 需要转义的内容   反函数 stripslashes
    +----------------------------------------------------------
     */
    public static function saddslashes($string) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = \Sham::saddslashes($val);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

    /**
    +----------------------------------------------------------
     * // html实体转义
    +----------------------------------------------------------
     * 参数:string 需要转义的内容   反函数 htmldecode
    +----------------------------------------------------------
     */
    public static function shtmlspecialchars($string) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = \Sham::shtmlspecialchars($val);
            }
        } else {
            $string = htmlspecialchars($string);
        }
        return $string;
    }

    /**
     * 剪切字符串
     * @param $startstr
     * @param $endstr
     * @param $str
     * @return string
     */
    public static function cut($startstr="",$endstr="",$str){
        if(empty($startstr) || empty($endstr))return '';
        $outstr="";
        if(!empty($str) && strpos($str,$startstr)!==false && strpos($str,$endstr)!==false){
            $startpos	= strpos($str,$startstr);
            $str		= substr($str,($startpos+strlen($startstr)),strlen($str));
            $endpos		= strpos($str,$endstr);
            $outstr		= substr($str,0,$endpos);
        }
        return trim($outstr);
    }

    /**
     * 判断字符串是否存在
     * @param type $haystack    字符
     * @param type $needle      字符组
     * @return bool
     */
    public static function strexists($haystack, $needle) {
        return !(strpos($haystack, $needle) === FALSE);
    }

    /**
    * 对象转成数组
     * @param type $obj 一个对象
     * @return array
    */
    public static function ob2ar($obj) {
        if(is_object($obj)) {
            $obj = (array)$obj;
            $obj = self::ob2ar($obj);
        } elseif(is_array($obj)) {
            foreach($obj as $key => $value) {
                $obj[$key] = self::ob2ar($value);
            }
        }
        return $obj;
    }

    /**
     * 对资源进行编码 /\的变换
     * @param type $url 资源地址
     * @return mix
     */
    public static function uri( $uri = '')
    {
        if(empty($uri))return '';
        return str_replace('/', DIRECTORY_SEPARATOR, $uri);
    }

    /**
     * 获取IP地址
     * @param type null
     * @return string like '12.70.0.1'
     */
    public static function GetIP(){
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif(!empty($_SERVER["REMOTE_ADDR"])){
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else{
            $cip = "无法获取！";
        }
        return $cip;
    }

    /**
     * 删除目录和目录里面的所有文件
     * @param type $dirName 目录名
     * @return type
     */
    function DeleteDirandfile( $dirName ='.' )
    {
        if ( $handle = opendir( "$dirName" ) ) {
            while ( false !== ( $item = readdir( $handle ) ) ) {
                if ( $item != "." && $item != ".." ) {
                    if ( is_dir( "$dirName/$item" ) ) {
                        \Sham::DeleteDirandfile( "$dirName/$item" );
                    } else {
                        unlink( "$dirName/$item" );
                        //if( unlink( "$dirName/$item" ) )    echo "成功删除文件： $dirName/$item<br />\n";
                    }
                }
            }
            closedir( $handle );
            rmdir( $dirName ) ;
            //if( rmdir( $dirName ) ) echo "成功删除目录： $dirName<br />\n";
        }
    }

}



