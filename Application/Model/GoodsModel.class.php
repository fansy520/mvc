<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/16 0016
 * Time: 下午 5:05
 *
 * 必须是模型类,因为基础模型类中的方法需要更加当前模型的名字 生成  表名
 */
class GoodsModel extends Model
{

    public function add($goods){
        //>>1. 对$goods中的商品状态数据进一步处理
        $goods_status = 0;
        if(isset($goods['goods_status'])){  //判定用户选中了商品的状态
            foreach($goods['goods_status'] as $v){
                $goods_status = $goods_status|$v;
            }
        }
        $goods['goods_status'] = $goods_status;
        //>.2.将处理后的数据保存到数据库表中
        return $this->insertData($goods);
    }

}