<?php
if($tcz['id']){
	$special=db_getshow('special','*','webid='.$web['id'].' and isok=0 and languages="'.$web['templang'].'" and dataid='.$tcz['id']);
	if(!$special)tipmsg('没有找到该记录，数据不存在、已被删除或尚未通过审核',true);
	if($tcz['log']=='special'){
		$web['tempfile']='special_show.'.setup_temptype;
		$web['tempcache'].='_show';
	}else{
		$web['tempfile']='special_list.'.setup_temptype;
		$web['tempcache'].='_list';
		}
	if($special['bcat']){
		$bcat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$special['bcat']);
		if($bcat){
			qyk::addpos($bcat['title'],'log='.$tcz['log'].'&bcat='.$bcat['classid']);
			$tcz['bcat']=$bcat['classid'];
			if($bcat['description']!='')$web['description']=$bcat['description'];
			if($bcat['keyword']!='')$web['keyword']=$bcat['keyword'];
			}
		}
	if($special['scat']){
		$scat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$special['scat']);
		if($scat){
			qyk::addpos($scat['title'],'log='.$tcz['log'].'&scat='.$scat['classid']);
			$tcz['scat']=$scat['classid'];
			if($scat['description']!='')$web['description']=$scat['description'];
			if($scat['keyword']!='')$web['keyword']=$scat['keyword'];
			}
		}
	if($special['lcat']){
		$lcat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$special['lcat']);
		if($lcat){
			qyk::addpos($lcat['title'],'log='.$tcz['log'].'&lcat='.$lcat['classid']);
			$tcz['lcat']=$lcat['classid'];
			if($lcat['description']!='')$web['description']=$lcat['description'];
			if($lcat['keyword']!='')$web['keyword']=$lcat['keyword'];
			}
		}
	$page_webtitle=$special['title'];
	if(isset($special['content'])){
		if(strstr($special['content'],setup_pagetag)){
			$contarr=explode(setup_pagetag,$special['content']);
			$allpage=count($contarr);
			if($tcz['page']>$allpage)tipmsg(getlang('sys','artpage_err'),true);
			else if($tcz['page']>1)$page_webtitle.=sprintf(getlang('sys','artpage'),$tcz['page']);
			$special['content']=$contarr[$tcz['page']-1];
			$page=new pagelist();
			$web['list_page']=$page->show($allpage,1,$link='',goif($web['mobiletemp'],'mobile','mini'),false);
		}else if($tcz['page']>100){
			tipmsg(getlang('sys','artpage_err'),true);
			}
		}
	if($special['keyword']!=''){
		qyk::addpos($page_webtitle,'log='.$tcz['log'].'&id='.$special['dataid'].goif($tcz['page']>1,'&page='.$tcz['page']),false,false);
		$web['keyword']=$special['keyword'];
	}else{
		qyk::addpos($page_webtitle,'log='.$tcz['log'].'&id='.$special['dataid'].goif($tcz['page']>1,'&page='.$tcz['page']),false,true);
		}
}else{
	if($tcz['lcat']){
		$lcat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$tcz['lcat']);
		if(!$lcat)tipmsg('不存在或未启用的分类：lcat='.$tcz['lcat'],true);
		$tcz['scat']=$lcat['scat'];
		$tcz['bcat']=$lcat['bcat'];
		}
	if($tcz['scat']){
		$scat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$tcz['scat']);
		if(!$scat)tipmsg('不存在或未启用的分类：scat='.$tcz['scat'],true);
		$tcz['bcat']=$scat['bcat'];
		}
	if($tcz['bcat']){
		$bcat=db_getshow('module','*','webid='.$web['id'].' and isok=0 and classid='.$tcz['bcat']);
		if(!$bcat)tipmsg('不存在或未启用的分类：bcat='.$tcz['bcat'],true);
		qyk::addpos($bcat['title'],'log='.$tcz['log'].'&bcat='.$bcat['classid']);
		$web['list_size']=$bcat['pagesize'];
		if($bcat['description']!='')$web['description']=$bcat['description'];
		if($bcat['keyword']!='')$web['keyword']=$bcat['keyword'];
		}
	if($tcz['scat']){
		qyk::addpos($scat['title'],'log='.$tcz['log'].'&scat='.$scat['classid']);
		$web['list_size']=$scat['pagesize'];
		if($scat['description']!='')$web['description']=$scat['description'];
		if($scat['keyword']!='')$web['keyword']=$scat['keyword'];
		}
	if($tcz['lcat']){
		qyk::addpos($lcat['title'],'log='.$tcz['log'].'&lcat='.$lcat['classid']);
		$web['list_size']=$lcat['pagesize'];
		if($lcat['description']!='')$web['description']=$lcat['description'];
		if($lcat['keyword']!='')$web['keyword']=$lcat['keyword'];
		}
	}
?>