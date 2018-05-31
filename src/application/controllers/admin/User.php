<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 用户管理
 */
class User extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('admin/User_mod');
    }

    /**
     *账号列表
     */
    public function userlist(){
        $action = $this->get_input('action');
        if(empty($action)) {
            $assign['role_list'] = $this->User_mod->role_list(array('arr_flag' => true));
            $this->load->view('admin/user_list', $assign);
        } elseif($action == 'getData') {
            $search_a = $this->get_input('search_a', 'post', 'trim');
            $search_b = $this->get_input('search_b', 'post', 'trim');

            //模糊搜索
            $where = '';
            if($search_a !== '') {
                $where .= " and NickName like '%{$search_a}%'";
            }
            if($search_b !== '') {
                $where .= " and Email like '%{$search_b}%'";
            }

            $params = array(
                'where' => $where
            );
            $data = $this->User_mod->get_userlist($params);
            echo json_encode($data);
        }
    }

    /**
     *添加用户账号
     *author:
     */
    public function adduser(){
        $data = array();
        $data['UserName'] = $this->get_input('UserName','post','trim');
        $data['Password'] = $this->get_input('Password','post','trim');
        $data['NickName'] = $this->get_input('NickName','post','trim');
        $data['RoleId'] = $this->get_input('RoleId','post','intval');
        $data['Status'] = $this->get_input('Status','post','intval');
        $data['Email'] = $this->get_input('Email','post','trim');

        $msg = '';
        if( $data['UserName']=='' ) $msg = $this->lang('UserNameIsNull');
        if( $data['Password']=='' ) $msg = $this->lang('PassWordIsNull');
        if( $data['NickName']=='' ) $msg = $this->lang('NickNameIsNull');
        if( $data['RoleId'] < 1 ) $msg = $this->lang('RoleIdIsNull');
        if( $data['Email'] == '' ) {
            $msg = $this->lang('EmailIsNull');
        } else {
            if( !preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $data['Email'] )) {
                $msg = $this->lang('EmailFormat');
            }
        }

        if($msg != '') {
            $result = json_encode_common(false, $msg);
        } else {
            if( ($errkey = $this->User_mod->add_user($data)) !== true ){
                $result = json_encode_common(false, $this->lang($errkey));
            } else {
                $result = json_encode_common(true, $this->lang('addUserOk'));
            }
        }
        echo $result;
    }

    /**
     *用户信息编辑
     *author:
     */
    public function edit_user(){
        $data = array();
        $data['Id'] = $this->get_input('Id','','intval');
        $data['Password'] = $this->get_input('Password','post','trim');
        $data['NickName'] = $this->get_input('NickName','post','trim');
        $data['RoleId'] = $this->get_input('RoleId','post','intval');
        $data['Status'] = $this->get_input('Status','post','intval');
        $data['Email'] = $this->get_input('Email','post','trim');

        if( $data['Id']=='' ) $this->msg_lang('UserIdIsNull');
        $msg = '';
        if( $data['NickName']=='' ) $msg = $this->lang('NickNameIsNull');
        if( $data['RoleId'] < 1 ) $msg = $this->lang('RoleIdIsNull');
        if( $data['Email'] == '' ) {
            $msg = $this->lang('EmailIsNull');
        } else {
            if( !preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $data['Email'] )) {
                $msg = $this->lang('EmailFormat');
            }
        }

        if($msg != '') {
            $result = json_encode_common(false, $msg);
        } else {
            if( ($errkey = $this->User_mod->edit_user($data)) !== true ){
                $result = json_encode_common(false, $this->lang($errkey));
            } else {
                $result = json_encode_common(true, $this->lang('editUserOk'));
            }
        }
        echo $result;
    }

    /**
     *账号删除
     *author:<>
     */
    public function delete(){
        $Id = $this->get_input('id','post','intval');

        if( $Id < 1 ) {
            $result = json_encode_common(false, $this->lang('ParamsErr'));
        } else {
            if( ($errkey = $this->User_mod->delete_user($Id)) !== true ){
                $result = json_encode_common(false, $this->lang($errkey));
            } else {
                $result = json_encode_common(true, $this->lang('deletedOk'));
            }
        }
        echo $result;
    }

    /**
     *用户登陆
     *author:
     */
    public function login(){
        $username = $this->get_input('username');
        $pw = trim($this->get_input('password'));
        $assign['link'] = geturl('home', 'index', 'admin');
        $assign['url'] = geturl();

        if($username !== '' && $pw !== '') {
            $ret = $this->User_mod->check_login_pw($username, $pw);
            echo json_encode($ret);
            exit();
        }

        $this->load->view('admin/login', $assign);
    }

    /**
     *退出登录
     *author:
     */
    public function logout(){
        session_start();
        session_destroy();
        $this->redirect(geturl('user','login','admin'));
    }
}

/* End of file user.php */
/* Location: application/controllers/admin/user.php */