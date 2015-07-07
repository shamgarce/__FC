<?php

namespace Seter\Core;

/**
 * 1 固定的
 * $this->error->E404();
 * $this->error->E500('发生一个错误');
 * $this->error->J404();
 * $this->error->J500('发生一个错误');
json自定义输出
 * $this->error->J(200,'ok',array('u'=>1));
error自定义输出
 * $this->error->E('内容');
 *
 * 等待扩展，开发环境输出更多的信息等待分析例如 router get post 等等 执行时间，数据读取 等等
 */

class Error{
    public function __construct(){
    }
    public static function E($body='')
    {
        echo static::generateTemplateMarkup('错误', "<p>$body</a>");
        exit;
    }
    public static function J($code,$msg='',$data=array())
    {
        static::jout($code,$msg,$data);
        exit;
    }

    public static function E404()
    {
        static::defaultNotFound();
        exit;
    }

    public static function E500($str = '')
    {
        static::defaultError($str);
        exit;
    }

    public static function J404()
    {
        static::jout(404);
        exit;
    }

    public static function J500($str = '')
    {
        static::jout(500,$str);
        exit;
    }

    protected static function jout($code,$msg='',$data=array())
    {
        $dt = array(
            'code'  => $code,
            'msg'   => $msg,
            'data'   => $data
        );
        if(empty($msg))     unset($dt['msg']);
        if(empty($data))    unset($dt['data']);
        $res = json_encode($dt);
        echo $res;
    }

    /**
     * Generate diagnostic template markup
     *
     * This method accepts a title and body content to generate an HTML document layout.
     *
     * @param  string   $title  The title of the HTML template
     * @param  string   $body   The body content of the HTML template
     * @return string
     */
    protected static function generateTemplateMarkup($title, $body)
    {
        return sprintf("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><title>%s</title><style>body{margin:0;padding:30px;font:16px/1.5 Helvetica,Arial,Verdana,sans-serif;font-family:\"微软雅黑,宋体\"}h1{margin:0;font-size:36px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>", $title, $title, $body);
    }



    /**
     * Default Not Found handler
     */
    protected static function defaultNotFound()
    {
        echo static::generateTemplateMarkup('404 Page Not Found', '<p>The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly. If all else fails, you can visit our home page at the link below.</p><a href="' . '$this->request->getRootUri()' . '/">Visit the Home Page</a>');
    }

    /**
     * Default Error handler
     */
    protected static function defaultError($e)
    {
        //$this->getLog()->error($e);
        echo self::generateTemplateMarkup('Error 500 : '.$e, '<p>A website error has occurred. The website administrator has been notified of the issue. Sorry for the temporary inconvenience.</p>');
    }

}