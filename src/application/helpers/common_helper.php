<?php

//创建目录
if( !function_exists('create_dir') ){
	function create_dir($dir,$mod=0755){
		if(!is_string($dir))return false;
		$dirarr=array();
		while (!is_dir($dir)) {
			array_unshift($dirarr,$dir);
			$dir=dirname($dir);
			$char=substr($dir,-1,1);
			if($char=='/'||$char=='\\'||$char==':')break;
		}
		foreach( $dirarr as $v){
			if(!@mkdir($v))return false;
			@chmod($v, $mod);
		}
		return true;
	}
}

//删除目录
if( !function_exists('delete_dir') ){
	function delete_dir($Dir){
		if($fp=opendir($Dir)){
			while(($filename=readdir($fp))!==false){
				if($filename=="." or $filename==".."){
					continue;
				}else{
					$f1=$Dir.'/'.$filename;
					if(is_file($f1)){
						@unlink($f1);
					}elseif(is_dir($f1)){
						delete_dir($f1);
					}
				}
			}
		}
		if($fp) closedir($fp);
		return rmdir($Dir);
	}
}

//遍历目录文件
if( !function_exists('getDirFiles') ){
	function getDirFiles($dir,$match='*.*'){
		$filearr = $dir_arr = array();
	    $dir = str_replace('\\','/',$dir);
	    if( substr($dir,-1) != '/' ) $dir.= '/';
	    $dir_arr = glob($dir.'*',GLOB_ONLYDIR);
	    if( !empty($dir_arr) ){
	            foreach($dir_arr as $_dir){
	                    if( is_dir($_dir) ) {
	                    	$filearr = array_merge($filearr,getDirFiles($_dir,$match));
	                    }
	            }
	    }else {
	    	$filearr = array_merge($filearr,glob($dir.$match));
	    }
	    return $filearr;
	}
}

/**
 * 文件删除
 *
 * @return boolean
 */
if( !function_exists('rm_file')) {
    function rm_file($file) {
        return @unlink($file);
    }
}

//获取绝对路径
if( !function_exists('abspath') ){
	function abspath($relfile='') {
		$curdir = str_replace('\\','/',dirname(__FILE__));
		$curdir = str_replace(strrchr($curdir,'/'),'',$curdir);
		$rootdir = str_replace(strrchr($curdir,'/'),'',$curdir).'/';
		return $rootdir.$relfile;
	}
}

//获取URL地址
if( !function_exists('geturl') ){
	function geturl($ctrlName='',$actionName='',$DirName='',$QueryString=''){
		$url = array();
		$CI =& get_instance();
		if( $DirName=='' && $CI->VAR_D !='' ) $DirName =& $CI->VAR_D;
		if( $ctrlName=='' && $CI->VAR_C !='' ) $ctrlName =& $CI->VAR_C;
		if( $actionName=='' && $CI->VAR_A !='' ) $actionName =& $CI->VAR_A;

		if( $DirName !='' ) $url[] = $CI->config->item('directory_trigger').'='.$DirName;
		if( $ctrlName !='' ) $url[] = $CI->config->item('controller_trigger').'='.strtolower($ctrlName);
		if( $actionName !='' ) $url[] = $CI->config->item('function_trigger').'='.$actionName;
		if( $QueryString !='' ) $url[] = $QueryString;
		return 'index.php?'.implode('&',$url);
	}
}

//获取连接内容
if( !function_exists('getlink') ){
    function getlink($ctrlName,$actionName,$DirName='',$QueryString=''){
        $linkstr = array('Url'=>'','Title'=>'');
        $CI = &get_instance();
        $CI->load->model('admin/Ctrl_mod');
        $controllerConfig = $CI->Ctrl_mod->getConfig();

        if( is_array($controllerConfig)){
            $linkstr = array(
                'Url'=>geturl($ctrlName,$actionName,$DirName,$QueryString),
                'Title'=>$controllerConfig[ucfirst($ctrlName)]['Methods'][$actionName]['Name']
            );
        }
        return $linkstr;
    }
}

if( !function_exists('getFuncTitle') ){
    function getFuncTitle($c=null,$a=null,$d=null){
        $CI = &get_instance();
        $CI->load->model('admin/Ctrl_mod');
        $ctrlConfig = $CI->Ctrl_mod->getConfig();

        if( $d==null && $CI->VAR_D !='' ) $d = $CI->VAR_D;
        if( $c==null && $CI->VAR_C !='' ) $c = $CI->VAR_C;
        if( $a==null && $CI->VAR_A !='' ) $a = $CI->VAR_A;
        if( $c !='' ) $c = ucfirst($c);
        return $ctrlConfig[$c]['Methods'][$a]['Name'];
    }
}

//检查用户权限
if( !function_exists('user_auth') ){
    function user_auth($c,$a,$d=''){
        $CI =& get_instance();
        if( !is_array($CI->userinfo) || empty($CI->userinfo) || !$CI->userinfo['RoleId'] ) return false;
        return role_auth($CI->userinfo['RoleId'], $c, $a,$d);
    }
}

//获取用户权限信息
if( !function_exists('get_role') ){
    function get_role($roleid){
        static $_static_roles = array();
        if( !isset($_static_roles[$roleid]) ){
            $CI = &get_instance();
            $CI->load->model('admin/User_mod');
            $_static_roles[$roleid] = $CI->User_mod->get_role($roleid);
            if( !is_array($_static_roles[$roleid]) || $_static_roles[$roleid]['Status']!=1 ) return false;
        }
        return $_static_roles[$roleid];
    }
}

//检查用户组权限
if( !function_exists('role_auth') ){
    function role_auth($roleid,$c,$a,$d=''){
        if( $roleid < 1 ) return false;
        $CI = &get_instance();
        $CI->load->model('admin/Ctrl_mod');
        $ctrlconfig = $CI->Ctrl_mod->getConfig();

        if( $c !='' ) $c = ucfirst($c);
        if( $ctrlconfig[$c]['Status'] != 1 ) return false;
        if( !isset($ctrlconfig[$c]['Methods'][$a]) ) return false;
        if( $ctrlconfig[$c]['Methods'][$a]['Status'] != 1 ) return false;
        if( $ctrlconfig[$c]['Methods'][$a]['REV_AUTH']==0 ||
            ($ctrlconfig[$c]['Methods'][$a]['REV_AUTH']==2 && $ctrlconfig[$c]['REV_AUTH']==0) ){
            return true;//无需验证的不用判断
        }
        $_RolePermissions = get_role($roleid);
        if( !isset($_RolePermissions['Permissions'][$c][$a]) ) return false;
        return true;
    }
}

//获取当前请求地址是否需要验证
if( !function_exists('isNeedAuth') ){
    function isNeedAuth($c,$a,$d=''){
        if( $d != '' && $d != 'admin' ) return false;//非admin目录不用验证
        $CI = &get_instance();
        $CI->load->model('admin/Ctrl_mod');
        $ctrlconfig = $CI->Ctrl_mod->getConfig();
        if( !$ctrlconfig ) $ctrlconfig = array();
        if( $c !='' ) $c = ucfirst($c);
        //默认控制器不用认证
        if( $CI->router->default_controller !== false && strtolower($c) == $CI->router->default_controller ){
            return false;
        }
        if( !isset($ctrlconfig[$c]) || $ctrlconfig[$c]['Status'] == 0 ){
            show_404("{$c}/{$a}");
        }
        if( !isset($ctrlconfig[$c]['Methods'][$a]) ) return false;
        //需要验证
        if( $ctrlconfig[$c]['Methods'][$a]['REV_AUTH']==1 ||
            ($ctrlconfig[$c]['Methods'][$a]['REV_AUTH']==2 && $ctrlconfig[$c]['REV_AUTH']==1) ){
            return true;
        }
        return false;
    }
}

//获取统一时间
if( !function_exists('gettime') ){
	function gettime(){
		return time();
	}
}

//模块获取用户信息
if( !function_exists('userinfo') ){
	function userinfo($field=''){
		$CI =& get_instance();
		if( empty($CI->userinfo) ) return false;
		if( $field!='' && isset($CI->userinfo[$field]) ){
			return $CI->userinfo[$field];
		}else{
			return $CI->userinfo;
		}
	}
}

//获取语言内容
if( !function_exists('lang_str') ){
	function lang_str($langKey,$langArr=array()){
		return get_instance()->lang($langKey,$langArr);
	}
}

if( !function_exists('call_msg') ){
	function call_msg($msg,$status=500,$redirect='',$waittme=2000,$jumptop=''){
		return get_instance()->msg($msg,$status,$redirect,$waittme,$jumptop);
	}
}

if( !function_exists('call_msg_lang') ){
	function call_msg_lang($langKey,$langArr=array(),$status=500,$redirect='',$waittme=2000,$jumptop=''){
		return call_msg(lang_str($langKey,$langArr),$status,$redirect,$waittme,$jumptop);
	}
}

//调用模块的成员方法
/*if( !function_exists('call_mod_func') ){
	function call_mod_func($mod,$method,$params=array()){
		return get_instance()->CallModel($mod,$method,$params);
	}
}*/

//设置COOKIE
if( !function_exists('_set_cookie') ){
	function _set_cookie($name,$value,$expire=0){
		$prefix = '';
		if( config_item('cookie_prefix') != null ) $prefix = config_item('cookie_prefix');
		if( $name!='' ) $name = $prefix.$name;
		if( $expire > 0 ) $expire = gettime()+$expire;
		setcookie($name,$value,$expire,config_item('cookie_path'),config_item('cookie_domain'),config_item('cookie_secure'));
	}
}

//读取COOKIE
if( !function_exists('_get_cookie') ){
	function _get_cookie($name){
		$prefix = '';
		if( config_item('cookie_prefix') != null ) $prefix = config_item('cookie_prefix');
		if( $name!='' ) $name = $prefix.$name;
		return $_COOKIE[$name];
	}
}

//数字转换成EXCEL列标签
if( !function_exists('IntToChr') ){
	function IntToChr($index, $start = 65) {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= IntToChr(floor($index / 26)-1);
        }
        return $str . chr($index % 26 + $start);
    }
}

//日期格式判断
if( !function_exists('is_date') ){
	function is_date($date,$fmt='Y-m-d'){
		if( empty($date) ) return false;
		return date($fmt,strtotime($date))== $date;
	}
}

//可逆加密
if( !function_exists('encrypt') ){
	function encrypt($str,$key) {
		if( !function_exists('xxtea_encrypt') ) include APPPATH.'libraries/xxtea.php';
		return str_replace(array('=','+','/'),array(',','_','('),base64_encode(gzcompress(xxtea_encrypt(serialize($str),$key))));
	}
}

//可逆解密
if( !function_exists('decrypt') ){
	function decrypt($str,$key) {
		if( !function_exists('xxtea_decrypt') ) include APPPATH.'libraries/xxtea.php';
		return unserialize(xxtea_decrypt(gzuncompress(base64_decode(str_replace(array(',','_','('),array('=','+','/'),$str))),$key));
	}
}

/**
* 获取配置文件的参数
 */
if( !function_exists('getconfig') ){
    function getconfig($cfname,$varname=NULL){
        static $configval = null;
        if( !isset($configval[$cfname]) ){
            get_instance()->config->load($cfname, TRUE, TRUE);
            $configval[$cfname] = get_instance()->config->item($cfname);
        }
        return null!==$varname ? $configval[$cfname][$varname] : $configval[$cfname];
    }
}

/**
 * 写日志
 */
if( !function_exists('writelog') ){
    function writelog($logname,$content){
        $LogFile = APPPATH.'cache/logs/'.$logname.'.log';
        if( $fp = fopen($LogFile,'a') ){
            if (flock($fp, LOCK_EX)) { // 进行排它型锁定
                fwrite($fp, $content);
                flock($fp, LOCK_UN); // 释放锁定
            }
        }
        fclose($fp);
    }
}

/**
 * @todo 导出csv文件
 * @param string $filename 文件名
 * @param string $data 数据
 */
if( !function_exists('export_csv_common')) {
    function export_csv_common($filename,$data) {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
    }
}

/**
 * @todo 获取文件后缀名
 * @param string $filename 文件名
 * @return string 返回文件名
 */
if( !function_exists('file_ext')) {
    function file_ext($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}

/**
 * @todo 生成随机字符串
 * @param string $length 长度
 * @return string 返回生成的随机字符串
 */
if( !function_exists('random_str')) {
    function random_str($length = 10) {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $strlen = 62;
        while($length > $strlen){
            $str .= $str;
            $strlen += 60;
        }

        $str = str_shuffle($str);  //随机地打乱字符串中的所有字符
        return substr($str, 0,$length);
    }
}

/**
 * @todo 生成固定长度字符串
 * @param string $length 长度
 * @return string 生成固定长度字符串
 */
if( !function_exists('add_str')) {
    function add_str($length,$str) {
        $ret = '';
        for($i=0; $i<$length; $i++){
            $ret .= $str;
        }
        return $ret;
    }
}

/**
 * @todo 文件上传函数
 * @param string $file 上传文件数组
 * @param string $dir 文件保存路径，相对路径
 * @param int $maxSize 文件的大小限制
 * @param array $type 文件类型限制
 * @return array 返回一维数组
 */
if( !function_exists('upload_file')) {
    function upload_file($file, $dir, $maxSize=2, $type=array()) {
        $fileType = strtolower(file_ext($file['name']));
        if( !empty($type) && !in_array($fileType, $type)) {
            $text = implode(',', $type);
            return array(
                'success' => false,
                'msg' => '您只能上传以下类型文件: ' . $text
            );
        }
        if($file['size'] > ($maxSize*1024*1024)) {
            return array(
                'success' => false,
                'msg' => '最大只能上传' . $maxSize . 'M的文件!'
            );
        }
        $fileNewName = date('YmdHis', gettime()) . '_' . md5(random_str(10));
        $uploadDir = $dir . '/' . $fileNewName . '.' . $fileType;
        if( !file_exists(FCPATH . $dir)) {
            create_dir(FCPATH . $dir);
        }
        if(move_uploaded_file($file['tmp_name'], $uploadDir)) {
            return array(
                'success' => true,
                'msg' => $uploadDir
            );
        }
        return array(
            'success' => false,
            'msg' => '上传失败'
        );
    }
}

/**
 * @todo 导入excel,返回二维数组形式的数据
 * @param string $filesfield name值
 * @param int $sheetIndex sheet
 * @return array 返回二维数组(excel数据)
 */
if( !function_exists('read_excel')) {
    function read_excel($filesfield,$sheetIndex=0){
        if( !isset($_FILES[$filesfield]) || !isset($_FILES[$filesfield]['tmp_name']) || !is_uploaded_file($_FILES[$filesfield]['tmp_name']) ){
            return '请上传excel文件';
        }
        //是否支持读取压缩版的excel
        if( !class_exists('ZipArchive') ) return '不支持读取压缩版excel';
        $allowExt = array('.xls'=>'Excel5','.xlsx'=>'Excel2007');
        $ext = strrchr($_FILES[$filesfield]['name'],'.');
        if( !array_key_exists($ext,$allowExt) ) return '上传文件类型错误';
        $CI = &get_instance();
        $CI->load->model('admin/Excel_mod');
        $PHPReader = $CI->Excel_mod->createReader($allowExt[$ext]);
        if( !$PHPReader->canRead($_FILES[$filesfield]['tmp_name']) ) return '上传文件类型错误';
        $PHPReader->setReadDataOnly(true);
        $currentSheet = $PHPReader->load($_FILES[$filesfield]['tmp_name'])->getSheet($sheetIndex);
        @unlink($_FILES[$filesfield]['tmp_name']);
        return $currentSheet->toArray();
    }
}

/**
 * @todo curl get/post方法
 * @params string $url 访问url
 * @params string $poststr提交的参数json
 * @params array $httpheader header参数
 * @return string json
 */
if( !function_exists('_curl') ){
    function _curl($url,$poststr='',$httpheader=array(),$usecookie=false){
        $ch = curl_init();
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if ( $SSL ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }
        if( $poststr!='' ){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $poststr);
        }
        if( $httpheader ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        }
        if( $usecookie ){
        	$cookie_jar = APPPATH.'cache/logs/curl_cookie_'.md5($url).'.log';
        	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return  $data;
    }
}

/**
 * @todo curl delete/put方法
 * @author zhangshimian<张仕勉>
 * @params string $url 访问url
 * @params array $params 提交的参数数组
 * @params string $opt 资源描述符，包括DELETE/PUT
 * @return string json
 */
if( !function_exists('_curl_del_put')) {
    function _curl_del_put($url, $params, $opt='DELETE') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $opt);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if( !function_exists('getimgs') ){
    function getimgs($str){
        preg_match_all('/<img.+?src=\"?(.+?\.(jpg|gif|bmp|bnp|png))\"?.+?>/i',$str,$match);
        return $match[1];
    }
}

if( !function_exists('getimgsAll') ){
    function getimgsAll($str){
        //匹配上传的图片和网络图片
        preg_match_all('/<img src=\"((http:)?\/\/.+?)\".+?(width=\"(\d+)\")?.+?(height=\"(\d+)\")?.*?>/is',$str,$match);
        return $match;
    }
}

//获取php配置参数后，返回字节数，如8k=>8*1024
if( !function_exists('ini_get_bytes')) {
    function ini_get_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }
}

//url正则判断
if( !function_exists('is_url')) {
    function is_url($value, $match='@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@') {
        $v = strtolower(trim($value));
        if(empty($v)) return false;
        return preg_match($match,$v);
    }
}

//url参数加密
if( !function_exists('url_encrypt')) {
    function url_encrypt($param, $access_token='mini-course') {
        return rawurlencode(base64_encode($param . $access_token));   //编码
    }
}

//url参数解密
if( !function_exists('url_decrypt')) {
    function url_decrypt($param, $access_token='mini-course') {
        $str = base64_decode(rawurldecode($param));   //解码
        $tokenLength = strlen($access_token);
        $token = substr($str, -$tokenLength);
        if($token !== $access_token) return false;
        return substr($str, 0, -$tokenLength);
    }
}

//easyui datagrid json加密
if( !function_exists('json_encode_common')) {
    function json_encode_common($status, $msg) {
        return json_encode(array(
            'success' => $status,
            'msg' => $msg
        ));
    }
}

//返回状态数组
if( !function_exists('array_common')) {
    function array_common($status, $msg) {
        return array(
            'success' => $status,
            'msg' => $msg
        );
    }
}

///设置系统名称
if( !function_exists('get_system_name')) {
    function get_system_name() {
        $system_name = '公选课后台管理系统';
        return $system_name;
    }
}


//正则匹配：email
if( !function_exists('preg_email')) {
    function preg_email($str) {
        $preg = '/^(\w+[-]*\w*@\w+\.\w+)$/';
        return preg_match($preg, $str);
    }
}
