<?php
class TestWidget extends Widget {
    public function invoke($arr){
        $data['res'] =  $arr;
        $this->display('',$data);
    }
}
