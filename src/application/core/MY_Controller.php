<?php
class MY_Controller extends CI_Controller {
	public $userinfo = array();
	public $VAR_D;
	public $VAR_C;
	public $VAR_A;
	private $language = array();
    public $website;

	public function __construct(){
		parent::__construct();
		$this->VAR_D = rtrim($this->router->directory,'/');//目录
		$this->VAR_C = $this->router->class;//控制器
		$this->VAR_A = $this->router->method;//方法函数

        //获取网站域名
        $this->load->helper('url');
        $this->website = str_replace('index.php', '', site_url());

        define('ADMINVIEWPATH', VIEWPATH . 'admin/');

        if( isNeedAuth($this->VAR_C,$this->VAR_A,$this->VAR_D) ){
            $this->load->library('encrypt');
            session_start();
            $user_data = $_SESSION['user_data'];
            $user_data = unserialize(gzuncompress($this->encrypt->decode($user_data)));
            if( !is_array($user_data) || empty($user_data) ) {
                header('Location: '.geturl('user','logout','admin'));
                exit();
            }
            if( !isset($user_data['RoleId']) ){
                $this->msg_lang('admin.user.RoleIsError',array(),500,geturl('user','logout','admin'),2000,1);
            }
            if( !role_auth($user_data['RoleId'], $this->VAR_C,$this->VAR_A,$this->VAR_D) ){
                $this->msg_lang('admin.user.NotAllowed',array(),500,geturl('user','logout','admin'),2000,1);
            }
            if( is_array($user_data) ) $this->userinfo = $user_data;
        }
	}

    /**
     * @todo 返回错误提示页面
     * @param string $msg 错误提示信息
     * @param string $url 页面跳转链接
     * @param int $status 页面跳转时间，单位秒
     * @author:zhangshimian<张仕勉>
     */
    public function msg($msg='参数有误', $url='', $status=2) {
        if($url != '') {
            header("refresh:{$status};url={$url}");
            $msg .= '<br/><br/><a href="' . $url . '">如果您的浏览器没有跳转，请点这里</a>';
        }

        $assign['title_msg'] = '操作提示';
        $assign['msg'] = $msg;
        $this->load->view('admin/msg_error', $assign);
        $this->output->_display();  //输出页面
        die();  //终止脚本
    }

    /**
     * @todo weui框架移动端返回错误提示页面
     * @param string $msg 错误提示信息
     * @param string $url 页面跳转链接
     * @param int $type 返回成功提示还是错误提示，默认是错误提示
     * @param int $status 页面跳转时间，单位秒
     * @author:zhangshimian<张仕勉>
     */
    public function weui_msg($msg='参数有误', $url='', $type=0, $status=2) {
        if($url != '') {
            header("refresh:{$status};url={$url}");
            $msg .= '<br/><br/><a href="' . $url . '">如果您的浏览器没有跳转，请点这里</a>';
        }

        $assign['type'] = $type;
        if($type) {
            $assign['title'] = '操作成功';
        }
        else {
            $assign['title'] = '操作失败';
        }

        $assign['msg'] = $msg;
        $this->load->view('weui/msg.php', $assign);
        $this->output->_display();  //输出页面
        die();  //终止脚本
    }

	//基于语言包的错误提示框
	public function msg_lang($langKey, $langArr=array(), $url='', $status=2){
		$this->msg($this->lang($langKey, $langArr), $url, $status);
	}

	//获取客户端提交信息
	public function get_input($var,$method='',$call_func='',$xss_clean=false){
		$result = false;
		if( empty($var) ) return $result;
		if( $method!='' && in_array($method, array('post','get','cookie')) ){
			$result = $this->input->$method($var,$xss_clean);
		}else{
			$result = $this->input->get_post($var,$xss_clean);
		}
		$result = $this->_callfunc($result, $call_func);
		return $this->escape($result);
	}

	//字符过滤
	public function escape($var){
        /*if( is_object($this->db) ){
			return $this->db->escape_str($var);
		}else{
			return $this->_callfunc($var, 'addslashes');
		}*/
        return $this->_callfunc($var, 'addslashes');
	}

	public function _callfunc($var,$func){
		if( $func=='' || !function_exists($func) ) return $var;
		if( is_array($var) ){
			foreach ($var as $k=>$v){
				$var[$k] = $this->_callfunc($v, $func);
			}
		}elseif ( is_object($var) ){
			foreach ($var as $k=>$v){
				$var->$k = $this->_callfunc($v, $func);
			}
		}else{
			$var = $func($var);
		}
		return $var;
	}

	//解析语言包
	public function lang($langKey,$langArr=array()){
		if( strpos($langKey, '.') !== false ){
			$langKey = explode('.', $langKey);
			if( count($langKey) > 2 ){
				$langfile = $langKey[0].'/'.strtolower($langKey[1]);
				$langKey = $langKey[2];
			}else{
				$langfile = strtolower($langKey[0]);
				$langKey = $langKey[1];
			}
		}else{
			if( $this->VAR_C == '' ) return '';
			$langfile = $this->router->directory.strtolower($this->VAR_C);
		}
		$this->lang->load($langfile);
		$str = $this->lang->line($langKey);
		$count = count($langArr);
		if( $count>0 && is_array($langArr) ){
			$str = str_replace(array_slice(array('{$1}','{$2}','{$3}','{$4}','{$5}'
	,'{$6}','{$7}','{$8}','{$9}','{$10}','{$11}','{$12}','{$13}','{$14}','{$15}'),0,$count),$langArr,$str);
		}
		return $str;
	}

	//跳转
	public function redirect($url){
        if( $this->input->is_ajax_request() ) {
            $exceptions =& load_class('Exceptions', 'core');
            $exceptions->ajaxReturn('redirect','',304,$url);
        }else{
            header("location:$url");
        }
		exit();
	}

	//调用模型
	public function CallModel($Model,$Method,$Params=array()){
		if( strpos($Model,'/') !== false ){
//			list(,$mod) = explode('/', $Model);
            $mod = str_replace('/','_',$Model);
		}else{
			$mod = $Model;
		}
		if( !is_object($this->{$mod}) ){
			$this->load->model($Model,$mod);
		}
		return call_user_func_array(array(&$this->{$mod},$Method), $Params);
	}

	public function getModelObj($Model){
		$this->load->model($Model);
		if( strpos($Model,'/') !== false ){
			list(,$Model) = explode('/', $Model);
		}
		return $this->{$Model};
	}

	public function getLib($class){
		$this->load->library($class);
		$class = strtolower($class);
		return $this->{$class};
	}

    /**
     * @todo 发邮箱
     * @param array $data 发邮箱的基本信息字段
     * @return bool|string
     */
    public function send_email($data){
        $config = getconfig('email');
        if( !isset($data['to']) || !isset($data['subject']) || !isset($data['message']) || !isset($data['sendName'])){ return false; }
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from($config['smtp_user'], $data['sendName']);
        $this->email->to($data['to']);
        if( isset( $data['cc'] ) ){
            $this->email->cc($data['cc']);
        }
        if( isset( $data['bcc'] ) ){
            $this->email->bcc($data['bcc']);
        }
        $this->email->subject($data['subject']);
        $this->email->message($data['message']);
        if($this->email->send()){
            return true;
        }else{
            return false;
//            return $this->email->print_debugger();
        }
    }
}