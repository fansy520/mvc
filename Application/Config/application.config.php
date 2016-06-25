<?php
 return   array(
         //和数据库相关的配置信息
        'db'=>array(
            'host'=>'127.0.0.1',
            'port'=>3306,
            'user'=>'root',
            'password'=>1234,
            'dbname'=>'myshop',
            'charset'=>'utf8',
            'prefix'=>'itsource_'
        ),
        //存放当前项目的中的默认信息
        'default'=>array(
            'defaultPlatform'=>'Admin',
            'defaultController'=>'AdminManager',
            'defaultAction'=>'list',
        ),
        //前台的配置信息
        'Home'=>array(),
        //后台的配置信息
        'Admin'=>array(
            'upload_path'=>'./Uploads',
            'upload_max_size'=>1024*1024*2,
            'upload_allow_type'=>'image/jpeg,image/png,image/gif',
        ),
    );