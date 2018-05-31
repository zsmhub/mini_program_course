<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** @name Mod 
** @ahthor
** @desc 模块管理
**/
class Mod extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('admin/Model_mod');
	}
	
	/**
	*模块列表
	*author:
	*/
	public function modlist(){		
		$modfiles = $this->Model_mod->getModels();
		$this->smarty->assign('modfiles',$modfiles);
		$tabarr = array(
			0=>array('c'=>'mod','a'=>'addmodel','d'=>'admin','q'=>''),
			1=>array('c'=>'mod','a'=>'add_dir','d'=>'admin','q'=>''),
		);
		foreach ($tabarr as $arr){
			if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
			$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
		}
		$this->smarty->assign('tablink',$tablink);
		$this->smarty->display('mod_list.htm');
	}

	/**
	*添加模块
	*author:
	*/
	public function addmodel(){
		$action = $this->input->post('action');
		if( $action != 'save' ){
			$dirs = $this->Model_mod->getModelDirs();
			$this->smarty->assign('dirs',$dirs);
			$this->smarty->assign('action','save');
			$tabarr = array(
				0=>array('c'=>'mod','a'=>'modlist','d'=>'admin','q'=>''),
				1=>array('c'=>'mod','a'=>'add_dir','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
			$this->smarty->assign('tablink',$tablink);
			$this->smarty->display('model_add.htm');
		}else{
			$data['Dir'] = $this->get_input('Dir','post');
			$data['ModelEname'] = $this->get_input('ModelEname','post');
			$data['ModelCname'] = $this->get_input('ModelCname','post');
			if( ($langKey = $this->Model_mod->add_model($data)) !==true ){
				$this->msg_lang($langKey);
			}else{
				$this->msg_lang('doSuccess','',200,geturl('mod','modlist','admin'));
			}
		}
	}

	/**
	*添加功能
	*author:
	*/
	public function addfunc(){
		$action = $this->get_input('action','post');
		if( $action !='save' ){
			$mod_name = $this->get_input('mod','get');
			$mod_dir = $this->get_input('dir','get');
			$modfiles = $this->Model_mod->getModels();
			$this->smarty->assign('ModelList',$modfiles);
			$this->smarty->assign('mod_name',$mod_name);
			$this->smarty->assign('mod_dir',$mod_dir);
			$this->smarty->assign('Access','public');
			$this->smarty->assign('action','save');
			$tabarr = array(
				0=>array('c'=>'mod','a'=>'modlist','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
			$this->smarty->assign('tablink',$tablink);
			$this->smarty->display('model_method_add.htm');
		}else{
			$data['ModelEname'] = $this->get_input('ModelEname','post');
			$data['ModelDir'] = $this->get_input('ModelDir','post');
			$data['ActionEname'] = $this->get_input('ActionEname','post');
			$data['ActionCname'] = $this->get_input('ActionCname','post');
			$data['Access'] = $this->get_input('Access','post');
			if( ($langKey = $this->Model_mod->addfunc($data)) !==true ){
				$this->msg_lang($langKey);
			}else{
				$this->msg_lang('doSuccess','',200,geturl('mod','methods','admin','mod='.$data['ModelEname'].'&dir='.$data['ModelDir']));
			}
		}
	}

	/**
	*功能列表
	*author:
	*/
	public function methods(){
		$mod_name = $this->get_input('mod','get');
		$mod_dir = $this->get_input('dir','get');
		if( empty($mod_name) ) show_errmsg('缺少参数');
		$methods = $this->Model_mod->getMethods($mod_name,$mod_dir);
		$this->smarty->assign('methods',$methods);
		$tabarr = array(
			0=>array('c'=>'mod','a'=>'addfunc','d'=>'admin','q'=>'mod='.$mod_name.'&dir='.$mod_dir),
		);
		foreach ($tabarr as $arr){
			if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
			$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
		}
		$this->smarty->assign('tablink',$tablink);
		$this->smarty->display('method_list.htm');
	}

	/**
	*新建模块目录
	*author:
	*/
	public function add_dir(){
		$action = $this->get_input('action');
		if( $action != 'save' ){
			$this->smarty->assign('basedir',APPPATH.'models/');
			$this->smarty->assign('action','save');
			$tabarr = array(
				0=>array('c'=>'mod','a'=>'modlist','d'=>'admin','q'=>''),
			);
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
			$this->smarty->assign('tablink',$tablink);
			$this->smarty->display('model_dir_add.htm');
		}else{
			$Dir = $this->get_input('Dir','post','trim');
			if( ($langKey = $this->Model_mod->add_dir($Dir)) !== true ){
				$this->msg_lang($langKey);
			}else{
				$this->msg_lang('doSuccess','',200,geturl('mod','modlist','admin'));
			}
		}
	}
}

/* End of file mod.php */
/* Location: application/controllers/admin/mod.php */