<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 课程管理
*/
class Course_mod extends MY_Model {
    private $table_course = 'mini_course';
    private $table_teacher = 'mini_teacher';
    private $table_student = 'mini_student';

    /**
    * 课程列表
    */
    public function course_list($params=array()) {
        $where = "deleted = 0";
        if(isset($params['where'])) $where .= $params['where'];
        $params_arr = array(
            'flag' => isset($params['flag']) ? $params['flag'] : true,
            'model_name' => $this->table_course,
            'where' => $where,
            'order' => 'id desc',
            'pagination' => isset($params['pagination']) ? $params['pagination'] : true
        );
        $result = $this->get_datagrid_data($params_arr);
        foreach($result['rows'] as &$row) {
            //课程报名状态
            $row['status_trans'] = '报名中';
            $row['status'] = '0';
            if($row['limit_num'] > 0 && $row['activity_num'] >= $row['limit_num']) {
                $row['status_trans'] = '名额满';
                $row['status'] = '1';
            }

            //人数限制情况
            $row['limit_trans'] = $row['limit_num'];
            if($row['limit_num'] <= 0) {
                $row['limit_trans'] = '无限制';
            }

            if($row['close_time'] <= gettime()) {
                $row['status_trans'] = '已结束';
                $row['status'] = '2';
            }

            $row['teacher_ids'] = substr($row['teacher_ids'], 1, -1);
            $row['close_time'] = date('Y-m-d H:i', $row['close_time']);
            $row['late_time'] = date('Y-m-d H:i', $row['late_time']);
            $row['operate_time'] = date('Y-m-d H:i', $row['operate_time']);
            $row['score'] = intval($row['score']);
        }
        return $result;
    }

    /**
    * 添加课程
    */
    public function course_add($params=array()) {
        $this->initdb()->trans_begin();

        //添加课程
        $teacher_ids = $params['teacher_ids'];
        $params['teacher_ids'] = '|' . $teacher_ids . '|';
        $this->initdb()->insert($this->table_course, $params);
        $course_id = $this->initdb()->insert_id();

        //添加课程任课老师
        $teacher_ids_arr = explode('|', $teacher_ids);
        $db_teacher = array();
        foreach($teacher_ids_arr as $username) {
            $db_teacher[] = array(
                'username' => $username,
                'course_id' => $course_id,
                'operate_time' => gettime()
            );
        }
        $this->initdb()->insert_batch($this->table_teacher, $db_teacher);

        if($this->initdb()->trans_status() === FALSE) {
            $this->initdb()->trans_rollback();
            return false;
        }

        $this->initdb()->trans_commit();
        return true;
    }

    /**
    * 编辑课程
    */
    public function course_edit($params=array()) {
        if(empty($id = $params['id'])) return false;
        unset($params['id']);

        $this->initdb()->trans_begin();

        //删除旧的的任课老师信息
        $old_data = $this->initdb()->select('teacher_ids')->where('id', $id)->get($this->table_course)->row_array();
        $old_teacher_ids = substr($old_data['teacher_ids'], 1, -1);
        $old_teacher_ids = "'" . str_replace('|', "','", $old_teacher_ids) . "'";
        $where_del = "course_id = '{$id}' and username in ({$old_teacher_ids})";
        $this->initdb()->where($where_del)->delete($this->table_teacher);

        //编辑课程
        $teacher_ids = $params['teacher_ids'];
        $params['teacher_ids'] = '|' . $teacher_ids . '|';
        $this->initdb()->where('id', $id)->update($this->table_course, $params);

        //重新添加新的任课老师
        $teacher_ids_arr = explode('|', $teacher_ids);
        $db_teacher = array();
        foreach($teacher_ids_arr as $username) {
            $db_teacher[] = array(
                'username' => $username,
                'course_id' => $id,
                'operate_time' => gettime()
            );
        }
        $this->initdb()->insert_batch($this->table_teacher, $db_teacher);

        if($this->initdb()->trans_status() === FALSE) {
            $this->initdb()->trans_rollback();
            return false;
        }

        $this->initdb()->trans_commit();
        return true;
    }

    /**
     * 删除课程
     */
    public function course_del($id){
        $this->initdb()->update($this->table_course,array('deleted'=>1),"id = '{$id}'");
        return true;
    }

    /**
    * 老师参与课程列表
    */
    public function teacher_list($params=array()) {
        $sql = "SELECT c.nickname, c.college, c.email, b.course_name, b.course_time
                FROM mini_teacher as a
                left join mini_course as b on a.course_id=b.id
                left join mini_user as c on a.username=c.username";
        if(isset($params['where'])) $sql .= ' where ' . $params['where'];
        $params_arr = array(
            'flag' => true,
            'pagination' => true,
            'sql' => $sql,
            'order' => 'a.operate_time desc'
        );
        $result = $this->get_datagrid_data($params_arr);
        return $result;
    }

    /**
    * 学生参与课程列表
    */
    public function student_list($params=array()) {
        $sql = "SELECT c.nickname, c.college, c.email, b.course_name, b.course_time, b.score as course_store, b.late_time, a.id as student_id, a.score, from_unixtime(a.apply_time, '%Y-%m-%d %H:%i') as apply_time, (case when a.is_sign=1 then from_unixtime(a.sign_time, '%Y-%m-%d %H:%i') else '未签到' end) as sign_time, a.is_sign
                FROM mini_student as a
                left join mini_course as b on a.course_id=b.id
                left join mini_user as c on a.username=c.username
                where a.status = 0";
        if(isset($params['where'])) $sql .= $params['where'];
        $params_arr = array(
            'flag' => true,
            'pagination' => true,
            'sql' => $sql,
            'order' => 'a.apply_time desc'
        );
        $result = $this->get_datagrid_data($params_arr);
        return $result;
    }

    /**
     * 生成任意二维码
     *
     * @param array $param 参数数组
     */
    public function make_img_freedom($param=array()){
        if( !class_exists('QRcode') ){
            include APPPATH.'third_party/phpqrcode.php';
        }

        $level = isset($param['level']) ? $param['level'] : 1;
        $size = isset($param['size']) ? $param['size'] : 5;  //点的大小：1到10
        $margin = isset($param['margin']) ? $param['margin'] : 4;  //二维码周围边框空白区域margin值
        $str = isset($param['str']) ? $param['str'] : 'null';  //跳转链接或文字
        QRcode::png($str,false,$level,$size,$margin);
    }

    /**
    * 学生报名
    */
    public function api_course_apply($params=array()) {
        $course_id = $params['course_id'];
        $username = $params['username'];
        $now = gettime();

        //判断课程是否存在
        $data_course = $this->initdb()->query("SELECT * from mini_course where id='{$course_id}' and deleted='0'")->row_array();
        if(empty($data_course)) return array_common(false, 'DeleteCourse');

        //判断是否重复报名
        if($this->api_course_apply_again($course_id, $username)) return array_common(false, 'Applyed');

        //报名截止时间判断
        if( !empty($data_course['close_time']) && $data_course['close_time'] <= $now) return array_common(false, 'OverCourse');

        //限制名额判断
        if($data_course['limit_num'] != 0 && $data_course['limit_num'] <= $data_course['activity_num']) return array_common(false, 'LimitError');

        $this->initdb()->trans_begin();
        $data = array(
            'course_id' => $course_id,
            'username' => $username,
            'apply_time' => $now
        );
        $this->initdb()->insert($this->table_student, $data);
        $insert_id = $this->initdb()->insert_id();

        //插入数据后，限制名额判断
        if($data_course['limit_num'] != 0) {
            $select_student_total = $this->initdb()->query("SELECT count(1) as total from mini_student where course_id='{$course_id}' and status='0'")->row_array();
            if($select_student_total['total'] > $data_course['limit_num']) {
                $this->initdb()->trans_rollback();
                return array_common(false, 'LimitError');
            }
        }

        //已报名人数自增
        $this->auto_increase(array('field' => 'activity_num', 'where' => array('id' => $course_id), 'table_name' => $this->table_course));

        if($this->initdb()->trans_status() === FALSE) {
            $this->initdb()->trans_rollback();
            return array_common(false, 'DBError');
        } else {
            $this->initdb()->trans_commit();

            if($insert_id) {
                return array_common(true, 'ApplySuccess');
            } else {
                return array_common(false, 'DBError');
            }
        }
    }

    /**
    * 查询该用户是否已报名
    */
    public function api_course_apply_again($course_id, $username) {
        //判断是否重复报名
        $select_student = $this->initdb()->query("SELECT id from mini_student where course_id='{$course_id}' and username='{$username}' and status='0'")->row_array();

        if(empty($select_student)) return false;

        return true;
    }

    /**
    * 获取已报名课程id数组
    */
    public function api_apply_course($username, $type) {
        if($type == 1) {  //老师参与课程列表
            $ret = $this->initdb()->query("SELECT course_id from mini_teacher where username = '{$username}'")->result_array();
        } else {  //学生参与课程列表
            $ret = $this->initdb()->query("SELECT course_id from mini_student where username='{$username}' and status='0'")->result_array();
        }

        $course_ids = array();
        foreach($ret as $row) {
            $course_ids[] = $row['course_id'];
        }

        return $course_ids;
    }

    /**
    * 学生参与课程编辑
    */
    public function student_edit($where, $data) {
        $this->initdb()->where($where)->update($this->table_student, $data);
        return $this->initdb()->affected_rows();
    }
}