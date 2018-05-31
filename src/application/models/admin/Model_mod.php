<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Model_mod extends MY_Model {
	
	private $Headstr = "<?php defined('BASEPATH') OR exit('No direct script access allowed');\r\n";
	
	/**
	 * 获取模块文件列表
	 *
	 * @return array
	 */
	public function getModels(){
		$modfiles = getDirFiles(APPPATH.'models/','*.php');
		foreach ($modfiles as $k=>$v){
			$dir = str_replace(strrchr($v,'/'),'',str_replace(APPPATH.'models/','',$v));
			$classname = ucfirst(str_replace(strstr($v,'.'),'',substr(strrchr($v,'/'),1)));
			$modfiles[$k] = array('name'=>$classname,'file'=>$v,'dir'=>$dir);
		}
		return $modfiles;
	}
	
	/**
	 * 获取模块的方法列表
	 *
	 * @param string $mod_name
	 * @param string $dir
	 * @return array
	 */
	public function getMethods($mod_name,$dir=''){
		if( $dir!='' && substr($dir,-1)!='/' ) $dir .= '/';
		$file = APPPATH.'models/'.$dir.strtolower($mod_name).EXT;
		$methods = array();
		if( file_exists($file) ){
			if( !class_exists(ucfirst($mod_name)) ){
				include $file;
			}
//			$methods = get_class_methods(ucfirst($mod_name));
			$rc = new ReflectionClass($mod_name);
			foreach($rc->getMethods() as $k=>$m){
				$methods[$k] = array('name'=>$m->name);
				if($m->isPrivate())
		            $methods[$k]['access'] = 'private';
		        elseif($m->isProtected())
		            $methods[$k]['access'] = 'protected';
		        else
		            $methods[$k]['access'] = 'public';
			}
		}
		return $methods;
	}
	
	public function add_dir($dir){
		if( empty($dir) ) return 'DirIsNull';
		if( !preg_match('/^[a-z_]+$/i',$dir) ) return 'DirIsInValid';//目录不能存在特殊字符
		$_dir = APPPATH.'models/'.$dir;
		if( is_dir($_dir) ) return 'DirIsExists';//目录已经存在
		return create_dir($_dir);
	}
	
	public function getModelDirs(){
		$dirs = array();
	    if($dir_arr = glob(APPPATH.'models/*',GLOB_ONLYDIR)){
	    	foreach ($dir_arr as $d){
	    		$dirs[] = substr(strrchr($d,'/'),1);
	    	}
	    }
	    return $dirs;
	}
	
	public function add_model($params){
		if( empty($params['ModelEname']) ) return 'ModKeyIsNull';//模块标识不能为空
		if( !empty($params['Dir']) && !preg_match('/^[a-z_]+$/i',$params['Dir']) ) return 'DirIsInValid';//非法的目录名
		if( !empty($params['Dir']) ) $params['Dir'] .= '/';
		if( !empty($params['Dir']) && !is_dir(APPPATH.'models/'.$params['Dir']) ) return 'DirIsNotExists';//目录不存在
		if( !preg_match('/^[a-z_]+$/i',$params['ModelEname']) ) return 'ModKeyIsInvalid';//非法的模块标识
		$params['ModelEname'] = strtolower($params['ModelEname']);
		if( $params['ModelCname']!='' ) $params['ModelCname'] = str_replace(array("\n","\r","\t")," ",$params['ModelCname']);
		$filename = APPPATH.'models/'.$params['Dir'].$params['ModelEname'].EXT;
		if( file_exists($filename) ) return 'ModIsExists';//模块已经存在
		$model_name = ucfirst($params['ModelEname']);
		$str = $this->Headstr."/**\n** @name {$model_name} \n** @author {$this->userinfo['UserName']}<{$this->userinfo['NickName']}>\n** @desc {$params['ModelCname']}\n**/\nclass ".$model_name." extends MY_Model {\r\n}\r\n\r\n";
		$str .= "/* End of file {$params['ModelEname']}.php */\n/* Location: $filename */";
		return file_put_contents($filename,$str)?true:'doError';
	}
	
	public function addfunc($params){
		if( empty($params['ModelEname']) || !preg_match('/^[a-z_]+$/i',$params['ModelEname']) ) return 'ModKeyIsInvalid';//模块参数有误
		if( !empty($params['ModelDir']) && substr($params['ModelDir'],-1)!='/' ) $params['ModelDir'] .= '/';
		if( !is_dir(APPPATH.'models/'.$params['ModelDir']) ) return 'DirIsNotExists';	//模块目录不存在		
		$ModelFile = APPPATH.'models/'.$params['ModelDir'].strtolower($params['ModelEname']).EXT;
		if( !file_exists($ModelFile) ) return 'ModFileNotExists';//模块文件不存在
		include_once($ModelFile);
		$model_name = ucfirst($params['ModelEname']);
		if( !class_exists($model_name) )return 'LoadModError';//加载模块出错
		if( empty($params['ActionEname']) || !preg_match('/^[a-z_]+$/i',$params['ActionEname']) ) return 'MethodIsInvalid';//功能参数有误
		$ModelClass = new $model_name();		
		if( method_exists($ModelClass,$params['ActionEname']) )return 'MethodIsExists';	//当前功能函数已经存在	
		if( !in_array($params['Access'],array('public','protected','private')) ) $params['Access'] = 'public';
		if( $params['ActionCname']!='' ) $params['ActionCname'] = str_replace(array("\n","\r","\t")," ",$params['ActionCname']);
		$s="\n\t/**\r\n\t* @todo ".$params['ActionCname']."\r\n\t* @author: {$this->userinfo['UserName']}<{$this->userinfo['NickName']}>\r\n\t*/\r\n\t{$params['Access']} function {$params['ActionEname']}(\$params=array()){\r\n\t}\r\n";
		$Content=file_get_contents($ModelFile);
		if(false!==$pos=strrpos($Content,'}')){
			$Content=substr($Content,0,$pos).$s.substr($Content,$pos);
			file_put_contents($ModelFile,$Content);
			return true;
		}
		return 'SysError';
	}

}