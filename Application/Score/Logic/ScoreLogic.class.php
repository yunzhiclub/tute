<?php
/**
 * 项目用户分值占比 逻辑
 */
namespace Score\Logic;
use Score\Model\ScoreModel;		//分值 占比表
class ScoreLogic extends ScoreModel
{
	public function getAllListsByProjectId($projectId)
	{
		$map['project_id'] = (int)$projectId;

		return $this->field($field)->where($map)->select();

	}

	/**
	 * 获取当前项目的总分值 
	 * @param  int $projectId  项目ID
	 * @return arrya            一维
	 */
	public function getSumPercentByProjectId($projectId)
	{
		$field["sum(score_percent)"] = "sum_percent";
		$map['project_id'] = $projectId;
		$data =  $this->field($field)->where($map)->find();
		// echo $this->getLastSql();
		return $data;
	}

	public function addByUserIdProjectIdScorePercent($userId, $projectId , $scorePercent = 100)
	{
		if( !$data['user_id'] = (int)$userId )
		{
			$this->error = "传入了空的userId";
			return false;
		}

		if( !$data['project_id'] = (int)$projectId )
		{
			$this->error = "传入了空的projectid";
			return false;
		}

		$data['score_percent'] = (int)$scorePercent;

		if($this->create($data))
		{
			$this->add();
			return true;
		}
		else
		{
			return false;
		}
	}
}