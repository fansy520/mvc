<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15 0015
 * Time: 上午 11:39
 */
class CaptchaController extends Controller
{
    public function indexAction(){
            //生成图片验证码
            CaptchaTool::generate();
    }

}