<?php
$words=arg('words','post','url');
$myval=arg('myval','post','url');
$other='';
$cont='|';
switch($tcz['desc']){
	case 'special':
		$myval=preg_replace('/([^0-9\,]+)/','',$myval);
		$myval=trim($myval,',');
		if($myval!=''){
			$mylist=db_getall('special','*','webid='.$website['webid'].' and isok=0 and dataid in('.$myval.')');
			foreach($mylist as $l){
				$other.='<a dataid="'.$l['dataid'].'" val="'.$l['dataid'].'" href="javascript:">◆ '.$l['title'].'</a>';
				}
			}
		$size=30;
		$start=$tcz['page']*$size-$size;
		$sql='webid='.$website['webid'].' and isok=0'.goif($words!='',' and LOCATE("'.$words.'",`title`)>0').' order by time_add desc,id desc';
		$allnum=db_count('special',$sql);
		$allpage=ceil($allnum/$size);
		if($tcz['page']>1)$cont.='first|';
		if($tcz['page']<$allpage)$cont.='last|';
		$sql.=' limit '.$start.','.$size;
		$list=db_getall('special','*',$sql);
		foreach($list as $val){
			$title=$val['title'];
			if($words!='')$title=str_replace($words,'<span class="red">'.$words.'</span>',$title);
			$res.='<a dataid="'.$val['dataid'].'" href="javascript:" class="choose1" val="'.$val['dataid'].'" title="'.$val['title'].'">'.$title.'</a>';
			}
	break;
	}
if($res=='')$res='<p align="center"><br><br>未找到相关记录~</p>';
ajaxreturn(0,$res,$other,$cont);