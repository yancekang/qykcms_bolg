<?php
$types=arg('types','post','url');
$idlist=arg('idlist','post','url');
if(!ispower($admin_group,'book_'.$types))ajaxreturn(1,'权限不足，操作失败');
switch($types){
	case 'view':
		db_upshow('feedback','time_view='.time().',user_admin="'.$tcz['admin'].'"','webid='.$website['webid'].' and id in('.$idlist.')');
		infoadminlog($website['webid'],$tcz['admin'],18,'标记阅读：'.$idlist);
		ajaxreturn(0,'标记阅读操作已完成');
	break;
	case 'isok':
		db_upshow('feedback','isok=0','webid='.$website['webid'].' and id in('.$idlist.')');
		infoadminlog($website['webid'],$tcz['admin'],18,'审核操作：'.$idlist);
		ajaxreturn(0,'审核操作已完成');
	break;
	}
