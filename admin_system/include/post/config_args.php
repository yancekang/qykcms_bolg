<?php
if($tcz['desc']=='config_args'&&!$website['isadmin'])ajaxreturn(9,'权限不足，操作失败');
if(!ispower($admin_group,'super'))ajaxreturn(9,'权限不足，操作失败');
$arr=array(
	'webid'=>goif($tcz['desc']=='config_args',1,$website['webid']),
	'cata'=>goif($tcz['desc']=='config_args',arg('cata','post','url'),'theme'),
	'contype'=>arg('contype','post','int'),
	'title'=>arg('title','post','txt'),
	'varname'=>strtolower(arg('varname','post','url')),
	'varval'=>arg('varval','post','txt'),
	'vartype'=>arg('vartype','post','int'),
	'infotype'=>arg('infotype','post','url'),
	'infosel'=>arg('infosel','post','txt'),
	'content'=>arg('content','post','txt'),
	'isedit'=>arg('isedit','post','int'),
	'isview'=>arg('isview','post','int'),
	'sort'=>arg('sort','post','int')
	);
if($arr['contype']>1)$arr['contype']=1;
if($arr['cata']=='basic'&&$arr['infotype']=='upload')ajaxreturn(1,'基础配置类参数输入方式不支持上传控件');
if(!preg_match('/^([a-z]+)/',$arr['varname']))ajaxreturn(1,'常量名称必须以字母开头');
$data=db_getshow('config','*','webid='.$arr['webid'].' and id='.$tcz['id'].goif($tcz['desc']=='config_args_theme',' and cata="theme"'));
$iswy=db_getshow('config','*','webid='.$arr['webid'].' and id!='.$tcz['id'].' and varname="'.$arr['varname'].'"');
if($iswy)ajaxreturn(1,'常量名称已被使用，请更换：'.$arr['varname']);
if($data){
	db_uparr('config',$arr,'webid='.$arr['webid'].' and id='.$tcz['id']);
	infoadminlog($website['webid'],$tcz['admin'],26,'保存参数：'.$arr['varname']);
}else{
	db_intoarr('config',$arr);
	infoadminlog($website['webid'],$tcz['admin'],26,'新建参数：'.$arr['varname']);
	}
ajaxreturn(0,'参数编辑成功');