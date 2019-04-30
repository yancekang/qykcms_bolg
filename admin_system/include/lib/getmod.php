<?php
$menutype=arg('menutype','post','int');
$languages=arg('lang','post','url');
if($languages=='')$languages=setup_language_def;
if($menutype==1){
	$menutype=0;
	$list=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and languages="'.$languages.'" and (modtype<8 or modtype>10) and (menutype=0 or menutype=999) order by menutype asc,bcat asc,sort asc,classid asc');
}else{
	$menutype-=1;
	$list=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and languages="'.$languages.'" and (modtype<8 or modtype>10) and menutype='.$menutype.' order by bcat asc,sort asc,classid asc');
	}
$res='['.$menutype;
foreach($list as $k=>$v){
	$title=$v['title'];
	if($v['menutype']>0&&$v['menutype']<999){
		$title_s=db_getone('module','title','webid='.$website['webid'].' and classid='.$v['bcat']);
		$title=$title_s.' - '.$title;
		}
	$res.=',{classid:'.$v['classid'].',modtype:'.$v['modtype'].',pagesize:'.$v['pagesize'].',"title":"'.$title.'"}';
	}
$res.=']';
ajaxreturn(0,$res);