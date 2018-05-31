<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
** @name User_mod
** @desc 用户控制模块
**/
class User_mod extends MY_Model {

    private $user_table = 'sys_user';
    private $role_table = 'sys_role';
    private $email_table = 'sys_email_login';

    /**
     *获取单个用户信息
     *author:
     */
    public function get_user($UserId){
        $sql = "SELECT u.* FROM {$this->user_table} u WHERE u.Id = '{$UserId}' AND Deleted = '0'";
        return $this->db_getOneBySql($sql);
    }

    /**
     *获取用户列表
     *
     * @params array $params 参数数组
     * @return array
     */
    public function get_userlist($params=array()){
        $where = "Deleted = 0";
        $role_list = $this->role_list(array('arr_flag' => true));

        //判断是否要获取以Id为下标的数组
        if(isset($params['arr_flag']) && $params['arr_flag']) {
            if( !$result = $this->cache->get('sys_user_list')) {
                $result = $this->db_getResultSet("SELECT * FROM {$this->user_table} WHERE $where", 'Id');
                foreach($result as $key => &$row) {
                    $row['RoleName'] = $role_list[$row['RoleId']]['Name'];
                }
                $this->cache->set('sys_user_list', $result, 86400*31);
            }
        } else {
            if(isset($params['where'])) $where .= $params['where'];
            $params_arr = array(
                'flag' => true,
                'model_name' => $this->user_table,
                'where' => $where,
                'order' => 'Id asc',
                'pagination' => true
            );
            $result = $this->get_datagrid_data($params_arr);
            foreach($result['rows'] as &$row) {
                $row['StatusTrans'] = ($row['Status'] == 1) ? '正常' : '禁用';
                $row['RoleName'] = $role_list[$row['RoleId']]['Name'];
            }
        }

        return $result;
    }

    /**
     *添加用户账号
     *author:
     */
    public function add_user($data){
        if( !$this->is_valid_username($data['UserName']) ) return 'UserNameIsInvalid';//账号无效
        if( $this->db_scalar($this->user_table,'Id',"UserName = '{$data['UserName']}' AND Deleted = '0'") ) return 'UserNameIsExists';//Username已经存在
        if( $data['Status'] != 0 ) $data['Status'] = 1;
        if( !$this->db_scalar($this->role_table,'Id',"Id = '{$data['RoleId']}'") ) return 'RoleNotExists';//不存在的角色ID
        if(strlen($data['Password']) < 8) return 'PassWordIsInvalid';
        $data['Password'] = md5($data['Password']);
        $this->initdb()->insert($this->user_table,$data);
        $this->cache->delete('sys_user_list');
        return true;
    }

    /**
     *用户编辑
     *author:
     */
    public function edit_user($data){
        if( !$this->db_scalar($this->user_table,'Id','Id =\''.$data['Id'].'\' AND Deleted = \'0\'')) return 'UserNotExists';
        if(empty($data['Password'])) {
            unset($data['Password']);
        } else {
            if(strlen($data['Password']) < 8) return 'PassWordIsInvalid';
            $data['Password'] = md5($data['Password']);
        }
        if( $data['Status'] != 0 ) $data['Status'] = 1;
        $data['LogFaild'] = 0;
        if( !$this->db_scalar($this->role_table,'Id','Id=\''.$data['RoleId'].'\'') ) return 'RoleNotExists';//不存在的角色ID
        $this->initdb()->update($this->user_table,$data,'Id = \''.$data['Id'].'\'');
        $this->cache->delete('sys_user_list');
        return true;
    }

    /**
     *删除用户
     *author: loong<梁龙>
     */
    public function delete_user($id){
        if( !$this->db_scalar($this->user_table,'Id',"Id = '{$id}' AND Deleted = '0'") ) return 'UserNotExists';
        $this->initdb()->update($this->user_table,array('Deleted'=>1),"Id = '{$id}'");
        $this->cache->delete('sys_user_list');
        return true;
    }

    /**
     *角色列表
     *author:
     */
    public function role_list($params=array()){
        $where = "Deleted = 0";

        //判断是否要获取以Id为下标的数组
        if(isset($params['arr_flag']) && $params['arr_flag']) {
            if( !$result = $this->cache->get('sys_role_list')) {
                $result = $this->db_getResultSet("SELECT * FROM {$this->role_table} WHERE $where", 'Id');
                $this->cache->set('sys_role_list', $result, 86400*31);
            }
        } else {
            if(isset($params['where'])) $where .= $params['where'];
            $params_arr = array(
                'flag' => true,
                'model_name' => $this->role_table,
                'where' => $where,
                'order' => 'Id asc',
                'pagination' => true
            );
            $result = $this->get_datagrid_data($params_arr);
            foreach($result['rows'] as &$row) {
                $row['StatusTrans'] = ($row['Status'] == 1) ? '正常' : '禁用';
            }
        }

        return $result;
    }

    /**
     * 获取单个角色信息
     *
     */
    public function get_role($Id,$field='*'){
        if( !$row = $this->db_getOne($this->role_table,$field,"Id='{$Id}' AND Deleted = '0'") ) return 'RoleNotExists';
        if( isset($row['Permissions']) ) $row['Permissions'] = unserialize($row['Permissions']);
        return $row;
    }

    /**
     *添加用户角色组
     *author:
     */
    public function add_role($data){
        if( $this->db_scalar($this->role_table,'Id','Name=\''.$data['Name'].'\' and Deleted=0') ) return 'RoleNameIsExists';//角色名已经存在
        if( !is_array($data['Permissions'])) return 'PermiIsNull';//没有选择授权
        $this->load->model('admin/Ctrl_mod');
        $contrlConfig = $this->Ctrl_mod->getConfig();
        foreach ($data['Permissions'] as $k=>$rs){
            $rs = explode(':', $rs);
            if( count($rs) != 2 ) {
                unset($data['Permissions'][$k]);continue;
            }
            if( !isset($contrlConfig[ucfirst($rs[0])]) ) {
                unset($data['Permissions'][$k]);continue;
            }
            if( !isset($contrlConfig[ucfirst($rs[0])]['Methods'][$rs[1]]) ) {
                unset($data['Permissions'][$k]);continue;
            }
            if( isset($data['Permissions'][$rs[0]][$rs[1]]) ) {
                unset($data['Permissions'][$k]);continue;
            }
            unset($data['Permissions'][$k]);
            $data['Permissions'][$rs[0]][$rs[1]] = array(
                'c'=>$rs[0],'a'=>$rs[1],'d'=>$contrlConfig[ucfirst($rs[0])]['Dir']
            );
        }

        if( empty($data['Permissions']) ) return 'PermiIsNull';//没有选择授权
        $data['Permissions'] = serialize($data['Permissions']);
        if( $data['Status'] != 0 ) $data['Status'] = 1;
        $this->initdb()->insert($this->role_table,$data);
        $this->cache->delete('sys_role_list');
        return true;
    }

    /**
     *用户组编辑
     *author:
     */
    public function edit_role($data){
        $roleId = $data['Id'];
        if( !$this->db_scalar($this->role_table,'Id','Id =\''.$roleId.'\' AND Deleted = \'0\'')) return 'RoleNotExists';
        if( $this->db_scalar($this->role_table,'Id','Id !=\''.$roleId.'\' AND Name=\''.$data['Name'].'\' AND Deleted = \'0\'') ) {
            return 'RoleNameIsExists';//角色名已经存在
        }
        if( !is_array($data['Permissions'])) return 'PermiIsNull';//没有选择授权
        $this->load->model('admin/Ctrl_mod');
        $contrlConfig = $this->Ctrl_mod->getConfig();
        foreach ($data['Permissions'] as $k=>$rs){
            $rs = explode(':', $rs);
            if( count($rs) != 2 ) {
                unset($data['Permissions'][$k]);continue;
            }
            if( !isset($contrlConfig[ucfirst($rs[0])]) ) {
                unset($data['Permissions'][$k]);continue;
            }
            if( !isset($contrlConfig[ucfirst($rs[0])]['Methods'][$rs[1]]) ) {
                unset($data['Permissions'][$k]);continue;
            }
            if( isset($data['Permissions'][$rs[0]][$rs[1]]) ) {
                unset($data['Permissions'][$k]);continue;
            }
            unset($data['Permissions'][$k]);
            $data['Permissions'][$rs[0]][$rs[1]] = array(
                'c'=>$rs[0],'a'=>$rs[1],'d'=>$contrlConfig[ucfirst($rs[0])]['Dir']
            );
        }


        if( empty($data['Permissions']) ) return 'PermiIsNull';//没有选择授权
        $data['Permissions'] = serialize($data['Permissions']);

        if( $data['Status'] != 0 ) $data['Status'] = 1;
        $where = 'Id = \''.$roleId.'\'';
        unset($data['Id']);
        $this->initdb()->update($this->role_table,$data,$where);
        $this->cache->delete('sys_role_list');
        return true;
    }


    /**
     *删除角色
     *author: loong<梁龙>
     */
    public function delete_role($id){
        if( !$this->db_scalar($this->role_table,'Id',"Id = '{$id}' AND Deleted = '0'") ) return 'RoleNotExists';
        //删除角色前，先删除成员
        $judge = $this->initdb()->select('Id')->where("RoleId = {$id}")->get($this->user_table)->result_array();
        if( !empty($judge)) {
            return 'RoleDelFail';
        }
        $this->initdb()->update($this->role_table,array('Deleted'=>1),"Id = '{$id}'");
        $this->cache->delete('sys_role_list');
        return true;
    }

    /**
    * 账号密码登录
    */
    public function check_login_pw($username, $pw) {
        if( !$user = $this->db_getOne($this->user_table,'*',"UserName='{$username}' and Deleted = '0'") ) return array_common(false, '该用户不存在');

        $pw = md5($pw);
        if( !$user = $this->db_getOne($this->user_table,'*',"UserName='{$username}' and Password='{$pw}' and Deleted = '0'") ) return array_common(false, '密码输入错误');

        if($user['Status'] == 0) return array_common(false, '该用户已被禁用');
        if(intval($user['RoleId']) < 1) return array_common(false, '角色权限出错');
        $role = $this->get_role($user['RoleId'],'Name,Status');
        if( !is_array($role)) return array_common(false, '角色权限出错');
        if($role['Status'] != 1) return array_common(false, '角色权限出错');

        $update_cookie = array(
            'UserId' => $user['Id'],
            'UserName' => $user['UserName'],
            'NickName' => $user['NickName'],
            'RoleName' => $role['Name'],
            'RoleId' => $user['RoleId'],
            'LastLogTime' => $user['LastLogTime'],
            'LastLogIP' => $user['LastLogIP']
        );
        $this->load->library('encrypt');
        $cookiedata = $this->encrypt->encode(gzcompress(serialize($update_cookie)));
        session_start();
        $_SESSION['user_data'] = $cookiedata;
        $update_sql = array('LastLogTime'=>gettime(),'LastLogIP'=>$this->input->ip_address(),'LogFaild'=>0);
        $this->initdb()->update($this->user_table, $update_sql, 'Id = '.$update_cookie['UserId']);
        unset($user,$role,$update_sql,$update_cookie);

        return array_common(true, '登录成功');
    }


	/**
	*角色记录总条数
	*author:
	*/
	public function get_role_totalnum($where=''){
		$sqlwhere = "Deleted = '0'";
		if( !empty($where) ) $sqlwhere .= ' AND '.$where;
		return $this->db_scalarBySql("SELECT COUNT(*) FROM {$this->role_table} WHERE $sqlwhere");
	}

    /**
     *用户记录总条数
     *author:
     */
    public function get_user_totalnum($where=''){
        $sqlwhere = "Deleted = '0'";
        if( !empty($where) ) $sqlwhere .= ' AND '.$where;
        return $this->db_scalarBySql("SELECT COUNT(*) FROM {$this->user_table} WHERE $sqlwhere");
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
}

/* End of file User_mod.php */
/* Location: application/models/admin/User_mod.php */