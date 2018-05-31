<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 师生信息管理
*/
class Contacts extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('admin/Contacts_mod', 'contacts_mod');
    }

    /**
    * 教师账号列表
    */
    public function teacher_list() {
        $action = $this->get_input('action');
        $type = 1;  //教师标志

        //展示页面
        if(empty($action)) {
            $assign = array(
                'url' => geturl(),
                'url_add' => geturl('contacts', 'add_user', 'admin', "type={$type}"),
                'url_edit' => geturl('contacts', 'edit_user', 'admin', "type={$type}"),
                'url_del' => geturl('contacts', 'delete', 'admin', "type={$type}"),
                'url_export' => geturl() . "&type={$type}&action=export",
                'url_upload' => geturl('contacts', 'import_user', 'admin') . "&type={$type}"
            );
            $this->load->view('admin/contacts_list', $assign);
        }

        $search_a = $this->get_input('search_a', 'post', 'trim');
        $search_b = $this->get_input('search_b', 'post', 'trim');

        //模糊搜索
        $where = " and type={$type}";
        if($search_a !== '') {
            $where .= " and username like '%{$search_a}%'";
        }
        if($search_b !== '') {
            $where .= " and nickname like '%{$search_b}%'";
        }

        $params = array(
            'where' => $where
        );
        $data = $this->contacts_mod->contacts_list($params);

        if($action == 'ajax_data') {
            echo json_encode($data);
        } elseif($action == 'export') {
            $param = array(
                'data' => $data['rows'],
                'table_head' => array('username' => '登陆账号', 'nickname' => '教师姓名', 'college' => '所属学院', 'email' => '邮箱', 'status_trans' => '账号状态'),
                'excel_title' => '教师账号列表'
            );
            $this->export_user($param);
        }
    }

    /**
    * 学生账号列表
    */
    public function student_list() {
        $action = $this->get_input('action');
        $type = 0;  //学生标志

        //展示页面
        if(empty($action)) {
            $assign = array(
                'url' => geturl(),
                'url_add' => geturl('contacts', 'add_user', 'admin', "type={$type}"),
                'url_edit' => geturl('contacts', 'edit_user', 'admin', "type={$type}"),
                'url_del' => geturl('contacts', 'delete', 'admin', "type={$type}"),
                'url_export' => geturl() . "&type={$type}&action=export",
                'url_upload' => geturl('contacts', 'import_user', 'admin') . "&type={$type}"
            );
            $this->load->view('admin/contacts_list', $assign);
        }

        $search_a = $this->get_input('search_a', 'post', 'trim');
        $search_b = $this->get_input('search_b', 'post', 'trim');

        //模糊搜索
        $where = " and type={$type}";
        if($search_a !== '') {
            $where .= " and username like '%{$search_a}%'";
        }
        if($search_b !== '') {
            $where .= " and nickname like '%{$search_b}%'";
        }

        $params = array(
            'where' => $where
        );
        $data = $this->contacts_mod->contacts_list($params);

        if($action == 'ajax_data') {
            echo json_encode($data);
        } elseif($action == 'export') {
            $param = array(
                'data' => $data['rows'],
                'table_head' => array('username' => '登陆账号', 'nickname' => '学生姓名', 'college' => '所属学院', 'email' => '邮箱', 'status_trans' => '账号状态'),
                'excel_title' => '学生账号列表'
            );
            $this->export_user($param);
        }
    }

    /**
    * 导出账号
    */
    public function export_user($param) {
        $this->load->model('Common_mod', 'common_mod');
        $this->common_mod->export_excel($param);
    }

    /**
    * 导入账号
    */
    public function import_user(){
        $action = $this->get_input('action');
        $type = $this->get_input('type', 'get', 'intval');  //师生类型

        $assign = array(
            'action' => 'save',
            'template' => geturl('contacts', 'import_user', 'admin', 'action=template'),  //下载模板链接
            'jump_url' => ($type == 0) ? geturl('contacts', 'student_list', 'admin') : geturl('contacts', 'teacher_list', 'admin'),  //返回链接
            'tip' => '*(批量导入账号后，默认密码为123456789)',  //导入excel注意事项
            'title' => '批量导入' . (($type == 0) ? '学生' : '教师') . '账号' //页面标题
        );

        if($action == 'save') {
            $dataExcel = read_excel('readExcel');
            if( !is_array($dataExcel)) {
                $this->msg($dataExcel, geturl() . '&type=' . $type);
            } else {
                $header = $dataExcel[0];
                $header['msg'] = '上传失败原因';
                $msg = array($header);  //保存错误信息
                $totalRecord = count($dataExcel) - 1;  //总共记录
                $successRecord = 0;  //成功记录
                $failureRecord = 0;  //失败记录
                unset($dataExcel[0]);

                $now_time = date('Y-m-d H:i:s', gettime());
                $data_db = array();  //导入数据库的成员数据
                foreach($dataExcel as $row) {
                    $data = array(
                        'username' => trim($row[0]),
                        'nickname' => trim($row[1]),
                        'college' => trim($row[2]),
                        'email' => trim($row[3]),
                        'password' => '123456789',  //默认密码
                        'status' => 1,
                        'type' => $type
                    );

                    $ret = $this->contacts_mod->add_user($data);
                    if($ret !== true) {
                        $row['msg'] = $this->lang($ret);
                        $msg[] = $row;
                        ++$failureRecord;
                    } else {
                        $successRecord++;
                    }
                }

                $assign['msg'] = $msg;
                $assign['totalRecord'] = $totalRecord;
                $assign['successRecord'] = $successRecord;
                $assign['failureRecord'] = $failureRecord;
                $assign['totalField'] = count($header);  //总共字段数
                $this->load->view('admin/excel_import', $assign);
            }
        } elseif($action == 'template') {
            $this->load->model('admin/Excel_mod');
            $cell = array('A1', 'B1', 'C1', 'D1');
            $value = array('登陆账号(必填)','姓名(必填)', '所属院系(必填)', '邮箱(必填)');
            $this->Excel_mod->setCellValue($cell, $value);  //设置表头
            $this->Excel_mod->setCellwidth(array('A', 'B', 'C', 'D'), array(20, 20, 30, 30), 0);  //像素值除以6约等于宽度

            $filename = $assign['title'] . '-' . date('YmdHis', gettime()) ;  //设置文件名
            $this->Excel_mod->saveExcel($filename, 'excel7');
        } else {
            $this->load->view('admin/excel_import', $assign);
        }
    }

    /**
    * 添加账号
    */
    public function add_user(){
        $data = array();
        $data['username'] = $this->get_input('username','post','trim');
        $data['password'] = $this->get_input('password','post','trim');
        $data['nickname'] = $this->get_input('nickname','post','trim');
        $data['college'] = $this->get_input('college','post','trim');
        $data['status'] = $this->get_input('status','post','intval');
        $data['email'] = $this->get_input('email','post','trim');
        $data['type'] = $this->get_input('type', 'get', 'intval');

        if( ($errkey = $this->contacts_mod->add_user($data)) !== true ){
            $result = json_encode_common(false, $this->lang($errkey));
        } else {
            $result = json_encode_common(true, $this->lang('addUserOk'));
        }
        echo $result;
    }

    /**
    * 编辑账号
    */
    public function edit_user(){
        $data = array();
        $data['id'] = $this->get_input('id','','intval');
        $data['password'] = $this->get_input('password','post','trim');
        $data['nickname'] = $this->get_input('nickname','post','trim');
        $data['college'] = $this->get_input('college','post','trim');
        $data['status'] = $this->get_input('status','post','intval');
        $data['email'] = $this->get_input('email','post','trim');
        $data['type'] = $this->get_input('type', 'get', 'intval');

        if( ($errkey = $this->contacts_mod->edit_user($data)) !== true ){
            $result = json_encode_common(false, $this->lang($errkey));
        } else {
            $result = json_encode_common(true, $this->lang('editUserOk'));
        }
        echo $result;
    }

    /**
    * 账号删除
    */
    public function delete(){
        $id = $this->get_input('id','post','intval');
        $type = $this->get_input('type', 'get', 'intval');

        if( $id < 1 ) {
            $result = json_encode_common(false, $this->lang('ParamsErr'));
        } else {
            if( ($errkey = $this->contacts_mod->delete_user($id, $type)) !== true ){
                $result = json_encode_common(false, $this->lang($errkey));
            } else {
                $result = json_encode_common(true, $this->lang('deletedOk'));
            }
        }
        echo $result;
    }
}