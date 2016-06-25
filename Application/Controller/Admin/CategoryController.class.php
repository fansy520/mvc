<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15 0015
 * Time: 下午 4:32
 */
class CategoryController extends Controller
{

    public function indexAction(){
        //>>1.获取请求参数
        //>>2.使用模型处理数据
          $model = new CategoryModel();
          $rows  = $model->getList();

          $this->assign('rows',$rows);
        //>>3.展示视图页面
          $this->display('index.html');
    }
    public function addAction(){
        //>>1.获取请求参数
        //>>2.使用模型处理数据
         $model = new CategoryModel();
         $rows  = $model->getList(); //得到商品分类
         $this->assign('rows',$rows);
        //>>3.展示视图页面
        $this->display('add.html');
    }
    public function insertAction(){
        //>>1.获取请求参数
        $category = $_POST;
        //>>2.使用模型处理数据
        $model = new CategoryModel();
        $result = $model->add($category);
        //>>3.展示视图页面
        if($result===false){
            //添加失败
            $this->redirect('index.php?p=Admin&c=Category&a=add','添加失败!'.$model->error ,3);
        }else{
            $this->redirect('index.php?p=Admin&c=Category&a=index');
        }
    }

    public function editAction(){
        //>>1.获取请求参数
             $id = $_GET['id'];
        //>>2.使用模型处理数据
                $model = new CategoryModel();
              //>>2.1.根据id查询出该数据分配到页面上回显
                $current_row = $model->getByPk($id);
                $this->assign('current_row',$current_row);
              //>>2.2 查询出所有的分类数据.在下拉框中显示
                $rows  = $model->getList(); //得到商品分类
                $this->assign('rows',$rows);
        //>>3.展示视图页面
            $this->display('edit.html');
    }

    public function updateAction(){
        //>>1.获取请求参数
            $category = $_POST;
        //>>2.使用模型处理数据
            $model = new CategoryModel();
            $result = $model->update($category);
        //>>3.展示视图页面
            if($result===false){
                //更新失败,还需要展示编辑页面, 所以说需要根据id查询出当前行的信息,回显出来
                $this->redirect('index.php?p=Admin&c=Category&a=edit&id='.$category['id'],'更新失败!'.$model->error ,3);
            }else{
                $this->redirect('index.php?p=Admin&c=Category&a=index');
            }
    }


    public function removeAction(){
        //>>1.获取请求参数
            $id = $_GET['id'];
        //>>2.使用模型处理数据
            $model = new CategoryModel();
            $result = $model->remove($id);
        //>>3.展示视图页面
            if($result===false){
                //删除失败需要提示错误信息
                $this->redirect('index.php?p=Admin&c=Category&a=index','删除失败!'.$model->error ,3);
            }else{
                //删除成功直接转向列表
                $this->redirect('index.php?p=Admin&c=Category&a=index');
            }
    }
}