<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/17 0017
 * Time: 下午 2:08
 */
class UploadTool
{
    /**
     * 允许上传的最大值
     * @var
     */
    private $max_size;
    /**
     * 允许上传的类型
     * @var
     */
    private $allow_type;
    /**
     * 上传的目录
     * @var
     */
    private $dir;
    /**
     * 上传失败的错误信息
     * @var
     */
    public $error_info;
    /**
     * 多文件上传存放的错误信息
     * @var array
     */
    public $error_infos=array();

    public function __construct($dir='',$allow_type='',$max_size=''){
        //如果没有指定参数,就是用配置文件中默认的.
        $this->dir = empty($dir)?$GLOBALS['config']['Admin']['upload_path']:$dir;
        $this->allow_type =  empty($allow_type)?explode(',',$GLOBALS['config']['Admin']['upload_allow_type']):$allow_type;
        $this->max_size =  empty($max_size)?$GLOBALS['config']['Admin']['upload_max_size']:$max_size;
    }

    /**
     * 上传功能
     * @param $file_info
     */
    public function upload($file_info){
        //>>1.判定是否上传成功
        if($file_info['error']!==0){
            $this->error_info="上传失败!";
            return false;
        }
        //>>2.判定上传文件的类型
        if(!in_array($file_info['type'],$this->allow_type)){
            $this->error_info="文件类型不支持!";
            return false;
        }
        //>>3.判定文件的大小
        if($file_info['size']>$this->max_size){
            $this->error_info="文件超出最大值!";
            return false;
        }
        //>>5. 根据先判定Uploads文件夹下面是否有当前日期的目录, 如果没有就生成一个目录
           $this->dir = $this->dir.'/'.date('Y-m-d');
           if(!is_dir($this->dir)){ //判定文件夹是否存在
                mkdir($this->dir,0777,true);
           }
        //>>4. 生成一个唯一的文件名:    20160317120202_唯一名字.后缀
        $new_file = uniqid(date('YmdHis')."_").strrchr($file_info['name'],'.');
                         //  ./Uploads/
        $new_file_path = $this->dir.'/'.$new_file;
        if(is_uploaded_file($file_info['tmp_name'])){
            move_uploaded_file($file_info['tmp_name'],$new_file_path);
            return $new_file_path;//返回上传后的路径
        }else{
            $this->error_info = '不是浏览器上传的文件!';
            return false;
        }
    }


    /**
     * 上传多个文件
     */
    public function mutilUpload($file_infos){  //$_FILES['file']
        //多个新的文件路径
        $new_file_paths = array();
        foreach($file_infos['error'] as $k=>$v){
            $fileinfo = array();
            if($v==0){
                //成功
                $fileinfo['error'] =$v;
                $fileinfo['name'] =$file_infos['name'][$k];
                $fileinfo['type'] =$file_infos['type'][$k];
                $fileinfo['size'] =$file_infos['size'][$k];
                $fileinfo['tmp_name'] =$file_infos['tmp_name'][$k];
            }
           //根据每个文件信息进行上传
           $result =  $this->upload($fileinfo);
           if($result===false){
                //将每一个上传失败的错误信息放到  error_infos数组中
                $this->error_infos[] = $this->error_info;
           }else{
               //每个上传后的路径都放到new_file_paths中
               $new_file_paths[] = $result;
           }
        }
        //只要有错误就返回错误false
        if(!empty($this->error_infos)){
            return false;
        }
        //只有全部成功之后才返回所有的新的路径
        return $new_file_paths;
    }
}