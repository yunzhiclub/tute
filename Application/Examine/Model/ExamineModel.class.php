<?php

/*
 * 审批链表对应的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Examine\Model;
use Think\Model;
use Chain\Model\ChainModel;
use Post\Model\PostModel;
class ExamineModel extends Model{
    //保存create信息
    /**
     * 根据传入的用户点击的信息存入数组再加上从界面上获取的岗位数量进行存库
     * @param array $array
     * @param int $num
     * 无返回值
     */
    public function save($array,$num){
        //先存入examine信息
        $name = I('post.name');
        $data = array();
        $data['name'] = $name;
        //自动生成随机数作为审批编号
        $id = createRand();
        $data['id'] = $id;
        $this->add($data);
        //传值，再存入examine对应的chain信息
        $chain = new ChainModel;
        $chain->save($array,$num,$id);
    }
    
    //根据审批表与审批链表取的审理流程信息
    public function index($str = "->") {
        $chain = new ChainModel;
        //取出审批对应的基本信息
        $data = array();
        $data = $this->select();
        //根据对应的num firstpost endpost取出整个审批流程
        $examine = array();
       foreach ($data as $key => $value) {
           $chain->setFirstId($value['chain_id']);
           $examine[] = $chain->getExamine();
       }
       $string = array();
       foreach ($examine as $key => $value) {
           foreach ($value as $k => $val) {
            if($k)
               $string[$key] .= $str  . $val;
            else
                $string[$key] .= $val;
           }
       }
       foreach ($data as $key => $value) {
           $data[$key][string] = $string[$key];
       }
       return $data;
    }
    
    //取出所有post名称供V层选择
    public function add(){
        //取岗位名称
        $post = new PostModel;
        $Info = $post->getPostInfo();
        $postname = array();
        foreach ($Info as $value) {
            $postname[] = $value['name'];
        }
        return $postname;
    }
    
    //生成随机数作为Examineid并与数据库中的id值比较
    public function createRand() {
        //生成从0到9999的随机数
        $data = array();
        $data = $this->select();
        //将生成数与数据库中编号数据对比
        foreach ($data as $value) {
            do{
                $id = rand(0000,9999);
            }while ($id != $value['id']);
        }
        return $id;
    }
    
}