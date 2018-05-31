<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ctrl extends MY_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/Ctrl_mod');
	}

    /**
     *控制器列表
     *author:
     */
    public function ctrllist(){
        $action = $this->get_input('action');
        if(empty($action)) {
            $this->load->view('admin/ctrl_list');
        } elseif($action == 'getData') {
            $ctrl_list = $this->Ctrl_mod->getConfig();
            $new_list = array();
            $html = '';
            foreach($ctrl_list as $row) {
                $new_list[] = $row;
                $html .= '<option value="' . $row['IdKey'] . '">' . $row['Name'] . '</option>';
            }
            $data = array(
                'total' => count($new_list),
                'rows' => $new_list,
                'ctrl_list' => $html
            );
            echo json_encode($data);
        }
    }

    /**
     * 添加控制器
     */
	public function addcontroller(){
        $data['Dir'] = 'admin';
        $data['IdKey'] = $this->get_input('IdKey','post');
        $data['Name'] = $this->get_input('Name','post');
        $data['REV_AUTH'] = $this->get_input('REV_AUTH','post','intval');
        $data['Status'] = $this->get_input('Status','post','intval');
        if( ($langKey = $this->Ctrl_mod->doController($data)) !== true ) {
            $result = array(
                'success' => false,
                'msg' => $this->lang($langKey)
            );
        } else {
            $result = array(
                'success' => true,
                'msg' => $this->lang('addOk')
            );
        }
        echo json_encode($result);
	}

    /**
     *编辑控制器
     *author:
     */
    public function editctrl(){
        $data['Dir'] = 'admin';
        $data['IdKey'] = $this->get_input('IdKey','post');
        $data['REV_AUTH'] = $this->get_input('REV_AUTH','post','intval');
        $data['Status'] = $this->get_input('Status','post','intval');
        if( ($langKey = $this->Ctrl_mod->editctrl($data)) !== true ){
            $result = array(
                'success' => false,
                'msg' => $this->lang($langKey)
            );
        }else{
            $result = array(
                'success' => true,
                'msg' => $this->lang('editOk')
            );
        }
        echo json_encode($result);
    }

    /**
     *函数列表
     *author:
     */
    public function funclist(){
        $ctrlname = $this->get_input('ctrl_name');
        $ctrllist = $this->Ctrl_mod->getConfig();
        if( !isset($ctrllist[$ctrlname]) ){
            echo json_encode(array(
                'success' => false,
                'msg' => $this->lang('ControllerNotExists')
            ));
            exit();
        }
        $fun_list = $ctrllist[$ctrlname]['Methods'];
        $new_list = array();
        foreach($fun_list as $row) {
            $new_list[] = $row;
        }
        $data = array(
            'total' => count($new_list),
            'rows' => $new_list
        );
        echo json_encode($data);
    }

	public function addaction(){
        $data['CtrlEname'] = $this->get_input('CtrlEname','post');
        $data['ActionEname'] = $this->get_input('ActionEname','post');
        $data['ActionCname'] = $this->get_input('ActionCname','post');
        $data['Auth'] = $this->get_input('Auth','post','intval');
        $data['Status'] = $this->get_input('Status','post','intval');
        if( ($langKey = $this->Ctrl_mod->doAction($data)) !== true ) {
            $result = array(
                'success' => false,
                'msg' => $this->lang($langKey)
            );
        } else {
            $result = array(
                'success' => true,
                'msg' => $this->lang('addOk')
            );
        }
        echo json_encode($result);
	}

    /**
     *功能修改
     *author:
     */
    public function editfunc(){
        $data['Dir'] = 'admin';
        $data['ctrlname'] = $this->get_input('CtrlEname','post');
        $data['actname'] = $this->get_input('ActionEname','post');
        $data['REV_AUTH'] = $this->get_input('Auth','post','intval');
        $data['Status'] = $this->get_input('Status','post','intval');
        if( ($langKey = $this->Ctrl_mod->editfunc($data)) !== true ){
            $result = array(
                'success' => false,
                'msg' => $this->lang($langKey)
            );
        }else{
            $result = array(
                'success' => true,
                'msg' => $this->lang('editOk')
            );
        }
        echo json_encode($result);
    }

	/**
	*新建目录
	*author:
	*/
	public function add_dir(){
		$action = $this->get_input('action');
		if( $action != 'save' ){
			$this->smarty->assign('basedir',APPPATH.'controllers/');
			$this->smarty->assign('action','save');
			$tabarr = array(0=>array('c'=>'ctrl','a'=>'ctrllist','d'=>'admin'));
			foreach ($tabarr as $arr){
				if( !user_auth($arr['c'], $arr['a'],$arr['d']) ) continue;
				$tablink[] = getlink($arr['c'],$arr['a'],$arr['d'],$arr['q']);
			}
			$this->smarty->assign('tablink',$tablink);
			$this->smarty->display('ctrl_dir_add.htm');
		}else{
			$Dir = $this->get_input('Dir','post','trim');
			if( ($langKey = $this->Ctrl_mod->add_dir($Dir)) !== true ){
				$this->msg_lang($langKey);
			}else{
				$this->msg_lang('doSuccess','',200,geturl('ctrl','ctrllist','admin'));
			}
		}
	}
}

/* End of file ctrl.php */
/* Location: ./application/controllers/ctrl.php */