<?php
$tcz['bcat']=$module['classid'];
$web['list_size']=$module['pagesize'];
if($module['description']!='')$web['description']=$module['description'];
if($module['keyword']!='')$web['keyword']=$module['keyword'];
qyk::addpos($module['title'],'log='.$tcz['log']);
$newshowfile=false;
if($tcz['id']){
	if($module['modtype']==1){
		$link=getlink('log='.$module['mark']);
		header('Location:'.$link);
		exit;
		}
	if($module['showfile']!=''){
		$newshowfile=true;
		$web['tempfile']=$module['showfile'];
		}
	$web['tempcache'].='_show';
	$tname='article';
	if($module['modtype']>10)$tname.='_'.$web['id'].'_'.$module['modtype'];
	$article=db_getshow($tname,'*','webid='.$web['id'].' and isok=0 and dataid='.$tcz['id'].' and mark="'.$tcz['log'].'"');
	if(!$article)tipmsg('没有找到该记录，数据不存在、已被删除或尚未通过审核',true);
	db_upshow($tname,'hits=hits+1','dataid='.$tcz['id']);
	$article['hits']++;
	if($article['linkurl']!='')header('Location:'.$article['linkurl']);
	if($article['scat']){
		$scat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$article['scat']);
		if($scat){
			qyk::addpos($scat['title'],'log='.$tcz['log'].'&scat='.$scat['classid']);
			$tcz['scat']=$scat['classid'];
			if($scat['showfile']!=''){
				$newshowfile=true;
				$web['tempfile']=$scat['showfile'];
				}
			if($scat['description']!='')$web['description']=$scat['description'];
			if($scat['keyword']!='')$web['keyword']=$scat['keyword'];
			}
		}
	if($article['lcat']){
		$lcat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$article['lcat']);
		if($lcat){
			qyk::addpos($lcat['title'],'log='.$tcz['log'].'&lcat='.$lcat['classid']);
			$tcz['lcat']=$lcat['classid'];
			if($lcat['showfile']!=''){
				$newshowfile=true;
				$web['tempfile']=$lcat['showfile'];
				}
			if($lcat['description']!='')$web['description']=$lcat['description'];
			if($lcat['keyword']!='')$web['keyword']=$lcat['keyword'];
			}
		}
	if($article['showfile']!=''){
		$web['tempfile']=$article['showfile'];
		//$web['tempcache'].='_'.$article['dataid'];
		$web['tempcache'].='_'.$article['showfile'];
	}else if(!$newshowfile){
		$web['tempfile']=preg_replace('/\.'.setup_temptype.'$/','_show.'.setup_temptype.'',$web['tempfile']);	//模板文件
		}
	$page_webtitle=$article['title'];
	if(isset($article['content'])){/*文章分页*/
		if(strstr($article['content'],setup_pagetag)){
			$contarr=explode(setup_pagetag,$article['content']);
			$allpage=count($contarr);
			if($tcz['page']>$allpage)tipmsg(getlang('sys','artpage_err'),true);
			else if($tcz['page']>1)$page_webtitle.=sprintf(getlang('sys','artpage'),$tcz['page']);
			$article['content']=$contarr[$tcz['page']-1];
			$page=new pagelist();
			$web['list_page']=$page->show($allpage,1,$link='',goif($web['mobiletemp'],'mobile','mini'),false);
		}else if($tcz['page']>100){
			tipmsg(getlang('sys','artpage_err'),true);
			}
		}
	if($article['keyword']!=''){
		qyk::addpos($page_webtitle,'log='.$tcz['log'].'&id='.$article['dataid'].goif($tcz['page']>1,'&page='.$tcz['page']),false,false);
		$web['keyword']=$article['keyword'];
	}else{
		qyk::addpos($page_webtitle,'log='.$tcz['log'].'&id='.$article['dataid'].goif($tcz['page']>1,'&page='.$tcz['page']),false,true);
		}
	if($article['description']!='')$web['description']=$article['description'];
	$article['mood_1_scale']=0;
	$article['mood_2_scale']=0;
	$article['mood_3_scale']=0;
	$article['mood_4_scale']=0;
	$article['mood_5_scale']=0;
	$mood_all=$article['mood_1']+$article['mood_2']+$article['mood_3']+$article['mood_4']+$article['mood_5'];
	if($mood_all){
		$article['mood_1_scale']=round($article['mood_1']/$mood_all*100);
		$article['mood_2_scale']=round($article['mood_2']/$mood_all*100);
		$article['mood_3_scale']=round($article['mood_3']/$mood_all*100);;
		$article['mood_4_scale']=round($article['mood_4']/$mood_all*100);;
		$article['mood_5_scale']=100-$article['mood_1_scale']-$article['mood_2_scale']-$article['mood_3_scale']-$article['mood_4_scale'];
		}
}else{
	if($module['modfile']!='')$web['tempfile']=$module['modfile'];
	switch($module['modtype']){
		case 1:
			$sql='webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and mark="'.$module['mark'].'"';
			if($tcz['scat']||$tcz['lcat']){
				$sql.=goif($tcz['scat'],' and scat='.$tcz['scat']).goif($tcz['lcat'],' and lcat='.$tcz['lcat'],' and lcat=0');
				$sql.=' order by sort desc,time_add desc,dataid desc';
			}else{
				$modauto=db_getshow('module','*','webid='.$web['id'].' and isok=0 and bcat='.$module['classid'].' and menutype=1 order by sort asc,classid asc');
				$modid=floatval($modauto['classid']);
				$sql.=' and scat='.$modid.' order by xu desc,sort desc,time_add desc,dataid desc';
				}
			$article=db_getshow('article','*,if(lcat=0,1,0) as xu',$sql);
			//echo $sql;
			if(!$article){
				$article=array(
					'id'=>0,
					'dataid'=>0,
					'title'=>'',
					'content'=>'No Data',
					'content1'=>'',
					'time_add'=>time(),
					'time_update'=>time(),
					'hits'=>0
					);
			}else{
				if($article['showfile']!=''){
					$web['tempfile']=$article['showfile'];
					//$web['tempcache'].='_'.$article['dataid'];
					$web['tempcache'].='_'.$article['showfile'];
					}
				$tcz['id']=$article['dataid'];
				$tcz['scat']=$article['scat'];
				$tcz['lcat']=$article['lcat'];
				/*文章分页*/
				if(strstr($article['content'],setup_pagetag)){
					$sort=$tcz['page']-1;
					$contarr=explode(setup_pagetag,$article['content']);
					$allpage=count($contarr);
					if($tcz['page']>$allpage)tipmsg('已超出当前页数范围',true);
					$article['content']=$contarr[$sort];
					$page=new pagelist();
					$web['list_page']=$page->show($allpage,1,$link='',$module='mini',false);
					}
				if($article['description']!='')$web['description']=$article['description'];
				if($article['keyword']!='')$web['keyword']=$article['keyword'];
				}
		break;
		}
	if($tcz['lcat']){
		$lcat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$tcz['lcat']);
		if(!$lcat)tipmsg('不存在或未启用的分类：lcat='.$tcz['lcat'],true);
		$tcz['scat']=$lcat['scat'];
		}
	if($tcz['scat']){
		$scat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$tcz['scat']);
		if(!$scat)tipmsg('不存在或未启用的分类：scat='.$tcz['scat'],true);
		qyk::addpos($scat['title'],'log='.$tcz['log'].'&scat='.$scat['classid']);
		$web['list_size']=$scat['pagesize'];
		if($scat['modfile']!=''){
			$web['tempfile']=$scat['modfile'];
			//$web['tempcache'].='_scat'.$tcz['scat'];
			$web['tempcache'].='_scat-'.$scat['modfile'];
			}
		if($scat['description']!='')$web['description']=$scat['description'];
		if($scat['keyword']!='')$web['keyword']=$scat['keyword'];
		}
	if($tcz['lcat']){
		qyk::addpos($lcat['title'],'log='.$tcz['log'].'&lcat='.$lcat['classid']);
		$web['list_size']=$lcat['pagesize'];
		if($lcat['modfile']!=''){
			$web['tempfile']=$lcat['modfile'];
			//$web['tempcache'].='_lcat'.$tcz['lcat'];
			$web['tempcache'].='_lcat-'.$lcat['modfile'];
			}
		if($lcat['description']!='')$web['description']=$lcat['description'];
		if($lcat['keyword']!='')$web['keyword']=$lcat['keyword'];
		}
	}
?>