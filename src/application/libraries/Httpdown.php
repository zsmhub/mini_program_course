<?php
class Httpdown{
	private $m_url = "";
	private $m_urlpath = "";
	private $m_scheme = "http";
	private $m_host = "";
	private $m_port = "80";
	private $m_user = "";
	private $m_pass = "";
	private $m_path = "/";
	private $m_query = "";
	private $m_fp = "";
	private $m_error = "";
	private $m_httphead = "" ;
	private $m_html = "";
	private $m_puthead = "";
	private $BaseUrlPath = "";
	private $HomeUrl = "";
	private $JumpCount = 0;//防止多重重定向陷入死循環的设置
	private $_Cookie=array();
	private $_Files=array();
	private $_FileContents=array();
	private $_Forms=array();
	//
	//初始化系統
	//
	function PrivateInit($url){
		if($url=="") return ;
		$urls = "";
		$urls = @parse_url($url);
		$this->m_url = $url;
		if(is_array($urls))
		{
			$this->m_host = $urls["host"];
			if(!empty($urls["scheme"])) $this->m_scheme = $urls["scheme"];

			if(!empty($urls["user"])){
				$this->m_user = $urls["user"];
			}

			if(!empty($urls["pass"])){
				$this->m_pass = $urls["pass"];
			}

			if(!empty($urls["port"])){
				$this->m_port = $urls["port"];
			}

			if(!empty($urls["path"])) $this->m_path = $urls["path"];
			$this->m_urlpath = $this->m_path;

			if(!empty($urls["query"])){
				$this->m_query = $urls["query"];
				$this->m_urlpath .= "?".$this->m_query;
			}
			$this->HomeUrl = $urls["host"];
			$this->BaseUrlPath = $this->HomeUrl.$urls["path"];
			$this->BaseUrlPath = ereg_replace("/([^/]*).(.*)$","/",$this->BaseUrlPath); // ??
			$this->BaseUrlPath = ereg_replace("/$","",$this->BaseUrlPath);
		}
	}
	
	//增加信息，文件不能提交，增加标识
	public function AddCookie($name,$value){
		if($name!=''){
			$this->_Cookie[]=$name.'='.urlencode($value);
		}
	}
	
	public function AddFile($Key,$FileName){
		if(file_exists($FileName)){
			if($Key==''){
				$this->_Files[]=$FileName;
			}else{
				$this->_Files[$Key]=$FileName;
			}
		}
	}
	
	public function AddFileContent($Key,$FileName,$Content){
		if($Key==''){
			return ;
		}else{
			$this->_FileContents[$Key][0]=$FileName;
			$this->_FileContents[$Key][1]=$Content;
		}
	}

	private $KeyValues=array();
	private function GetKeyValues($Name,$value){
		if(!is_array($value)){
			$this->KeyValues[$Name]=$value;
			return ;
		}
		foreach ($value as $k=>$v){
			$this->GetKeyValues($Name.'['.$k.']',$v);
		}
	}
	public function AddForm($name,$value){
		$this->KeyValues=array();
		if($name!=''){
			$this->GetKeyValues($name,$value);
			foreach ($this->KeyValues as $k=>$v){
				$this->_Forms[$k]=$v;
			}
		}
	}

	//打開指定網址
	//
	function OpenUrl($url,$NoWait=false){
		$this->Reset();
		//初始化系統
		$this->PrivateInit($url);
		$this->PrivateStartSession($NoWait);
	}
	//
	//打開303重定向網址
	//
	function JumpOpenUrl($url){
		$this->Reset();
		//初始化系統
		$this->PrivateInit($url);
		$this->PrivateStartSession();
	}

	//重設各參數
	private function Reset(){
		$this->m_url = "";
		$this->m_urlpath = "";
		$this->m_scheme = "http";
		$this->m_host = "";
		$this->m_port = "80";
		$this->m_user = "";
		$this->m_pass = "";
		$this->m_path = "/";
		$this->m_query = "";
		$this->m_error = "";
		$this->JumpCount++;
		$this->m_httphead = Array() ;
		$this->m_html = "";
		$this->Close();
	}
	//
	//獲得某操作錯誤的原因
	//
	function printError(){
		echo "錯誤資訊：".$this->m_error;
		echo "具體返回頭：<br>";
		echo $this->GetRaw();
		foreach($this->m_httphead as $k=>$v)
		{ echo "$k => $v <br>\r\n"; }
	}
	//
	//判別用Get方法送出的頭的應答結果是否正確
	//
	function IsGetOK(){
		if( ereg("^2",$this->GetHead("http-state")) ){        
			return true; 
		}else{
			$this->m_error .= $this->GetHead("http-state")." - ".$this->GetHead("http-describe")."<br>";
			return false;
		}
	}
	//
	//看看返回的網頁是否是text類型
	//
	function IsText(){
		if(ereg("^2",$this->GetHead("http-state"))
		&& eregi("^text",$this->GetHead("content-type"))){        
			return true; 
		}else{
			$this->m_error .= "內容爲非文本類型或網址重定向<br>";
			return false;
		}
	}
	//
	//判斷返回的網頁是否是特定的類型
	//
	function IsContentType($ctype){
		if(ereg("^2",$this->GetHead("http-state"))
		&& $this->GetHead("content-type")==strtolower($ctype))
		{        return true; }
		else
		{
			$this->m_error .= "類型不對 ".$this->GetHead("content-type")."<br>";
			return false;
		}
	}
	//
	//用Http協議下載文件
	//
	function SaveToBin($savefilename){
		if(!$this->IsGetOK()) return false;
		if(@feof($this->m_fp)) { $this->m_error = "連接已經關閉！"; return false; }
		if($fp = fopen($savefilename,"w")){
			$raw=&GetRaw();
			fwrite($fp,$raw,strlen($raw));
			fclose($this->m_fp);
			fclose($fp);
			return true;
		}else{
			//can not open the file
		}
	}

	function &GetRaw(){
		$rBin="";
		while(!feof($this->m_fp)){
			$rBin.=fread($this->m_fp,1024);
		}
		return $rBin;
	}
	//
	//保存網頁內容爲Text文件
	//
	function SaveToText($savefilename){
		if($this->IsText()) $this->SaveBinFile($savefilename);
		else return "";
	}
	//
	//用Http協議獲得一個網頁的內容
	//
	function GetHtml(){
		if(!$this->IsText()) return "";
		if($this->m_html!="") return $this->m_html;
		if(!$this->m_fp||@feof($this->m_fp)) return "";
		while(!feof($this->m_fp)){
			$this->m_html .= fgets($this->m_fp,256);
		}
		@fclose($this->m_fp);
		return $this->m_html;
	}
	//
	//開始HTTP會話
	//
	function PrivateStartSession($NoWait=false){
		if(!$this->PrivateOpenHost($NoWait)){
			$this->m_error .= "打開遠程主電腦出錯!";
			return false;
		}

		if($this->GetHead("http-edition")=="HTTP/1.1") $httpv = "HTTP/1.1";
		else $httpv = "HTTP/1.0";
		$Method=(count($this->_Forms)+count($this->_Files)+count($this->_FileContents))>0?'POST':'GET';
		//送出固定的起始請求頭GET、Host資訊
		fputs($this->m_fp,"{$Method} ".$this->m_urlpath." $httpv\r\n");
		
		//echo "{$Method} ".$this->m_urlpath." $httpv\r\n";
		$this->m_puthead["Host"] = $this->m_host;
		$boundary=$boundary_2='';
		if($Method=='POST'){
			if(count($this->_Files)>0||count($this->_FileContents)>0){
				$boundary = "---------------------------".md5(rand(0,32000000).time());
				$boundary = "###";
//				$boundary = md5(rand(0,32000000).time());
				$boundary_2 =  "--{$boundary}";
				$this->m_puthead['Content-type']="multipart/form-data; boundary={$boundary}";
			}else{
				$this->m_puthead['Content-type']="application/x-www-form-urlencoded";
			}
		}

		//送出使用者自定義的請求頭
		if(!isset($this->m_puthead["Accept"])) { $this->m_puthead["Accept"] = "*/*"; }
		if(!isset($this->m_puthead["Accept-Language"])) { $this->m_puthead["Accept-Language"] = "zh-CN"; }
		if(!isset($this->m_puthead["If-Modified-Since"])) { $this->m_puthead["If-Modified-Since"] = "0"; }
		if(!isset($this->m_puthead["Accept-Encoding"])) { $this->m_puthead["Accept-Encoding"] = "text/plain"; }
		if(!isset($this->m_puthead["User-Agent"])) { $this->m_puthead["User-Agent"] = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; InfoPath.2; .NET4.0C; .NET4.0E; MALC)"; }
		if(!isset($this->m_puthead["Accept-Language"])) { $this->m_puthead["User-Agent"] = "zh-CN"; }
		if(!isset($this->m_puthead["Refer"])) { $this->m_puthead["Refer"] = "http://".$this->m_puthead["Host"]; }
		foreach($this->m_puthead as $k=>$v){
			$k = trim($k);
			$v = trim($v);
			if($k!=""&&$v!=""){
				fputs($this->m_fp,"$k: $v\r\n");
			}
		}
		//var_dump($this->m_puthead);
		if(count($this->_Cookie)>0){
			fputs($this->m_fp,"Cookie:".join(';',$this->_Cookie)."\r\n");
		} $this->_Cookie=array();



		//发送内容
		$Content='';
		if(count($this->_Files)==0&&count($this->_FileContents)==0&&count($this->_Forms)>0){
			foreach ($this->_Forms as $k=>$v){
				if($Content==''){
					$Content=$k.'='.urlencode($v);
				}else{
					$Content.='&'.$k.'='.urlencode($v);
				}
			}
		}elseif(count($this->_Files)>0||count($this->_FileContents)>0){
			foreach ($this->_Forms as $k=>$v){
				$Content .= $boundary_2."\r\nContent-Disposition: form-data; name=\"".$k."\"\r\n\r\n";
				$Content .= rawurlencode($v)."\r\n";
			}

			foreach ($this->_Files as $k=>$FileName){
				$Content .= $boundary_2."\r\nContent-Disposition: form-data; name=\"{$k}\"; filename=\"{$FileName}\"\r\nContent-Type: text/plain\r\n\r\n";

				$Content.=file_get_contents($FileName);
				$Content .= "\r\n".$boundary_2."--\r\n\r\n";
			}
			foreach ($this->_FileContents as $k=>$FileContent){
				$Content .= $boundary_2."\r\nContent-Disposition: form-data; name=\"{$k}\"; filename=\"{$FileContent[0]}\"\r\nContent-Type: text/plain\r\n\r\n";
				$Content.=$FileContent[1];
				$Content .= "\r\n".$boundary_2."--\r\n\r\n";
			}
		} $this->_Forms= $this->_Files=$this->_FileContents=array();

		fputs($this->m_fp,"Content-Length: ".strlen($Content)."\r\n");
		fputs($this->m_fp,"Connection: Keep-Alive\r\n\r\n");
		if($NoWait)return;
		
		
		if($Content!=''){
			fputs($this->m_fp,$Content);
		}


		$httpstas = explode(" ",fgets($this->m_fp,256));
		$this->m_httphead["http-edition"] = trim($httpstas[0]);
		$this->m_httphead["http-state"] = trim($httpstas[1]);
		$this->m_httphead["http-describe"] = "";
		for($i=2;$i<count($httpstas);$i++){
			$this->m_httphead["http-describe"] .= " ".trim($httpstas[$i]);
		}
		//獲取詳細應答頭
		while(!feof($this->m_fp)){
			$line = trim(fgets($this->m_fp,256));
			if($line == "") break;
			$hkey = "";
			$hvalue = "";
			$v = 0;
			for($i=0;$i<strlen($line);$i++){
				if($v==1) $hvalue .= $line[$i];
				if($line[$i]==":") $v = 1;
				if($v==0) $hkey .= $line[$i];
			}
			$hkey = trim($hkey);
			if($hkey!="") $this->m_httphead[strtolower($hkey)] = trim($hvalue);
		}
		//判斷是否是3xx開頭的應答
		if(ereg("^3",$this->m_httphead["http-state"]))
		{
			if($this->JumpCount > 3) return;
			if(isset($this->m_httphead["location"])){
				$newurl = $this->m_httphead["location"];
				if(eregi("^http",$newurl)){
					$this->JumpOpenUrl($newurl);
				}else{
					$newurl = $this->FillUrl($newurl);
					$this->JumpOpenUrl($newurl);
				}
			}else{
				$this->m_error = "無法識別的轉移應答！";
				return false;
			}
		}
	}
	//
	//獲得一個Http頭的值
	//
	function GetHead($headname)
	{
		$headname = strtolower($headname);
		if(isset($this->m_httphead[$headname]))
		return $this->m_httphead[$headname];
		else
		return "";
	}
	//
	//設置Http頭的值
	//
	function SetHead($skey,$svalue)
	{
		$this->m_puthead[$skey] = $svalue;
	}
	//
	//打開連接
	//
	function PrivateOpenHost($nopsock=true)
	{
		if($this->m_host=="") return false;
		$this->m_fp = @fsockopen($this->m_host, $this->m_port, $errno, $errstr,3);
		/*if($nopsock){
			$this->m_fp = @fsockopen($this->m_host, $this->m_port, $errno, $errstr,3);
		}else{
			$this->m_fp = @pfsockopen($this->m_host, $this->m_port, $errno, $errstr,3);
		}*/
		if(!$this->m_fp){
			$this->m_error = $errstr;
			return false;
		}
		else{
			return true;
		}
	}
	//
	//關閉連接
	//
	function Close(){
		if($this->m_fp)@fclose($this->m_fp);
	}
	//
	//補全相對網址
	//
	function FillUrl($surl)
	{
		$i = 0;
		$dstr = "";
		$pstr = "";
		$okurl = "";
		$pathStep = 0;
		$surl = trim($surl);
		if($surl=="") return "";
		$pos = strpos($surl,"#");
		if($pos>0) $surl = substr($surl,0,$pos);
		if($surl[0]=="/"){
			$okurl = "http://".$this->HomeUrl.$surl;
		}else if($surl[0]=="."){
			if(strlen($surl)<=2) return "";
			else if($surl[0]=="/")
			{
				$okurl = "http://".$this->BaseUrlPath."/".substr($surl,2,strlen($surl)-2);
			}
			else{
				$urls = explode("/",$surl);
				foreach($urls as $u){
					if($u=="..") $pathStep++;
					else if($i<count($urls)-1) $dstr .= $urls[$i]."/";
					else $dstr .= $urls[$i];
					$i++;
				}
				$urls = explode("/",$this->BaseUrlPath);
				if(count($urls) <= $pathStep)
				return "";
				else{
					$pstr = "http://";
					for($i=0;$i<count($urls)-$pathStep;$i++)
					{ $pstr .= $urls[$i]."/"; }
					$okurl = $pstr.$dstr;
				}
			}
		}

		else
		{
			if(strtolower(substr($surl,0,7))=="http://")
			$okurl = $surl;
			else
			$okurl = "http://".$this->BaseUrlPath."/".$surl;
		}
		$okurl = eregi_replace("^(http://)","",$okurl);
		$okurl = eregi_replace("/{1,}","/",$okurl);
		return "http://".$okurl;
	}
}