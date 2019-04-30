<?php
//$test=arg('post_setup_header_pc','post','txt');
//ajaxreturn(1,$test);
if(!ispower($admin_group,'sys_setup'))ajaxreturn(1,'权限不足，操作失败');
$langlist=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"');
$langarr=explode(',',$langlist);
$upvarlist=db_getall('config','varname','webid='.$website['webid'].' and infotype="upload"');
$upvararr=array();
foreach($upvarlist as $vn){
	array_push($upvararr,$vn['varname']);
	}
$catalist=explode(',',setup_am_setup_cata);
foreach($catalist as $sort=>$cv){
	$iv=explode('|',$cv);
	$conf=db_getlist('select * from '.tabname('config').' where webid='.$website['webid'].' and isview=0 and cata="'.$iv[0].'" order by sort asc,id asc');
	foreach($conf as $v){
		if($v['isedit']==1){
			$pv=$v['varval'];
		}else if($v['varname']=='setup_theme_folder'){
			$pv=arg('post_'.$v['varname'],'post','url');
			if($pv!=$v['varval']){
				$pv=preg_replace('/([^0-9a-z_]+)/','',strtolower($pv));
				if($pv=='')$pv=randomkeys(8,2).'_'.randomkeys(8,2);
				foreach($langarr as $lang){
					$en=current(explode('|',$lang));
					$themepath='../'.setup_webfolder.$website['webid'].'/'.$en.'/';
					if(is_dir($themepath.$v['varval'])){
						if(!is_dir($themepath.$pv)){
							@rename($themepath.$v['varval'],$themepath.$pv);
							}
						}
					$themepath_mobile='../'.setup_webfolder.$website['webid'].'/'.$en.'_mobile/';
					if(is_dir($themepath_mobile.$v['varval'])){
						if(!is_dir($themepath_mobile.$pv)){
							@rename($themepath_mobile.$v['varval'],$themepath_mobile.$pv);
							}
						}
					}
				db_upshow('config','varval="'.$pv.'"','webid='.$website['webid'].' and varname="'.$v['varname'].'"');
				}
		}else if(in_array($v['varname'],$upvararr)){
			$pv=arg('post_'.$v['varname'],'post','url');
			$oldmark=db_getone('config','varval','webid='.$website['webid'].' and varname="'.$v['varname'].'"');
			if($oldmark!=$pv){
				if($oldmark!=''){
					$delpath='..'.getfile_admin('pic',$oldmark);
					if(file_exists($delpath))@unlink($delpath);
					}
				if($pv!=''){
					$oldurl='../'.$website['upfolder'].setup_uptemp.$pv;
					if($v['varname']=='setup_favicon')$pv='config/favicon.ico';
					else $pv='config/'.date('Ym_').$pv;
					copy($oldurl,'../'.$website['upfolder'].$pv);
					}
				db_upshow('config','varval="'.$pv.'"','webid='.$website['webid'].' and varname="'.$v['varname'].'"');
				}
		}else{
			$pv=arg('post_'.$v['varname'],'post',goif($v['vartype']==2,'int','txt'));
			db_upshow('config','varval='.goif($v['vartype']==2,$pv,'"'.$pv.'"'),'webid='.$website['webid'].' and varname="'.$v['varname'].'"');
			}
		}
	}
countcapacity($website['webid']);
$upstatus=updatacofig($website['webid'],'global');
if(!$upstatus)ajaxreturn(1,'保存文件失败：config/global.php');
infoadminlog($website['webid'],$tcz['admin'],15,'保存系统设置：config/global.php');
ajaxreturn(0,'系统设置已成功保存');