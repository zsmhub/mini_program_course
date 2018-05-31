<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {
	/**
	*后台首页
	*author:
	*/
	public function index(){
        $assign['menu'] = $this->show_menu();
        $assign['top'] = $this->show_top();
        $this->load->view('admin/index', $assign);
	}

    /**
     * 用户信息
     *
     * @return mixed
     */
	private function show_top(){
		$this->load->model('admin/Menu_mod');
		$assign['NICKNAME'] = $this->userinfo['NickName'];
		$assign['ROLENAME'] = $this->userinfo['RoleName'];
		$assign['LASTLOGTIME'] = date('Y-m-d H:i:s',$this->userinfo['LastLogTime']);
		$assign['LASTLOGIP'] = $this->userinfo['LastLogIP'];
		return $assign;
	}

    /**
     * 获取导航菜单
     * @return array
     */
	private function show_menu(){
		$this->load->model('admin/Menu_mod');
        $menu['cat'] = $this->Menu_mod->get_user_cat($this->userinfo['RoleId']);
        $menu['list'] = $this->Menu_mod->get_user_menu($this->userinfo['RoleId']);
        return $menu;
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */