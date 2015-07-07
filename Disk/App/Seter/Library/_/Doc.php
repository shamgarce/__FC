<?php

namespace Seter\Library;

/*
//========================================================
////$list = $this->Seter->doc->getbooklist();                 //获取booklist
////$list = $this->Seter->doc->book('s2')->getnodelist();     //根据book获取node list
////$list = $this->Seter->doc->book('s2')->node('3')->getver();             //获取版本号
//$list = $this->Seter->doc->book('s2')->node('3')->ver(1)->getnode();        //获取某个节点的内容
//$nr = '';
//$ar = array(
//    'nr'=>$nr,
//    'lc'=>$nr,
//    'sx'=>$nr,
//);
//$list = $this->Seter->doc->book('s2')->node('3')->put($ar);

//$this->Seter->doc->deletebook('s34');               //删除一个book
//$this->Seter->doc->deletebookwz('s3','Seter','36'); //删除book 下的一个文件
//$this->Seter->doc->deletebookwz('s3','Seter');      //删除book 下的一个文件
 * */
class Doc
{
    public $base = '';              //默认的根目录
    public $book = 'default';
    public $node = 'default';
    public $ver = 0;
    public $booklist = array();


    public function __construct()
    {
        $this->S = \Seter\Seter::getInstance();
        $this->base = SHAM_PATH . '\Document';
        $this->booklist = $this->getbooklist_();            //获取book列表
    }

        public function getver()
    {
        if($this->ver == 0){
            $nodelist = $this->getnodelist();
            $vergroup = $nodelist[$this->node];
            if(empty($vergroup)){
                $this->ver = 1;
            }else{
                $this->ver = max($vergroup);
            }
        }
        //getver
        return $this->ver;
    }

    public function getputver()
    {
        //根据book node 计算出ver
        $nodelist = $this->getnodelist();
        $vergroup = $nodelist[$this->node];
        if(empty($vergroup)){
            return 1;
        }else{
            return max($vergroup)+1;
        }
    }

    public function put($ar = array())
    {
        $bookpath = $this->base.'\Doc_'.$this->book;
        $newver = $this->getputver();
        //根据book node ver 获取node内容
        $filename       = $bookpath.'\Doc_'.$this->node."_$newver.ME";   //文档
        $filenamesx     = $bookpath.'\Seq_'.$this->node."_$newver.ME";   //流程
        $filenamelc     = $bookpath.'\Flo_'.$this->node."_$newver.ME";   //时序

        !empty($ar['nr']) && \Sham::Fs($filename,$ar['nr']);
        !empty($ar['sx']) && \Sham::Fs($filenamesx,$ar['sx']);
        !empty($ar['lc']) && \Sham::Fs($filenamelc,$ar['lc']);
        return true;
    }

    public function getnode()
    {
        $bookpath = $this->base.'\Doc_'.$this->book;
        $this->ver = $this->getver();               //版本号修正


        //根据book node ver 获取node内容
        $filename       = $bookpath.'\Doc_'.$this->node."_$this->ver.ME";   //文档
        $filenamesx     = $bookpath.'\Flo_'.$this->node."_$this->ver.ME";   //流程
        $filenamelc     = $bookpath.'\Seq_'.$this->node."_$this->ver.ME";   //时序
        //获取内容
        $nr = \Sham::Fr($filename);
        $nrsx = \Sham::Fr($filenamesx);
        $nrlc = \Sham::Fr($filenamelc);
//echo $filenamesx;
        $wznr['book'] = $this->book;
        $wznr['node'] = $this->node;
        $wznr['ver']  = $this->ver;
        //===============================================
        //读取
        $wznr['nr']   = $nr;
        //获取流程
        $wznr['sx']   = $nrsx;
        //获取时序
        $wznr['lc']   = $nrlc;
        return $wznr;
    }

    public function getnodelist()
    {
        $path = $this->base.'\Doc_'.$this->book;
        !is_dir($path) && mkdir($path);
        $dirHandle = @opendir($path);
        $list = array();
        while (($pname = readdir($dirHandle)) !== false) {
            if ($pname != '.' && $pname != '..') {
                if(substr($pname,0,4) == 'Doc_'){
                    $pname = substr($pname,4,-3);
                    $pn = \Sham::getarr($pname,0,'_');
                    $list[$pn[0]][] = $pn[1];
                }

            }
        }
        foreach($list as $key=>$value){
            $ar = $value;
            rsort($ar);
            $list[$key] =  $ar;
        }
        return $list;
    }

    public function getbooklist()
    {
        return $this->booklist;
    }

    public function getbooklist_()
    {
        $dirHandle = @opendir($this->base) or die("打开目录不成功");
        $list = array();
        while (($pname = readdir($dirHandle)) !== false) {
            if ($pname != '.' && $pname != '..') {
                $file = $this->base . '\\' . $pname;
                if (is_dir($file)) {
                    $len = strlen($pname);
                    if ($len > 4) {
                        if (substr($pname, 0, 4) == 'Doc_') {
                            array_push($list, substr($pname, 4));
                        }
                    }
                }
            }
        }
        return $list;
    }


    public function book($book = 'default')
    {
        $this->book = trim($book);
        //不存在创建
        $path = $this->base.'\Doc_'.$book;
        !is_dir($path) && mkdir($path);
        return $this;
    }

    public function node($node = 'default')
    {
        $this->node = trim($node);
        return $this;
    }

    public function ver($ver = '1')
    {
        $this->ver = intval($ver);
        return $this;
    }

    public function refresh(){

        $bookpath = $this->base.'\Doc_'.$this->book;
        $this->ver = $this->getver();               //版本号修正

        //值留下最新的，其他删除
        $mlist =   $this->getnodelist();
        $vlist = $mlist[$this->node];
        //ok 最大的留下，其他全部删掉
        if(!empty($vlist)){
            foreach($vlist as $key=>$value){
                if($value != $this->ver){
                    $filename       = $bookpath.'\Doc_'.$this->node."_$value.ME";   //文档
                    $filenamesx     = $bookpath.'\Flo_'.$this->node."_$value.ME";   //流程
                    $filenamelc     = $bookpath.'\Seq_'.$this->node."_$value.ME";   //时序
                    @unlink($filename);
                    @unlink($filenamesx);
                    @unlink($filenamelc);
                }
            }
        }
    }

//    //首先删除所有文件，然后删除目录
    public function deletebook($path='')
    {
//        $filepath = $this->base.'\Doc_'.$path;
//        is_dir($filepath) && \Sham::delDirAndFile($filepath);
        return true;
    }


//    public function deletebookwz($path='',$wzchr='',$ver='')
//    {
//        if(empty($path) || empty($wzchr)) return array();
//        $wzlist = $this->wzlist($path);
//
//        $verlist = $wzlist[$wzchr];
//        if(empty($ver)){
//            //删除所有文件
//            if(!empty($verlist)){
//                foreach($verlist as $key=>$value){
//                    $filename  =$this->base.'\\'.'Doc'.'_'.$path.'\\'.$this->dtype.'_'.$wzchr."_$value.ME";
//                    @unlink($filename);
//                }
//            }
//        }else{
//            //删除本文件
//            $filename  =$this->base.'\\'.'Doc'.'_'.$path.'\\'.$this->dtype.'_'.$wzchr."_$ver.ME";
//            @unlink($filename);
//        }
//        return true;
//    }





}


