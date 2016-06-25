<?php

/**
 *
 * 该基础模型类完成了DB类的初始化功能
 */
class Model
{
    public $error  = ''; //保存模型出现的错误信息

    protected $db;

    protected $table_name = '';  //子类可以覆盖该属性明确指定操作的表

    /**
     * 例如:
     * array(
        'pk'=>'id','name','intro','age'
     * )
     */
    protected $fields = array(); //包含当前类操作表的字段信息


    /**
     * 对象创建之后立马调用initDB连接数据库
     */
    public function __construct(){
        $this->initDB();
        $this->initFields();
    }
    /**
     * 初始化数据库操作类
     */
    private function initDB(){
        //>>1.连接数据库
        $this->db = DB::getInstance($GLOBALS['config']['db']);  //why ?
    }

    /**
     * 获取当前操作表的字段名字放到$this->fields上
     */
    private function initFields(){
        $sql = "desc {$this->table()}";
        //>>1.获取表中每个字段
        $rows = $this->db->fetchAll($sql);
        foreach($rows as $row){
            if($row['Key']=='PRI'){  //如果是主键,通过pk的键名指定主键列的名字
                $this->fields['pk'] = $row['Field'];
            }else{
                $this->fields[] = $row['Field'];   //直接取出列表放到fields
            }
        }
    }
    /**
     * 获取表名
     * @return string
     */
    public function table(){
        if($this->table_name==''){  //如果没有指定逻辑表名,就获取当前模型类的名字,然后去掉Model.并且小写,作为表名
            $class_name = get_class($this); //得到当前模型对象的类名
            $this->table_name = strtolower(strstr($class_name,'Model',true));
        }
        return '`'.$GLOBALS['config']['db']['prefix'].$this->table_name. '`';
    }

    /**
     * 根据主键查询出一行数据
     * @param $pk
     */
    public function getByPk($pk){
        $sql = "select * from {$this->table()} where {$this->fields['pk']}  = $pk";
        return $this->db->fetchRow($sql);
    }

    /**
     * 根据主键删除一条数据
     * @param $pk
     * @return mixed
     */
    public function deleteByPk($pk){
        $sql = "delete from {$this->table()}  where {$this->fields['pk']} = $pk";
        return $this->db->query($sql);
    }

    /**
     * 根据条件查询数据
     * @param string $condition  例如:   写where后面的条件语句. 不需要添加where
     * @return mixed  返回二维数组
     */
    public function getAll($condition=''){
        $sql = "select * from  {$this->table()}";
        if(!empty($condition)){
            $sql.= " where ".$condition;
         }
        return $this->db->fetchAll($sql);
    }

    /**
     * 根据条件查询出一行数据
     * @param string $condition
     * @return bool  一维数组,     没有查询出内容返回false
     */
    public function getRow($condition=''){
        $rows = $this->getAll($condition);  // where name = 'xxx' and password = ''
        if(!empty($rows)){
            return $rows[0];  //取出一行
        }
        return  false;
    }
    /**
     * 根据条件查询出第一行第一列的数据
     * @param string $condition
     * @return bool  一维数组,     没有查询出内容返回false
     * 该方法拼装:
     * select 字段 from 表  where   id = ''
     */
    public function getColumn($column_name,$condition=''){
        $sql = "select $column_name from {$this->table()}";
        if(!empty($condition)){
            $sql .= '  where '.$condition;
        }
        return $this->db->fetchColumn($sql);
    }



    /**
     * 将data的数据保存数据表中, 实际上根据的键来拼装insert语句
     * @param $data
     *      $sql = "insert into 表名 set `列1`='值1',`列2`='值2',`列3`='值3'";
     *
     * 风险:
     *     $data中的数据可能和数据表的字段不对应
     * 解决方案:
     *   将不对应的数据从$data删除.
     *
     * 例如:
     * $data = array('name'=>'',parent_id=>'',intro=>'',xxx=>,yyy=>,kkk=>);
     */
    public function insertData($data){
        //>>将$data中没有字段对应数据 删除
          $this->ignoreErrorKey($data);

        //>>再将对应插入到数据库表中
        $sql = "insert into {$this->table()} set ";
        $arr = array();
        foreach($data as $key=>$value){
            $arr[] = "`$key` = '$value'";   //拼装set后的内容
        }
        $sql.=implode(',',$arr);   //通过它将set后的内容使用,连接起来
        return  $this->db->query($sql);  //执行该sql
    }

    /**
     * 忽略掉data中没有对应上的数据
     * $data = array('name'=>'',parent_id=>'',intro=>'',xxx=>,yyy=>,kkk=>);
     *  处理后的结果
     * $data = array('name'=>'',parent_id=>'',intro=>'');
     * @param $data
     *
     * $this->fields = array('pk'=>'id',name,parent_id)
     */
    private function ignoreErrorKey(&$data){ //一定要添加引用传值
        foreach($data as $key=>$value){
            if(!in_array($key,$this->fields)){  //如果$data的键不在$fields中,将$data中的数据删除
                unset($data[$key]);
            }
        }
    }


    /**
     * 按照$data拼装修改的sql并且执行
     * @param $data
     * @param $where 修改条件
     *
     *  $sql = "update {$this->table()}  set  列1=值1 ,列2=值2,列3=值3  where 条件"
     */
    public function updateData($data,$condition=''){
        //>>1.先忽略无效的数据
        $this->ignoreErrorKey($data);

        //>>2.根据$data中的数据拼装update的sql
        $sql = "update {$this->table()} set ";
        $arr = array();
        foreach($data as $key=>$value){
            $arr[] = "`$key` = '$value'";   //拼装set后的内容
        }
        $sql.= implode(',',$arr);   //通过它将set后的内容使用,连接起来
        //>>3.设置更新条件
        if(!empty($condition)){
            //根据指定的条件修改
            $sql.=" where ".$condition;
        }elseif(isset($data[$this->fields['pk']]) && !empty($data[$this->fields['pk']])){   // $this->fields['pk'] 主键的名字
            //根据主键来修改
            $sql.=" where {$this->fields['pk']} = '{$data[$this->fields['pk']]}'";
        }else{
            $this->error = '没有更新条件不能够更新!';
            return false;
        }
        //>>4.执行sql
        return $this->db->query($sql);
    }


    /**
     * 根据页面获取页面上需要的数据
     * @param $page
     */
    public function getPageResult($page,$url='',$pageSize = 2){
        //>>1.准备分页列表数据
//        $pageSize = 2;//每页多少条
        $start  = ($page-1)*$pageSize;//从哪条开始查询
        $sql = "select * from {$this->table()}  limit $start,$pageSize";
        $rows = $this->db->fetchAll($sql);

        //>>2.准备分页工具条所需数据
        //>>2.1.计算出总条数
        $sql  = "select count(*) from {$this->table()}";
        $total = $this->db->fetchColumn($sql);
        //>>2.2.计算出总页数
//        $pageSize = 2;//每页多少条
        $total_page = ceil($total/$pageSize);


        $pageToolHtml = PageTool::show($url,$total,$total_page,$page);
        return array('rows'=>$rows,'pageToolHtml'=>$pageToolHtml);
    }
}