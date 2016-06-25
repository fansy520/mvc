<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15 0015
 * Time: 上午 10:50
 */
class LoginController extends Controller
{
    /**
     * 展示登录页面
     */
    public function loginAction(){
        //>>1. 接受请求数据

        //>>2. 对数据进一步处理

        //>>3. 展示视图页面
        $this->display('login.html');
    }

    /**
     * 验证用户是否登录
     */
    public function checkUserAction(){
        /**
         * 验证用户录入的验证码是否正确
         */
     /*    if(!CaptchaTool::checkCode($_POST['captcha'])){
            $this->redirect('index.php?p=Admin&c=Login&a=login','验证码错误',2);
         }*/


        //>>1. 接受请求数据
        $username = $_POST['username'];
        $password = $_POST['password'];
        //>>2. 对数据进一步处理
//            require   MODEL_PATH.'AdminMangerModel.class.php';
        $model = new AdminManagerModel();
        $result = $model->checkUser($username,$password);
        //>>3. 展示视图页面
        if($result===false){
            //登录失败(提示跳转)
            $this->redirect("index.php?p=Admin&c=Index&a=index",'用户名或者密码出错!',3);
        }else{
            //登录成功(直接跳转)
            //setcookie('isLogin','yes');//增加登录标示,该cookie的内容保存在浏览器中
            new SessionTool();
            $_SESSION['isLogin'] = true;
            if(isset($_POST['remember'])){

                //生成一个随机的字符串
                $str = substr(str_shuffle("abcdefglkcnd1234567890"),0,6);
                $model = new AdminManagerModel();
                $model->setAutoKey($result['id'],$str);

                //说明打钩记住我
                setcookie('id',$result['id'],time()+60*60*24,'/');
                //对密码加密的算法:  md5('数据库中的密码'.'xxxxxx')
                setcookie('auto_key',md5($str.'itsource'),time()+60*60*24,'/');
            }
            $this->redirect("index.php?p=Admin&c=Index&a=index");
        }
    }
}