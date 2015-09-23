<?php
/**
 * 项目表
 * author: panjie
 * 3792535@qq.com
 */
namespace Project\Logic;
use Project\Model\ProjectModel;
class ProjectLogic extends ProjectModel
{
	protected $tableName = '';	//数据表后缀

	//通过后缀设置数据表
	public function setTableSuffix($suffix)
	{
		$tableName = '__project_detail_' . $suffix . '__';
		$this->tableName = strtoupper($tableName);
	}

	//通过项目的详情ID来获取Lists数据
	public function getListByIdFromSuffixTable($projectDetailId = 0)
	{
		if($this->tableName === null)
		{
			$this->error = "未传入数据表后缀";
			return false;
		}

		if($this->tableName === '')
		{
			$this->error = "未设置数据表后缀";
			return null;
		}
		
		//查询并返回数据.为避免因数据表错误而导致的异常，必须进行异常处理。
		$map['id'] = $projectDetailId;
		try
		{
			$return = $this->table("$this->tableName")->where($map)->find();
			return $return;
		}
		catch(\Think\Exception $e)
		{
			$this->error = "数据查询出现错误，错误信息:" . $e->getMessage();
			return null;
		}	
	}

	public function getListByProjectDetailIdSuffix($projectDetailId = 0, $suffix = '')
	{
		//设置表后缀
		$this->setTableSuffix($suffix);

		//查询出数据
		return $this->getListByIdFromSuffixTable($projectDetailId);
	}

	/**
	 * 返回同一项目类别的数据
	 * @param  int $projectCategoryId 项目类别ID
	 * @return array                    二维数组
	 */
	public function getListsByProjectCategoryId($projectCategoryId)
	{
		$map['project_category_id'] = $projectCategoryId;
		$data = $this->where($map)->select();
		return $data;
	}

	/**
	 * 根据周期ID获取相关数据信息
	 * @param  int $cycleId 周期ID
	 * @return array          二维数据或empty
	 */
	public function getListsByCycleId($cycleId)
	{
		$cycleId = (int)$cycleId;
		$map['cycle_id'] = $cycleId;
		return $this->where($map)->page($this->p,$this->pageSize)->select();
	}

	/**
	 * 根据周期ID获取相关数据信息
	 * @param  int $cycleId 周期ID
	 * @return array          二维数据或empty
	 */
	public function getAllListsByCycleId($cycleId)
	{
		$cycleId = (int)$cycleId;
		if($cycleId == 0)
		{
			$this->error = "传入的cycleId值为0";
			return flase;
		}
		$map['cycle_id'] = $cycleId;
		return $this->where($map)->select();
	}

	public function getListsByUserIdCycleId($userId , $cycleId)
	{
		$userId = (int)$userId;
		$cycleId = (int)$cycleId;
		$map['user_id'] = $useId;
		$map['cycle_id'] = $cycleId;
		$return = $this->where($map)->page($this->p,$this->page)->order($this->order)->select();
		return $return;
	}

	public function getListById($id)
	{
		$id = (int)$id;
		$map['id'] = $id;
		$return = $this->where($map)->find();
		if($return == null)
		{
			$this->error = "ID为$id的纪录未找到";
		}
		return $return;
	}
}