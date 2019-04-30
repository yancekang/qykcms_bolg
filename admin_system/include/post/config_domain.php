<?php
if(!ispower($admin_group,'super'))ajaxreturn(9,'权限不足，操作失败');
$arr=array(
	'webid'=>$website['webid'],
	'setup_weburl'=>strtolower(arg('weburl','post','url')),
	'setup_record'=>arg('record','post','url'),
	'isdef'=>arg('isdef','post','int')
	);
if(!preg_match('/([a-z\0-9\-]+)\.([a-z\0-9]{1,5})$/',$arr['setup_weburl']))ajaxreturn(1,'输入的域名不正确，示例：www.qingyunke.com（中文域名请先转码）');
$domainmax=db_getone('websetup','domainmax','webid='.$website['webid']);
if(!$domainmax)ajaxreturn(1,'未知的站点');
$iswy=db_count('website','setup_weburl="'.$arr['setup_weburl'].'" and id!='.$tcz['id'])+0;
if($iswy)ajaxreturn(1,'域名 '.$arr['setup_weburl'].' 已被其它站点使用，请更换域名重试');
$data=db_getshow('website','*','webid='.$website['webid'].' and id='.$tcz['id']);
if($data){
	if($arr['isdef']!=$data['isdef']){
		if($arr['isdef']){
			db_upshow('website','isdef=0,isadmin=0','webid='.$website['webid']);
			if($website['isadmin'])$arr['isadmin']=1;
		}else{
			ajaxreturn(1,'请至少设置一个主域名，如需更改主域名请直接设置其它域名为主域名即可');
			}
		}
	db_uparr('website',$arr,'webid='.$website['webid'].' and id='.$tcz['id']);
	infoadminlog($website['webid'],$tcz['admin'],25,'修改域名绑定：'.$arr['setup_weburl']);
}else{
	$domainlen=db_count('website','webid='.$website['webid'])+0;
	if($domainlen>=$domainmax)ajaxreturn(1,'当前站点最多可绑定 '.$domainmax.' 个域名，目前已绑定 '.$domainlen.' 个');
	$arr['isadmin']=0;
	if($arr['isdef']){
		db_upshow('website','isdef=0,isadmin=0','webid='.$website['webid']);
		if($website['isadmin'])$arr['isadmin']=1;
		}
	db_intoarr('website',$arr);
	infoadminlog($website['webid'],$tcz['admin'],25,'绑定新域名：'.$arr['setup_weburl']);
	}
if($arr['isdef'])updatacofig($website['webid'],'global');
ajaxreturn(0,'域名绑定设置成功');