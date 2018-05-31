<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统控制器操作日志
 */
class Log {
	private $CI = NULL;
	private $delayLog = array();
	
	public function __construct(){
		$this->CI =& get_instance();		
	}
	
	public function WriteLog($content){
		$LogDir = APPPATH.'cache/logs/';
		if( !is_dir($LogDir) ) create_dir($LogDir);
		$LogFile = $LogDir.date('Y-m-d',gettime()).'.log';		
		if( $fp = fopen($LogFile,'a') ){						
			if (flock($fp, LOCK_EX)) { // 进行排它型锁定
			    fwrite($fp, $content);
			    flock($fp, LOCK_UN); // 释放锁定
			}
		}
		fclose($fp);
		return true;
	}
	
	public function post_controller_constructor(){
		if( !is_cli() && isset($this->CI->VAR_D) && $this->CI->VAR_D=='admin' ){
			$Logs = 'URI:'.geturl($this->CI->VAR_C,$this->CI->VAR_A,$this->CI->VAR_D)."\tIP:".$this->CI->input->ip_address()."\tTIME:".date('Y-m-d H:i:s',gettime()).(!empty($this->CI->userinfo)?"\tUSER:{$this->CI->userinfo['UserName']}({$this->CI->userinfo['NickName']})":'')."\r\n";
			if( strtolower($this->CI->VAR_C) =='api' && $_SERVER['HTTP_REFERER']!='' ){
				$Logs .= '请求来源:'.$_SERVER['HTTP_REFERER']."\r\n";
			}
			$Logs .= 'GET参数：'.http_build_query($_GET);
			if( isset($_POST) && !empty($_POST) ){
				if( $this->CI->VAR_C=='user' && ($this->CI->VAR_A=='login' || $this->CI->VAR_A=='modify_pwd') ){
					$Logs .= "\r\nPOST参数：".encrypt(http_build_query($_POST),config_item('encryption_key')).' (涉及到帐号密码信息,当前参数已被加密)';
				}else{
					$Logs .= "\r\nPOST参数：".urldecode(http_build_query($_POST));
				}
			}
			$Logs .= "\r\n------------------------------------------------------------------------------------------\r\n";
			$this->delayLog['post_controller_constructor'] = $Logs;
		}		
	}
	
	public function __destruct(){
		if( !empty($this->delayLog) ){
			$this->WriteLog(implode("\r\n",$this->delayLog));
			$this->delayLog = array();
		}
	}
}