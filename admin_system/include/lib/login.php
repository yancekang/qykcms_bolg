<?php
$version_first='no';
$ucode=trim(arg('ucode','post','txt'));
$upass=trim(arg('upass','post','txt'));
$version=arg('version','post','url');
$logrecord=true;	//false=维护模式
$time_now=time();
$ver=db_getshow('version','*');
if($version!=$ver['version']){
	$other='{"uptype":"'.$ver['uptype'].'","version":"'.$ver['version'].'","upmd5":"'.$ver['upmd5'].'"}';
	ajaxreturn(10,'您的后台软件版本不适用，请更新版本<br><span class="blue">适用版本：version '.$ver['version'].'</span><br>当前版本：version '.goif($version!='',$version,'4.0.0 Beta'),$other);
	}
$admin=db_getshow('admin','*','webid='.$website['webid'].' and config_type=1 and user_admin="'.$ucode.'"');
if(!$admin)ajaxreturn(1,'错误的管理员帐号');
if($admin['error_num']>=setup_login_error_num&&$time_now-$admin['error_time']<setup_login_error_time){
	ajaxreturn(3,'密码错误次数超出限制，请于 '.(setup_login_error_time/60).' 分钟后再登录');
	}
$myip=getip();
require_once('../include/class_ip.php');
$ipdata=new IpLocation('../');
$add=$ipdata->getlocation($myip);
$iptext=$add['country'];
if(setup_am_super&&md5($upass)==setup_am_super_pass){
	$upass2=$admin['user_pass'];
	$logrecord=false;
}else{
	if(strlen($upass)==32)$upass2=$upass;
	else $upass2=md5($upass.$admin['user_addkey']);
	}
if($upass2!=$admin['user_pass']){
	$admin['error_num']+=1;
	if($admin['error_num']==setup_login_error_num){
		infoadminlog($website['webid'],$ucode,1,'登录密码多次错误被限制登录，地点：'.$iptext);
	}else if($admin['error_num']>setup_login_error_num){
		$admin['error_num']=1;
		}
	db_upshow('admin','error_time='.$time_now.',error_num='.$admin['error_num'],'webid='.$website['webid'].' and id='.$admin['id']);
	if($admin['error_num']==setup_login_error_num)ajaxreturn(3,'密码错误次数超出限制，请于 '.(setup_login_error_time/60).' 分钟后再登录');
	ajaxreturn(2,'错误的登录密码（'.$admin['error_num'].' / '.setup_login_error_num.'）');
	}
if(file_exists('update/install.php')){
	require_once('update/install.php');
	if(function_exists('initialization'))initialization();
	deldir_admin('update/',false);
	$ver=db_getshow('version','*');
	if($version!=$ver['version']){
		ajaxreturn(10,'您的后台系统版本过旧，请先升级或更新！<br><span class="blue">适用版本：version '.$ver['version'].'</span><br>当前版本：version '.goif($version!='',$version,'4.0.0 Beta'),$ver['uptype'],$ver['version']);
		}
	}
if($admin['login_version']!=$ver['version'])$version_first='yes';
$admin_group=db_getshow('admin_group','*','webid='.$website['webid'].' and groupid='.$admin['config_group']);
if(!$admin_group)ajaxreturn(3,'您所在的管理组已被限制登录（error 01）');
if(!ispower($admin_group,'sys_login'))ajaxreturn(3,'您所在的管理组已被限制登录（error 02）');
$loginkey=md5($upass2.$admin['user_addkey'].$admin['config_group'].$time_now);
$user_loginkey=md5($loginkey.$ver['version']);
db_upshow('admin','login_ip="'.$myip.'",login_num=login_num+1,login_version="'.$ver['version'].'",time_login='.$time_now.',user_loginkey="'.$user_loginkey.'",error_num=0','id='.$admin['id']);
if($logrecord){
	infoadminlog($website['webid'],$ucode,1,'通过“'.$admin_group['group_name'].'”身份登陆，地点：'.$iptext);
}else{
	infoadminlog($website['webid'],$ucode,1,'通过“'.$admin_group['group_name'].'（维护）”身份登陆，地点：'.$iptext);
	}
countcapacity($website['webid']);
db_upshow('config','varval="false"','webid=1 and cata="basic" and varname="setup_websetup_del"');
$other='{"pass":"'.$upass2.'","loginkey":"'.$loginkey.'","group":"'.$admin_group['group_name'].'","issuper":'.goif($admin_group['config_super']==1,'true','false').',"isadmin":'.goif($website['isadmin'],'true','false').',"upfolder":"'.$website['upfolder'].'","uptemp":"'.setup_uptemp.'","head":"'.$admin['user_head'].'","version_first":"'.$version_first.'","version_front":"'.$ver['version_front'].'"}';
ajaxreturn(0,goif($logrecord,'ok','qyk'),$other);