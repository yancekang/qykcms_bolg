<?php
$res='';
$rescata='';
$catalist=explode(',',setup_am_setup_cata);
$cataon=arg('other','post','url');
foreach($catalist as $sort=>$cv){
	$iv=explode('|',$cv);
	if($cataon=='')$cataon=$iv[0];
	$rescata.='<a'.goif($cataon=='theme'&&$cataon!=$iv[0]||$iv[0]=='theme'&&$cataon!='theme',' style="display:none"').' href="javascript:" class="'.goif($cataon!=$iv[0],'out','on').'" onclick="ajaxcata(this,\'config_show\','.($sort+1).',\'win_show_config_admin\');return false" hidefocus="true">'.$iv[1].'</a>';
	$conf=db_getlist('select * from '.tabname('config').' where webid='.$website['webid'].' and cata="'.$iv[0].'" and isview=0 order by contype asc,sort asc,id asc');
	if($conf)$res2=getargsform($conf);
	else $res2='<tr><td class="td7_2"><div class="ui_none">当前栏目暂无可设置参数</div></td></tr>';
	$res.='<table'.goif($cataon!=$iv[0],' style="display:none"').' id="config_show_'.($sort+1).'" class="ajax_tablist" cellpadding="12" cellspacing="1">'.$res2.'</table>';
	}
$res='<div class="win_ajax ajax_user"><div class="ajax_cata">'.$rescata.'</div>'.$res.'</div>';