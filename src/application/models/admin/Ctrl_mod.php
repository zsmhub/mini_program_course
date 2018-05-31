<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Ctrl_mod extends MY_Model {
	public function __construct(){
		parent::__construct();		
	}
	
	private $Headstr = "<?php defined('BASEPATH') OR exit('No direct script access allowed');\r\n";
    private $ext = '.php';

	public function doController($Params=array()){		
		if( empty($Params['IdKey']) || !preg_match('/^[a-z_]+$/i',$Params['IdKey']) ) return 'CtrlKeyIsInvalid';
		if( empty($Params['Name']) ) return 'CtrlNameIsNull';
		if( $Params['Dir']!='' && !preg_match('/^[a-z_]+$/i',$Params['Dir']) ) return 'DirIsNull';
		if( $Params['Dir']!='' && !is_dir(APPPATH.'controllers/'.$Params['Dir']) ) return 'DirIsNotExists';
		if( $Params['Dir'] =='0' ) $Params['Dir'] = '';
		$Dir = $Params['Dir'];
		if( $Params['Dir']!='' && substr($Params['Dir'],-1)!='/' )	$Dir .= '/';
		$Params['IdKey'] = ucfirst(strtolower($Params['IdKey']));
		$file = APPPATH.'controllers/'.$Dir.$Params['IdKey'] . $this->ext;
		if( file_exists($file) ) return 'CtrlFileReadFail';	
		$controller = $Params['IdKey'];
		$str = $this->Headstr."/**\n* @name {$controller} \n* @author {$this->userinfo['UserName']}<{$this->userinfo['NickName']}>\n* @desc {$Params['Name']}\n*/\nclass ".$controller." extends MY_Controller {\r\n}\r\n\r\n";
//		$str .= "/* End of file {$Params['IdKey']}{$this->ext} */\n/* Location: $file */";
		file_put_contents($file,$str);
		$this->set_controller_config($controller,'',array(
		'Name'=>$Params['Name'],'IdKey'=>$controller,'REV_AUTH'=>$Params['REV_AUTH'],'Status'=>$Params['Status'],
		'Dir' => $Params['Dir']
		));
		$this->setLang($Dir,$controller);
		return true;
	}
	
	public function doAction($Params=array()){
		if( empty($Params['CtrlEname']) || !preg_match('/^[a-z_]+$/i',$Params['CtrlEname']) ) return 'CtrlKeyIsInvalid';
		$Config = $this->getConfig();
		if( !isset($Config[$Params['CtrlEname']]) ) return 'ControllerNotExists';
		$dir = '';
		if( !empty($Config[$Params['CtrlEname']]['Dir']) ) $dir = $Config[$Params['CtrlEname']]['Dir'].'/';	
		$CtrlFile = APPPATH.'controllers/'.$dir.$Params['CtrlEname'] . $this->ext;
		if( !file_exists($CtrlFile) ) return 'CtrlFileReadFail';
		include_once($CtrlFile);
		$controller = ucfirst($Params['CtrlEname']);
		if(!class_exists($controller))return 'CtrlFileReadFail';
//		$CtrlClass=new $controller();
		if(method_exists($controller,$Params['ActionEname']))return 'MethodIsExists';
		if( empty($Params['ActionEname']) || !preg_match('/^[a-z_]+$/i',$Params['ActionEname']) ) return 'MethodKeyIsInvalid';
		if( empty($Params['ActionCname']) ) return 'MethodDescIsNull';
		
		$s="\n\t/**\r\n\t* @todo ".$Params['ActionCname']."\r\n\t* @author {$this->userinfo['UserName']}<{$this->userinfo['NickName']}>\r\n\t*/\r\n\tpublic function {$Params['ActionEname']}(){\r\n\t}\r\n";
		$Content=file_get_contents($CtrlFile);
		if(false!==$pos=strrpos($Content,'}')){
			$Content=substr($Content,0,$pos).$s.substr($Content,$pos);
			file_put_contents($CtrlFile,$Content);
			$this->set_controller_config($controller,$Params['ActionEname'],array(
			'Name'=>$Params['ActionCname'],'IdKey'=>$Params['ActionEname'],'REV_AUTH'=>$Params['Auth'],'Status'=>$Params['Status']
			));
			return true;
		}
		return 'SystemError';
	}

	public function set_controller_config($controller,$action='',$params=array()){
		if( empty($controller) || empty($params) ) return false;
		$config = array();
		$ConfigFile = APPPATH.'config/controllerlist.php';
		if( file_exists($ConfigFile) ){
			include $ConfigFile;
		}
		if( !empty($action) ){
			if( !isset($config[$controller]) ) return false;
			if( !isset($config[$controller]['Methods'][$action]) ){
				$config[$controller]['Methods'][$action] = array(
					'Name' => $params['Name'],//中文名
					'IdKey' => $action,//标识名
					'REV_AUTH' => $params['REV_AUTH'],//是否需要验证(0:不需要验证,1:需要验证,2:继承)
					'Status' => $params['Status'],//状态 0:禁用 1:正常
				);
			}else{
				foreach ($params as $k=>$v){
					if( isset($config[$controller]['Methods'][$action][$k]) ){
						$config[$controller]['Methods'][$action][$k] = $v;
					}					
				}
			}						
		}else{
			if( !isset($config[$controller]) ){
				$config[$controller] = array(
					'Name' => $params['Name'],//中文名
					'IdKey' => $controller,//标识名
					'REV_AUTH' => $params['REV_AUTH'],//是否需要验证(0:不需要验证,1:需要验证)
					'Status' => $params['Status'],//状态 0:禁用 1:正常
					'Dir' => $params['Dir'],//控制器所在目录 默认为空
					'Methods' => array(//成员方法列表
					)
				);
			}else {
				foreach ($params as $k=>$v){
					if( isset($config[$controller][$k]) ){
						$config[$controller][$k] = $v;
					}					
				}
			}
		}
		file_put_contents($ConfigFile,"<?php\n \$config = ".var_export($config,true).";\n");
		return true;
	}
	
	private function setLang($ctrldir='',$controller){
		$config =& get_config();
		$deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
		$path = APPPATH.'language/'.$deft_lang.'/'.$ctrldir;
		if( !is_dir($path) ) create_dir($path);
		if( substr($path, -1) !='/' ) $path .= '/';
		$file = $path.strtolower($controller).'_lang.php';
		if( file_exists($file) ) return false;
		file_put_contents($file, "<?php defined('BASEPATH') OR exit('No direct script access allowed');\n\$lang = array();");
	}
	
	public function getConfig($ctrl=''){
		$this->config->load('controllerlist', TRUE, TRUE);
		$config = $this->config->item('controllerlist');
		if( !is_array($config) ) return array();
		//非本地环境去掉控制器和模型管理配置
		if( $this->input->ip_address()!='127.0.0.1' ){
			//unset($config['Ctrl'],$config['Mod']);
		}
		return $ctrl!='' ? $config[$ctrl] : $config;
	}

	/**
	*编辑控制器
	*author:
	*/
	public function editctrl($params){
		if( !isset($params['IdKey']) || empty($params['IdKey']) ) return 'CtrlKeyIsInvalid';
		if(!$ctrlconfig = $this->getConfig($params['IdKey'])) return 'ControllerNotExists';
		$this->set_controller_config($params['IdKey'],'',$params);
		return true;
	}
	
	/**
	*编辑功能
	*author:
	*/
	public function editfunc($params){
		if( !isset($params['ctrlname']) || empty($params['ctrlname']) ) return 'CtrlKeyIsInvalid';
		if(!$ctrlconfig = $this->getConfig($params['ctrlname'])) return 'ControllerNotExists';
		if( !isset($ctrlconfig['Methods'][$params['actname']]) ) return 'MethodNotExists';
		$this->set_controller_config($params['ctrlname'],$params['actname'],array('REV_AUTH'=>$params['REV_AUTH'],'Status'=>$params['Status']));
		return true;
	}

	/**
	* @todo 添加控制器目录
	* @author: loong<梁龙>
	*/
	public function add_dir($dir){
		if( empty($dir) ) return 'DirIsNull';
		if( !preg_match('/^[a-z_]+$/i',$dir) ) return 'DirIsInValid';//目录不能存在特殊字符
		$_dir = APPPATH.'controllers/'.strtolower($dir);
		if( is_dir($_dir) ) return 'DirIsExists';//目录已经存在
		return create_dir($_dir);
	}
	
	/**
	* @todo 获取控制器目录
	* @author: loong<梁龙>
	*/
	public function getCtrlDirs(){
		$dirs = array();
	    if($dir_arr = glob(APPPATH.'controllers/*',GLOB_ONLYDIR)){
	    	foreach ($dir_arr as $d){
	    		$dirs[] = substr(strrchr($d,'/'),1);
	    	}
	    }
	    return $dirs;
	}
}