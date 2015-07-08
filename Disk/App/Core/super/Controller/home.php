<?php


class home extends BaseController {
    public function doIndex(){
        $data = [
            'te' => 'te1',
            'title'=>'测试页',
        ];
        $this->display('',$data);       //默认的index.php
    }

    public function doSrequest($ar = array())
    {
        echo '< get >';
        $get = $this->request->get;
        D($get);
        echo 'post';
        $post = $this->request->post;
        D($post);
        echo 'cookie';
        $cookie = $this->request->cookie;
        D($cookie);
        exit;
    }

    public function doS($ar = array())
    {
        switch($ar){
            case 'model':
                $this->model->modeltest->go();
                $this->model->modeltest2->go();
                exit;
            break;
            case 'db':
                echo "
                <br>getone
                <br>getrow
                <br>getcol
                <br>getmap
                <br>getall
                <br>sutoExecute<br>
                ";
//                $this->db->autoExecute();
//                $this->db->query();
                $rc = $this->db->getone("select * from dy_user");
                D($rc);
                $rc = $this->db->getrow("select * from dy_user");
                D($rc);
                $rc = $this->db->getcol("select * from dy_user");
                D($rc);
                $rc = $this->db->getmap("select * from dy_user");
                D($rc);
                $rc = $this->db->getall("select * from dy_user");
                D($rc);
                exit;
                break;
            case 'table':
                echo '$rc = $this->table->dy_user->order("uid desc")->colmn()->group()->where()->order()->limit(0,2)->getall();';
                $rc = $this->table->dy_user->order("uid desc")->colm()->group()->where()->order()->limit(0,2)->getall();
                D($rc);
                exit;
                break;
            case 'user':
                echo '<pre>';
                echo 'USER :
                ->regsign("username",$usinfo)   //注册新用户
                ->ver("user","password")        //验证用户是否正确
                ->delete("username")            //删除用户
                ->changepwassword("username","newpassword") //更改密码
                ->logout()    登出
                ->info()      获取用户信息
                ->groupinfo()
                ->isguest()
                ->isadmin()
                ->viewtable()
                ';
                exit;
                break;
        }


        $this->display();       //默认的index.php
    }

    /**
     * @param array $ar
     * 后面功能规划
     */
    public function doGh($ar = array())
    {
        $this->display();       //默认的index.php
    }

    /**
     * @param array $ar
     * 依赖注入
     */
    public function doRej($ar = array())
    {
        $this->user->test();
    }

    /**
     * @param array $ar
     * 简明参数
     */
    public function doParams($ar = array()){
        D($ar);
    }

    /**
     * mca指向
     */
    public function doUrl(){
        echo 'm=home.url';
        echo 'url mca指向测试成功';
    }

    /**
     * 重定向
     */
    public function doRedirect(){
        $this->redirect('http://www.baidu.com'); //302跳转到百度
    }

    /**
     * ajax输出
     */
    public function doAjax(){
        $ret = array(
            'result' => true,
            'data'   => 123,
        );
        $this->AjaxReturn($ret);                //将$ret格式化为json字符串后输出到浏览器
    }

    /**
     * 类自动载入
     */
    public function doAutoLoad(){
        $t = new Test();
        echo $t->hello();
    }

    /**
     * 页面上调用widget
     */
    public function doWidget(){
        $this->display();
    }

    /**
     * fetch页面
     */
    public function doFetch(){
        $html = $this->fetch('index',['title'=>'ob输出']);
       echo $html;
    }

    /**
     * 路由和环境变量
     */
    public function doRouter()
    {
        echo '<params>';
        D($this->params);
        echo '<$this->router>';
        D($this->router);
        echo '<$this->env>';
        D($this->env);
        echo '<config>';
        D(C('G'));

    }

    /**
     * 日志演示
     */
    public function doLog(){
        Log::fatal('something');
        Log::warn('something');
        Log::notice('something');
        Log::debug('something');
        Log::sql('something');
        echo '请到Log文件夹查看效果。如果是SAE环境，可以在日志中心的DEBUG日志查看。';
    }

}
