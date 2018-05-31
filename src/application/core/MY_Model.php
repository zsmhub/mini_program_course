<?php
class MY_Model extends CI_Model {
    public $website;
    private $default_db = 'default';
    private $_db = array();

    public function __construct(){
        parent::__construct();

        //获取网站域名
        $this->load->helper('url');
        $this->website = str_replace('index.php', '', site_url());
    }

	public function __call($method, $arg_array){
		$CI =& get_instance();
		if( method_exists($CI, $method) ){
			return call_user_func_array(array(&$CI,$method),$arg_array);
		}
	}

    protected function db_change($db_name){
        $this->default_db = $db_name;
    }
    protected function db_trans_start($dbgroup=''){
        $this->initdb($dbgroup)->trans_start();
    }
    protected function db_trans_complete($dbgroup=''){
        $this->initdb($dbgroup)->trans_complete();
    }
    protected function db_trans_status($dbgroup=''){
        return $this->initdb($dbgroup)->trans_status();
    }
    public function initdb($dbgroup=''){
        $dbgroup = empty($dbgroup) ? $this->default_db : $dbgroup;
        if( !isset($this->_db[$dbgroup]) || !is_object($this->_db[$dbgroup]) ){
            $this->_db[$dbgroup] = $this->load->database($dbgroup,TRUE);
        }
        return $this->_db[$dbgroup];
    }

    public function db_query($sql,$dbgroup=''){
        return $this->initdb($dbgroup)->query($sql);
    }

    /**
     * @todo 获取记录集
     * @param string $sql
     * @param string $ArrIndex 作为返回数组的下标字段
     * @param int $pagesize 返回记录的条数
     * @param int $offset 返回记录的起始行
     * @return array
     */
    public function db_getResultSet($sql,$ArrIndex='',$offset=NULL,$pagesize=NULL,$dbgroup=''){
        if( $offset!==null && $offset >= 0 && $pagesize > 0 ){
            $sql .= ' LIMIT '.$offset.','.$pagesize;
        }elseif ( $pagesize > 0 ){
            $sql .= ' LIMIT '.$pagesize;
        }
        $ret = array() ;
        $query = $this->db_query($sql,$dbgroup);
        if ($query->num_rows() > 0){
            foreach ($query->result_array() as $row){
                if( $ArrIndex!='' ){
                    $ret[$row[$ArrIndex]] = $row;
                }else{
                    $ret[] = $row;
                }
            }
        }
        return $ret;
    }

    /**
     * @todo 根据SQL语句获取单条记录
     * @param string $sql
     * @return array
     */
    public function db_getOneBySql($sql,$dbgroup=''){
        if( !preg_match('/LIMIT/i',$sql) ) $sql .= ' LIMIT 1';
        return $this->db_query($sql,$dbgroup)->row_array();
    }

    /**
     * @todo 获取单条记录
     * @param string $table 表名
     * @param string $field 字段列表
     * @param string $where 查询条件
     * @return array
     */
    public function db_getOne($table,$field='*',$where='',$dbgroup=''){
        return $this->db_getOneBySql("SELECT {$field} FROM {$table}".(!empty($where)?' WHERE '.$where:''),$dbgroup);
    }

    /**
     * @todo 根据SQL语句获取第一条记录的$rfield字段值
     * @param string $sql
     * @param string $rfield 要返回的字段名
     * @return array
     */
    public function db_scalarBySql($sql,$rfield='',$dbgroup=''){
        $ret = '';
        if( $row = $this->db_getOneBySql($sql,$dbgroup) ){
            $ret =  $rfield ? $row[$rfield] : current($row);
        }
        return $ret;
    }

    /**
     * @todo 获取第一条记录的$rfield字段值
     * @param string $table 表名
     * @param string $rfield 要返回的字段名
     * @param string $where 查询条件
     * @return array
     */
    public function db_scalar($table,$rfield='',$where='',$dbgroup=''){
        return $this->db_scalarBySql("SELECT {$rfield} FROM {$table}".(!empty($where)?' WHERE '.$where:''),$rfield,$dbgroup);
    }

    public function db_update($table,$data=array(),$where=array(),$dbgroup=''){
        return $this->initdb($dbgroup)->update($table, $data, $where);
    }

    public function db_insert($table,$data=array(),$dbgroup=''){
        $this->initdb($dbgroup)->insert($table,$data);
        return $this->initdb($dbgroup)->insert_id();
    }

    public function db_insert_batch($table,$data=array(),$dbgroup=''){
        return $this->initdb($dbgroup)->insert_batch($table,$data);
    }

    public function db_insert_duplicate($table,$insertdata=array(),$updateField=array(),$returnSqlOnly=false,$dbgroup=''){
        if( empty($insertdata) ) return ;
        $sql = "INSERT INTO {$table}";
        if( isset($insertdata[0]) ){
            $sql .= '(`'.implode('`,`',array_keys($insertdata[0])).'`) VALUES ';
            foreach ($insertdata as $dataarr){
                $sql .= '(\''.implode("','",array_values($dataarr)).'\'),';
            }
            $sql = rtrim($sql,',');
        }else{
            $sql .= '(`'.implode('`,`',array_keys($insertdata)).'`) VALUES (\''.implode("','",array_values($insertdata)).'\')';
        }
        if( $updateField ){
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            if( isset($updateField[0]) ){
                foreach ($updateField as $f){
                    $sql .= ($f.' = VALUES('.$f.'),');
                }
            }else{
                foreach ($updateField as $k=>$v){
                    $sql .= "`{$k}` = `{$v}`,";
                }
            }
            $sql = rtrim($sql,',');
        }
        return $returnSqlOnly ? $sql : $this->db_query($sql,$dbgroup);
    }

    public function db_delete($table, $where=array(), $dbgroup=''){
        return $this->initdb($dbgroup)->delete($table, $where);
    }

    /**
     * 获取数据库数据
     *
     * @param array $params 参数数组
     * @author zhangshimian<张仕勉>
     * @return array
     */
    public function get_datagrid_data($params=array()) {
        //数据库组
        $db_group = isset($params['db_group']) ? $params['db_group'] : '';

        //多表sql连接查询
        $sql = isset($params['sql']) ? $params['sql'] : false;

        //单表查询的表名
        $model_name = isset($params['model_name']) ? $params['model_name'] : '';

        //条件数组
        $where = (isset($params['where']) && !empty($params['where'])) ? $params['where'] : '';

        //group分组
        $group_by = isset($params['group_by']) ? $params['group_by'] : '';

        //排序
        $order = isset($params['order']) ? $params['order'] : '';

        //是否分页
        $pagination = isset($params['pagination']) ? $params['pagination'] : FALSE;

        //页数
        $page = isset($params['page']) ? $params['page'] : 1;

        //行数
        $rows = isset($params['rows']) ? $params['rows'] : 20;

        //是否返回datagrid数组格式的数据标志
        $flag = isset($params['flag']) ? $params['flag'] : false;

        if($flag) {
            $sortPost = $this->input->post('sort');
            $orderPost = $this->input->post('order');
            $pagePost = $this->input->post('page');
            $rowsPost = $this->input->post('rows');
            if( !empty($sortPost) && !empty($orderPost)) {
                $order = $sortPost . ' ' . $orderPost;
            }
        }

        //获取记录
        $db = $this->initdb($db_group);

        if($sql !== false) {  //多表sql连接查询
            $sql_query = $sql;
            if($pagination === TRUE) {
                if($flag) {
                    $page = !empty($pagePost) ? $pagePost : $page;
                    $rows = !empty($rowsPost) ? $rowsPost : $rows;
                }
                $offset = ($page - 1) * $rows;
                $sql_query .= ' order by ' . $order . ' limit ' . $offset . ',' . $rows;
            }
            $list = $db->query($sql_query)->result_array();
        } else {  //单表查询
            if( !empty($model_name)) $db->from($model_name);
            if( !empty($where)) $db->where($where);
            if( !empty($group_by)) $db->group_by($group_by);
            if( !empty($order)) $db->order_by($order);
            if($pagination === TRUE) {
                if($flag) {
                    $page = !empty($pagePost) ? $pagePost : $page;
                    $rows = !empty($rowsPost) ? $rowsPost : $rows;
                }
                $offset = ($page - 1) * $rows;
                $list = $db->limit($rows, $offset)->get()->result_array();
            } else {
                $list = $db->get()->result_array();
            }
        }

        unset($db);

        if($list === NULL) {
            $list = array();
        }

        if($flag) {
            $db = $this->initdb($db_group);
            if($sql !== false) {  //多表sql连接查询
                $total = count($db->query($sql)->result_array());
            } else {  //单表查询
                //计算记录总数
                if( !empty($model_name)) $db->from($model_name);
                if( !empty($where)) $db->where($where);
                if( !empty($group_by)) $db->group_by($group_by);
                $total = count($db->get()->result_array());
            }
            unset($db);

            $result = array(
                'total' => $total,
                'rows' => $list
            );
            return $result;
        } else {
            return $list;
        }
    }

    /**
     * @todo 字段自增
     * @author zhangshimian<张仕勉>
     * @param array $params 参数数组
     */
    public function auto_increase($params=array()) {
        $dbgroup = isset($params['db_group']) ? $params['db_group'] : '';  //数据库
        $tableName = isset($params['table_name']) ? $params['table_name'] : FALSE;  //表名
        $field = isset($params['field']) ? $params['field'] : FALSE;  //字段名
        $where = isset($params['where']) ? $params['where'] : array();  //条件数组
        $method = isset($params['method']) ? $params['method'] : '+';  //默认自增,自增或自减，分别为+/-
        $num = isset($params['num']) ? $params['num'] : 1;  //默认自增1

        if($tableName !== FALSE || $field !== FALSE) {
            $this->initdb($dbgroup)->set($field, "$field{$method}{$num}", FALSE);
            $this->initdb($dbgroup)->where($where)->update($tableName);
        }
    }
}