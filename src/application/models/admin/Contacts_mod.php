<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 师生信息管理
*/
class Contacts_mod extends MY_Model {
    private $user_table = 'mini_user';

    /**
    * 获取师生信息列表
    */
    public function contacts_list($params=array()) {
        $where = "deleted = 0";
        if(isset($params['where'])) $where .= $params['where'];
        $params_arr = array(
            'flag' => true,
            'model_name' => $this->user_table,
            'where' => $where,
            'order' => 'id desc',
            'pagination' => true
        );
        $result = $this->get_datagrid_data($params_arr);
        foreach($result['rows'] as &$row) {
            $row['status_trans'] = ($row['status'] == 1) ? '正常' : '禁用';
        }
        return $result;
    }

    /**
     * 添加账号
     */
    public function add_user($data) {
        //字段数据判断
        if( $data['username']=='' || !$this->is_valid_username($data['username'])) return 'UserNameIsInvalid';
        $ret = $this->is_vaild_pw($data['password']);
        if($ret !== true) return $ret;
        if( $data['nickname']=='' ) return 'NickNameIsNull';
        if( $data['college'] == '' ) return 'CollegeIsNull';
        if( $data['email'] == '' ) {
            return 'EmailIsNull';
        } elseif( !preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $data['email'] )) {
            return 'EmailFormat';
        }

        if( $this->db_scalar($this->user_table,'id',"username = '{$data['username']}' AND deleted = '0' and type='{$data['type']}'") ) return 'UserNameIsExists';
        if( $data['status'] != 0 ) $data['status'] = 1;

        $data['password'] = md5($data['password']);
        $this->initdb()->insert($this->user_table, $data);
        return true;
    }

    /**
     * 账号编辑
     */
    public function edit_user($data){
        if( $data['id'] == '' ) return 'UserIdIsNull';
        if( $data['nickname']=='' ) return 'NickNameIsNull';
        if( $data['college'] == '' ) return 'CollegeIsNull';
        if( $data['email'] == '' ) {
            return 'EmailIsNull';
        } elseif( !preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $data['email'] )) {
            return 'EmailFormat';
        }

        if( !$this->db_scalar($this->user_table,'id',"id ='{$data['id']}' AND deleted = '0' and type='{$data['type']}'")) return 'UserNotExists';
        if(empty($data['password'])) {
            unset($data['password']);
        } else {
            $ret = $this->is_vaild_pw($data['password']);
            if($ret !== true) return $ret;
            $data['password'] = md5($data['password']);
        }
        if( $data['status'] != 0 ) $data['status'] = 1;
        $this->initdb()->update($this->user_table,$data,"id = '".$data['id']."'");
        return true;
    }

    /**
     *删除用户
     *author: loong<梁龙>
     */
    public function delete_user($id, $type){
        if( !$this->db_scalar($this->user_table,'id',"id = '{$id}' AND deleted = '0' and type='{$type}'") ) return 'UserNotExists';
        $this->initdb()->update($this->user_table,array('deleted'=>1),"id = '{$id}'");
        return true;
    }

    /**
     *用户名有效性检测
     *author:
     */
    public function is_valid_username($username){
        //长度3-16位
        if( !preg_match('/^[a-z0-9_]{3,30}$/i', $username)) return false;
        return true;
    }

    /**
    * 密码有效性检测
    */
    public function is_vaild_pw($pw) {
        $len = strlen($pw);
        if( $pw=='' || $len < 8 || $len > 30) return 'PassWordIsInvalid';
        return true;
    }

    /**
    * 小程序登陆
    */
    public function api_login($data) {
        $username = $data['username'];
        $pw = $data['pw'];
        $type = intval($data['type']);

        //参数判断
        if( !$this->is_valid_username($username)) return array('success' => false, 'msg' => 'UserNameIsInvalid', 'test' => $username, 'ret' => $data);
        $ret = $this->is_vaild_pw($pw);
        if($ret !== true) return array('success' => false, 'msg' => $ret);

        //获取用户信息
        $where = array('username' => $username, 'password' => md5($pw), 'type' => $type, 'deleted' => '0');
        $ret = $this->initdb()->select('username, nickname, college, email, type, status')->where($where)->get($this->user_table)->row_array();
        if(empty($ret)) return array('success' => false, 'msg' => 'UserPwError');
        if($ret['status'] != 1) return array('success' => false, 'msg' => 'UserNameIsDisabled');
        return array('success' => true, 'msg' => 'loginSuccess', 'info' => $ret);
    }
}