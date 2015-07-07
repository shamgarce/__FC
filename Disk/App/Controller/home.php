<?php


class home extends Controller {
    public function doIndex(){

//        $this->S->user()->login();
//        $this->S->user()->logout();
//        $this->S->user()->regsign();
//        $this->S->user()->islogin();

//$this->S->user->login('irones','irones');
//echo $this->S->user->logout();
//echo $this->user->isguest();
//echo $this->S->user->islogin();
//print_r($this->S->jsonarr);
exit;

       D(C('G'));
//exit;
//

        //模型
        //模型是一个工厂类


        $ms = new Ms();
exit;


        //ok的
        //db
        //table

        D(C('G'));
        $rc = $this->S->db->getMap('select * from dy_user');
        $this->table->dy_user->where('a=1')->test();
        //echo $this->db->version();
        print_r($rc);
        //var_dump($this->db->query());
        exit;

        $data = [
            'te' => 'te1',
            'title'=>'测试页',
        ];
        echo 'mark';
        $this->display('example',$data);       //默认的index.php
    }



}
