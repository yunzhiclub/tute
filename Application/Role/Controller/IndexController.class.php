<?php

/*
 *
 * @author xuao
 */
namespace Role\Controller;
use Admin\Controller\AdminController;
use Role\Model\RoleModel;
use Menu\Model\MenuModel;
use RoleMenu\Model\RoleMenuModel;
use UserRole\Model\UserRoleModel;
use RoleStaff\Model\RoleStaffModel;
class IndexController extends AdminController
{
    //初始化方法
    public function indexAction()
    {
        //获取角色列表
        $roleModel = new RoleModel();
        $roleList = $roleModel->getRoleList();
        $roleList = $this->_addurl($roleList,"_url");
        
        $this->assign('totalCount',$roleModel->getTotalCount());
        //传值
        $this->assign('roleList',$roleList);
        $this->assign('YZBODY',$this->fetch());
        //前台显示
        $this->display(YZTemplate);
    }
    //添加角色
    public function addRoleAction(){

        //参考私有方法_getPermissionList()获取权限信息
        $permissionList = $this->_getPermissionList();

        $menuList = $this->_treeToList($permissionList);

        //定义提交url
        $submitUrl = U('save');
        //页面显示
        $this->assign('menuList',$menuList);
        $this->assign('submitUrl',$submitUrl);
        $this->assign("js",$this->fetch("addRoleJs"));
        $this->assign("css",$this->fetch("addRoleCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }

    //编辑角色
    public function editRoleAction(){
        //获取该角色具体信息
        $id = I('get.id');
        $roleModel = new RoleModel();
        $roleInfo = $roleModel->getRoleById($id);

        //获取所有权限信息
        $permissionList = $this->_getPermissionList();
        $this->assign('permissionList',$permissionList);

        //获取已有的权限信息
        $roleMenuModel = new RoleMenuModel();
        $originalPermission = $roleMenuModel->getMenuListByRoleId($id);
        $this->assign('originalPermission',$originalPermission);

        $menuList = $this->_treeToList($permissionList);
        
        //定义提交url
        $submitUrl = U('update?id='.$id);
        $this->assign('submitUrl',$submitUrl);

        //页面显示
        $this->assign('menuList',$menuList);
        $this->assign('roleInfo',$roleInfo);
        $this->assign("js",$this->fetch("addRoleJs"));
        $this->assign("css",$this->fetch("addRoleCss"));
        $this->assign('YZBODY',$this->fetch('addRole'));

        $this->display(YZTemplate);
    }

    /**
     * 获取树状的HTML信息
     * 由于需要用到递归,将前台的处理变为后台处理.
     * @return html 
     */
    private function _treeToList($tree , $list = array() , $parentIds = array("0") )
    {  
        foreach($tree as $value)
        {   
            $value['_parents'] = $parentIds;
            array_push($list , $value);
            if(is_array($value['_son']))
            {
                array_push($parentIds , $value['id']);
                $list = $this->_treeToList($value['_son'] , $list , $parentIds);
                array_pop($parentIds);
            }
        }
        return $list;
    }
    //显示角色人员信息
    public function peopleAction(){
        $id = I('get.id');
        $roleStaffModel = new RoleStaffModel;
        $staffList = $roleStaffModel ->getInRoleStaffByRoleId($id);
        foreach ($staffList as $key => $value) {
            $staffList[$key]['_url'] = U('movePeople?roleId='.$id.'&staffId='.$value['id']);
        }
        $url = U('addPeople?id='.$id);
        $this->assign('url',$url);

        $this->assign('staffList',$staffList);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //删除角色
    public function deleteRoleAction(){
        //判断该角色下是否有用户
        $roleId = I('get.id');
        $userRoleM = new UserRoleModel;
        $hasUser = $userRoleM -> getInRoleStaffByRoleId($roleId);
        if($hasUser){
            //如果有，提示不能删除
            $url = U('index',I('get.'));
            $this -> error('该角色下有用户存在，不能删除',$url);
        }else{
            //如果没有，删除该角色
            $roleM = new RoleModel;
            $state = $roleM -> deleteRoleByRoleId($roleId);
            if($state){
                $url = U('index',I('get.'));
                $this -> success('删除成功',$url);
            }
        }
    }
    //保存
     public function updateAction(){
        //1、更新角色信息(在内部方法中给定了下一步骤所需的值，
        //该步骤不能与下一步顺序颠倒)
        $roleModel = new RoleModel();
        $roleModel->updateRole();

        //2、更新角色的权限信息
        $roleMenuModel = new RoleMenuModel();
        $roleMenuModel -> updateMenuAndRole();

       //3、界面跳转
        $url = U('index');
        $this->success('保存成功',$url);
    }
    //添加
    public function saveAction(){
        //保存角色信息
        $roleModel = new RoleModel();
        $roleModel->saveRole();

        //保存角色的权限信息
        $roleMenuModel = new RoleMenuModel();
        $roleMenuModel -> saveMenuAndRole();

        $url = U('index');
        $this->success('保存成功',$url);
    }
    //移除用户
    public function movePeopleAction(){
        $roleStaffModel = new RoleStaffModel;
        $state = $roleStaffModel->deleteRoleStaff();
        if($state){
            $url = U('people?id='.I('get.roleId'));
            $this->success('移除成功',$url);
        }
    }
    //添加用户
    public function savePeopleAction(){
        $roleStaffModel = new RoleStaffModel;
        $state = $roleStaffModel->saveRoleStaff();
        if($state){
            $url = U('people?id='.I('get.roleId'));
            $this->success('添加成功',$url);
        }
    }
    //添加教工
    public function addPeopleAction(){
        //获取没在该角色中的所有教工信息
        $id = I('get.id');
        $roleStaffModel = new RoleStaffModel;
        $outRoleStaff = $roleStaffModel -> getOutRoleStaffByRoleId($id);
        foreach ($outRoleStaff as $key => $value) {
            $outRoleStaff[$key]['_url'] = U('savePeople?staffId='.$value['id'].'&roleId='.$id);
        }

        //传值
        $this->assign('outRoleStaff',$outRoleStaff);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     * 添加url信息
     * @param  [type] 要添加的数组
     * @param  [type] 要填写的下标名
     * @return [type] 拼接好url信息的数组
     */
    private function _addurl($array,$string){
        $data = $array;
        foreach ($data as $key => $value) {
            $data[$key][$string] = array(
                'edit'=>U('editRole?id='.$value['id']),
                'delete'=>U('deleteRole?id='.$value['id']),
                'people'=>U('people?id='.$value['id']),
                );
        }
        return $data;
    }
    /**
     * 获取角色对应的权限列表（也就是菜单列表）
     */
    private function _getPermissionList(){
        $menuModel = new MenuModel();
        $permissionList = $menuModel ->getMenuTree(null, null, 0, 3); 
        return $permissionList;
    }
}
