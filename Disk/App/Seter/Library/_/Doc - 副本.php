<?php

namespace Seter\Library;

/*
 *
*
//$list = $this->Seter->doc->indexlist();               //booklist
//$list = $this->Seter->doc->wzlist('s3');              //book中的文章list
// $nr = $this->Seter->doc->getnr('s3','Seter');        //内容
//$nr = $this->Seter->doc->getnr('s34','Seter','36');   //内容
//print_r($nr);
////写文件
//$nr = 123;
//$this->Seter->doc->save('s34','Seter',$nr);
//$this->Seter->doc->deletebook('s34');               //删除一个book
//$this->Seter->doc->deletebookwz('s3','Seter','36'); //删除book 下的一个文件
//$this->Seter->doc->deletebookwz('s3','Seter');      //删除book 下的一个文件
 * */



class Doc
{
    public $base    = '';
    public $dtype   = 'Doc';
    public $indexlist  = array();
    public $wz      = array();



    public function __construct()
    {
        $this->S = \Seter\Seter::getInstance();
        $this->base = SHAM_PATH . '\Document';
        $this->indexlist = $this->getindexlist_();       //获得所有的book
    }

    public function set($chr ='')
    {
        $arr = array('Doc','Flo','Seq');
        if(in_array($chr,$arr)){
            $this->dtype = $chr;
        }else{
            $this->dtype = 'Doc';
        }
        return $this;
    }

    public function getindexlist()
    {
        return $this->indexlist;
    }


    //获取book中文章列表
    public function wzlist($path='')
    {
        $list = $this->indexlist;
        if(empty($list) || empty($path)) return array();
        if(!in_array($path,$list)){
            return array();
        }
        //=========================================================
        $PA = $this->base.'\Doc_'.$path;
        $dirHandle = @opendir($PA) or die("打开目录不成功");
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

    //文章内容
    public function getnr($path,$wzchr,$ver='')
    {
        if(empty($path) || empty($wzchr)) return array();

        //如果ver空，则最新版本，否则
        $wzlist = $this->wzlist($path);
        $wz = $wzlist[$wzchr];
        empty($ver) &&  !empty($wz)     &&  $ver = max($wz);
        if(!empty($wz)){
            if(!in_array($ver,$wz)) $ver = max($wz);
        }
        empty($ver) && $ver = 1;
        //获得文件名
        $filename       = $this->base.'\Doc_'.$path.'\Doc_'.$wzchr."_$ver.ME";   //文档
        $filenamesx     = $this->base.'\Doc_'.$path.'\Flo_'.$wzchr."_$ver.ME";   //流程
        $filenamelc     = $this->base.'\Doc_'.$path.'\Seq_'.$wzchr."_$ver.ME";   //时序
        //获取内容
        $nr = \Sham::Fr($filename);
        $nrsx = \Sham::Fr($filenamesx);
        $nrlc = \Sham::Fr($filenamelc);
//echo $filenamesx;
        $wznr['path'] = $path;
        $wznr['wzchr']= $wzchr;
        $wznr['ver']  = $ver;
        //===============================================
        //读取
        $wznr['nr']   = $nr;
        //获取流程
        $wznr['sx']   = $nrsx;
        //获取时序
        $wznr['lc']   = $nrlc;
        return $wznr;
    }

    //首先删除所有文件，然后删除目录
    public function deletebook($path='')
    {
        $filepath = $this->base.'\Doc_'.$path;
        is_dir($filepath) && \Sham::delDirAndFile($filepath);
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

    //保存内容
    public function save($path='',$wzchr='',$nr='')
    {
        $path   = trim($path);
        $wzchr  = trim($wzchr);
        if(empty($path) || empty($wzchr) || EMPTY($nr)) return array();

        //目录露监测
        $filepath = $this->base.'\Doc_'.$path;
        !is_dir($filepath) && mkdir($filepath);

        //计算版本id
        $list = $this->wzlist($path);
        $verlist = $list[$wzchr];                           //获取到了所有的版本


//        if(!empty($ver)){
//
//        }

if($this->dtype == 'Doc'){
    $nver = !empty($verlist)?max($verlist)+1:1;         //获取到了新的版本id
}else{
    //流程图和时序图
    $nver = max($verlist);         //获取到了新的版本id
}

        //获得文件名
       // $filename  =$this->base.'\\'.$this->dtype.'_'.$path.'\\'.$this->dtype.'_'.$wzchr."_$nver.ME";
        $filename  =$this->base.'\Doc_'.$path.'\\'.$this->dtype.'_'.$wzchr."_$nver.ME";

        //写文件
        \Sham::Fs($filename,$nr);
        return true;
    }

    /*
     * 获得所有的book
     * */
    public function getindexlist_()
    {

        $dirHandle = opendir($this->base) or die("打开目录不成功");
        $list = array();
        while (($pname = readdir($dirHandle)) !== false) {
            if ($pname != '.' && $pname != '..') {
                $file = $this->base . '\\' . $pname;
                if (is_dir($file)) {
                    $len = strlen($pname);
                    if ($len > 5) {
                        if (substr($pname, 0, 4) == 'Doc_') {
                            array_push($list, substr($pname, 4));
                        }
                    }
                }
            }
        }
        return $list;
    }















}

