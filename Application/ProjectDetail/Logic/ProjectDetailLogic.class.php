<?php
/**
 * 项目扩展信息 逻辑
 */
namespace ProjectDetail\Logic;
use ProjectDetail\Model\ProjectDetailModel;
class ProjectDetailLogic extends ProjectDetailModel
{

	public function deleteByProjectId($projectId)
	{
		$map['project_id'] = (int)$projectId;
		$data = $this->where($map)->delete();
		return $data;
	}
	
	/**
	 * 获取某项目的所有设置数值。
	 * @param  [type] $projectId [description]
	 * @return [type]            [description]
	 */
	public function getListsByProjectId($projectId)
	{
		$map['project_id'] = $projectId;
		$data = $this->where($map)->select();

		$return = array();
		foreach($data as $value)
		{
			$return[$value['name']] = $value;
		}
		return $return;
	}
	public function getListByProjectId($projectId)
	{
		$map['project_id'] = $projectId;
		return $this->where($map)->find();
	}

	public function deleteById($id)
	{
		return $this->where("id = $id")->delete();
	}

	public function getListByProjectIdName($projectId , $name)
	{
		$map['project_id'] = (int)$projectId;
		$map['name'] = $name;
		$data = $this->where($map)->find();
		//echo $this->getLastSql();
		return $data;
	}
}