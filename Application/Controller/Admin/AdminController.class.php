<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15 0015
 * Time: 上午 10:45
 */
class AdminController extends Controller
{
    public  function __construct(){
        $this->checkLogin();
    }
    /***
     * 如果没有登录就跳转到登录页面.  如果登录. 继续向下执行
     */
    public function checkLogin(){
        //>>1.接收请求参数
        //  检测浏览器发送的cookie中是否包含isLogin=yes的cookie,如果在说明之前登录过,展示首页首页
        new SessionTool();
        if(isset($_SESSION['isLogin']) && $_SESSION['isLogin']){
            //>>2.使用模型处理请求数据
            //>>3.调用视图
            // require CURRENT_VIEW_PATH.'index.html';
        }elseif(isset($_COOKIE['id']) && isset($_COOKIE['auto_key'])){
            //验证id和密码是否正确
            $id = $_COOKIE['id'];
            $auto_key = $_COOKIE['auto_key'];
            $model = new AdminManagerModel();
            $result = $model->checkUserByCookie($id,$auto_key);  //验证id和$auto_key是否正确
            if($result===false){  //没有验证成功
                $this->redirect('index.php?p=Admin&c=Login&a=login'); //登录页面
            }else{
                //验证成功, 在session中加上登录标示
                $_SESSION['isLogin'] = true;  //第二次访问后台首页时可以直接根据session中的标示加载后台首页.
                //>>3.加载后台首页
                // require CURRENT_VIEW_PATH.'index.html';
            }

        }else{
            $this->redirect('index.php?p=Admin&c=Login&a=login');
        }
    }

}