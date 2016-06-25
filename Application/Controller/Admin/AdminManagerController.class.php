<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/8 0008
 * Time: 下午 5:02
 */
class AdminManagerController extends AdminController
{
    public function listAction()
    {
        //>>1.需要查询出数据库中的数据
//        require MODEL_PATH.'AdminManagerModel.class.php';
        $model = new AdminManagerModel();
        $rows = $model->getAll();
        $this->assign('rows',$rows);
        //>>2. 使用一个表格展示出rows中的数据
       $this->display('user_view.html');
    }


    public function removeAction()
    {
        //>>1.  获取请求参数
        $id = $_GET['id'];
//>>2.  从数据库中删除id对应记录
//        require MODEL_PATH.'AdminManagerModel.class.php';
        $model = new AdminManagerModel();
        $model->deleteByPk($id);
//>>3. 转向index.php控制帮我显示列表,
       self::redirect('index.php?p=Admin&c=AdminManager&a=list');
    }

    public function addAction()
    {
        //用来完成添加页面的展示
        $this->display('add_view.html');
    }




    /**
     * 用来退出登录
     *   a. 清空session中的登录标示
         b. 删除cookie中的用户信息
     */
    public function logoutAction(){
        //>>1.接受请求参数
        //>>2.调用模型处理请求数据
        //>>3.展示视图
        session_start();
        unset($_SESSION['isLogin']);
        setcookie('id',null,-1,'/');
        setcookie('password',null,-1,'/');
        $this->redirect('index.php?p=Admin&c=Login&a=login');
    }
}