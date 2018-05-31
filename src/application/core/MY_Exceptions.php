<?php
class MY_Exceptions extends CI_Exceptions {

	/**
     +----------------------------------------------------------
     * Ajax方式返回数据到客户端
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $message 提示信息
     * @param array $data 要返回的数据
     * @param int $status 返回状态
     * @param string $redirect 回调链接
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public function ajaxReturn($message,$data=array(),$status=200,$redirect=''){
		$result  =  array();
        $result['errno']  =  $status;
        $result['message'] =  $message;
        if(empty($data)) {
            $data = null;
        }
        $result['data'] = $data;
        $result['redirect'] = $redirect;
//        set_status_header($status);
        header('Content-Type:text/html; charset=utf-8');
        exit(json_encode($result));
	}

	public function show_msg($message,$status=500, $jumpurl='-1',$waittime=2000,$jumptop=''){
		$heading = $status==200?'操作成功':'操作失败';
		set_status_header($status);
		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
		if( $jumpurl =='') $jumpurl = '-1';
		ob_start();
		include(APPPATH.'views/errors/html/error_and_jump.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		exit($buffer);
	}
}