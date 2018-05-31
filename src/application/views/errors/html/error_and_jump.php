<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>操作提示</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #DFF0F8;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	-webkit-box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
		<?php if( isset($jumpurl) && $jumpurl !=''):?>
        <p style="display:block;text-align:left">
        <a href="javascript:jump('<?php echo $jumpurl;?>','<?php echo $jumptop;?>');">
        如果您的浏览器没有跳转，请点击这里</a>
        </p>
        <script language="javascript">
		setTimeout("jump('<?php echo $jumpurl;?>','<?php echo $jumptop;?>');",<?php echo $waittime;?>);
		function jump(u,jumptop){
			if( u == '-1' ){
				history.go(-1);
			}else{
				if(jumptop == '1' && self!=top){
					top.window.location.href = u;
				}else{
					window.location.href = u;
				}
			}
		}
	    </script>
        <?php endif;?>
	</div>
    
</body>
</html>