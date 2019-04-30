<?php
if(!ispower($admin_group,'skin_option'))ajaxreturn(1,'权限不足，操作失败');
$data=db_getshow('select','*','webid='.$website['webid'].' and id='.$tcz['id']);
$sort=arg('sort','post','int');
$types=arg('types','post','url');
$title=arg('title','post','txt');
$bcat=arg('bcat','post','int');
$arr=array(
	'webid'=>$website['webid'],
	'bcat'=>$bcat,
	'types'=>$types,
	'title'=>$title,
	'sort'=>$sort
	);
if($data){
	db_uparr('select',$arr,'webid='.$website['webid'].' and id='.$data['id']);
	infoadminlog($website['webid'],$tcz['admin'],27,'编辑自定义选项“'.$data['title'].'”（ID='.$data['id'].'）');
}else{
	$newid=db_intoarr('select',$arr,true);
	infoadminlog($website['webid'],$tcz['admin'],27,'新建自定义选项“'.$title.'”（ID='.$newid.'）');
	}
ajaxreturn(0,'选项已保存');