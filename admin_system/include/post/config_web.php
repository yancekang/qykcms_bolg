<?php
if(!ispower($admin_group,'sys_config'))ajaxreturn(1,'权限不足，操作失败');
$post_languages=arg('post_languages','post','url');
$upvarlist=db_getall('config','varname','webid='.$website['webid'].' and infotype="upload" and cata="'.$post_languages.'"');
$upvararr=array();
foreach($upvarlist as $vn){
	array_push($upvararr,$vn['varname']);
	}
if($post_languages=='')ajaxreturn(1,'未知的语言版本');
$conf=db_getlist('select * from '.tabname('config').' where webid='.$website['webid'].' and isview=0 and cata="'.$post_languages.'" order by sort asc,id asc');
foreach($conf as $v){
	if($v['isedit']==1){
		$pv=$v['varval'];
	}else if(in_array($v['varname'],$upvararr)){
		$pv=arg('post_'.$v['varname'],'post','url');
		$oldmark=db_getone('config','varval','webid='.$website['webid'].' and varname="'.$v['varname'].'" and cata="'.$post_languages.'"');
		if($oldmark!=$pv){
			if($oldmark!=''){
				$delpath='..'.getfile_admin('pic',$oldmark);
				if(file_exists($delpath))@unlink($delpath);
				}
			if($pv!=''){
				$oldurl='../'.$website['upfolder'].setup_uptemp.$pv;
				$pv='config/'.date('Ym_').$pv;
				copy($oldurl,'../'.$website['upfolder'].$pv);
				}
			db_upshow('config','varval="'.$pv.'"','webid='.$website['webid'].' and varname="'.$v['varname'].'" and cata="'.$post_languages.'"');
			}
	}else{
		$pv=arg('post_'.$v['varname'],'post',goif($v['vartype']==2,'int','txt'));
		db_upshow('config','varval='.goif($v['vartype']==2,$pv,'"'.$pv.'"'),'webid='.$website['webid'].' and varname="'.$v['varname'].'" and cata="'.$post_languages.'"');
		}
	}
$upstatus=updatacofig($website['webid'],$post_languages);
if(!$upstatus)ajaxreturn(1,'保存文件失败：config/'.$post_languages.'.php');
infoadminlog($website['webid'],$tcz['admin'],15,'保存网站配置：config/'.$post_languages.'.php');
ajaxreturn(0,'网站配置已成功保存');