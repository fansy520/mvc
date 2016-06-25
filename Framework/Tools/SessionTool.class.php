<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/13 0013
 * Time: 下午 3:14
 */
class SessionTool
{
    private $db;
    public function __construct(){
        session_write_close(); //关闭前面开启的session
        //对象创建时,告知PHP说, 当前对象中的方法来重写session存储机制
        session_set_save_handler(
            array($this,'open'),
            array($this,'close'),
            array($this,'read'),
            array($this,'write'),
            array($this,'destroy'),
            array($this,'gc')
        );
        session_start();//必须开启session
    }

    /**
     * @param $savePath   session文件存储路径
     * @param $sessionName  session的名字
     */
   public function open($savePath, $sessionName){
        //>>1.连接上数据库
         $this->db  = DB::getInstance($GLOBALS['config']['db']);
        return true;
    }

    public function close(){
        return true;
    }

    /**
     * 只有根据sessonid才能够找到session数据
     * @param $sessionId
     * @return  返回数据库中的原值,不需要反序列化 . 该数据被放在$_SESSION中
     */
    public function read($sessionId){
        $sql = "select sess_data from session where sess_id = '$sessionId'";
        $sess_data = $this->db->fetchColumn($sql);
        if($sess_data===false){
            return '';
        }else{
            return $sess_data;
        }
    }

    /**
     * PHP代码执行完毕之后, $data数据写到数据库中
     * @param $sessionId
     * @param $data ($_SESSION已经被php序列化了,就等待着下面的方法写到数据库中 )
     */
    public function write($sessionId, $data){
        $sql = "insert into session values('$sessionId','$data',unix_timestamp()) on duplicate key update sess_data = '$data',last_modified=unix_timestamp()";
        $this->db->query($sql);
    }

    /**
     * 该方法执行时会根据session_id删除session表中的记录
     * @param $sessionId
     * @return bool
     */
    public function destroy($sessionId){
        $sql = "delete from session where sess_id = '$sessionId'";
        $this->db->query($sql);
    }

    public function gc($lifetime){
        //>>1. 确定哪些数据时垃圾数据,删除这些垃圾数据
        $sql = "delete from session where  $lifetime<unix_timestamp()-last_modified";
        $this->db->query($sql);
    }
}