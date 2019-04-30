<?php
if(!ispower($admin_group,'module_edit'))ajaxreturn(1,'权限不足，操作失败');
$lang=arg('lang','post','url');
if($lang=='')ajaxreturn(1,'未知的语言版本');
$title=arg('title','post','txt');
$title_en=arg('title_en','post','txt');
$cover=arg('cover','post','url');
$keyword=arg('keyword','post','txt');
$description=arg('description','post','txt');
$isok=arg('isok','post','int');
$menutype=arg('menutype','post','int');
$bcat=$tcz['bcat'];
$scat=0;
$modtype=arg('modtype','post','int');
$linkurl=arg('linkurl','post','txt');
$sort=arg('sort','post','int');
$mark=arg('mark','post','url');
$modfile=arg('modfile','post','url');
$showfile=arg('showfile','post','url');
$pagesize=arg('pagesize','post','num');
$computer=arg('computer','post','int');
$mobile=arg('mobile','post','int');
$additional=arg('additional','post','url');
if($modtype!=9)$linkurl='';
else{
	$modfile='';
	$showfile='';
	}
if($modtype==1||$modtype>=8)$showfile='';
if($modtype!=2&&$modtype!=3)$additional='';
if($menutype==0||$menutype==999){
	if($mark=='')ajaxreturn(1,'请输入唯一标识');
	if($mark=='special')ajaxreturn(1,'“special”被系统内置功能占用，请更换标识');
	$bcat=0;
}else{
	$bigmod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$bcat);
	if(!$bigmod)ajaxreturn(1,'上一级模块不存在');
	$modtype=$bigmod['modtype'];	//与上一级一样的数据类型
	$mark=$bigmod['mark'];			//与上一级一样的标识
	if($bigmod['bcat']){
		$bigmod2=db_getshow('module','*','webid='.$website['webid'].' and classid='.$bigmod['bcat']);
		if(!$bigmod2)ajaxreturn(1,'第一级模块不存在');
		$scat=$bigmod['classid'];
		$bcat=$bigmod2['classid'];
		}
	}
$mod=db_getshow('module','*','webid='.$website['webid'].' and id='.$tcz['id']);
//ajaxreturn(1,'测：'.$bcat.' - '.$scat);
if($mark!=''&&($menutype==0||$menutype==999)){
	$mod_is=db_count('module','webid='.$website['webid'].' and id!='.$tcz['id'].' and (menutype=0 or menutype=999) and mark="'.$mark.'"')+0;
	if($mod_is)ajaxreturn(1,'唯一标识“'.$mark.'”已被其它模块使用，必须是唯一的');
	}
$filepath='module/';
if(!is_dir('../'.$website['upfolder'].$filepath))createDirs('../'.$website['upfolder'].$filepath);
$coversize=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_cover_size"');
if(!$coversize)$coversize='480,360';
$coversize=explode(',',$coversize.',1');
$cover_w=(int)$coversize[0];
$cover_h=(int)$coversize[1];
if($cover_w<1)$cover_w=1;
if($cover_h<1)$cover_h=1;
if($mod){
	if($mod['cover']!=$cover){
		if($mod['cover']!=''){
			$delpath1='..'.getfile_admin('pic',$mod['cover'],'s');
			if(file_exists($delpath1))unlink($delpath1);
			$delpath2='..'.getfile_admin('pic',$mod['cover'],'b');
			if(file_exists($delpath2))unlink($delpath2);
			}
		if($cover!=''){
			$oldcover='../'.$website['upfolder'].setup_uptemp.$cover;
			$cover=$filepath.$cover;
			$cover_b=str_replace('{size}','b',$cover);
			$cover_s=str_replace('{size}','s',$cover);
			copy($oldcover,'../'.$website['upfolder'].$cover_b);
			$ps=new photo();
			$ps->setOpenpath($oldcover);
			$ps->setSavepath('../'.$website['upfolder'].$cover_s);
			$ps->setImgresize(true);
			$ps->setImgquality(90);
			$ps->setImgsize($cover_w,$cover_h,$cover_w,$cover_h);
			$ps->createImg();
			}
		}
	$mod_is=db_count('module','webid='.$website['webid'].' and id!='.$tcz['id'].' and classid='.$mod['classid'])+0;
	if($mod_is)ajaxreturn(1,'ID“'.$mod['classid'].'”已被其它模块使用，必须是唯一的');
	if($modtype!=$mod['modtype']){	//数据类型改变时，下级模块同时要变更
		db_upshow('module','modtype='.$modtype,'webid='.$website['webid'].' and (bcat='.$mod['classid'].' or scat='.$mod['classid'].')');
		}
	if(($menutype==0||$menutype==999)&&$mark!=$mod['mark']){	//标识改变时，下级模块及文章都同时要变更
		db_upshow('module','mark="'.$mark.'"','webid='.$website['webid'].' and (bcat='.$mod['classid'].' or scat='.$mod['classid'].')');
		db_upshow('article','mark="'.$mark.'"','webid='.$website['webid'].' and bcat='.$mod['classid']);
		}
	db_upshow('module','title="'.$title.'",title_en="'.$title_en.'",cover="'.$cover.'",keyword="'.$keyword.'",description="'.$description.'",isok='.$isok.',menutype='.$menutype.',modtype='.$modtype.',linkurl="'.$linkurl.'",sort='.$sort.',mark="'.$mark.'",modfile="'.$modfile.'",showfile="'.$showfile.'",pagesize='.$pagesize.',computer='.$computer.',mobile='.$mobile.',additional="'.$additional.'"','id='.$tcz['id']);
	infoadminlog($website['webid'],$tcz['admin'],12,'编缉网站模块“'.$title.'”（ID='.$mod['classid'].'）');
}else{
	if($cover!=''){
		$oldcover='../'.$website['upfolder'].setup_uptemp.$cover;
		$cover=$filepath.$cover;
		$cover_b=str_replace('{size}','b',$cover);
		$cover_s=str_replace('{size}','s',$cover);
		copy($oldcover,'../'.$website['upfolder'].$cover_b);
		$ps=new photo();
		$ps->setOpenpath($oldcover);
		$ps->setSavepath('../'.$website['upfolder'].$cover_s);
		$ps->setImgresize(true);
		$ps->setImgquality(90);
		$ps->setImgsize($cover_w,$cover_h,$cover_w,$cover_h);
		$ps->createImg();
		}
	$classid=getdataid($website['webid'],'module','classid');
	$tab='webid,classid,languages,bcat,scat,modtype,title,title_en,cover,keyword,description,sort,mark,modfile,showfile,linkurl,menutype,isok,additional,computer,mobile,pagesize';
	$val=$website['webid'].','.$classid.',"'.$lang.'",'.$bcat.','.$scat.','.$modtype.',"'.$title.'","'.$title_en.'","'.$cover.'","'.$keyword.'","'.$description.'",'.$sort.',"'.$mark.'","'.$modfile.'","'.$showfile.'","'.$linkurl.'",'.$menutype.','.$isok.',"'.$additional.'",'.$computer.','.$mobile.','.$pagesize;
	infoadminlog($website['webid'],$tcz['admin'],12,'新建网站模块“'.$title.'”（ID='.$classid.'）');
	db_intoshow('module',$tab,$val);
	}
ajaxreturn(0,'已成功保存模块信息');