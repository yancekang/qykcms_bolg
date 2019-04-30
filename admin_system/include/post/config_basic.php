<?php
if(!$website['isadmin']||!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
$conf=db_getlist('select * from '.tabname('config').' where webid=1 and isview=0 and cata="basic" order by sort asc,id asc');
foreach($conf as $v){
	if($v['isedit']==1){
		$pv=$v['varval'];
	}else{
		$pv=arg('post_'.$v['varname'],'post',goif($v['vartype']==2,'int','txt'));
		db_upshow('config','varval='.goif($v['vartype']==2,$pv,'"'.$pv.'"'),'webid=1 and varname="'.$v['varname'].'" and cata="basic"');
		}
	}
$upstatus=updateallconf();
if(!$upstatus)ajaxreturn(1,'保存文件失败：include/config.php');
infoadminlog($website['webid'],$tcz['admin'],19,'保存基础配置：config/config.php');
ajaxreturn(0,'基础配置已成功保存');