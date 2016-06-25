<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 下午 3:51
 */
class Framework
{
    public static  function run(){
        //一定要严格按照以下的调用顺序来调用.
        self::initPath();
        self::initConfig();
        self::initRequestParam();
        self::initClassMap();
        self::initAutoload();//告诉PHP,当使用未加载的类时,请调用哪个方法
        self::dispache();
    }
    /**
     * 初始化目录常量
     */
    private static  function initPath(){
        defined('DS') or define('DS',DIRECTORY_SEPARATOR);   //定义目录分隔符
        defined('ROOT_PATH') or define('ROOT_PATH',dirname($_SERVER['SCRIPT_FILENAME']).DS);  //定义项目根目录
        defined('APP_PATH') or define('APP_PATH',ROOT_PATH.'Application'.DS);  //Application所在的目录
        defined('FRAME_PATH') or define('FRAME_PATH',ROOT_PATH.'Framework'.DS);  //Framework所在的目录
        defined('PUBLIC_PATH') or define('PUBLIC_PATH',ROOT_PATH.'Public'.DS);  //Public所在的目录
        defined('CONFIG_PATH') or define('CONFIG_PATH',APP_PATH.'Config'.DS);  //Application/Config所在的目录
        defined('CONTROLLER_PATH') or define('CONTROLLER_PATH',APP_PATH.'Controller'.DS);  //Application/Controller所在的目录
        defined('MODEL_PATH') or define('MODEL_PATH',APP_PATH.'Model'.DS);  //Application/Model所在的目录
        defined('VIEW_PATH') or define('VIEW_PATH',APP_PATH.'View'.DS);  //Application/View所在的目录
        defined('TOOLS_PATH') or define('TOOLS_PATH',FRAME_PATH.'Tools'.DS);  //Framework/Tools所在的目录
    }

    /**
     * 初始化(加载)配置文件
     */
    private static function initConfig(){
        //必须使用$GLOBALS将config作为一个全局变量
        $GLOBALS['config'] = require CONFIG_PATH.'application.config.php';  //该文件中的配置信息被通过return返回,所以说需要通过变量接收配置信息
    }
    /**
     * 接收请求参数
     */
    private static function initRequestParam(){
        $p = isset($_GET['p'])?$_GET['p']:$GLOBALS['config']['default']['defaultPlatform'];  //默认是后台
        defined('PLATFROM_NAME') or define('PLATFROM_NAME',$p);  //将请求中参数p作为平台名字的常量
        $c = isset($_GET['c'])?$_GET['c']:$GLOBALS['config']['default']['defaultController'];  //给定他的默认值
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME',$c);  //将请求中参数c作为控制器名字的常量
        $a = isset($_GET['a'])?$_GET['a']:$GLOBALS['config']['default']['defaultAction'];
        defined('ACTION_NAME') or define('ACTION_NAME',$a); //将请求中参数a作为方法名字的常量

        //定义一个用户正在访问的哪个平台下的控制器的路径常量
        defined('CURRENT_CONTROLLER_PATH') or define('CURRENT_CONTROLLER_PATH',CONTROLLER_PATH.$p.DS);
        //定义一个用户正在访问的哪个平台下的View的路径常量
        defined('CURRENT_VIEW_PATH') or define('CURRENT_VIEW_PATH',VIEW_PATH.$p.DS);
    }
    /**
     * 类名和类文件路径映射
     */
    private static function initClassMap(){
        $GLOBALS['map'] = array(  //因为map需要在userAutoload中使用
            'DB'=>TOOLS_PATH.'DB.class.php',
            'Model'=>FRAME_PATH.'Model.class.php',
            'Controller'=>FRAME_PATH.'Controller.class.php'
        );
    }
    /**
     * 根据用户的请求参数调用哪个控制器的哪个方法执行.
     */
    private static function dispache(){
        //>>1.1. 加载控制器类文件
        $controller_name = CONTROLLER_NAME.'Controller';   //AdminManagerController
        //require   CURRENT_CONTROLLER_PATH."$controller_name.class.php";  //到当前控制器下中该文件
        //>>1.2. 创建控制器对象
        $controller = new $controller_name();
        //>>2. 根据a参数调用控制器中的方法
        $actionName = ACTION_NAME.'Action';
        $controller->$actionName();
    }

    /**
     * 告诉PHP,当使用未加载的类时,请调用哪个方法
     */
    private static function initAutoload(){
        //如何告知PHP, 当使用未加载的类时,请调用userAutoLoad方法
        spl_autoload_register("Framework::userAutoLoad");  //来作为__autoload函数的功能
    }
    /**
     * 类的自动加载
     */
    private  static function userAutoLoad($class_name){
        if(isset($GLOBALS['map'][$class_name])){  //加载特殊类
            require  $GLOBALS['map'][$class_name];
        }elseif(substr($class_name,-10)=='Controller'){ //说明该类是一个控制器xxxxController
            require  CURRENT_CONTROLLER_PATH.$class_name.'.class.php';
        }elseif(substr($class_name,-5)=='Model'){
            require  MODEL_PATH.$class_name.'.class.php';
        }elseif(substr($class_name,-4)=='Tool'){  //如果该类名是以Tool结尾. 都到Tools下加载
            require  TOOLS_PATH.$class_name.'.class.php';
        }
    }
}