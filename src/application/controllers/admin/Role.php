<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** @name role 
** @ahthor
** @desc 角色授权
**/
class Role extends MY_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/Ctrl_mod');
		$this->load->model('admin/User_mod');
	}

	/**
	*角色列表
	*author:
	*/
	public function rolelist(){
        $action = $this->get_input('action');
        if(empty($action)) {
            $this->load->view('admin/role_list');
        } elseif($action == 'getData') {
            $search = '';
            $search_a = $this->get_input('search_a', '', 'trim');
            if( !empty($search_a)) {
                $search .= " and Name like '%{$search_a}%'";
            }

            $params = array(
                'where' => $search
            );
            $data = $this->User_mod->role_list($params);
            echo json_encode($data);
        }
	}

	/**
	*新增角色
	*author:
	*/
	public function addrole(){
        $action = $this->get_input('action','post');
        if( $action == 'save' ){
            $data = array();
            $data['Name'] = $this->get_input('Name','post','trim');
            $data['Intro'] = $this->get_input('Intro','post');
            $data['Permissions'] = $this->get_input('Permissions','post');
            $data['Status'] = $this->get_input('Status','post','intval');

            $msg = '';
            if( $data['Name']=='' ) $msg = $this->lang('RoleNameIsNull');
            if( $data['Intro']=='' ) $msg = $this->lang('IntroIsNull');
            if( !is_array($data['Permissions']) || empty($data['Permissions']) ){
                $msg = $this->lang('PermiIsNull');
            }

            $jump_url = geturl('role', 'rolelist', 'admin');
            if($msg != '') {
                $this->msg($msg, $jump_url);
            } else {
                if( ($errkey = $this->User_mod->add_role($data)) !== true ){
                    $this->msg($this->lang($errkey), $jump_url);
                } else {
                    $this->msg($this->lang('addRoleOk'), $jump_url);
                }
            }
        } else {
            $assign['ctrl_list'] =  $this->Ctrl_mod->getConfig();
            $assign['action'] = 'save';
            $this->load->view('admin/role_edit', $assign);
        }
	}

	/**
	*角色修改
	*author:
	*/
	public function editrole(){
		$action = $this->get_input('action','post');

        if($action == 'save') {
            $data = array();
            $data['Id'] = $this->get_input('Id','post','intval');
            $data['Name'] = $this->get_input('Name','post','trim');
            $data['Intro'] = $this->get_input('Intro','post');
            $data['Permissions'] = $this->get_input('Permissions','post');
            $data['Status'] = $this->get_input('Status','post','intval');

            $msg = '';
            if( !$data['Id'] ) $msg = $this->lang('ParamsErr');
            if( $data['Name']=='' ) $msg = $this->lang('RoleNameIsNull');
            if( $data['Intro']=='' ) $msg = $this->lang('IntroIsNull');
            if( !is_array($data['Permissions']) || empty($data['Permissions']) ){
                $msg = $this->lang('PermiIsNull');
            }

            $jump_url = geturl('role', 'rolelist', 'admin');
            if($msg != '') {
                $this->msg($msg, $jump_url);
            } else {
                if( ($errkey = $this->User_mod->edit_role($data)) !== true ){
                    $this->msg($this->lang($errkey), $jump_url);
                }else{
                    $this->msg($this->lang('editRoleOk'), $jump_url);
                }
            }
        } else {
            $id = $this->get_input('id','get', 'intval');
            $assign['info'] = $this->User_mod->get_role($id);
            $assign['ctrl_list'] =  $this->Ctrl_mod->getConfig();
            $assign['action'] = 'save';
            $this->load->view('admin/role_edit', $assign);
        }
	}

	/**
	*删除角色
	*author:<>
	*/
	public function delete(){
		$Id = $this->get_input('id','get','intval');
        $jump_url = geturl('role', 'rolelist', 'admin');

		if( $Id < 1 ) $this->msg($this->lang('ParamsErr'), $jump_url);

		if( ($errkey = $this->User_mod->delete_role($Id)) !== true ){
			$this->msg($this->lang($errkey), $jump_url);
		}else{
            $this->msg($this->lang('deletedOk'), $jump_url);
		}
	}
}

/* End of file Role.php */
/* Location: application/controllers/admin/role.php */