<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15 0015
 * Time: 下午 2:52
 */
class CaptchaTool
{
    private static function makeCode($max){
            $chars = array_merge(range('A','Z'),range(0,9));  //生成一个A-Z和0-9的数组
            $chars = implode('',$chars);  //将数组中的元素连接成字符串
            $chars = str_shuffle($chars);  //打乱字符串
            $random_code = substr($chars,0,$max); //截取四个
        return $random_code;
    }
    /**
     * 生成一个验证码, 并且图片上面的验证有max个
     * @param int $max
     */
    public static function generate($max = 5){
        //>>1.生成随机码
            $random_code =  self::makeCode($max);
            new SessionTool();
            $_SESSION['random_code'] =$random_code;
        //>>2.将随机码写在图片上
            //>>2.1 使用图片
                $imageFile = TOOLS_PATH."captcha/captcha_bg".mt_rand(1,5).".jpg";
                list($width,$height) = getimagesize($imageFile);  //获取到图片的宽和高
                $image = imagecreatefromjpeg($imageFile);
            //>>2.2 白色的边框
                $white = imagecolorallocate($image,255,255,255);

                imagerectangle($image,0,0,$width-1,$height-1,$white);
            //>>2.3 再随机码写在图片
                 $black = imagecolorallocate($image,0,0,0);
                 imagestring($image,5,$width/3,$height/6,$random_code,mt_rand(0,1)?$white:$black);

            //>>2.4.加上干扰像素
                for($i=0;$i<100;++$i){
                    $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
                    imagesetpixel($image,mt_rand(0,$width),mt_rand(0,$height),$color);
                }

            //>>2.6.加上干扰线
                for($i=0;$i<2;++$i){
                    $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
                    imageline($image,mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height),$color);
                }




        //>>3.将图片发送给浏览器
            header('Content-Type: image/jpeg;charset=utf-8');
            imagejpeg($image);
            imagedestroy($image);

    }

    /**
     * 验证用户输入的验证码
     * @param $captcha  ---用户输入的验证码
     */
    public static function checkCode($captcha){
        new SessionTool();
        return $_SESSION['random_code'] == $captcha;
    }
}