<?php
if(!ispower($admin_group,'sys_head'))ajaxreturn(1,'权限不足，操作失败');
$admin=db_getshow('admin','*','webid='.$website['webid'].' and user_admin="'.$tcz['admin'].'"');
$user_head=arg('user_head','post','url');
if($admin['user_head']!=$user_head){
	if($admin['user_head']!=''){
		$delpath='..'.getfile_admin('pic',$admin['user_head']);
		if(file_exists($delpath))unlink($delpath);
		}
	if($user_head!=''){
		$oldhead='../'.$website['upfolder'].setup_uptemp.$user_head;
		$user_head='admin/'.date('Ym_').$user_head;
		copy($oldhead,'../'.$website['upfolder'].$user_head);
		}
	}
db_upshow('admin','user_head="'.$user_head.'"','id='.$admin['id']);
if($user_head!='')infoadminlog($website['webid'],$tcz['admin'],22,'上传头像：'.$user_head);
else infoadminlog($website['webid'],$tcz['admin'],22,'删除头像');
countcapacity($website['webid']);
ajaxreturn(0,'头像修改成功');