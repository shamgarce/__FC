<?php
/*
//        $ms = $this->Seter->table->dy_user->where()->order('od desc')->limit(0,9)->getall();
//        $ms = $this->Seter->table->dy_user->where()->order('od desc')->limit(0,9)->getall();
$ms = $this->Seter->table->dy_user->getall();
print_r($ms);

//$ms = $this->Seter->table->dy_user->colm('adfs')->where()->order()->limit()->getall();
*/


namespace Seter\Library;

class TableObj
{
    private $tablename = '';
    private $where = array();
    private $limit = '';
    private $order = '';
    private $colm = '*';
    private $group = '';

    private $sqltype = '';          //结果集类型
    private $sql = '';              //结果集的sql
    private $_sql = '';             //上个结果集的sql语句

    private $db = null;
    private $result = null;

    public function __construct($tablename = '')
    {
        $this->tablename = trim($tablename);
        $this->db = \Seter\Seter::getInstance()->db;
        //获取所有的表明
        $sql = "select table_name
                from information_schema.tables
                where table_schema='{$this->db->Config['database']}' and table_type='base table'
                ";
        $colar = $this->db->getcol($sql);
        if(!in_array($tablename,$colar)) die('error tablename;');
    }

    public function colm($chr = '*')
    {
        $this->colm = $chr;
        return $this;
    }

    //
    public function where($where = '1')
    {
        $this->where[] = $where;
        return $this;
    }

    public function order($order = '')
    {
        $this->order = $order;
        return $this;
    }

    public function group($group = '')
    {
        $this->group = $group;
        return $this;
    }


    public function limit($bnum, $num = 0)
    {
        if (empty($num)) {
            $this->limit = $bnum;
        } else {
            $this->limit = "$bnum,$num";
        }
        return $this;
    }

    public function getinsertid()
    {
        return $this->db->insert_id();
    }

    public function delete()
    {
        if(!empty($this->where)){
            $wheres = \Sham::getstr($this->where,0,' and ');
            $wheres = " $wheres";
        }ELSE{
            DIE('where missing');
        }
        $sql = "delete from {$this->tablename} WHERE $wheres ";
        $this->db->query($sql);
        return true;
    }

    public function insert($res=array())
    {
        $res = \Sham::saddslashes($res);
        $this->db->autoExecute($this->tablename,$res,'INSERT');
        return $this->db->insert_id();
    }

    public function update($res)
    {
        if(!empty($this->where)){
            $wheres = \Sham::getstr($this->where,0,' and ');
        }ELSE{
            DIE('where missing');
        }
        $this->db->autoExecute($this->tablename,$res,'UPDATE',$wheres);
        return true;
    }


    public function getone()
    {
        //读取
        $this->result = $this->buildsql();
        if($this->sql != $this->_sql){
            $this->result   = $this->db->getone($this->sql);
            $this->_sql     = $this->sql;
        }
        $this->sqltype     = 'getone';
        return $this->result;
    }

    public function getmap()
    {
        //读取
        $this->result = $this->buildsql();
        if($this->sql != $this->_sql){
            $this->result   = $this->db->getmap($this->sql);
            $this->_sql     = $this->sql;
        }
        $this->sqltype     = 'getmap';
        return $this->result;
    }

    public function getcount()
    {
        $this->sqltype     = 'getcount';
        //读取
        $this->result = $this->buildsql();
        if($this->sql != $this->_sql){
            $this->result   = $this->db->getone($this->sql);
            $this->_sql     = $this->sql;
        }
        return $this->result;
    }

    public function getcol()
    {
        //读取
        $this->result = $this->buildsql();
        if($this->sql != $this->_sql){
            $this->result   = $this->db->getcol($this->sql);
            $this->_sql     = $this->sql;
        }
        $this->sqltype     = 'getcol';
        return $this->result;
    }

    public function getall()
    {
        //读取
        $this->result = $this->buildsql();
//        echo $this->sql;
        if($this->sql != $this->_sql){
            $this->result   = $this->db->getall($this->sql);
            $this->_sql     = $this->sql;
        }
        $this->sqltype     = 'getall';
        return $this->result;
    }

    public function getrow()
    {
        //读取
        $this->result = $this->buildsql();
        if($this->sql != $this->_sql){
            $this->result   = $this->db->getrow($this->sql);
            $this->_sql     = $this->sql;
        }
        $this->sqltype     = 'getrow';
        return $this->result;
    }

    public function buildsql()
    {
        if($this->sqltype == 'getcount'){
            $sql = " select count(*)";
        }else{
            $sql = " select {$this->colm}";
        }

        $sql .= " from {$this->tablename}";
        if(!empty($this->where)){
            $wheres = \Sham::getstr($this->where,0,' and ');
            $sql .= " where $wheres";
        }

        if(!empty($this->group)){
            $sql .= " group by {$this->group}";
        }

        if(!empty($this->order)){
            $sql .= " order by {$this->order}";
        }

        if(!empty($this->limit)){
            $sql .= " limit {$this->limit}";
        }
        $this->sql = $sql;
        return $this;
    }

    public function close()
    {
        $this->db->close();
    }



    //测试用的方法
    public function getsql()
    {
        return $this->sql;
    }

    //=============================================
    //=============================================
    //=============================================
    public function test(){
        $this->buildsql();
        $this->getsql();
        echo $this->sql;
        exit;
        echo 'tableobj:test:';
        exit;
    }

    /*
     * 不存在的方法
     * */
    public function __get($action) {
        //先判断是否有active
        //如果存在，则执行，并且返回result
        //其他情况，则返回错误信息
    }

}

