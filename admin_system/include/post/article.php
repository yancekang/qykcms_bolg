<?php
if(!ispower($admin_group,'art_edit'))ajaxreturn(1,'权限不足，操作失败');
$filepath='article/'.date('Y_m').'/';
createDirs('../'.$website['upfolder'].$filepath);
$cata=arg('cata','post','url');
$cata_arr=explode('_',$cata.'__');
$bcat=floatval($cata_arr[0]);
$scat=floatval($cata_arr[1]);
$lcat=floatval($cata_arr[2]);
$mod=db_getshow('module','mark,showfile,title,modtype,languages,classid','webid='.$website['webid'].' and (menutype=0 or menutype=999) and classid='.$bcat);
if(!$mod)ajaxreturn(1,'不存在的栏目');
$mark=$mod['mark'];
$mod_showfile=$mod['showfile'];
$mod_title=$mod['title'];
$modtype=$mod['modtype'];
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
$time_top_check=arg('time_top_check','post','url');
$data=array(
	'webid'=>$website['webid'],
	'dataid'=>getdataid($website['webid'],'article','dataid'),
	'mark'=>$mark,
	'showfile'=>trim(arg('showfile','post','txt')),
	'modtype'=>$modtype,
	'bcat'=>$bcat,
	'scat'=>$scat,
	'lcat'=>$lcat,
	'special_id'=>trim(arg('special_id','post','url')),
	'languages'=>$mod['languages'],
	'title'=>trim(arg('title','post','txt')),
	'source'=>trim(arg('source','post','txt')),
	'keyword'=>trim(arg('keyword','post','txt')),
	'description'=>trim(arg('description','post','txt')),
	'sort'=>arg('sort','post','int'),
	'star'=>arg('star','post','int'),
	'cover'=>trim(arg('cover','post','url')),
	'piclist'=>'',
	'content'=>'',
	'content1'=>'',
	'content2'=>'',
	'content3'=>'',
	'content4'=>'',
	'linkurl'=>trim(arg('linkurl','post','txt')),
	'isok'=>arg('isok','post','int'),
	'time_add'=>arg('time_add','post','url'),
	'time_update'=>time(),
	'time_top'=>arg('time_top','post','url'),
	'time_color'=>arg('time_color','post','url'),
	'user_admin'=>$tcz['admin'],
	'parameters'=>trim(arg('parameters','post','txt')),
	'computer'=>arg('computer','post','int'),
	'mobile'=>arg('mobile','post','int'),
	'mood_1'=>arg('mood_1','post','int'),
	'mood_2'=>arg('mood_2','post','int'),
	'mood_3'=>arg('mood_3','post','int'),
	'mood_4'=>arg('mood_4','post','int'),
	'mood_5'=>arg('mood_5','post','int')
	);
if($data['showfile']=='')$data['showfile']=$mod_showfile;
if($data['title']=='')$data['title']=$mod_title;
if($data['time_add']!='')$data['time_add']=strtotime($data['time_add']);
else $data['time_add']=time();
if($time_top_check=='ok'){
	if($data['time_top']!='')$data['time_top']=strtotime($data['time_top']);
	else $data['time_top']=$data['time_add'];
}else{
	$data['time_top']=0;
	}
$art=db_getshow('article','*','webid='.$website['webid'].' and id='.$tcz['id']);
if($art){
	$data['dataid']=$art['dataid'];
	$data=deltable($data,'webid');
	}
@require_once('../'.setup_webfolder.$website['webid'].'/config/global.php');
$covercut=explode(',',setup_cover_cut.',0');
$cut_w=(int)$covercut[0];
$cut_h=(int)$covercut[1];
$coversize=explode(',',setup_cover_size.',1');
$cover_w=(int)$coversize[0];
$cover_h=(int)$coversize[1];
if($cover_w<1)$cover_w=1;
if($cover_h<1)$cover_h=1;
$covercanvas=setup_cover_canvas;
//专题
db_upshow('special_list','isdel=1','webid='.$website['webid'].' and modtype='.$mod['modtype'].' and dataid='.$data['dataid']);
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
			'modtype'=>$mod['modtype'],
			'isdel'=>0
			);
		$isspe=db_getshow('special_list','id','webid='.$website['webid'].' and isdel=1');
		if($isspe)db_uparr('special_list',$sarr,'webid='.$website['webid'].' and id='.$isspe['id']);
		else db_intoarr('special_list',$sarr);
		}
	$data['special_id']=$newspe;
	}
switch($modtype){
	case 1:
		$content=arg('content','post','url');
		if($art){
			$cont=handleImage($content,$art['piclist']);
			$data['content']=$cont['cont'];
			$data['piclist']=$cont['img'];
			db_uparr('article',$data,'webid='.$website['webid'].' and id='.$art['id']);
			infoadminlog($website['webid'],$tcz['admin'],13,'编辑文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
		}else{
			$cont=handleImage($content);
			$data['content']=$cont['cont'];
			$data['piclist']=$cont['img'];
			db_intoarr('article',$data);
			infoadminlog($website['webid'],$tcz['admin'],13,'新建文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
			}
	break;
	case 2:
	case 3:
		$content=arg('content','post','url');
		$content1=arg('content1','post','url');
		$content2=arg('content2','post','url');
		$content3=arg('content3','post','url');
		$content4=arg('content4','post','url');
		if($art){
			if($art['cover']!=$data['cover']){
				if($art['cover']!=''){
					$delpath1='..'.getfile_admin('pic',$art['cover'],'s');
					if(file_exists($delpath1))unlink($delpath1);
					$delpath2='..'.getfile_admin('pic',$art['cover'],'b');
					if(file_exists($delpath2))unlink($delpath2);
					}
				if($data['cover']!=''){
					$oldcover='../'.$website['upfolder'].setup_uptemp.$data['cover'];
					$data['cover']=$filepath.$data['cover'];
					$cover_b=str_replace('{size}','b',$data['cover']);
					$cover_s=str_replace('{size}','s',$data['cover']);
					if(!setup_marktype_cover||setup_markimg=='')copy($oldcover,'../'.$website['upfolder'].$cover_b);
					else{
						$ps2=new photo();
						$ps2->setOpenpath($oldcover);
						$ps2->setSavepath('../'.$website['upfolder'].$cover_b);
						$ps2->setImgquality(90);
						$ps2->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
						$ps2->createImg();
						}
					$ps=new photo();
					$ps->setOpenpath($oldcover);
					$ps->setSavepath('../'.$website['upfolder'].$cover_s);
					$ps->setImgresize($covercanvas);
					$ps->setImgquality(90);
					$ps->setImgsize($cover_w,$cover_h,$cover_w,$cover_h);
					if(setup_marktype_cover&&setup_markimg!='')$ps->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
					$ps->setCutsize_end($cut_w,$cut_h);
					$ps->createImg();
					}
				}
			$piclist=explode('$',$art['piclist'].'$$$$$');
			$cont=handleImage($content,$piclist[0]);
			$cont1=handleImage($content1,$piclist[1]);
			$cont2=handleImage($content2,$piclist[2]);
			$cont3=handleImage($content3,$piclist[3]);
			$cont4=handleImage($content4,$piclist[4]);
			$data['piclist']=$cont['img'].'$'.$cont1['img'].'$'.$cont2['img'].'$'.$cont3['img'].'$'.$cont4['img'];
			$data['content']=$cont['cont'];
			$data['content1']=$cont1['cont'];
			$data['content2']=$cont2['cont'];
			$data['content3']=$cont3['cont'];
			$data['content4']=$cont4['cont'];
			db_uparr('article',$data,'webid='.$website['webid'].' and id='.$art['id']);
			infoadminlog($website['webid'],$tcz['admin'],13,'编辑文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
		}else{
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
			$cont=handleImage($content);
			$cont1=handleImage($content1);
			$cont2=handleImage($content2);
			$cont3=handleImage($content3);
			$cont4=handleImage($content4);
			$data['piclist']=$cont['img'].'$'.$cont1['img'].'$'.$cont2['img'].'$'.$cont3['img'].'$'.$cont4['img'];
			$data['content']=$cont['cont'];
			$data['content1']=$cont1['cont'];
			$data['content2']=$cont2['cont'];
			$data['content3']=$cont3['cont'];
			$data['content4']=$cont4['cont'];
			db_intoarr('article',$data);
			infoadminlog($website['webid'],$tcz['admin'],13,'新建文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
			}
	break;
	case 4:
	case 6:
		$data['content']=arg('content','post','txt');
		if($art){
			if($art['cover']!=$data['cover']){
				if($art['cover']!=''){
					$delpath1='..'.getfile_admin('pic',$art['cover'],'s');
					if(file_exists($delpath1))unlink($delpath1);
					$delpath2='..'.getfile_admin('pic',$art['cover'],'b');
					if(file_exists($delpath2))unlink($delpath2);
					}
				if($data['cover']!=''){
					$oldcover='../'.$website['upfolder'].setup_uptemp.$data['cover'];
					$data['cover']=$filepath.$data['cover'];
					$cover_b=str_replace('{size}','b',$data['cover']);
					$cover_s=str_replace('{size}','s',$data['cover']);
					if(!setup_marktype_cover||setup_markimg=='')copy($oldcover,'../'.$website['upfolder'].$cover_b);
					else{
						$ps2=new photo();
						$ps2->setOpenpath($oldcover);
						$ps2->setSavepath('../'.$website['upfolder'].$cover_b);
						$ps2->setImgquality(90);
						$ps2->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
						$ps2->createImg();
						}
					$ps=new photo();
					$ps->setOpenpath($oldcover);
					$ps->setSavepath('../'.$website['upfolder'].$cover_s);
					$ps->setImgresize($covercanvas);
					$ps->setImgquality(90);
					$ps->setImgsize($cover_w,$cover_h,$cover_w,$cover_h);
					if(setup_marktype_cover&&setup_markimg!='')$ps->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
					$ps->setCutsize_end($cut_w,$cut_h);
					$ps->createImg();
					}
				}
			db_uparr('article',$data,'webid='.$website['webid'].' and id='.$art['id']);
			infoadminlog($website['webid'],$tcz['admin'],13,'编辑文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
		}else{
			if($data['cover']!=''){
				$oldcover='../'.$website['upfolder'].setup_uptemp.$data['cover'];
				$data['cover']=$filepath.$data['cover'];
				$cover_b=str_replace('{size}','b',$data['cover']);
				$cover_s=str_replace('{size}','s',$data['cover']);
				if(!setup_marktype_cover||setup_markimg=='')copy($oldcover,'../'.$website['upfolder'].$cover_b);
				else{
					$ps2=new photo();
					$ps2->setOpenpath($oldcover);
					$ps2->setSavepath('../'.$website['upfolder'].$cover_b);
					$ps2->setImgquality(90);
					$ps2->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
					$ps2->createImg();
					}
				$ps=new photo();
				$ps->setOpenpath($oldcover);
				$ps->setSavepath('../'.$website['upfolder'].$cover_s);
				$ps->setImgresize($covercanvas);
				$ps->setImgquality(90);
				$ps->setImgsize($cover_w,$cover_h,$cover_w,$cover_h);
				if(setup_marktype_cover&&setup_markimg!='')$ps->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
				$ps->setCutsize_end($cut_w,$cut_h);
				$ps->createImg();
				}
			db_intoarr('article',$data);
			infoadminlog($website['webid'],$tcz['admin'],13,'新建文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
			}
	break;
	case 5:
		$data['content']=arg('content','post','txt');
		if($art){
			db_uparr('article',$data,'webid='.$website['webid'].' and id='.$art['id']);
			infoadminlog($website['webid'],$tcz['admin'],13,'编辑文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
		}else{
			db_intoarr('article',$data);
			infoadminlog($website['webid'],$tcz['admin'],13,'新建文章“'.$data['title'].'”（ID='.$data['dataid'].'）');
			}
	break;
	}
if($art){
	if($art['bcat']!=$bcat){
		db_upshow('feedback','bcat='.$bcat,'webid='.$website['webid'].' and bcat='.$art['bcat'].' and dataid='.$art['dataid']);
		}
	cleardatacache('article',$art);
	}
countcapacity($website['webid']);
ajaxreturn(0,'已成功保存文章信息');