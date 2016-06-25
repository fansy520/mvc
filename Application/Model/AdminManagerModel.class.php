<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/8 0008
 * Time: 下午 2:56
 */
//require FRAME_PATH.'Model.class.php';
class AdminManagerModel extends Model
{
    /**
     * 检测用户名和密码是否在同一个记录中,如果在,返回true,如果不在返回false
     * @param $username
     * @param $password
     */
    public function checkUser($username,$password){
        //>>1.先对密码进行加密
        $password = md5($password);
        //>>2.加密之后再与数据库中进行对比.
//        $sql = "select * from admin_manager where  ";
        $row = $this->getRow("username = '$username' and password='$password'");
        if($row===false){  //如果row为空,说明没有查询出数据
             return false;
        }else{
            return $row;  //如果查询出来就返回该用户的信息
        }
    }


    /**
     * 根据id和密码进行登录
     * @param $id   用户的id
     * @param $password   加密之后的密码    加密算法: md5(数据库中的密码.'itsource')
     */
    public function checkUserByCookie($id,$auto_key){
        //>>1.根据id将数据库中的密码查询出来
            $auto_keyInDB = $this->getColumn('auto_key',"id = '$id'");  //select auto_key from 表名 where  条件
            if($auto_keyInDB===false){
                return false;
            }else{
                //>>2.将数据库中的密码加密 之后 和  $password进行对比
                if(md5($auto_keyInDB.'itsource')==$auto_key){
                     return true;
                }else{
                    return false;
                }
            }

    }

    public function setAutoKey($id,$str){
        //>>1.将需要修改的数据封装到$data中
        $data['id'] = $id;
        $data['auto_key'] = $str;
        //>>2.让updateData将数据修改到数据库中
        $this->updateData($data);
    }
}