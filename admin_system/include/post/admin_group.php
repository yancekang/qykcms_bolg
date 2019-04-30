<?php
if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
$group_name=arg('group_name','post','txt');
$config_super=arg('config_super','post','int');
$config_rank=arg('config_rank','post','url');
$group=db_getshow('admin_group','*','webid='.$website['webid'].' and id='.$tcz['id']);
if($group){
	if($config_super!=1){
		$super_group=db_count('admin_group','webid='.$website['webid'].' and config_super=1 and id!='.$group['id']);
		if(!$super_group)ajaxreturn(1,'请至少保留一个超级管理权限的管理组');
		}
	db_upshow('admin_group','group_name="'.$group_name.'",config_super='.$config_super.',config_rank="'.$config_rank.'"','id='.$tcz['id']);
	infoadminlog($website['webid'],$tcz['admin'],12,'编缉管理组“'.$group_name.'”（ID='.$tcz['id'].'）');
}else{
	$groupid=getdataid($website['webid'],'admin_group','groupid');
	$getgroup=db_getshow('admin_group','id','webid='.$website['webid'].' and group_name="'.$group_name.'"');
	if($getgroup)ajaxreturn(0,'分组名 '.$group_name.' 已被使用，请更换');
	$tab='webid,groupid,group_name,config_super,config_rank';
	$val=$website['webid'].','.$groupid.',"'.$group_name.'",'.$config_super.',"'.$config_rank.'"';
	db_intoshow('admin_group',$tab,$val);
	infoadminlog($website['webid'],$tcz['admin'],2,'新建管理组“'.$group_name.'”');
	}
ajaxreturn(0,'管理员分组编辑成功');