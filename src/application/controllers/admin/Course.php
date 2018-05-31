<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 课程管理
*/
class Course extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('admin/Course_mod', 'course_mod');
    }

    /**
    * 课程列表
    */
    public function course_list() {
        $action = $this->get_input('action');

        //展示页面
        if(empty($action)) {
            $assign = array(
                'url' => geturl(),
                'url_add' => geturl('course', 'course_add', 'admin'),
                'url_edit' => geturl('course', 'course_edit', 'admin'),
                'url_del' => geturl('course', 'course_del', 'admin'),
                'url_export' => geturl() . "&action=export",
                'url_get_teacher' => geturl() . "&action=get_teacher",
                'url_sign' => geturl('course', 'course_sign', 'admin'),
                'url_apply_ret' => geturl('course', 'course_apply_result', 'admin', 'action=ajax_data'),
                'url_export_ret' => geturl('course', 'course_apply_result', 'admin', 'action=export'),
            );
            $this->load->view('admin/course_list', $assign);
        }

        //返回教师信息
        if($action == 'get_teacher') {
            $search = $this->get_input('search');
            $this->load->model('admin/Contacts_mod', 'contacts_mod');
            $where = " and type = '1'";
            if($search !== '') $where .= " and (nickname like '%{$search}%' or college like '%{$search}%')";
            $params = array(
                'where' => $where
            );
            $data = $this->contacts_mod->contacts_list($params);
            echo json_encode($data);
            exit();
        }

        //模糊搜索和导出excel
        $search_a = $this->get_input('search_a', 'post', 'trim');
        $search_b = $this->get_input('search_b', 'post', 'trim');

        //模糊搜索
        $where = "";
        if($search_a !== '') {
            $where .= " and course_name like '%{$search_a}%'";
        }
        if($search_b !== '') {
            $where .= " and teacher_names like '%{$search_b}%'";
        }

        $params = array(
            'where' => $where
        );
        $data = $this->course_mod->course_list($params);

        if($action == 'ajax_data') {
            echo json_encode($data);
        } elseif($action == 'export') {
            $param = array(
                'data' => $data['rows'],
                'table_head' => array('course_name' => '课程名', 'place' => '上课地点', 'course_time' => '上课时间', 'teacher_names' => '任课教师', 'outline' => '课程简介', 'score' => '课程学分', 'limit_trans' => '报名人数上限', 'close_time' => '报名截止时间', 'late_time' => '扫码迟到时间', 'activity_num' => '已报名人数', 'operator' => '创建者', 'operate_time' => '创建时间'),
                'excel_title' => '课程信息列表'
            );
            $this->export_course($param);
        }
    }

    /**
    * 导出课程
    */
    public function export_course($param) {
        $this->load->model('Common_mod', 'common_mod');
        $this->common_mod->export_excel($param);
    }

    /**
    * 添加课程
    */
    public function course_add(){
        $data = array(
            'course_name' => $this->get_input('course_name', 'post', 'trim'),
            'place' => $this->get_input('place', 'post', 'trim'),
            'course_time' => $this->get_input('course_time', 'post', 'trim'),
            'outline' => $this->get_input('outline', 'post', 'trim'),
            'score' => $this->get_input('score', 'post', 'intval'),
            'teacher_names' => $this->get_input('teacher_names', 'post', 'trim'),
            'teacher_ids' => $this->get_input('teacher_ids', 'post', 'trim'),
            'limit_num' => $this->get_input('limit_num', 'post', 'intval'),
            'close_time' => strtotime($this->get_input('close_time', 'post', 'trim')),
            'late_time' => strtotime($this->get_input('late_time', 'post', 'trim')),
            'operator' => $this->userinfo['NickName'],
            'operate_time' => gettime()
        );

        if( ($errkey = $this->course_mod->course_add($data)) !== true ){
            $result = json_encode_common(false, $this->lang('addCourseFailed'));
        } else {
            $result = json_encode_common(true, $this->lang('addCourseOk'));
        }
        echo $result;
    }

    /**
    * 编辑课程
    */
    public function course_edit(){
        $data = array(
            'id' => $this->get_input('id','get','intval'),
            'course_name' => $this->get_input('course_name', 'post', 'trim'),
            'place' => $this->get_input('place', 'post', 'trim'),
            'course_time' => $this->get_input('course_time', 'post', 'trim'),
            'outline' => $this->get_input('outline', 'post', 'trim'),
            'score' => $this->get_input('score', 'post', 'intval'),
            'teacher_names' => $this->get_input('teacher_names', 'post', 'trim'),
            'teacher_ids' => $this->get_input('teacher_ids', 'post', 'trim'),
            'limit_num' => $this->get_input('limit_num', 'post', 'intval'),
            'close_time' => strtotime($this->get_input('close_time', 'post', 'trim')),
            'late_time' => strtotime($this->get_input('late_time', 'post', 'trim')),
            'operator' => $this->userinfo['NickName'],
            'operate_time' => gettime()
        );

        if( ($errkey = $this->course_mod->course_edit($data)) !== true ){
            $result = json_encode_common(false, $this->lang('editCourseFailed'));
        } else {
            $result = json_encode_common(true, $this->lang('editCourseOk'));
        }
        echo $result;
    }

    /**
    * 删除课程
    */
    public function course_del(){
        $id = $this->get_input('id','post','intval');

        if( $id < 1 ) {
            $result = json_encode_common(false, $this->lang('ParamsErr'));
        } else {
            if( ($errkey = $this->course_mod->course_del($id)) !== true ){
                $result = json_encode_common(false, $this->lang($errkey));
            } else {
                $result = json_encode_common(true, $this->lang('deletedOk'));
            }
        }
        echo $result;
    }

    /**
    * 报名管理
    */
    public function course_apply_result() {
        if(($id = $this->get_input('id')) == '') {
            exit(json_encode_common(false, $this->lang('ParamsErr')));
        }

        $action = $this->get_input('action');
        $whereSql = " and a.course_id = {$id}";
        $data = $this->course_mod->student_list(array('where' => $whereSql));

        if($action == 'ajax_data') {
            echo json_encode($data);
        } elseif($action == 'export') {
            $param = array(
                'data' => $data['rows'],
                'table_head' => array('nickname' => '姓名', 'college' => '学院', 'email' => '邮箱', 'sign_time' => '签到结果'),
                'excel_title' => '课程报名列表'
            );
            $this->export_course($param);
        }
    }

    /**
    * 导出报名结果
    */
    public function export_student_apply() {
        $this->load->model('Common_mod', 'common_mod');
        $this->common_mod->export_excel($param);
    }

    /**
    * 二维码签到
    */
    public function course_sign(){
        if( !$id = $this->get_input('id', 'get', 'intval')) {
            $this->msg($this->lang('ParamsErr'));
        }
        $action = $this->get_input('action', 'get', 'trim');

        if(empty($action)) {
            $where = " and id = {$id}";
            $params = array('where' => $where, 'flag' => false);
            $info = $this->course_mod->course_list($params);
            $info = $info[0];

            if( !empty($info)) {
                $assign['title'] = '二维码签到';  //标题
                $assign['content'] = $info['course_name'] . ' 扫码签到';  //显示内容
                $assign['img'] = geturl('course', 'course_sign', 'admin', 'action=getimg&id=' . $id);
                $this->load->view('admin/course_sign', $assign);
            } else {
                $this->msg($this->lang('deletedBefore'));
            }
        } elseif($action == 'getimg') {  //获取二维码图片
            $param['str'] = $this->website . geturl('course_mini', 'qrcode_sign', 'api', 'action=sign&param=' . url_encrypt($id));  //二维码链接
            $this->course_mod->make_img_freedom($param);  //生成二维码图片
        }
    }

    /**
    * 教师参与课程列表
    */
    public function teacher_list() {
        $action = $this->get_input('action');

        //展示页面
        if(empty($action)) {
            $assign = array(
                'url' => geturl(),
                'url_export' => geturl() . "&action=export"
            );
            $this->load->view('admin/teacher_list', $assign);
        }

        //模糊搜索和导出excel
        $search_a = $this->get_input('search_a', 'post', 'trim');
        $search_b = $this->get_input('search_b', 'post', 'trim');
        $search_c = $this->get_input('search_c', 'post', 'trim');

        //模糊搜索
        $where = "1=1";
        if($search_a !== '') {
            $where .= " and b.course_name like '%{$search_a}%'";
        }
        if($search_b !== '') {
            $where .= " and c.nickname like '%{$search_b}%'";
        }
        if($search_c !== '') {
            $where .= " and c.college like '%{$search_c}%'";
        }

        $params = array(
            'where' => $where
        );
        $data = $this->course_mod->teacher_list($params);

        if($action == 'ajax_data') {
            echo json_encode($data);
        } elseif($action == 'export') {
            $param = array(
                'data' => $data['rows'],
                'table_head' => array('nickname' => '教师姓名', 'college' => '所属学院', 'course_name' => '课程名', 'course_time' => '上课时间', 'email' => '联系邮箱'),
                'excel_title' => '教师参与课程列表'
            );
            $this->export_teacher($param);
        }
    }

    /**
    * 导出教师参与课程列表
    */
    public function export_teacher($param) {
        $this->load->model('Common_mod', 'common_mod');
        $this->common_mod->export_excel($param);
    }

    /**
    * 学生参与课程列表
    */
    public function student_list() {
        $action = $this->get_input('action');

        //展示页面
        if(empty($action)) {
            $assign = array(
                'url' => geturl(),
                'url_export' => geturl() . "&action=export"
            );
            $this->load->view('admin/student_list', $assign);
        }

        //模糊搜索和导出excel
        $search_a = $this->get_input('search_a', 'post', 'trim');
        $search_b = $this->get_input('search_b', 'post', 'trim');
        $search_c = $this->get_input('search_c', 'post', 'trim');

        //模糊搜索
        $where = " and 1=1";
        if($search_a !== '') {
            $where .= " and b.course_name like '%{$search_a}%'";
        }
        if($search_b !== '') {
            $where .= " and c.nickname like '%{$search_b}%'";
        }
        if($search_c !== '') {
            $where .= " and c.college like '%{$search_c}%'";
        }

        $params = array(
            'where' => $where
        );
        $data = $this->course_mod->student_list($params);

        if($action == 'ajax_data') {
            echo json_encode($data);
        } elseif($action == 'export') {
            $param = array(
                'data' => $data['rows'],
                'table_head' => array('nickname' => '学生姓名', 'college' => '所属学院', 'course_name' => '课程名', 'course_time' => '上课时间', 'email' => '联系邮箱', 'score' => '上课获得学分', 'apply_time' => '报名时间', 'sign_time' => '签到时间'),
                'excel_title' => '学生参与课程列表'
            );
            $this->export_student($param);
        }
    }

    /**
    * 导出学生参与课程列表
    */
    public function export_student($param) {
        $this->load->model('Common_mod', 'common_mod');
        $this->common_mod->export_excel($param);
    }
}