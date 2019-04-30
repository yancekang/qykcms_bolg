<?php
$lang=arg('lang','post','url');
if($lang=='')ajaxreturn(1,'请至少选择一种语言');
$themefolder=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_theme_folder"');
if(!$themefolder)ajaxreturn(1,'丢失主题文件夹参数：setup_theme_folder');
$langarr=explode(',',$lang);
$langconfig=db_getall('config','*','webid=1 and cata="web" order by sort asc,id asc');
if(!$langconfig)ajaxreturn(1,'丢失网站配置参数列表');
foreach($langarr as $l){
	$larr=explode('|',$l);
	if(!is_dir('../'.setup_webfolder.$website['webid'].'/'.$larr[0].'/ui/')||!is_dir('../'.setup_webfolder.$website['webid'].'/'.$larr[0].'/'.$themefolder.'/')){
		ajaxreturn(1,'<span class="red">当前主题不支持“'.$larr[1].'（'.$larr[0].'）”语言，如果您希望自己设计该语言版本主题，请参照：</span><br>1、在当前站点模板目录中创建该语言版本文件夹：'.setup_webfolder.$website['webid'].'/'.$larr[0].'/<br>2、在创建的“'.$larr[0].'”文件夹中创建名为“<span class="red">ui</span>”的子文件夹，用于存放css样式及图片<br>3、在创建的“'.$larr[0].'”文件夹中创建名为“<span class="red">'.$themefolder.'</span>”的子文件夹，用于存放'.setup_temptype.'模板文件<br>4、在这里勾选'.$larr[1].'（'.$larr[0].'）语言版本并保存<br>5、在左侧菜单中选择网站管理（'.$larr[1].'）- 网站配置，设置好相关信息并保存<br>6、现在开始设计主题吧，更多详细帮助请登录官网查询');
		return;
		}
	db_upshow('config','isview=1','webid='.$website['webid'].' and cata="'.$larr[0].'"');
	foreach($langconfig as $c){
		$c=deltable($c,'id');
		$c['webid']=$website['webid'];
		$c['cata']=$larr[0];
		$conf=db_getshow('config','*','webid='.$website['webid'].' and cata="'.$larr[0].'" and varname="'.$c['varname'].'"');
		if($conf){
			$c=deltable($c,'varval');
			db_uparr('config',$c,'webid='.$website['webid'].' and id='.$conf['id']);
		}else{
			db_intoarr('config',$c);
			}
		}
	}
db_upshow('config','varval="'.$lang.'"','webid='.$website['webid'].' and varname="setup_language"');
ajaxreturn(0,'语言版本保存成功');