<?php
/**
 * 工作流日志
 * 用于记录当前流程处理人员的处理信息
 * panjie 3792535@qq.com
 */
namespace WorkflowLog\Model;
use Think\Model;
class WorkflowLogModel extends Model
{
	protected $totalCount = 0; //记录数据总数
	//设置自动完成规则
	protected $_auto = array(
		array('time','time',3,'function'),//在新增和编辑时，对time字段回调time()方法
		);

	public function getTotalCount()
	{
		return $this->totalCount;
	}
	/**
	 * 通过userId获取待办的数据
	 * @param  [type] $userId [用户ID]
	 * @return [type]         [包括所有信息的二维数组 ]
	 */
	public function getTodoListsByUserId($userId = null)
	{
		if($userId === null)
			return false;
		$map["user_id"] = $userId;
		$map["is_commited"] = 0; //用户未提交(待办或是在办)
		$map["is_clicked"] = 0; //未点击(待办)
		$map["is_shelved"] = 0; //未搁置
		$return = $this->page($this->p,$this->pageSize)->where($map)->select();

		return $return;
	}
	/**
	 * 通过以下关键字获取列表信息.最后改为更灵活的数组传值。
	 * @param  nubmer $user_id     用户关键字
	 * @param  number $is_clicked  是否被点击。0为未点击，待办。
	 * @param  number $is_commited 是否已办。1为已办。
	 * @param  nubmer $is_shelved  是否被搁置。1为搁置。
	 * @return array              二维LIST列表
	 */
	public function getListsByUserIdIsClickIsCommitedIsShelved($user_id,$map = array())
	{
		$where['user_id'] = $user_id;

		if(isset($map['isClicked']))
		{
			$where["is_clicked"] = $map['isClicked'];
		}	
		if(isset($map['isCommited']))
		{
			$where['is_commited'] = $map['isCommited'];
		}
		if(isset($map['isShelved']))
		{
			$where['is_shelved'] = $map['isShelved'];
		}
		$this->totalCount = $this->where($where)->count();
		return $this->page($this->p,$this->pageSize)->order('time desc')->where($where)->select();
	}
	/**
	 * 依据工作流ID,获取此工作流下的所有工作流日志信息
	 * @param  [string] $workflowId [工作流ID]
	 * @return [array]             [所以关于此工作流的留痕]
	 */
	public function getListsByWorkflowId($workflowId = null)
	{
		if($workflowId === null)
		{
			$this->error = "未传入正确的ID值";
			return false;
		}
		$map['workflow_id'] = $workflowId;
		$return = $this->where($map)->select();
		return $return;
	}

	/**
	 * 通过ID，设置已读
	 * @param number $id 
	 * 成功:true;
	 * 失败:false;
	 */
	public function setIsClickedById($id = null)
	{
		if(!is_numeric($id))
		{
			$this->error ="传入id有误";
			return false;
		}

		$data['id'] = $id;
		$data['is_clicked'] = '1';
		$this->data($data)->save();
		return true;
	}
	/**
	 * 通过ID获取基本信息	
	 * @param  number $id 
	 * @return  无结果false
	 */
	public function getListById($id = null)
	{
		if(!is_numeric($id))
		{
			$this->error = "参数不正确";
			return false;
		}

		$map['id'] = $id;
		$data = $this->where($map)->find();
		if($data == null)
		{
			$this->error = "记录未找到";
			return false;
		}
		return $data;
	}
	/**
	 * 通过workflowid 与is_commit的值来获取信息
	 * @var string
	 */
	public function getListByWorkflowIdAndIsCommit($workflowId = null , $isCommited = '0')
	{
		if(!is_numeric($workflowId) || !is_numeric($isCommited))
		{
			$this->error = "未传入正确的值";
		}
		$map['workflow_id'] = $workflowId;
		$map['is_commited'] = $isCommited;
		return $this->where($map)->find();
	}

	/**
	 * 查找首结点信息
	 * @param  string $id id
	 * @return 该结点链表的首结点     array
	 */
	public function getStartListById($id)
	{
		do
		{
			$map = array();
			$map['id'] = $id;
			$data = $this->where($map)->find();
			if($data == null)
			{
				$this->error = "查找结点时发生错误，该原因可以是存结点表时发生的，也可能是用户传入了错误的数据造成的";
				return false;
			}
			$id = $data['pre_id'];
		}
		while($id != 0);
		return $data;
	}

	/**
	 * 获取工作流的待办信息
	 * @param  int $workflowId 工作流ＩＤ
	 * @return array             一维数组
	 */
	public function getTodoListByWorkflowId($workflowId)
	{
		$map['workflow_id']	= $workflowId;
		$map['is_commited'] = 0;
		return $this->where($map)->find();
	}
}