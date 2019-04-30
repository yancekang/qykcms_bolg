<?php
if(db_database=='')header('Location:install/index.php');
$db=new mysql();
$webdomain=$_SERVER['SERVER_NAME'];
$website=db_getshow('website','*','setup_weburl="'.$webdomain.'"');
if(!$website){
	$url='';
	$website2=db_getshow('website','setup_weburl','isdef=1 and isadmin=1 order by id asc');
	if($website2)$url='http://'.$website2['setup_weburl'];
	tipmsg('您访问的域名尚未绑定：'.$webdomain.'<br>请使用安装时的域名登录QYKCMS网站后台绑定该域名：<br>网站后台 &raquo; 系统综合 &raquo; 域名绑定',true,'系统提示',$url);
	}
if(!$website['isdef']){
	$website2=db_getshow('website','*','webid='.$website['webid'].' and isdef=1 order by id asc');
	if($website2){
		Header("HTTP/1.1 301 Moved Permanently");
		Header("Location: http://".$website2['setup_weburl']);
		exit;
		}
	}
@include(setup_webfolder.$website['webid'].'/config/global.php');
if(setup_web_close){
	$closemess=getwebclosemess(setup_web_close);
	tipmsg($closemess,true);
	}
if(setup_theme_folder=='')tipmsg('您正在访问的站点尚未安装网站主题，请登录系统后台处理：<br>主题模板 &raquo; 安装主题',true);
$tcz=array('log'=>arg('log','get'),'id'=>arg('id','get','int'),'bcat'=>arg('bcat','get','int'),'scat'=>arg('scat','get','int'),'lcat'=>arg('lcat','get','int'),'page'=>arg('page','get','num'),'desc'=>arg('desc','get','url'),'word'=>arg('word','get','url'),'seartype'=>arg('seartype','get','url'));
if($tcz['log']=='')$tcz['log']='index';
if($tcz['page']==0)$tcz['page']=1;
$templang=setup_language_def;
if(isset($_COOKIE['web_templang'])){
	if(strstr(','.setup_language,','.$_COOKIE['web_templang'].'|'))$templang=$_COOKIE['web_templang'];
	}
@include(setup_webfolder.'/'.$website['webid'].'/config/'.$templang.'.php');
$web=array(
	'id'=>$website['webid'],
	'mobile'=>is_mobile_request(),
	'mobiletemp'=>false,
	'name'=>setup_webname_page,
	'title'=>goif($tcz['log']=='index',setup_webname,setup_webname_page),
	'record'=>setup_record,
	'url'=>setup_weburl,
	'keyword'=>setup_keyword,
	'description'=>setup_description,
	'location'=>'<a href="/" title="'.setup_shortname.'" name="location">'.goif(setup_shortname!='',setup_shortname,'首页').'</a>',
	'column'=>setup_shortname,
	'column2'=>'',
	'ismod'=>true,
	'themeheader'=>true,
	'themeframe'=>setup_theme_frame,
	'themeframe_basic'=>setup_theme_frame_basic,
	'templang'=>$templang,
	'tempfolder'=>$templang,
	'tempfolder_file'=>setup_theme_folder,
	'temproot'=>'/'.setup_webfolder.$website['webid'].'/',
	'tempui'=>'/'.setup_webfolder.$website['webid'].'/'.$templang.'/ui/',
	'tempcache'=>'',
	'tempfile'=>'',
	'datacache_status'=>setup_datacache*60,
	'datacache'=>'',
	'list_size'=>12,
	'list_page'=>'',
	'list_record'=>0
	);
$tempview='auto';
if(setup_mobile){
	if(isset($_COOKIE['web_tempview']))$tempview=$_COOKIE['web_tempview'];
	if($tempview=='auto'&&$web['mobile']){
		if(setup_mobile_url!=''){
			Header("HTTP/1.1 301 Moved Permanently");
			Header("Location: ".setup_mobile_url);
			exit;
			}
		$web['tempfolder']=$web['templang'].'_mobile';
		$web['tempui']='/'.setup_webfolder.$website['webid'].'/'.$web['tempfolder'].'/ui/';
		$web['mobiletemp']=true;
		$web['themeframe']=setup_theme_frame_mobile;
		$web['themeframe_basic']=setup_theme_frame_mobile_basic;
		}
	}
include('include/lang/'.$web['templang'].'.php');
$cook=getcook();
?>