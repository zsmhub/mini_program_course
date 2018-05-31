<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
** @name Excel_mod
** @desc EXCEL表格处理
**/
class Excel_mod extends MY_Model {
	private $phpexcel; 							//PHPExcel 类
	private $nowsheet;							//目前正在操作的sheet
	private $factory;

	public function __construct(){
		include_once APPPATH.'libraries/PHPExcel.php';
		$this->phpexcel = new PHPExcel();
		$this->createSheet(1);
		$this->setsheet('0');					//默认目前操作sheet 为0
	}

	//设置当前sheet
	public function setsheet($index){
		$this->nowsheet = $index;
		$this->phpexcel->setActiveSheetIndex($this->nowsheet);
	}

	//创建 sheet
	public function createSheet($num=1){
		for($i = 0; $i < $num; $i++){
			$this->phpexcel->createSheet();
		}
	}

	public function &createReaderForFile($file){
		return PHPExcel_IOFactory::createReaderForFile($file);
	}

	public function &createReader($readerType='Excel2007'){
		return PHPExcel_IOFactory::createReader($readerType);
	}

	//设置属性(Creator,LastModifiedBy,Title,Subject,Description,Keywords,Category)
	public function setProperty($Propertys=array()){
		foreach ($Propertys as $key=>$value){
			$func = 'set'.$key;
			if( method_exists($this->phpexcel->getProperties(), $func) ){
				$this->phpexcel->getProperties()->$func($value);
			}
		}
	}

	/*
	 * 给多个sheet命名
	 * @param array $name
	 * @example setsheetName(('1','2'))
 	 * */
	public function setsheetName($name){
		if(is_array($name)){
			foreach($name as $key => $value){
				$this->setsheet($key);
				$this->phpexcel->getActiveSheet()->setTitle($value);
			}
		}else{
			$this->phpexcel->getActiveSheet()->setTitle($name);
		}
	}

	/**
	 * 合并单元格
	 * @param array $merges 开始 结束的单元
	 * @param int   $sheet  操作的sheet
	 * @example setMergeCells('A1:B1')
	 */
	public  function  setMergeCells($merges, $sheet='0'){
		if ( $sheet != $this->nowsheet ){
			$this->phpexcel->setActiveSheetIndex($sheet);
		}
		if (is_array($merges)){
			foreach ( $merges as $key => $val){
				$this->phpexcel->getActiveSheet()->mergeCells($val);
			}
		} else{
			$this->phpexcel->getActiveSheet()->mergeCells($merges);
		}
	}

	/**
	 * 合并单元格
	 * @param array $merges 开始 结束的单元
	 * @param int   $sheet  操作的sheet
	 * @example setmUnergeCells('A1:B1')
	 */
	public function setUnmergeCells($merges,$sheet = ''){
		if ($sheet != $this->nowsheet){
			$this->phpexcel->setActiveSheetIndex($sheet);
		}
		if (is_array($merges)){
			foreach ( $merges as $key => $val){
				$this->phpexcel->getActiveSheet()->mergeCells($val);
			}
		} else{
			$this->phpexcel->getActiveSheet()->unmergeCells($merges);
		}
	}

	/**
	 * 单元格宽度
	 * @param string $cell 单元 一个 或者连续几个
	 * @param int   $sheet  操作的sheet
	 * @param int   $width
	 */
	public function setCellwidth($cell, $width,$sheet=''){
		if ($sheet != $this->nowsheet){
			$this->phpexcel->setActiveSheetIndex($sheet);
		}
		$this->phpexcel->setActiveSheetIndex(0);
		if (is_array($cell) && is_array($width)){
			for ($i = 0; $i < count($cell); $i++){
				if ($width[$i] == 'auto'){
					$this->phpexcel->getActiveSheet()->getColumnDimension($cell[$i])->setAutoSize(true);
				} else{
					$this->phpexcel->getActiveSheet()->getColumnDimension($cell[$i])->setWidth($width[$i]);
				}
				$this->phpexcel->getActiveSheet()->getStyle($cell[$i])->getAlignment()->setWrapText(true);
			}
		} else{
			$this->phpexcel->getActiveSheet()->getColumnDimension($cell)->setWidth($width);
			$this->phpexcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
		}
	}
	//设置单元格是否自动换行
	public function setWrapTextOf($cell, $wrap, $sheet=''){
		if ($sheet != $this->nowsheet){
			$this->phpexcel->setActiveSheetIndex($sheet);
		}
		if (is_array($cell) && is_array($wrap)){
			for ($i = 0; $i < count($cell); $i++){
				$this->phpexcel->getActiveSheet()->getStyle($cell[$i])->getAlignment()->setWrapText($wrap[$i]);
			}
		} else{
			$this->phpexcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText($wrap);
		}
	}


	public function setRowHeight($row, $height, $sheet=''){
		if ($sheet != $this->nowsheet){
			$this->phpexcel->setActiveSheetIndex($sheet);
		}
		$this->phpexcel->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
	}

	/*
	 * 单元格字体样式设置
	 * @param string $cell 0为 所有单元格
	 * @param int $size  字体大小
	 * @param string $blod 字体粗细
	 * @param string  $name 字体
	 * @param string  $rgb 字体颜色
	 * @param string $underline  下划线
	 * array (
				PHPExcel_Style_Font::UNDERLINE_NONE,
				PHPExcel_Style_Font::UNDERLINE_DOUBLE,
				PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING,
				PHPExcel_Style_Font::UNDERLINE_SINGLE,
				PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING
			)
	 * */
	public function setFont($cell, $size='14', $blod=false, $name='', $rgb=PHPExcel_Style_Color::COLOR_BLACK,$underline=PHPExcel_Style_Font::UNDERLINE_NONE){
		$font = $cell == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle()->getFont() : $this->phpexcel->getActiveSheet()->getStyle($cell)->getFont();
		$font->setSize($size);
		$font->setBold($blod);
		$name == '' ? '' : $font->setName($name);
		$rgb == '' ? '' :$font->getColor()->setARGB($rgb);
		$font->setUnderline($underline);
		//$font->setColor($color);
	}

	/*
	 * 通过数组来设置单元格样式
	 * @param array $fontarray
	 * @param string $cell 0为所有单元格
	 * */
	public function setFontarray($fontarray , $cell){
		$font = $cell == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle()->getFont() : $this->phpexcel->getActiveSheet()->getStyle($cell)->getFont();
		$font->applyFromArray($fontarray);
	}

	public function setFormatCode($code , $cell){
		if( is_array($cell) ){
			for($i=0; $i<count($cell); $i++){
				$font = $cell[$i] == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle()->getNumberFormat() : $this->phpexcel->getActiveSheet()->getStyle($cell[$i])->getNumberFormat();
				if( is_array($code) ){
					$font->setFormatCode($code[$i]);
				}else{
					$font->setFormatCode($code);
				}

			}
		}else{
			$font = $cell == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle()->getNumberFormat() : $this->phpexcel->getActiveSheet()->getStyle($cell)->getNumberFormat();
			$font->setFormatCode($code);
		}
	}

	/*
	 * 设置单元格位置
	 * @param string $horizontal 水平
	 * @param string $vertical 垂直
	 * @param string $cell 0 所有的单元格
	 * */
	public function setCellAlign($cell='0', $horizontal = PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,$vertical=PHPExcel_Style_Alignment::VERTICAL_CENTER){
		$style = $cell == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle() : $this->phpexcel->getActiveSheet()->getStyle($cell);
		$style->getAlignment()->setHorizontal($horizontal);
		$style->getAlignment()->setVertical($vertical);
	}

	public function setCellValue($cell,$value){
		if (is_array($cell) && is_array($value)){
			for ($i = 0; $i < count($cell); $i++){
				$this->phpexcel->getActiveSheet()->setCellValue($cell[$i], $value[$i]);
			}
		} else{
			$this->phpexcel->getActiveSheet()->setCellValue($cell, $value);
		}
	}
	/*
	 * 设置列总和
	 * @param array $cell
	 * @param int $begin 起始
	 * @param int $end 结束
	 * @param array $end 单元格数组
	 * @param array $end 值数组
	 * */
	public function getCellSum($cell, $begin, $end, &$cellArr, &$valueArr){
		$_cell = $_value = array();
		if( is_array($cell) ){
			for( $i = 0; $i < count($cell); $i++ ){
				$cellArr[] = $cell[$i].($end + 1);
				$valueArr[] = '=SUM('.$cell[$i].$begin.':'.$cell[$i].$end.')';
			}
		}else{
			$cellArr[] = $cell.($end + 1);
			$valueArr[] = '=SUM('.$cell.$begin.':'.$cell.$end.')';
		}
	}

	public function setFormartCodeDetail($cell,$value = PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1){
		if (is_array($cell)){
			for ($i = 0; $i < count($cell); $i++){
				$this->phpexcel->getActiveSheet()->getStyle($cell[$i])->getNumberFormat()->setFormatCode($value);
			}
		} else{
			$this->phpexcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode($value);
		}
	}

	/*
	 * 设置边框
	 * @param string $cell 0 所有
	 * @param string $align
	 * @param string $style 边框线条
	 * @param string $color 边框颜色
	 * */
	public function setCellBorder($cell,$align='all',$sty = PHPExcel_Style_Border::BORDER_THIN,$color='#000'){
		$style = $cell == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle() : $this->phpexcel->getActiveSheet()->getStyle($cell);
		switch ($align){
			case 'top':
				$border =& $style->getBorders()->getTop();
				break;
			case 'bottom' :
				$border =& $style->getBorders()->getBottom();
				break;
			case 'left' :
				$border =& $style->getBorders()->getLeft();
				break;
			case 'right':
				$border =& $style->getBorders()->getRight();
				break;
			default:
				$border =& $style->getBorders()->getAllBorders();
				break;
		}
		$border->setBorderStyle($sty);
		$border->getColor()->setARGB($color);
	}

	/*
	 * 设置边框填充颜色
	 * @param string $cell
	 * @param string $color
	 * @param type $type
	 * */
	public function setCellColor($cell, $color,$type = PHPExcel_Style_Fill::FILL_SOLID ){
		$style = $cell == '0' ? $this->phpexcel->getActiveSheet()->getDefaultStyle() : $this->phpexcel->getActiveSheet()->getStyle($cell);
		$style->getFill()->setFillType($type);
 		$style->getFill()->getStartColor()->setARGB($color);
	}
	/*
	 * 导出excel
	 * @param string $title 文件名
	 * @param string $type 导出文件类型
	 * */
	public function saveExcel($title,$type='excel5'){
		if ($type == 'excel5'){
			header('Content-Type: application/vnd.ms-excel');
			$ext = '.xls';
			$create = 'Excel5';
		} elseif($type == 'excel7' ){
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			$ext = '.xlsx';
			$create = 'Excel2007';
		}
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/download");
		header('Content-Tyep:text/html;charset=utf-8');
		header('Content-Disposition: attachment;filename="'.iconv('utf-8', "gb2312", $title).$ext.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, $create);
		$objWriter->save('php://output');
	}

	//保存excel到本地
	public function saveNative($title,$type='excel5'){
		if ($type == 'excel5'){
			$ext = '.xls';
		} elseif($type == 'excel7' ){
			$ext = '.xlsx';
		}
		$objWriter = new PHPExcel_Writer_Excel5($this->phpexcel);
		$objWriter->save(FCPATH.'style/new/plugin/'.iconv('utf-8', "gb2312", $title).$ext);
	}

	//自定义路径保存
	public function pathSave($title,$path,$type='excel5'){
		if($type == 'excel5'){
			$ext = '.xls';
            $create = 'Excel5';
		}elseif($type == 'excel7'){
			$ext = '.xlsx';
            $create = 'Excel2007';
		}
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, $create);
		$objWriter->save(APPPATH.$path.$title.$ext);
	}

	//修改
	public function editSave($filepath,$cell,$value,$title,$type='excel5'){
		$objPHPExcel = PHPExcel_IOFactory::load($filepath);
		$objPHPExcel->getSheet(0)->setCellValue($cell,$value);
		if ($type == 'excel5'){
			$ext = '.xls';
		} elseif($type == 'excel7' ){
			$ext = '.xlsx';
		}
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter->save(FCPATH.'style/new/plugin/'.iconv('utf-8', "gb2312", $title).$ext);
	}

	public function editSheet($filepath,$cell,$value,$title){
		$this->phpexcel = PHPExcel_IOFactory::load($filepath);
		$this->phpexcel->createSheet(1);
		$sheet = $this->phpexcel->getSheet(1);
		$this->phpexcel->setActiveSheetIndex(1);
		if (is_array($cell) && is_array($value)){
			for ($i = 0; $i < count($cell); $i++){
				$this->phpexcel->getActiveSheet()->setCellValue($cell[$i], $value[$i]);
			}
		} else{
			$this->phpexcel->getActiveSheet()->setCellValue($cell, $value);
		}
		$objWriter = new PHPExcel_Writer_Excel5($this->phpexcel);
		$objWriter->save(FCPATH.'style/new/plugin/'.iconv('utf-8', "gb2312", $title).'.xls');
	}

	public function edit1($filepath){
		$this->phpexcel = PHPExcel_IOFactory::load($filepath);
		$this->phpexcel->createSheet(1);
		$sheet = $this->phpexcel->getSheet(1);
		$this->phpexcel->setActiveSheetIndex(1);
	}

	public function edit2($title){
		$objWriter = new PHPExcel_Writer_Excel5($this->phpexcel);
		$objWriter->save(FCPATH.'style/new/plugin/'.iconv('utf-8', "gb2312", $title).'.xls');
	}

	//设置特定单元格为文本格式
	public function setFormatToText($cell,$value){
		$this->phpexcel->getActiveSheet()->setCellValueExplicit($cell,$value,PHPExcel_Cell_DataType::TYPE_STRING);
	}

	//文本格式输出($num_cell = array('A')) ==> 设置A列为数字格式
	public function setCellValueToText($cell,$value,$num_cell=array()){
		if(is_array($cell) && is_array($value) && count($cell) == count($value) ){
			for($i=0;$i<count($cell);$i++){
				if(in_array($cell[$i][0], $num_cell))
					$this->phpexcel->getActiveSheet()->setCellValue($cell[$i], $value[$i]);
				else
					$this->phpexcel->getActiveSheet()->setCellValueExplicit($cell[$i],$value[$i]);
			}
		}else{
			$this->phpexcel->getActiveSheet()->setCellValueExplicit($cell,$value);
		}
	}

	/**
	**	@author loong
	**	@todo 读取excel数据
	**	@param $filename 文件名
	**	@param $startRow 从哪一行开始读取
	**	@param $endRow 读取行数
	**	@param $sheetindex 数据表格下标
	**/
	public function readExcel($filename,$startRow=1, $endRow=null,$sheetindex=0){
        $excelReader =& $this->createReaderForFile($filename);
        $excelReader->setReadDataOnly(true);
        //如果有指定行数，则设置过滤器
	    if ($startRow && $endRow) {
	        $perf           = new MyPHPExcelReadFilter();
	        $perf->startRow = $startRow;
	        $perf->endRow   = $endRow;
	        $excelReader->setReadFilter($perf);
	    }

        $activeSheet = $excelReader->load($filename)->getSheet($sheetindex);
        // return $activeSheet->toArray();
        if (!$endRow) {
	        $endRow = $activeSheet->getHighestRow(); //总行数
	    }
	    $highestColumn      = $activeSheet->getHighestColumn(); //最后列数所对应的字母，例如第2列就是B
    	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //总列数

	    $data = array(); 
        for ($row = $startRow; $row <= $endRow; $row++) { 
            for ($col = 0; $col < $highestColumnIndex; $col++) { 
                $data[$row][] = (string) $activeSheet->getCellByColumnAndRow($col, $row)->getValue(); 
            } 
            if(implode($data[$row], '') == '') { 
                unset($data[$row]); 
            } 
        } 
        return $data; 
	}

}


/* End of file Excel_mod.php */
/* Location: application/models/admin/Excel_mod.php */