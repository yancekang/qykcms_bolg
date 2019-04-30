<?php
ini_set("max_execution_time","3600");
include('../include/config.php');
include('../include/class_mysql.php');
include('../include/function.php');
include('../include/class_thumb.php');
include('include/function.php');
include('include/class_page.php');
nocache();
$tcz=array('admin'=>arg('admin','all'),'key'=>arg('key','all'),'log'=>arg('log','all','txt'),'desc'=>arg('desc','all','url'),'bcat'=>arg('bcat','all','int'),'id'=>arg('id','post','int'),'page'=>arg('page','post','num'));
$webdomain=$_SERVER['SERVER_NAME'];
$db=new mysql(2);
$website=db_getshow('website','*','setup_weburl="'.$webdomain.'"');
if(!$website)ajaxreturn(9,'当前域名：'.$webdomain.'<br>该域名尚未绑定站点，请通过安装时的域名登录后台绑定');
if(!$website['isdef']){
	$mydomain=db_getone('website','setup_weburl','webid='.$website['webid'].' and isdef=1');
	ajaxreturn(9,'当前域名：'.$webdomain.'<br>请使用站点主域名 <span class="blue">'.$mydomain.'</span> 登录后台');
	}
$website['upfolder']=setup_upfolder.$website['webid'].'/';
if($tcz['log']!='login'){
	if($tcz['key']==''||$tcz['admin']=='')ajaxreturn(1,'登录账号或较验码错误，请关闭重新登录');
	$ver=db_getshow('version','*');
	$md5key=md5($tcz['key'].$ver['version']);
	$admin_check=db_getshow('admin','*','webid='.$website['webid'].' and user_admin="'.$tcz['admin'].'"');
	if(!$admin_check)ajaxreturn(1,'登录账号或密码有误，请关闭重新登录');
	if($md5key!=$admin_check['user_loginkey'])ajaxreturn(1,'当前账号可能在别处登录被迫下线，请尝试重新登录');
	if($admin_check['config_type']!=1)ajaxreturn(1,'您的管理账号状态异常，无法继续操作');
	$admin_group=db_getshow('admin_group','*','webid='.$website['webid'].' and groupid='.$admin_check['config_group']);
	}
$res='';
switch($tcz['log']){
	case 'login':
	case 'sidemenu':
	case 'search':
	case 'welcome':
	case 'uploadzip':
	case 'sendemail':
	case 'upfile':
	case 'down':
	case 'getmod':
	case 'tipdata':
	case 'delete':
	case 'choose':
		require('include/lib/'.$tcz['log'].'.php');
	break;
	case 'config_args':
	case 'config_args_theme':
		require('include/list/config_args.php');
	break;
	case 'template':
	case 'config_domain':
	case 'websetup':
	case 'label':
	case 'option':
	case 'tool_email_address':
	case 'tool_email':
	case 'advert':
	case 'customer':
	case 'admin':
	case 'admin_group':
	case 'admin_log':
	case 'special':
	case 'module':
	case 'module_user':
	case 'feedback':
	case 'article':
	case 'article_user':
		require('include/list/'.$tcz['log'].'.php');
	break;
	case 'show':
		$other='';
		switch($tcz['desc']){
			case 'config_args':
			case 'config_args_theme':
				require('include/show/config_args.php');
			break;
			default:
				require('include/show/'.$tcz['desc'].'.php');
			break;
			}
		ajaxreturn(0,$res,$other);
	break;
	case 'post':
		switch($tcz['desc']){
			case 'config_args':
			case 'config_args_theme':
				require('include/post/config_args.php');
			break;
			case 'theme_open':
			case 'theme_open_def':
				require('include/post/theme_open.php');
			break;
			case 'tool_email':
			case 'tool_email2':
				require('include/post/tool_email.php');
			break;
			default:
				require('include/post/'.$tcz['desc'].'.php');
			break;
			}
	break;
	default:
		ajaxreturn(1,'未知参数：log='.$tcz['log'].'&desc='.$tcz['desc']);
	break;
	}
?>