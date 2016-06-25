<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 上午 11:41
 */
class IndexController extends AdminController
{

    public  function indexAction(){

        $this->display('index.html');
    }
    public  function topAction(){
        //>>1.接收请求参数

        //>>2.使用模型处理请求数据

        //>>3.调用视图
        $this->display('top.html');
    }

    public  function leftAction(){
        //>>1.接收请求参数

        //>>2.使用模型处理请求数据

        //>>3.调用视图
        $this->display('left.html');
    }
    public  function mainAction(){
        //>>1.接收请求参数

        //>>2.使用模型处理请求数据

        //>>3.调用视图
        $this->display('main.html');
    }
}