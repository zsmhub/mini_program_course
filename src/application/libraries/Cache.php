<?php
/**
 * 多缓存类 eaccelerator,memcache,file
 * @author loong www.lianglong.org
 * @date 2012-09
 */
class Cache {
	private $config=NULL;
	private $memcacheOBJ = false;//memcache对象
	private $eacceleratorOBJ = false;//eac缓存对象
	private $EnabledMemcache = false ;
	private $EnabledEac = false ;
	private $EnabledFile = true ;
	private $safeHead = '<?php exit();?>';
	
	public function __construct(){
		$CI = & get_instance();
		$CI->config->load('cache', TRUE, TRUE);
		$config = $CI->config->item('cache');	
		$this->config = $config;
		//检查 memcache功能
		if( $this->config['EnabledMemcache'] && (extension_loaded('memcache') || extension_loaded('memcached')) 
		&&isset($this->config['memcache']['Host']) &&isset($this->config['memcache']['Port']) ){
			$this->init_memcache($this->config['memcache']);			
		}
		//检查EAC开启情况
		if( $this->config['EnabledEac'] && function_exists('eaccelerator_get') ){
			$this->EnabledEac = true ;
		}
		//检查文件缓存开启情况
		if( $this->config['EnabledFile'] ){
			$this->EnabledFile = true ;
			if( $this->config['path'] ) $this->config['path'] = abspath($this->config['path']);
			if( !empty($this->config['path']) && !is_dir($this->config['path']) ){
				create_dir($this->config['path']);
			}
		}	
	}
	
	public function get($key){
		if( $this->EnabledEac ){
			if( (false!==$ret=$this->get_eac($key))&&($ret!==null) )return $ret;			
		}
		if( $this->EnabledMemcache ){
			if( (false!==$ret=$this->get_memcache($key))&&($ret!==null) )return $ret;			
		}
		if( $this->EnabledFile ){
			if( (false!==$ret=$this->get_file($key))&&($ret!==null) )return $ret;			
		}		
		return false;
	}
	
	public function set($key,$value,$ttl=60){
		if( $key===false ) return false;
		if($this->EnabledEac){
			$this->set_eac($key,$value,$ttl);
		}				
		if($this->EnabledMemcache){
			$this->set_memcache($key,$value,$ttl);
		}		
		if( $this->EnabledFile ){
			$this->set_file($key,$value,$ttl);
		}
		return true;
	}
	
	public function delete($key){
		if($this->EnabledEac){
			$this->delete_eac($key);
		}				
		if($this->EnabledMemcache){
			$this->delete_memcache($key);
		}		
		if( $this->EnabledFile ){
			$this->delete_file($key);
		}
	}
	
	public function clear(){
		if( $this->EnabledEac ){
			eaccelerator_clear();
		}
		if( $this->EnabledFile ){
			delete_dir($this->config['path']);
		}
		if( $this->EnabledMemcache ){
			$this->memcacheOBJ->flush();
		}
	}
	
	public function get_file($key){
		if( !$this->EnabledFile ) return false;
		$keymd5 = md5($this->config['prefix'].$key);
		$key_path = $this->config['path'].substr($keymd5, 0, 2).'/';
		$file = $key_path.$keymd5.'.php';
		if( !file_exists($file) ) return false;
		// $TryTimes = 5;
		// $filesize = filesize($file);
		// if($filesize==0)return '';
		$content='';
		// while (strlen($content)!=$filesize&&$TryTimes>0){
		// 	$content = file_get_contents($file);
		// 	$TryTimes--;
		// 	if($content==''&&$filesize>0)usleep(50000);
		// }

		//增加锁定模式
		if($handle = fopen($file, "r")){
			if (flock($handle, LOCK_EX)) { 
			    $content = fread($handle, filesize($file));
			 //    while ( !feof($handle) ){
				// 	$content .= fgets($handle,4096);
				// }
			    flock($handle, LOCK_UN); 
			} 
			fclose($handle);
		}

		$safestr_len = strlen($this->safeHead);
		if(strlen($content)<$safestr_len+4||
		(($arr=unpack('l',substr($content,$safestr_len,4)))&&$arr[1]< gettime())){
			@unlink($file);
			return false;
		};
		return @unserialize(substr($content,$safestr_len+4));
	}
	
	public function set_file($key,$value,$ttl=60){
		if( !$this->EnabledFile ) return false;
		$keymd5 = md5($this->config['prefix'].$key);
		$key_path = $this->config['path'].substr($keymd5, 0, 2).'/';
		$file = $key_path.$keymd5.'.php';
		if( !is_dir($key_path) ) create_dir($key_path);
		$content = $this->safeHead.pack('l',gettime() + $ttl).serialize($value);
		if( $fp = fopen($file, 'w+') ){
			if (flock($fp, LOCK_EX)) { // 进行排它型锁定
			    fwrite($fp, $content);
			    flock($fp, LOCK_UN); // 释放锁定
			} 
			fclose($fp); 			
		}
		return true;
	}
	
	public function delete_file($key){
		$keymd5 = md5($this->config['prefix'].$key);
		$key_path = $this->config['path'].substr($keymd5, 0, 2).'/';
		$file = $key_path.$keymd5.'.php';
		if(is_file($file))return @unlink($file);
		return true;
	}
	
	public function get_eac($key){
		if( !$this->EnabledEac ) return false;
		$keyname = md5($this->config['prefix'].$key);
		return eaccelerator_get($keyname);
	}
	
	public function set_eac($key,$value,$ttl=60){
		if( !$this->EnabledEac ) return false;
		$keyname = md5($this->config['prefix'].$key);
		eaccelerator_lock($keyname);
		eaccelerator_put($keyname,$value,$ttl);
		eaccelerator_unlock($keyname);
		return true;
	}
	
	public function delete_eac($key){
		if( $this->EnabledEac ) {
			$keyname = md5($this->config['prefix'].$key);
			return eaccelerator_rm($keyname);
		}
	}
	
	public function get_memcache($key){
		if( !$this->EnabledMemcache ) return false;
		$keyname = md5($this->config['prefix'].$key);
		return $this->memcacheOBJ->get($keyname);
	}
	
	public function set_memcache($key,$value,$ttl=60){
		if( !$this->EnabledMemcache ) return false;
		$keyname = md5($this->config['prefix'].$key);
		return $this->memcacheOBJ->set($keyname, $value, 0, $ttl);
	}
	
	public function delete_memcache($key){
		if( !$this->EnabledMemcache ) return false;
		$keyname = md5($this->config['prefix'].$key);
		return $this->memcacheOBJ->delete($keyname);
	}
	
	private function init_memcache($config){
		if( is_object($this->memcacheOBJ) ) return true;
		$this->memcacheOBJ = new Memcache();
		$IsConnect = $config['PConnect']?
		$this->memcacheOBJ->pconnect($config['Host'],$config['Port'],5):
		$this->memcacheOBJ->connect($config['Host'],$config['Port'],5);
		$this->memcacheOBJ->setCompressThreshold(2000, 0.2); //大于2K自动压缩
		if( $IsConnect ){
			$this->EnabledMemcache = true;
		}else{
			$this->memcacheOBJ = $this->EnabledMemcache = false;
		}		
		return $IsConnect;
	}
}