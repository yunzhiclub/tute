<?php
/**
 * 数据管理－－服务育人数据管理
 * panjie
 * 3792535
 * 2015.9.22
 */
namespace DataManage\Controller;
class PostServiceEducationController extends IndexController
{
	public function indexAction()
	{
		try
		{
			parent::indexAction();

			$this->assign("js",$this->fetch('Index/indexJs'));
			$this->assign("YZBODY",$this->fetch('Index/index'));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}