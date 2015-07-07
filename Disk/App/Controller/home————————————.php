<?php


class home extends Controller {
    public function doIndex(){

        $data = [
            'te' => 'te1',
            'title'=>'测试页',
        ];
        $this->display('',$data);       //默认的index.php
    }
    public function doGh($ar = array())
    {
        $this->display();       //默认的index.php
    }
    public function doRej($ar = array())
    {
        $this->test->hello();
    }
    public function doParams($ar = array()){
        D($ar);
    }
    public function doUrl(){
        echo 'm=home.url';
        echo '<br>';
        echo '/home/url';
        echo '<br>';
        echo 'url测试成功';
    }

    public function doRedirect(){
        $this->redirect('http://www.baidu.com'); //302跳转到百度
    }

    public function doAjax(){
        $ret = array(
            'result' => true,
            'data'   => 123,
        );
        $this->AjaxReturn($ret);                //将$ret格式化为json字符串后输出到浏览器
    }

    public function doAutoLoad(){
        $t = new Test();
        echo $t->hello();
    }

    public function doWidget(){
        $this->display();
    }

    public function doFetch(){
        $html = $this->fetch('index',['title'=>'ob输出']);
       echo $html;
    }
    public function doRouter()
    {
        D($this->router);
        D($this->env);

        D(C('G'));

    }

    public function doLog(){
        Log::fatal('something');
        Log::warn('something');
        Log::notice('something');
        Log::debug('something');
        Log::sql('something');
        echo '请到Log文件夹查看效果。如果是SAE环境，可以在日志中心的DEBUG日志查看。';
    }

}
