<?php
if(!ispower($admin_group,'sys_cache'))ajaxreturn(1,'权限不足，操作失败');
$cachetype=arg('cachetype','post','url');
switch($cachetype){
	case 'tempcache':
		$dir='../'.setup_webfolder.$website['webid'].'/runtime/temp/';
		$istype=deldir_admin($dir,false);
		infoadminlog($website['webid'],$tcz['admin'],14,'清除模板缓存：'.$dir);
		countcapacity($website['webid']);
		ajaxreturn(0,'已清除模板缓存');
	break;
	case 'tempfile':
		$dir='../'.$website['upfolder'].setup_uptemp;
		$istype=deldir_admin($dir,false);
		infoadminlog($website['webid'],$tcz['admin'],14,'删除临时文件：'.$dir);
		countcapacity($website['webid']);
		ajaxreturn(0,'已删除临时文件');
	break;
	case 'datacache':
		$dir='../'.setup_webfolder.$website['webid'].'/runtime/';
		$istype=deldir_admin($dir.'cache/',false);
		$istype=deldir_admin($dir.'temp/',false);
		infoadminlog($website['webid'],$tcz['admin'],14,'清除网站所有缓存：'.$dir);
		countcapacity($website['webid']);
		ajaxreturn(0,'已清除网站所有缓存');
	break;
	case 'calendar':
		//$mod=db_getall('module','*','webid='.$website['webid'].' where menutype=0');
		//ajaxreturn(0,'日历系统标记已更新');
	break;
	}