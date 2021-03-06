<?php
ini_set("max_execution_time","3600");
define("install_version","4.1.4");
define("install_version_front","4.3.0");
define("install_theme_name","博客主题");
chdir('../');
include('include/function.php');
nocache();
$log=arg('log','all','url');
switch($log){
	case "install":
		if(file_exists('install/install_lock.php'))ajaxreturn(1,'可能存在重复安装，系统已终止运行');
		include('install/start.php');
	break;
	case "getnewver":
		@$ver=file_get_contents("http://api.qingyunke.com/system.php?log=newver") or ajaxreturn(1);
		if(!preg_match('/{"status":/i',$ver))ajaxreturn(1);
		$arr=json_decode($ver,true);
		if(!is_array($arr))ajaxreturn(1);
		ajaxreturn(0,$arr['ver'],$arr['link']);
	break;
	default:
		$goto=arg('setup','get','num');
		if($goto==3&&file_exists('install/install_lock.php'))tipmsg('QYKCMS已执行过安装，如需重新安装请按以下步骤：<br>1、重新上传install目录覆盖网站根目录下的install目录<br>2、如果install目录内有install_lock.php文件，请手动删除<br>3、刷新本页面开始安装',true);
		else if($goto>4)tipmsg("不存在的页面",true);
		include('install/header.php');
		include('install/setup_'.$goto.'.php');
		include('install/footer.php');
	break;
	}
?>