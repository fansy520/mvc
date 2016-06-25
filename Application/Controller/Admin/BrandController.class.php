<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/9 0009
 * Time: 上午 10:02
 */
class BrandController extends AdminController
{

    public function listAction(){
        //>>1.查询品牌数据
               //>>1.1 加载BrandModel模型类.
                    $page = isset($_GET['page'])?$_GET['page']:1;
               //>>1.2 创建模型类对象
                    $model = new BrandModel();
                //>>1.3 调用模型类对象中的getList方法
                   $pageResult =  $model->getPageResult($page,"index.php?p=Admin&c=Brand&a=list",1);
                    $this->assign('rows',$pageResult['rows']);
                    $this->assign('pageToolHtml',$pageResult['pageToolHtml']);

        //>>2.展示品牌数据
        $this->display('brand_list_view.html');
    }

    public function editAction(){
        //>>1. 根据id查询出对应的数据
                $id = $_GET['id'];
        //>>2. 引入模型
                $model = new BrandModel();
                $row = $model->getByPk($id);
                $this->assign('row',$row);
        //>>2. 需要页面回显该数据
                $this->display('brand_edit.html');
    }

    public function updateAction(){
        //>>1.接受请求数据
         /*   $id = $_POST['id'];
            $name = $_POST['name'];
            $intro = $_POST['intro'];*/
//            $brand = $_POST;
        //>>2.让模型处理请求数据
            $model = new BrandModel();
            $model->updateData($_POST);

        //>>3.转向列表的控制器展示数据
           $this->redirect('index.php?p=Admin&c=Brand&a=list');
    }


}