<?php
if(!ispower($admin_group,'book_view'))ajaxreturn(1,'权限不足，操作失败');
$data=db_getshow('feedback','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data)ajaxreturn(1,'留言/评论记录不存在');
$reply=arg('reply','post','url');
switch($reply){
	case 'yes':
		$cont=nl2br(arg('cont','post','txt'));
		db_upshow('feedback','reply="'.$cont.'",time_view='.time().',user_admin="'.$tcz['admin'].'"','id='.$tcz['id']);
		infoadminlog($website['webid'],$tcz['admin'],18,'回复留言“'.$data['name'].'”（ID='.$tcz['id'].'）');
		ajaxreturn(0,'该留言/评论已保存回复');
	break;
	default:
		db_upshow('feedback','time_view='.time().',user_admin="'.$tcz['admin'].'"','id='.$tcz['id']);
		infoadminlog($website['webid'],$tcz['admin'],18,'标记阅读“'.$data['name'].'”（ID='.$tcz['id'].'）');
		ajaxreturn(0,'该留言/评论已标记阅读状态');
	break;
	}