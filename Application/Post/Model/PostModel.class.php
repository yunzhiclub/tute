<?php

/*
 * 岗位对应的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Post\Model;
use Think\Model;
class PostModel extends Model{
    public function getPostInfo() {
        $data = $this->select();
        return $data;
    }
}