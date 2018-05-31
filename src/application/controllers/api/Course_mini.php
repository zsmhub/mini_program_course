<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 课程管理
*/
class Course_mini extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('admin/Course_mod', 'course_mod');
        $this->load->model('admin/Contacts_mod', 'contacts_mod');
    }

    /**
    * 登陆判断
    */
    public function login() {
        $data = array(
            'username' => $this->input->post('username'),
            'pw' => $this->input->post('pw'),
            'type' => $this->input->post('type')
        );

        $ret = $this->contacts_mod->api_login($data);
        if($ret['success']){
            $result = json_encode(array('success' => true, 'msg' => $this->lang($ret['msg']), 'info' => $ret['info']));
        } else {
            $result = json_encode(array('success' => false, 'msg' => $this->lang($ret['msg'])));
        }
        echo $result;
    }

    /**
    * 获取课程列表
    */
    public function course_list() {
        $course_list = $this->course_mod->course_list(array('pagination' => false));

        //数据整合
        $course_new = array();
        foreach($course_list['rows'] as $row) {
            $course_new[] = array(
                'id' => $row['id'],
                'course_name' => $row['course_name'],
                'outline' => $row['outline'],
                'activity_num' => $row['activity_num'],
                'score' => $row['score'],
                'course_time' => $row['course_time']
            );
        }

        if(empty($course_new)) $course_new = false;
        echo json_encode(array('success' => true, 'data' => $course_new));
    }

    /**
    * 获取课程明细信息
    */
    public function course_detail($course_id=null, $username=null) {
        $flag = true;
        if(empty($course_id) || empty($username)) {
            $flag = false;
            $course_id = $this->input->post('course_id');
            $username = $this->input->post('username');
            if(empty($course_id) || empty($username)) exit(json_encode_common(false, $this->lang('ParamsErr')));
        }

        $where = " and id='{$course_id}'";
        $ret= $this->course_mod->course_list(array('where' => $where));

        //数据整合
        $course_detail = array();
        if( !empty($ret['rows'][0])) {
            $info = $ret['rows'][0];
            $course_detail = array(
                'id' => $info['id'],
                'course_name' => $info['course_name'],
                'operate_time' => $info['operate_time'],
                'activity_num' => $info['activity_num'],
                'limit_num' => $info['limit_num'],
                'status' => $info['status'],
                'status_trans' => $info['status_trans'],
                'close_time' => $info['close_time'],
                'list' => array(
                    array('任课老师', $info['teacher_names']),
                    array('上课地点', $info['place']),
                    array('上课时间', $info['course_time']),
                    array('课程简介', $info['outline']),
                    array('课程学分', $info['score'] . '分')
                )
            );
        }

        //返回整理好格式的课程明细数据
        if($flag) {
            return $course_detail;
        }

        if(empty($course_detail)) {
            exit(json_encode_common(false, $this->lang('ErrOperater')));
        }

        //该用户报名状态
        $apply_status = $this->course_mod->api_course_apply_again($course_id, $username);

        echo json_encode(array('success' => true, 'data' => $course_detail, 'apply_status' => $apply_status));

    }

    /**
    * 获取我参与的课程列表
    */
    public function course_self() {
        $username = $this->input->post('username');
        $type = intval($this->input->post('type'));
        if(empty($username) || $username == 'undefined') exit(json_encode_common(false, $this->lang('ParamsErr')));

        $apply_course = $this->course_mod->api_apply_course($username, $type);

        //数据整合
        $course_new = array();
        if( !empty($apply_course)) {
            $where = " and id in ("  . implode(',', $apply_course). ')';
            $course_list = $this->course_mod->course_list(array('pagination' => false, 'where' => $where));

            foreach($course_list['rows'] as $row) {
                $course_new[] = array(
                    'id' => $row['id'],
                    'course_name' => $row['course_name'],
                    'outline' => $row['outline'],
                    'activity_num' => $row['activity_num'],
                    'score' => $row['score'],
                    'course_time' => $row['course_time']
                );
            }
        }

        if(empty($course_new)) $course_new = false;
        echo json_encode(array('success' => true, 'data' => $course_new));
    }

    /**
    * 课程报名
    */
    public function course_apply() {
        $course_id = $this->input->post('course_id');
        $username = $this->input->post('username');
        if(empty($course_id) || empty($username) || $username == 'undefined') exit(json_encode_common(false, $this->lang('ParamsErr')));

        $params = array('course_id' => $course_id, 'username' => $username);
        $ret = $this->course_mod->api_course_apply($params);
        $ret['msg'] = $this->lang($ret['msg']);

        //报名成功则获取最新的课程明细数据返回给小程序异步展现页面
        if($ret['success']) $ret['course_detail'] = $this->course_detail($course_id, $username);

        echo json_encode($ret);
    }

    /**
    * 二维码扫码签到
    */
    public function qrcode_sign() {
        //课程id获取
        $param = $this->input->get('param');
        if(empty($param)) exit(json_encode_common(false, $this->lang('ParamsErr')));
        $course_id = url_decrypt($param);
        if(empty($course_id)) exit(json_encode_common(false, $this->lang('ParamsErr')));

        //用户id获取
        $username = $this->input->post('param2');
        if(empty($username)) $this->weui_msg($this->lang('ParamsScanErr'));

        //信息判断
        $template_success = "%s！, 签到时间为: %s, 获得学分为：%s 分";  //签到成功返回模板
        $where = " and a.course_id='{$course_id}' and a.username='{$username}'";
        $course_student = $this->course_mod->student_list(array('where' => $where));
        $course_student = $course_student['rows'][0];
        if(empty($course_student)) exit(json_encode_common(false, $this->lang('SignFailed')));
        if($course_student['is_sign']) exit(json_encode_common(false, sprintf($template_success, '您已签到过了', $course_student['sign_time'], intval($course_student['score']))));

        //增加签到信息
        $now = gettime();
        $student_id = $course_student['student_id'];
        $where_student = "id = '{$student_id}'";
        $data = array(
            'score' => $course_student['course_store'],
            'is_sign' => 1,
            'sign_time' => $now
        );
        $ret = $this->course_mod->student_edit($where_student, $data);
        if(!$ret) exit(json_encode_common(false, $this->lang('SignFailedError')));
        if($data['sign_time'] > $course_student['late_time']) exit(json_encode_common(true, sprintf($template_success, '您迟到了', date('Y-m-d H:i', $data['sign_time']), intval($data['score']))));
        echo json_encode_common(true, sprintf($template_success, '您已签到成功', date('Y-m-d H:i', $data['sign_time']), intval($data['score'])));
    }
}