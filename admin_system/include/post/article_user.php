<?php
if(!ispower($admin_group,'art_edit'))ajaxreturn(1,'权限不足，操作失败');
$filepath='article/'.date('Y_m').'/';
createDirs('../'.$website['upfolder'].$filepath);
$cata=arg('post_cata','post','url');
$cata_arr=explode('_',$cata.'__');
$bcat=floatval($cata_arr[0]);
$scat=floatval($cata_arr[1]);
$lcat=floatval($cata_arr[2]);
$bmod=db_getshow('module','mark,showfile,title,modtype,languages,classid','webid='.$website['webid'].' and (menutype=0 or menutype=999) and classid='.$bcat);
if(!$bmod)ajaxreturn(1,'不存在的栏目');
$usermod=db_getshow('module_user','*','webid='.$website['webid'].' and dataid='.$bmod['modtype']);
if(!$usermod)ajaxreturn(1,'不存在的自定义模块');
$tname='article_'.$website['webid'].'_'.$bmod['modtype'];
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
$time_top_check=arg('post_time_top_check','post','url');
$data=array(
	'webid'=>$website['webid'],
	'dataid'=>getdataid($website['webid'],$tname,'dataid'),
	'mark'=>$mark,
	'showfile'=>trim(arg('post_showfile','post','txt')),
	'modtype'=>$bmod['modtype'],
	'bcat'=>$bcat,
	'scat'=>$scat,
	'lcat'=>$lcat,
	'special_id'=>trim(arg('post_special_id','post','url')),
	'languages'=>$bmod['languages'],
	'title'=>trim(arg('post_title','post','txt')),
	'source'=>trim(arg('post_source','post','txt')),
	'keyword'=>trim(arg('post_keyword','post','txt')),
	'description'=>trim(arg('post_description','post','txt')),
	'sort'=>arg('post_sort','post','int'),
	'star'=>arg('post_star','post','int'),
	'piclist'=>'',
	'linkurl'=>trim(arg('post_linkurl','post','txt')),
	'isok'=>arg('post_isok','post','int'),
	'time_add'=>arg('post_time_add','post','url'),
	'time_update'=>time(),
	'time_top'=>arg('post_time_top','post','url'),
	'time_color'=>arg('post_time_color','post','url'),
	'user_admin'=>$tcz['admin'],
	'computer'=>arg('post_computer','post','int'),
	'mobile'=>arg('post_mobile','post','int'),
	'mood_1'=>arg('post_mood_1','post','int'),
	'mood_2'=>arg('post_mood_2','post','int'),
	'mood_3'=>arg('post_mood_3','post','int'),
	'mood_4'=>arg('post_mood_4','post','int'),
	'mood_5'=>arg('post_mood_5','post','int')
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
$art=db_getshow($tname,'*','webid='.$website['webid'].' and id='.$tcz['id']);
if($art){
	$data['dataid']=$art['dataid'];
	}
$covercut=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_cover_cut"');
if(!$covercut)$covercut='0,0';
$covercut=explode(',',$covercut.',0');
$cut_w=(int)$covercut[0];
$cut_h=(int)$covercut[1];
$coversize=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_cover_size"');
if(!$coversize)$coversize='480,360';
$coversize=explode(',',$coversize.',1');
$cover_w=(int)$coversize[0];
$cover_h=(int)$coversize[1];
if($cover_w<1)$cover_w=1;
if($cover_h<1)$cover_h=1;
$covercanvas=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_cover_canvas"');
if($covercanvas=='true')$covercanvas=true;
else $covercanvas=false;
$conf_cover=array();
$conf_file=array();
$conf_editor=array();
$conf=db_getall('module_field','*','webid='.$website['webid'].' and modid='.$bmod['modtype'].' order by sort asc');
foreach($conf as $val){
	switch($val['infotype']){
		case 'up_cover':
			array_push($conf_cover,$val['varname']);
			$pv=arg('post_'.$val['varname'],'post','url');
		break;
		case 'up_file':
			array_push($conf_file,$val['varname']);
			$pv=arg('post_'.$val['varname'],'post','url');
		break;
		case 'editor':
			array_push($conf_editor,$val['varname']);
			$pv=arg('post_'.$val['varname'],'post','url');
		break;
		default:
			$pv=arg('post_'.$val['varname'],'post','txt');
		break;
		}
	$data[$val['varname']]=$pv;
	}
if($art){
	foreach($conf_cover as $vname){
		if($art[$vname]!=$data[$vname]){
			if($art[$vname]!=''){
				$delpath1='..'.getfile_admin('pic',$art[$vname],'s');
				if(file_exists($delpath1))unlink($delpath1);
				$delpath2='..'.getfile_admin('pic',$art[$vname],'b');
				if(file_exists($delpath2))unlink($delpath2);
				}
			if($data[$vname]!=''){
				$oldcover='../'.$website['upfolder'].setup_uptemp.$data[$vname];
				$data[$vname]=$filepath.$data[$vname];
				$cover_b=str_replace('{size}','b',$data[$vname]);
				$cover_s=str_replace('{size}','s',$data[$vname]);
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
		}
	foreach($conf_file as $vname){
		if($art[$vname]!=$data[$vname]){
			if($art[$vname]!=''){
				$delpath1='..'.getfile_admin('file',$art[$vname]);
				if(file_exists($delpath1))unlink($delpath1);
				}
			if($data[$vname]!=''){
				$oldfile='../'.$website['upfolder'].setup_uptemp.$data[$vname];
				$data[$vname]=$filepath.$data[$vname];
				@rename($oldfile,'../'.$website['upfolder'].$data[$vname]);
				}
			}
		}
	$picarr=unserialize(htmlspecialchars_decode($art['piclist']));
	if(!is_array($picarr)||empty($picarr))$picarr=array();
	$picarr_new=array();
	foreach($conf_editor as $vname){
		$picdata='';
		if(array_key_exists($vname,$picarr)){
			$picdata=$picarr[$vname];
			$picarr=deltable($picarr,$vname);
			}
		$cont=handleImage($data[$vname],$picdata);
		$data[$vname]=$cont['cont'];
		$picarr_new[$vname]=$cont['img'];
		}
	//删除垃圾图片
	foreach($picarr as $pic){
		$p=explode('|',$pic);
		foreach($p as $p2){
			if($p2!=''){
				$delpath='../'.$website['upfolder'].$p2;
				@unlink($delpath);
				}
			}
		}
	$data['piclist']=htmlspecialchars(serialize($picarr_new));
	//专题
	db_upshow('special_list','isdel=1','webid='.$website['webid'].' and modtype='.$bmod['modtype'].' and dataid='.$art['dataid']);
	if($data['special_id']!=''){
		$special=db_getall('special','*','dataid in('.$data['special_id'].')');
		$newspe='';
		foreach($special as $spe){
			if($newspe!='')$newspe.=',';
			$newspe.=$spe['dataid'];
			$sarr=array(
				'webid'=>$website['webid'],
				'special_id'=>$spe['dataid'],
				'dataid'=>$art['dataid'],
				'modtype'=>$bmod['modtype'],
				'isdel'=>0
				);
			$isspe=db_getshow('special_list','id','webid='.$website['webid'].' and isdel=1');
			if($isspe)db_uparr('special_list',$sarr,'webid='.$website['webid'].' and id='.$isspe['id']);
			else db_intoarr('special_list',$sarr);
			}
		$data['special_id']=$newspe;
		}
	db_uparr($tname,$data,'webid='.$website['webid'].' and id='.$art['id']);
	if($art['bcat']!=$bcat){
		db_upshow('feedback','bcat='.$bcat,'webid='.$website['webid'].' and bcat='.$art['bcat'].' and dataid='.$art['dataid']);
		}
	cleardatacache('article',$data);
	infoadminlog($website['webid'],$tcz['admin'],13,'编辑文章“'.$data['title'].'”（ID='.$data['dataid'].'，MODID='.$bmod['modtype'].'）');
}else{
	foreach($conf_cover as $vname){
		if($data[$vname]!=''){
			$oldcover='../'.$website['upfolder'].setup_uptemp.$data[$vname];
			$data[$vname]=$filepath.$data[$vname];
			$cover_b=str_replace('{size}','b',$data[$vname]);
			$cover_s=str_replace('{size}','s',$data[$vname]);
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
	foreach($conf_file as $vname){
		if($data[$vname]!=''){
			$oldfile='../'.$website['upfolder'].setup_uptemp.$data[$vname];
			$data[$vname]=$filepath.$data[$vname];
			@rename($oldfile,'../'.$website['upfolder'].$data[$vname]);
			}
		}
	$picarr_new=array();
	foreach($conf_editor as $vname){
		$cont=handleImage($data[$vname]);
		$data[$vname]=$cont['cont'];
		$picarr_new[$vname]=$cont['img'];
		}
	$data['piclist']=htmlspecialchars(serialize($picarr_new));
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
	db_intoarr($tname,$data);
	infoadminlog($website['webid'],$tcz['admin'],13,'新建文章“'.$data['title'].'”（ID='.$data['dataid'].'，MODID='.$bmod['modtype'].'）');
	}
countcapacity($website['webid']);
ajaxreturn(0,'已成功保存文章信息');