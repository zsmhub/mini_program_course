<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends MY_Controller {
    private $url_index;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/Ctrl_mod');
		$this->load->model('admin/Menu_mod');

        $this->url_index = geturl('menu', 'menulist', 'admin');
	}

    /**
     *菜单列表
     *author:
     */
    public function menulist(){
        $assign['catelist'] = $this->Menu_mod->getMenuCat();
        $assign['menulist'] = $this->Menu_mod->getMenuByCid();
        $tabarr = array(
            0=>array('c'=>'menu','a'=>'add_cat','d'=>'admin'),
        );
        foreach ($tabarr as $arr){
            if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
            $assign['tablink'][] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
        }
        $this->load->view('admin/menu_list', $assign);
    }
	
	public function add_menu(){
		$action = $this->input->post('action');
		if( $action != 'save' ){
			$ParentId = $this->get_input('cid','get','intval');
			$contrlConfig = $this->Ctrl_mod->getConfig();
			$catlist = $this->Menu_mod->getMenuCat();
			$tabarr = array(
				0=>array('c'=>'menu','a'=>'menulist','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
            $assign = array(
                'info' => array('ParentId'=>$ParentId,'Status'=>1),
                'contrllist' => $contrlConfig,
                'catlist' => $catlist,
                'action' => 'save',
                'tablink' => $tablink
            );
            $this->load->view('admin/menu_edit', $assign);
		}else{
			$data = array();
			$data['ParentId'] = $this->get_input('ParentId','post','intval');
			$data['Title'] = $this->get_input('Title','post','htmlspecialchars');
			$data['LinkInfo'] = $this->get_input('LinkInfo','post');
			$data['Sort'] = $this->get_input('Sort','post','intval');
			$data['Status'] = $this->get_input('Status','post','intval');
			if( ($langKey = $this->Menu_mod->doMenu($data)) !==true ){
				$this->msg_lang($langKey, '', $this->url_index);
			}else{
				$this->msg_lang('addOk', '', $this->url_index);
			}
		}
	}
	
	public function edit_menu(){
		$action = $this->get_input('action','post');
		if( $action != 'save' ){
			$Id = $this->get_input('Id','get','intval');
			$info = $this->Menu_mod->getMenu($Id);
			$contrlConfig = $this->Ctrl_mod->getConfig();
			$catlist = $this->Menu_mod->getMenuCat();
			$tabarr = array(
				0=>array('c'=>'menu','a'=>'menulist','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
            $assign = array(
                'info' => $info,
                'contrllist' => $contrlConfig,
                'catlist' => $catlist,
                'action' => 'save',
                'tablink' => $tablink
            );
            $this->load->view('admin/menu_edit', $assign);
		}else{
			$data = array();
			$data['Id'] = $this->get_input('Id','post','intval');
			$data['ParentId'] = $this->get_input('ParentId','post','intval');
			$data['Title'] = $this->get_input('Title','post','htmlspecialchars');
			$data['LinkInfo'] = $this->get_input('LinkInfo','post');
			$data['Sort'] = $this->get_input('Sort','post','intval');
			$data['Status'] = $this->get_input('Status','post','intval');
			if( !$data['Id'] ) $this->msg_lang('ParamsError', '', $this->url_index);
			if( ($langKey = $this->Menu_mod->doMenu($data)) !==true ){
				$this->msg_lang($langKey, '', $this->url_index);
			}else{
				$this->msg_lang('editOk', '', $this->url_index);
			}
		}
	}
	
	public function add_cat(){
		$action = $this->input->post('action');
		if( $action != 'save' ){
			$tabarr = array(
				0=>array('c'=>'menu','a'=>'menulist','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
            $assign['info'] = array('Status'=>1,'Sort'=>0);
            $assign['action'] = 'save';
            $assign['tablink'] = $tablink;
            $this->load->view('admin/menu_edit_cat', $assign);
		}else{
			$data['Title'] = $this->get_input('Title','post','trim');
			$data['Status'] = $this->get_input('Status','post','intval');
			$data['Sort'] = $this->get_input('Sort','post','intval');
			$data['icon'] = $this->get_input('icon','post','trim');
			if( empty($data['Title']) ) $this->msg_lang('ClassNameIsNull', '', $this->url_index);
			if( ($langKey = $this->Menu_mod->doMenuCat($data)) !==true ){
				$this->msg_lang($langKey, '', $this->url_index);
			}else{
				$this->msg_lang('addOk', '', $this->url_index);
			}
		}
	}
	
	/**
	*修改分类
	*author:loong<梁龙>
	*/
	public function edit_cat(){
		$action = $this->get_input('action','post');
		if( $action != 'save' ){
			$cid = $this->get_input('cid','get','intval');
			if(!$catinfo = $this->Menu_mod->getMenuCat($cid)) $this->msg_lang('MenuClassNotExists', '', $this->url_index);
			$tabarr = array(
				0=>array('c'=>'menu','a'=>'menulist','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
            $assign['info'] = $catinfo;
            $assign['action'] = 'save';
            $assign['tablink'] = $tablink;
            $this->load->view('admin/menu_edit_cat', $assign);
		}else{
			$data['Id'] = $this->get_input('Id','post','intval');
			$data['Title'] = $this->get_input('Title','post','trim');
			$data['Status'] = $this->get_input('Status','post','intval');
			$data['Sort'] = $this->get_input('Sort','post','intval');
			$data['icon'] = $this->get_input('icon','post','trim');
			if( ($langKey = $this->Menu_mod->doMenuCat($data)) !== true ){
				$this->msg_lang($langKey, '', $this->url_index);
			}else{
				$this->msg_lang('editOk', '', $this->url_index);
			}
		}
	}

	/**
	*菜单删除
	*author:
	*/
	public function delete(){
		$id = $this->get_input('id','get','intval');
		if( ($langKey = $this->Menu_mod->delete($id)) !== true ){
			$this->msg_lang($langKey, '', $this->url_index);
		}else{
			$this->msg_lang('doSuccess', '', $this->url_index);
		}
	}
}

/* End of file menu.php */
/* Location: ./application/controllers/menu.php */