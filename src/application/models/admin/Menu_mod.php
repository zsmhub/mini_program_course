<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Menu_mod extends MY_Model {

    public function doMenu($data=array()){
        if( $data['ParentId'] < 1 ) return 'ParentIdIsNull';
        if( !$this->getMenuCat($data['ParentId']) ) return 'MenuClassNotExists';
        if( $data['Title'] == '' ) return 'MenuTitleIsNull';
        if( $data['LinkInfo'] =='' ) return 'LinkInfoIsNull';
        if( strpos($data['LinkInfo'],':') === false ) return 'LinkPararmIsError';
        list($ctrl,$action) = explode(':',$data['LinkInfo']);
        $this->load->model('admin/Ctrl_mod');
        $contrlConfig = $this->Ctrl_mod->getConfig();
        if( !isset($contrlConfig[$ctrl]['Methods'][$action]) ) return 'LinkPararmIsError';
        $data['LinkInfo'] = serialize(array('d'=>$contrlConfig[$ctrl]['Dir'],'c'=>$ctrl,'a'=>$action));
        if( isset($data['Id']) ){
            if( !$this->db_scalar('sys_menu','Id','Id = \''.$data['Id'].'\' AND Deleted = \'0\'') ){
                return 'resultNotExists';
            }
            if( $this->db_scalar('sys_menu','Id','Id != \''.$data['Id'].'\' AND Title = \''.$data['Title'].'\' AND Deleted = \'0\'') ){
                return 'MenuTitleIsExists';
            }
            $where = "Id = '{$data['Id']}'";
            unset($data['Id']);
            $this->initdb()->update('sys_menu',$data,$where);
            $this->getMenu(NULL,true);
        }else{
            if( $this->db_scalar('sys_menu','Id','Title = \''.$data['Title'].'\' AND Deleted = \'0\'') ) return 'MenuTitleIsExists';
            if($this->initdb()->insert('sys_menu',$data)){
                $this->getMenu(NULL,true);
            }
        }
        return true;
    }

    public function doMenuCat($params=array()){
        if( $params['Title'] == '' ) return 'ClassNameIsNull';
        if( $params['Status'] != 0 ) $params['Status'] = 1;
        if( isset($params['Id']) && $params['Id'] > 0 ){//修改
            if( !$this->db_scalar('sys_menu','Id','Id = \''.$params['Id'].'\' AND Deleted = \'0\'') ) return 'MenuClassNotExists';
            if( $this->db_scalar('sys_menu','Id','Title=\''.$params['Title'].'\' AND Id != \''.$params['Id'].'\' AND Deleted = \'0\'') ) return 'ClassNameIsExists';
            if( $this->initdb()->update('sys_menu',$params,'Id = '.$params['Id']) ){
                $this->getMenuCat(NULL,true);return true;
            }
        }else{//新增
            if( $this->db_scalar('sys_menu','Id','Title=\''.$params['Title'].'\' AND Deleted = \'0\'') ) return 'ClassNameIsExists';
            if($this->initdb()->insert('sys_menu',$params)){
                $this->getMenuCat(NULL,true);return true;
            }
        }
    }

    public function getMenuCat($catid=null,$refresh=false){
        $dir = APPPATH.'cache/system/';
        if( !is_dir($dir) ) create_dir($dir);
        $cacheFile = $dir.'MenuClassify.php';

        $MenuClassify = array();
        if( !file_exists($cacheFile) || $refresh==true ){
            $MenuClassify = $this->db_getResultSet("SELECT Id,Title,Status,Sort,icon FROM sys_menu WHERE ParentId = '0' AND Deleted = '0' ORDER BY Sort DESC,Id ASC",'Id');
            file_put_contents($cacheFile,"<?php\n//生成时间:".date('Y-m-d H:i')."\n\$MenuClassify = ".var_export($MenuClassify,true).';');
        }else{
            include $cacheFile;
        }
//		foreach ($MenuClassify as $k=>$v){
//			if( !$this->getMenuByCid($k) ) unset($MenuClassify[$k]);
//		}
        if( $this->input->ip_address()!='127.0.0.1' ){
//			unset($MenuClassify[2]);
        }
        return $catid > 0 ? $MenuClassify[$catid] : $MenuClassify ;
    }

    public function getMenu($menuid=null,$refresh=false){
        $dir = APPPATH.'cache/system/';
        if( !is_dir($dir) ) create_dir($dir);
        $cacheFile = $dir.'MenuList.php';
        $MenuList = array();
        if( !file_exists($cacheFile) || $refresh==true ){
            $sql = "SELECT Id,ParentId,Title,LinkInfo,Status,Sort FROM sys_menu WHERE ParentId > 0 AND Deleted = '0' ORDER BY Sort DESC,Id ASC";
            if($result = $this->db_getResultSet($sql)){
                foreach ($result as $row){
                    $row['LinkInfo'] = unserialize($row['LinkInfo']);
                    $row['Url'] = geturl($row['LinkInfo']['c'],$row['LinkInfo']['a'],$row['LinkInfo']['d']);
                    $MenuList[$row['Id']] = $row;
                }
            }
            file_put_contents($cacheFile,"<?php\n//生成时间:".date('Y-m-d H:i')."\n\$MenuList = ".var_export($MenuList,true).';');
        }else{
            include $cacheFile;
        }
        //隐藏不存在的链接地址
        $this->load->model('admin/Ctrl_mod');
        $ctrlconfig = $this->Ctrl_mod->getConfig();
        foreach ($MenuList as $k=>$row){
            if( !isset($ctrlconfig[ucfirst($row['LinkInfo']['c'])]['Methods'][$row['LinkInfo']['a']]) ){
                unset($MenuList[$k]);
            }
        }
        return $menuid > 0 ? $MenuList[$menuid] : $MenuList ;
    }

    public function getMenuByCid($ClassId=null){
        $return = array();
        if( $menu = $this->getMenu() ){
            foreach ($menu as $row){
                $return[$row['ParentId']][$row['Id']] = $row;
            }
        }
        return ( !empty($ClassId) && $ClassId>0) ? (isset($return[$ClassId]) ? $return[$ClassId] : null) : $return;
    }

    /**
     *获取用户组对应的菜单分类
     *author:
     */
    public function get_user_cat($roleid){
        if( !$roleid ) return array();
        $catlist = $this->getMenuCat();
        $retcat = array();
        foreach ($catlist as $catid=>$row){
            if( $row['Status'] != 1 || !$this->get_user_menu($roleid, $catid) ) continue;
            $retcat[$catid] = $catlist[$catid];
        }
        return $retcat;
    }

    /**
     *获取用户组对应的菜单
     *author:
     */
    public function get_user_menu($roleid,$catid=NULL){
        if( !$menu = $this->getMenuByCid($catid) ) return array();
        foreach ($menu as $key=>$row){
            if($catid === NULL) {
                foreach($row as $k=>$r) {
                    if( !role_auth($roleid, $r['LinkInfo']['c'], $r['LinkInfo']['a'], $r['LinkInfo']['d']) ){
                        unset($menu[$key][$k]);

                        if(empty($menu[$key])) unset($menu[$key]);
                    }
                }
            } else {
                if( !role_auth($roleid, $row['LinkInfo']['c'], $row['LinkInfo']['a'], $row['LinkInfo']['d']) ){
                    unset($menu[$key]);
                }
            }
        }
        return $menu;
    }

    /**
     *删除记录
     *author: loong<梁龙>
     */
    public function delete($id){
        if( $id < 1 )return 'ParamsError';
        if( !$this->db_scalar('sys_menu','Id',"Id = '{$id}' AND Deleted = '0'") ) return 'resultNotExists';
        if( $this->db_scalar('sys_menu','Id',"ParentId = '{$id}' AND Deleted = '0'") ) return 'SubIsExists';
        $this->initdb()->update('sys_menu',array('Deleted'=>1),"Id = '{$id}'");
        $this->getMenu(NULL,true);
        $this->getMenuCat(NULL,true);
        return true;
    }
}