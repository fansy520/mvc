<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/16 0016
 * Time: 下午 4:55
 */
class GoodsController extends AdminController
{
    /**
     * 展示添加页面
     */
    public function addAction(){
        //>>1.接受请求参数
        //>>2.调用模型来处理数据
                //>>2.1 查询出所有的分类数据
                $model  = new CategoryModel();
                $rows = $model->getList();//得到树结构的数据
                //>>2.2 将分类数据分配到页面上
                $this->assign('rows',$rows);
                //>>2.3 查询出品牌并且分配到页面上
                $brandModel = new BrandModel();
                $brands = $brandModel->getAll();
                $this->assign('brands',$brands);
        //>>3.展示视图页面(需要商品分类数据)
            $this->display('add.html');
    }

    /**
     * 将表单中的数据添加到goods表中
     */
    public function insertAction(){
        //>>1.接受请求参数
            //>>1.1 先接收文件数据
                $uploader = new UploadTool();
                $result = $uploader->upload($_FILES['goods_img']);
                if($result===false){
                    $this->redirect('index.php?p=Admin&c=Goods&a=add','上传图片失败!'.$uploader->error_info,3);
                }else{
                    //上传成功之后,$result就是上传后的地址
                    $_POST['image_ori'] = $result;  //因为$_POST中的数据要被保存到数据库中的
                }

            //>>1.2. 根据大图片生成小图片
                $thumb_filename = ImageTool::thumb($result,50,50);  //$result上传后的原图片路径
                if($thumb_filename!==false){
                    $_POST['image_thumb'] = $thumb_filename;  //为了让$_POST中的数据保存到数据库表中
                }else{
                    $this->redirect('index.php?p=Admin&c=Goods&a=add','缩略图生成失败!'.ImageTool::$error_info.$uploader->error_info,3);
                }

            //>>1.2 再接收表单中的文本数据
            $goods = $_POST;
        //>>2.调用模型来处理数据
            $goodsModel = new GoodsModel();
            $goodsModel->add($goods);
        //>>3.展示视图页面(需要商品分类数据)
            echo '添加成功!';
    }



    public function indexAction(){
        //>>1.接受请求参数;
             $page = isset($_GET['page'])?$_GET['page']:1;
        //>>2.使用模型来处理
                $model = new GoodsModel();
              //>>2.1 获取分页中需要的数据
                $pageResult = $model->getPageResult($page,"index.php?p=Admin&c=Goods&a=index");

                $this->assign('rows',$pageResult['rows']); //列表
                $this->assign('pageToolHtml',$pageResult['pageToolHtml']); //分页工具条的html

            /*
                $this->assign('total',$pageResult['total']); //总条数
                $this->assign('total_page',$pageResult['total_page']); //总页数
                $this->assign('page',$pageResult['page']); //当前页码
                $this->assign('pageSize',$pageResult['pageSize']); //每页多少条*/
        //>>3.加载视图
            $this->display('index.html');
    }
}