<?php

/**
 * 主要是生成缩略图
 */
class ImageTool
{
    public static $error_info;

    public static $createImageFuns = array(
         'image/jpeg'=>'imagecreatefromjpeg',
         'image/png'=>'imagecreatefrompng',
         'image/gif'=>'imagecreatefromgif',
    );
    public static $outImageFuns = array(
         'image/jpeg'=>'imagejpeg',
         'image/png'=>'imagepng',
         'image/gif'=>'imagegif',
    );

    /**
     * //根据大图地址和大小. 来生成图片,返回生成图片地址
     * @param $src_filename   原图片文件
     * @param $max_w   缩略图宽
     * @param $max_h   缩略图高
     */
  public static function  thumb($src_filename,$max_w,$max_h){
             if(!is_file($src_filename)){
                 self::$error_info = '大图片不存在!';
                 return false;
             }

             //获取一个文件的类型
             $type = mime_content_type($src_filename); //通过该函数可以获取一个文件的类型.


        //>>1. 从已存在的大图上创建大图 对象
              //>>1.1 创建大图
             $createImgFn = self::$createImageFuns[$type];  //根据类型找到对应创建图片对象的函数
              $src_image =    $createImgFn($src_filename);
              //>>1.2 得到大图片的宽和高
              list($src_width,$src_height) = getimagesize($src_filename);



        //>>2.创建小图  对象
              $dist_image = imagecreatetruecolor($max_w,$max_h);


        //>>3.计算出压缩的比例.  哪个比例大,就是用哪个.  (哪个: 宽和高)
              $scale  =    max($src_width/$max_w,$src_height/$max_h);

        //>>4.计算放到小图片中的大小
              $dst_width = $src_width/$scale;
              $dst_height = $src_height/$scale;

        //>>5.补白(将背景变成白色)
              $white = imagecolorallocate($dist_image,255,255,255);
              imagefill($dist_image,0,0,$white);
        //>>6.图片居中
              $dst_x = ($max_w-$dst_width)/2;
              $dst_y = ($max_h-$dst_height)/2;
        //>>3.需要将大图上面的一部分取出来  放在小图片上
              /**
               * bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y ,
               *               int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
               *
               * $dst_image:  目标图片(小图)
               * $src_image: 原图
               * int $dst_x , int $dst_y:  拷贝到目标图片的哪个位置上
               * int $src_x , int $src_y : 从原图的哪个位置上进行拷贝
               * int $dst_w , int $dst_h : 拷贝到模板图片上的大小
               * int $src_w , int $src_h : 从原图上拷贝多大过来
               */
              imagecopyresampled($dist_image,$src_image,$dst_x,$dst_y,0,0,$dst_width,$dst_height,$src_width,$src_height);

        //>>4. 输出小图,保存到指定的文件夹中
                 //根据大图片计算出小图片路径:
                  //  ./Uploads/2016-03-17/20160317155503_56ea62d755ca8.jpg  大图片路径
                  //  ./Uploads/2016-03-17/20160317155503_56ea62d755ca8_50x50.jpg  小图片路径
              $pathinfo = pathinfo($src_filename);
              $thumb_filename = $pathinfo['dirname'].'/'.$pathinfo['filename']."_{$max_w}x{$max_h}.".$pathinfo['extension'];
//              imagepng($dist_image);  //默认的情况下是发送给浏览器
               $outImgFun = self::$outImageFuns[$type];  //根据类型找到输出的函数
               $outImgFun($dist_image,$thumb_filename);  //默认的情况下是发送给浏览器
              imagedestroy($dist_image);
               return $thumb_filename;
  }

}