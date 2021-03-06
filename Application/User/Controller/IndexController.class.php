<?php
/*教工管理模块
 * author:weijingyun
 * email:1028193951@qq.com
 * create date:2015.07.16
 */
namespace User\Controller;
use Admin\Controller\AdminController;
use User\Model\UserModel;
use Role\Model\RoleModel;
use Department\Model\DepartmentModel;
use Post\Model\PostModel;
use DepartmentPost\Model\DepartmentPostModel;
use UserRole\Model\UserRoleModel;
use UserDepartmentPost\Model\UserDepartmentPostModel;
use UserRole\Logic\UserRoleLogic;                               //用户－角色
use UserDepartmentPost\Logic\UserDepartmentPostLogic;           //用户－部门－岗位　表
use User\Logic\UserLogic;                                       //用户
class IndexController extends AdminController {

    //教工列表显示
    public function indexAction(){
        //获取教工列表
        $staffModel = new UserModel();
        $staffList = $staffModel -> getStaffList();
        $staffList = $this -> _addurl($staffList,'_url');
        //dump($staffList);
        $totalCount = $staffModel->getTotalCount();
        $this->assign('totalCount',$totalCount);
        $this->assign('staffList',$staffList);
        $this->assign('js',$this->fetch("indexJs"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //教工管理添加教工
    public function addAction(){
        //传值，前台进行处理
        $url = U('save',I('get.'));
        $this->assign('url',$url);

        //传递角色列表（添加教工的角色复选框）
        $this -> assign('roleList',$this -> _fetchRoleList());

        //传递部门-岗位列表（添加教工页面的部门-岗位下拉选框。要求：二级联动）
        $this -> assign('departmentPostList',$this -> _fetchDepartmentPostList());
        $this -> assign('css',$this->fetch("addCss"));
        $this->assign('js',$this->fetch("addJs"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教工管理编辑
    public function editAction(){
        $id = I('get.id');
        //获取当前教工的信息
        $staffModel = new UserModel();
        $staffInfo = $staffModel -> getStaffById($id);
        $this ->assign('staffInfo',$staffInfo);

        $this -> assign('hasRole',$staffInfo['Role']);

        //设置url
        $url = U('update?id='.$id , I('get.'));
        $this->assign('url',$url);

        //传递该用户已有的职位
        $UserDepartmentPostM = new UserDepartmentPostModel();
        $userDepartmentPosts = $UserDepartmentPostM->getDepartmentPostInfoListsById($id);
        // dump($userDepartmentPosts);
        $this -> assign('userDepartmentPosts',$userDepartmentPosts);
        // var_dump($userDepartmentPosts);
        
        //传递角色列表（编辑教工的角色复选框）
        $this -> assign('roleList',$this -> _fetchRoleList());
        //传递部门-岗位列表（添加教工页面的部门-岗位下拉选框。要求：二级联动）
        $this -> assign('departmentPostList',$this -> _fetchDepartmentPostList());
        // var_dump($this -> _fetchDepartmentPostList());
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('js',$this->fetch("addJs"));

        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //删除教工
    public function deleteAction(){
        try
        {
            //获取用户ＩＤ
            $userId = (int)I('get.id');

            //删除该教工与角色的对应信息
            $UserRoleL = new UserRoleLogic();
            $UserRoleL->deleteByUserId($userId);

            //删除该教工与部门岗位的对应信息
            $UserDepartmentPostL = new UserDepartmentPostLogic();
            $UserDepartmentPostL = $UserDepartmentPostL->deleteByUserId($userId);
            
            //删除该教工
            $UserL = new UserLogic();
            $UserL = $UserL->deleteById($userId);
            
            $this->success('删除成功', U('index?id=',I('get.')));
        }
        catch(\Think\Exception $e)
        {
            $this->error = $e->getMessage();
            $this->_empty();
        }
        
    }
    //添加教工完成
    public function saveAction(){

        //添加教工信息（UserModel）
        $staffModel = new UserModel();
        $staffModel -> addStaff();

        if(count($errors = $staffModel->getErrors()) !== 0)
        {
            $error = implode('<br />' , $errors);
            $this->error("新增错误,信息：$error" , 'index' );
            return;
        }


        //添加教工-角色信息(RoleUserModel)
        $roleUserModel = new UserRoleModel;
        $roleUserModel -> addRoleUser();
        
        //添加教工-部门岗位信息(UserDepartmentPostModel)
        //
        $this->success('新增成功', U('index',I('get.')));
    }
    //编辑教工完成
    public function updateAction(){
        
        $data = I('post.');
        //保存教工信息（UserModel）
        $staffModel = new UserModel();
        $state = $staffModel -> updateStaff();

        //保存教工-角色信息(RoleUserModel)
        $roleUserModel = new UserRoleModel;
        $roleUserModel -> saveRoleUser();

        //保存教工-部门岗位信息(UserDepartmentPostModel)
        if($state){
            $this->success('修改成功', U('index?id=', I('get.')));
        }
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
                'edit'=>U('edit?id='.$value['id'] , I('get.')),
                'delete'=>U('delete?id='.$value['id'] , I('get.')),
                'resetPassword'=>U('resetPassword?id='.$value['id'] , I('get.')),
                );
        }
        return $data;
    }

    public function resetPasswordAction(){
        $userId = I('get.id');
        $userM = new UserModel;
        $state = $userM -> resetPassword($userId);
        if($state){
            $url = U('index?id=',I('get.'));
            $this ->success('您的密码已重置，新密码为:'.C(DEFAULT_PASSWORD),$url);
        }
    }

    //修改用户邮箱，手机号，密码等个人信息
    public function changeUserInfoAction(){
        $model = new UserModel;
        $newpsw = I('post.newpsw');
        $oldpsw = I('post.oldpsw');
        $userId = I('get.id');
        //先进行密码验证
        if($model->checkPsw($oldpsw,$userId)){
            $this->error('密码错误，请重新输入');
        }
        //进行分类，如果新密码为空，我们认为是仅修改邮箱或手机
        //不为空，我们认为修改密码
        if($model->changePhoneOrEmail($userId)||$model->changePsw($newpsw,$userId)){
                $this->success('修改信息成功');
        }
    }
    /**
     * [_fetchRoleList 获取角色列表传递到前台]
     * @return [type] [角色列表]
     */
    public function _fetchRoleList(){
        $roleModel = new RoleModel;
        $roleList = $roleModel -> getAllLists();
        return $roleList;
    }

    /**
     * [_fetchDepartmentPostList 获取 到部门-岗位信息]
     * 其中部门下有多个岗位，没有岗位的部门不包括在该列表内
     * 岗位信息在key为_post下，信息只包含岗位名；（尽量减少数组维度）
     * _post下的key为部门-岗位id;
     * @return [type] [部门-岗位列表]
     * xuao 295184686@qq.com
     */

    public function _fetchDepartmentPostList(){
        //通过部门Model获取所有部门列表
        $departmentModel = new DepartmentModel;

        $departmentTree = $departmentModel -> getDepartmentTree(0,2,'_son');
        $departmentList = tree_to_list($departmentTree,1,'_son','_level','order');
        //$departmentList = change_key($departmentList,'id');
        //通过部门-岗位Model获取所有部门和它的下属岗位id列表
        $departmentPostModel = new DepartmentPostModel;
        $postModel = new PostModel;

        $departmentPostList = $departmentList;
        foreach ($departmentList as $key => $value) {
            $postId = $departmentPostModel -> getDepartmentPostInfoByDepartId($value['id']);
            //判断该部门的岗位信息是否为空；如果为空，从数组中删除该信息
            if($postId){
                //通过岗位Model获取所有部门和它的下属岗位信息列表
                $postNameList  = array();
                foreach ($postId as $key1 => $value1) {
                    $postInfo = $postModel -> getPostInfoById($value1['post_id']);
                    $postNameList[$value1['id']] = $postInfo['name'];
                }
                $departmentPostList[$key]['_post'] = $postNameList;
            }else{
                //从数组中删除该信息
                unset($departmentPostList[$key]);
            }
        }
        return $departmentPostList;
    }

}