<?php
/*
 * 所有controller都需要继承该类
 * 在本类中，将进行用户的权限验主，统一设置模板等操作。
 * author:panjie joinpan@gmail.com
 */

namespace Admin\Controller;
use Think\Controller;
use Menu\Model\MenuModel;
class AdminController extends Controller{
    private $cssArr = null; //css
    private $jsArr = null; //js
    private $cdnJsArr = null; //js for cdn
    private $ie9CdnJsArr = null; //js for ie9
    
    public function addCss($css){
        $this->cssArr[] = $css;
    }
    
    public function addJs($js){
        $this->jsArr[] = $js;
    }
    
    public function addCdnJs($cdnJs){
        $this->cdnJsArr[] = $cdnJs;
    }
    
    public function addIe9CdnJs($ie9CdnJs){
        $this->ie9CdnJsArr[] = $ie9CdnJs;
    }
     /**
     * 判读是否存在该菜单
     * 如果不存在，跳转404界面
     */
    public function _empty(){
        $this->redirect( 'Fail/Index/fail');
        exit();
    }
    public function __construct() {
        parent::__construct();
        $cssArr[] = '/css/bootstrap.min.css';
        $cssArr[] = '/css/font-awesome.min.css';
        $cssArr[] = '/css/ionicons.min.css';
        $cssArr[] = '/css/morris/morris.css';
        $cssArr[] = '/css/jvectormap/jquery-jvectormap-1.2.2.css';
        $cssArr[] = '/css/fullcalendar/fullcalendar.css';
        $cssArr[] = '/css/daterangepicker/daterangepicker-bs3.css';
        $cssArr[] = '/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css';
        $cssArr[] = '/css/AdminLTE.css';
        $cssArr[] = '/css/select2/select2.min.css';
        
        $jsArr[] = '/js/jquery-2.1.4.min.js';
        $jsArr[] = '/js/jquery-ui-1.10.3.min.js';   
        $jsArr[] = '/js/bootstrap.min.js';
        $jsArr[] = '/js/plugins/morris/morris.min.js';
        $jsArr[] = '/js/plugins/sparkline/jquery.sparkline.min.js';
        $jsArr[] = '/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js';
        $jsArr[] = '/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js';
        $jsArr[] = '/js/plugins/fullcalendar/fullcalendar.min.js';
        $jsArr[] = '/js/plugins/jqueryKnob/jquery.knob.js';
        $jsArr[] = '/js/plugins/daterangepicker/daterangepicker.js';
        $jsArr[] = '/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js';
        $jsArr[] = '/js/plugins/iCheck/icheck.min.js';
        $jsArr[] = '/js/AdminLTE/app.js';
        $jsArr[] = '/js/AdminLTE/dashboard.js';
        $jsArr[] = '/js/select2/select2.min.js';
        
        
        $cdnJsArr[] = '//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js';
        
        $ie9CdnJsArr[] = 'https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js';
        $ie9CdnJsArr[] = 'https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js';
        
        $this->assign('ie9CdnJsArr',$ie9CdnJsArr);
        $this->assign('cdnJsArr',$cdnJsArr);
        $this->assign('cssArr',$cssArr);
        $this->assign('jsArr',$jsArr);
        $this->assign('YZBodyClass','skin-blue wysihtml5-supported  pace-done');
        
        //判断是否已经登录
        
        //开始进行菜单访问权限判断
        //1.获取用户点击或输入的url
        $url = $this->_getUrl();
        //2.判读该用户是否有该权限
        $isJump = $this->_checkUrl($url);
        //3.判读该url是否有该菜单（判断应用位运算）
        
        // $menuMo = new MenuModel();
        // $isHave = $menuMo->checkMenu($url);
        // if(!($isJump && $isHave)){
        //     $this->redirect( 'Fail/Index/fail');
        //     exit();
        // }
        
        //获取该用户可见的菜单列表并传递给给Left菜单栏
        //1.new菜单Model
        $menu = new MenuModel();
        //2.获取菜单的信息
        $data = $menu->getMenuTree(null, null, 1, 2);
        $data = $this->_addMenuActive($data, $url, '_son');
        //3.将获取到的信息传递给V层
        $this->assign('leftMenu',$data);
        
        $headerTpl = T('Admin@Admin/header');
        $this->assign('header',$this->fetch($headerTpl));
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
    }
    /**
     * 获取url信息
     * 无参数值
     * @return type 返回值是数组类型，包括module,controller,action
     * 2015年7月18日19:20:06
     */
    private function _getUrl(){
        //获取url
        $url = array();
        $url['module'] = MODULE_NAME;
        $url['controller'] = CONTROLLER_NAME;
        $url['action'] = ACTION_NAME;
        return $url;
    }
    /**
     * 验证该用户是否有权限
     * @param type $url 获取到的url信息
     * @param type $userId 用户id
     * @return boolean 是否有权限进行跳转
     * 2015年7月18日19:29:51
     */
    private function _checkUrl($url,$userId){
        //进行url判断
        return true;
    }
    /**
     * 
     * @param array $arr 传入数组
     * @param array $url url数组
     * @param string $type 数组下级数组下标值
     * @return arr 添加了active的数组
     */

    private function _jumpUrl(){
        $url = U('Fail/Index/fail');
        redirect($url);
    }
    private function _addMenuActive($arr,$url,$type){
        //$arr = change_key($arr, 'id');
        $menuModel = new MenuModel();
        $res = $menuModel->getMenuByUrl($url);
        $id = $res[0]['id'];
        $parentId = $res['parent_id'];
        foreach (arr as $key => $value) {
            if($value['id'] == $id || $value['id'] == $parentId){
                $arr['isActive'] = [1];
            }
            foreach ($value[$type] as $k => $v) {
                if($value['id'] == $id || $value['id'] == $parentId){
                $value[$type]['isActive'] = [1];
                }
            }
        }
        return $arr;
    }
}
