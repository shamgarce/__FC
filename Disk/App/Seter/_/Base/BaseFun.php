<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart <info@slimframework.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     2.4.2
 * @package     Slim
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Seter\Base;


class Base
{

    //--------------------------------------------------------------------------------
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
        //================================================
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
        //================================================
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
                $string[$key] = saddslashes($val);
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
                $string[$key] = shtmlspecialchars($val);
            }
        } else {
            $string = htmlspecialchars(strip_sql($string), ENT_QUOTES);
        }
        return $string;
    }

    /**
    +----------------------------------------------------------
     * // 内容截取
    +----------------------------------------------------------
     * 参数
    +----------------------------------------------------------
     */
    public static function cut($startstr="",$endstr="",$str){
        if(empty($startstr) || empty($endstr))return false;
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
    +----------------------------------------------------------
     * //判断字符串是否存在
    +----------------------------------------------------------
     */
    public static function strexists($haystack, $needle) {
        return !(strpos($haystack, $needle) === FALSE);
    }
    //--------------------------------------------------------------------------------

    //对象转成数组
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

    public static function uri($uri)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $uri);
    }

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



}
