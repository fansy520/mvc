<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15 0015
 * Time: 下午 4:37
 */
class CategoryModel extends Model
{

    /**
     * 根据parent_id找到子孙分类
     * @param int $parent_id 父id
     * @return array
     */
    public function getList($parent_id  = 0){
        //直接使用基础模型类中的getAll查询出所有的数据
        $rows =  $this->getAll();
        return $this->getTreeList($rows,$parent_id,0);
    }

    /**
     * 从rows中查询parent_id的子节点
     * @param $rows   所有的儿子
     * @param $parent_id
     */
    private function getTreeList(&$rows,$parent_id,$level){
        static $tree  = array();//专门用来存放儿子的容器 .  static 表示该变量只初始化一次
        foreach($rows as $row){
            if($row['parent_id']==$parent_id){  //检测每个儿子的爹是不是parent_id
                $row['level'] = $level;  //每个儿子上添加一个级别.
                $row['name_text'] = str_repeat('&nbsp;',$level*5).$row['name'];  //带有缩进的名字.

                $tree[] = $row; //使用[]是指让row放到tree中
                //将row作为爹,然后从rows中再找儿子
                $this->getTreeList($rows,$row['id'],$level+1);
            }
        }
        return $tree;
    }


    /**
     * 将分类数据添加到数据表category中
     * @param $category
     * @return   bool:  false出错了
     */
    public function add($category){
        //>>1. 分类的名字不能为空
            if($category['name']==''){
                $this->error = '分类名称不能为空!';
                return false;
            }
        //>>2. 同级分类下不能够出现相同的名字
            $sql = "select count(*) from {$this->table()} where parent_id = {$category['parent_id']} and name = '{$category['name']}'";
            $count = $this->db->fetchColumn($sql);
            if($count>0){
                $this->error = '分类名字已经在同级下存在!';
                return false;
            }
        return $this->insertData($category);
    }


    /**
     * 根据id查询出一行数据
     * @param $id

    public function get($id){
        $sql  = "select * from {$this->table()} where id = ".$id;
        $row = $this->db->fetchRow($sql);
        return $row;
    }
    **/

    /**
     * 将$category中的数据更新到数据表中
     * @param $category
     */
    public function update($category){
        //>>1.判定名称不能为空
            if($category['name'] == ''){
                $this->error = '分类名称不能为空!';
                return false;
            }
        //>>2.不能够将父分类修改当前分类和子分类.即parent_id不能在当前分类id和当前的子分类id.
             //>>2.1 找到自己分类的子孙分类(应该要包含自己)
                    $rows = $this->getList($category['id']);
                    //取出所有子孙分类的id
                    /*$ids = array();
                    foreach($rows as $row){
                        $ids[] = $row['id'];
                    }*/

                    //rows中的每一个元素都会传递给第一个参数的函数,函数的返回值作为 array_map返回值中元素
                    $ids = array_map(function($row){  return $row['id'];},$rows);

                    $ids[] = $category['id'];//将自己放进去
             //>>2.2 判定用户指定的父类的id是否 在  子孙分类id中. 如果在,就是错误的.
                    if(in_array($category['parent_id'],$ids)){
                        $this->error = '不能选择自己或自己的子分类作为父分类!';
                        return false;
                    }


       return  $this->updateData($category);
    }


    /**
     * 根据id删除数据库中的一行记录
     * @param $id
     */
    public function remove($id){
        //删除一个分类时,如果该分类下有子分类不允许删除.
        $rows = $this->getList($id);
        if(!empty($rows)){
            $this->error = '不能删除有子分类的分类!';
            return false;
        }
        return  $this->deleteByPk($id);
    }
}