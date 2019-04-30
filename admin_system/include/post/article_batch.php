<?php
if(!ispower($admin_group,'art_edit'))ajaxreturn(1,'权限不足，操作失败');
$filepath='article/'.date('Y_m').'/';
createDirs('../'.$website['upfolder'].$filepath);
$cata=arg('cata','post','url');
$cata_arr=explode('_',$cata.'__');
$bcat=floatval($cata_arr[0]);
$scat=floatval($cata_arr[1]);
$lcat=floatval($cata_arr[2]);
$bmod=db_getshow('module','mark,showfile,title,modtype,languages,classid','webid='.$website['webid'].' and (menutype=0 or menutype=999) and classid='.$bcat);
if(!$bmod)ajaxreturn(1,'不存在的栏目');
$mark=$bmod['mark'];
$mod_showfile=$bmod['showfile'];
$mod_title=$bmod['title'];
if($scat){
	$smod=db_getshow('module','mark,showfile,title,classid','webid='.$website['webid'].' and menutype=1 and classid='.$scat);
	if(!$smod)ajaxreturn(1,'不存在的一级类别');
	$mark=$smod['mark'];
	$mod_showfile=$smod['showfile'];
	$mod_title=$smod['title'];
	}
if($lcat){
	$lmod=db_getshow('module','mark,showfile,title,classid','webid='.$website['webid'].' and menutype=2 and classid='.$lcat);
	if(!$lmod)ajaxreturn(1,'不存在的二级类别');
	$mark=$lmod['mark'];
	$mod_showfile=$lmod['showfile'];
	$mod_title=$lmod['title'];
	}
@require_once('../'.setup_webfolder.$website['webid'].'/config/global.php');
$data=array(
	'webid'=>$website['webid'],
	'dataid'=>getdataid($website['webid'],'article','dataid'),
	'mark'=>$mark,
	'showfile'=>$mod_showfile,
	'modtype'=>$bmod['modtype'],
	'bcat'=>$bcat,
	'scat'=>$scat,
	'lcat'=>$lcat,
	'special_id'=>trim(arg('special_id','post','url')),
	'languages'=>$bmod['languages'],
	'cover'=>trim(arg('cover','post','url')),
	'title'=>trim(arg('title','post','txt')),
	'source'=>'',
	'keyword'=>'',
	'description'=>'',
	'sort'=>1,
	'star'=>0,
	'piclist'=>'',
	'content'=>'',
	'linkurl'=>'',
	'isok'=>0,
	'time_add'=>time(),
	'time_update'=>time(),
	'time_top'=>0,
	'time_color'=>'',
	'user_admin'=>$tcz['admin'],
	'computer'=>0,
	'mobile'=>0
	);
$covercut=explode(',',setup_cover_cut.',0');
$cut_w=(int)$covercut[0];
$cut_h=(int)$covercut[1];
$coversize=explode(',',setup_cover_size.',1');
$cover_w=(int)$coversize[0];
$cover_h=(int)$coversize[1];
if($cover_w<1)$cover_w=1;
if($cover_h<1)$cover_h=1;
$covercanvas=setup_cover_canvas;
if($data['cover']!=''){
	$oldcover='../'.$website['upfolder'].setup_uptemp.$data['cover'];
	$data['cover']=$filepath.$data['cover'];
	$cover_b=str_replace('{size}','b',$data['cover']);
	$cover_s=str_replace('{size}','s',$data['cover']);
	$ps=new photo();
	$ps->setOpenpath($oldcover);
	$ps->setSavepath('../'.$website['upfolder'].$cover_s);
	$ps->setImgresize($covercanvas);
	$ps->setImgquality(90);
	$ps->setImgsize($cover_w,$cover_h,$cover_w,$cover_h);
	if(setup_marktype_cover&&setup_markimg!='')$ps->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
	$ps->setCutsize_end($cut_w,$cut_h);
	$ps->createImg();
	if(!setup_marktype_cover||setup_markimg=='')copy($oldcover,'../'.$website['upfolder'].$cover_b);
	else{
		$ps2=new photo();
		$ps2->setOpenpath($oldcover);
		$ps2->setSavepath('../'.$website['upfolder'].$cover_b);
		$ps2->setImgquality(90);
		$ps2->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
		$ps2->createImg();
		}
	}
db_intoarr('article',$data);
//专题
db_upshow('special_list','isdel=1','webid='.$website['webid'].' and modtype='.$bmod['modtype'].' and dataid='.$data['dataid']);
if($data['special_id']!=''){
	$special=db_getall('special','*','dataid in('.$data['special_id'].')');
	$newspe='';
	foreach($special as $spe){
		if($newspe!='')$newspe.=',';
		$newspe.=$spe['dataid'];
		$sarr=array(
			'webid'=>$website['webid'],
			'special_id'=>$spe['dataid'],
			'dataid'=>$data['dataid'],
			'modtype'=>$bmod['modtype'],
			'isdel'=>0
			);
		$isspe=db_getshow('special_list','id','webid='.$website['webid'].' and isdel=1');
		if($isspe)db_uparr('special_list',$sarr,'webid='.$website['webid'].' and id='.$isspe['id']);
		else db_intoarr('special_list',$sarr);
		}
	$data['special_id']=$newspe;
	}
infoadminlog($website['webid'],$tcz['admin'],13,'导入文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
countcapacity($website['webid']);
ajaxreturn(0,'已成功导入数据');