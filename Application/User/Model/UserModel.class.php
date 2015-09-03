<?php

/*
 * User用户Model
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace User\Model;
use Think\Model;
class UserModel extends Model{
	protected $error;

	public function getAllName()
	{
		$res = $this->field('name,id')->select();
		return $res;
	}
	/**
	 * 通过userid获取用户全部信息
	 * @param  [type] $userId [关键字]
	 * @return [type]         包括一个用户信息的数组
	 */
	public function getUserById($userId = null)
	{
		if($userId === null)
		{
			$this->error = "未传入userid";
			return false;
		}

		$map['id'] = $userId;
		return $this->where($map)->find();
	}
	/**
	 * 通过包括有USERID的数组获取用户信息
	 * @param  array $lists   包括有userid项的数组
	 * @param  string $keyWord KEYWORD键值
	 * @return array          以userid为下标的数组
	 * panjie 3792535@qq.com
	 */
	public function getListsByLists($lists = null , $keyWord = "user_id")
	{
		if(!is_array($lists) || $lists === null)
		{
			$this->error = "传入的数据格式不正确";
			return false;
		}

		$return = array();
		foreach($lists as $key => $value)
		{
			if(!isset($value[$keyWord]))
			{
				$this->error = "关键字传入错误";
				return false;
			}
			$map['id'] = $value[$keyWord];
			$return[$value[$keyWord]] = $this->where($map)->find();
		}
		return $return;
	}

		//获取教工列表
	public function getStaffList(){
		$data = $this -> select();
		return $data;
	}
	//获取固定id的教工的信息
	public function getStaffById($id){
		$map['id'] = $id;
		$data = $this -> where($map) ->find();
		return $data;
	}
	//添加教工
	public function addStaff(){
		$data = I('post.');
		$this->add($data);
		return true;
	}
	//编辑教工
	public function updateStaff(){
		$data = I('post.');
		$data[id] = I('get.id');
		$this -> save($data);
		return true;
	}
	public function deleteStaff(){
		$data[id] = I('get.id');
		$state = $this -> where($data) ->delete();
		return $state;
	}
}