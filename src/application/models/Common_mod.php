<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** @name Common_mod
** @desc 通用的模型
**/
class Common_mod extends MY_Model {

    /**
     * @todo 通用标识函数,设置获取参数
     * @param $name
     * @param null $value
     * @param bool $getcache
     * @return array|mixed|null
     */
    public function getlog($name,$value=NULL,$getcache=TRUE){
        $name = md5($name);
        $key = 'getlog_'.$name;
        if( NULL !== $value ){
            if( $value === false ){//删除
                $this->initdb()->query("DELETE FROM `log` WHERE `name` = '{$name}'");
                $this->cache->delete($key);
            }else{
                $datetime = date('Y-m-d H:i:s', time());
                $upvalue = base64_encode(gzcompress(serialize($value)));
                $sql = "INSERT INTO `log`(`name`,`value`,`time`)VALUES('{$name}','{$upvalue}','{$datetime}') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`),`time` = VALUES(`time`)";
                $this->initdb()->query($sql);
                $this->cache->set($key,$value,86400*31);
            }
        }else{
            if( $getcache == TRUE )	{
                if( ($value = $this->cache->get($key)) === false ){
                    if( ($value = $this->db_scalar('log','value',"name = '{$name}'"))!==false ){
                        $value = unserialize(gzuncompress(base64_decode($value)));
                        $this->cache->set($key,$value,86400*31);
                    }
                }
            }else{
                $value = unserialize(gzuncompress(base64_decode($this->db_scalar('log','value',"name = '{$name}'"))));
            }
        }
        return $value;
    }

    /**
    * 导出excel——通用
    *
    * @param array $param 参数数组
    */
    public function export_excel($param) {
        //传递参数
        $data = $param['data'];  //导出数据
        $table_head = $param['table_head'];  //导出excel标题
        $excel_title = $param['excel_title'] . '_' . date('YmdHis', gettime());  //导出excel文件名

        set_time_limit(0);
        ini_set('memory_limit', -1);

        $this->load->model('admin/Excel_mod','excel_mod');
        $this->excel_mod->setsheet('0');

        $row_num = 1;  //行数
        $column_num = 0;  //标志第几列,0列代表A

        //标题
        foreach($table_head as $title) {
            $temp = IntToChr($column_num);  //当前列标
            $this->excel_mod->setCellwidth($temp, 20, 0);
            $this->excel_mod->setCellValue($temp . $row_num, $title);
            $column_num++;
        }
        $row_num++;
        $end = IntToChr($column_num-1);  //标志最后一列

        //数据
        $column_num = 0;
        foreach($data as $d_k => $row) {
            foreach($table_head as $k => $value) {
                $this->excel_mod->setCellValue(IntToChr($column_num) . $row_num, strip_tags($row[$k]));
                $column_num++;
            }
            $row_num++;
            $column_num = 0;

            unset($data[$d_k]);
        }
        unset($table_head);
        $end_row = $row_num-1;  //标志最后一行

        /**---------------------------------------样式代码------------------------------------**/
        //设置单元格字体样式
        $this->excel_mod->setFont('0',12);
        //设置单元格位置对齐方式
        $this->excel_mod->setCellAlign('A1:' . $end . $end_row);
        //设置单元格边框
        $this->excel_mod->setCellBorder('A1:' . $end . $end_row);
        /**-------------------------------------样式代码结束------------------------------------**/

        $this->excel_mod->saveExcel($excel_title, 'excel7');
    }
}

/* End of file Common_mod.php */
/* Location: application/models/Common_mod.php */