<?php
$keyword=arg('keyword','post','url');
$star=arg('star','post','int');
$scat=arg('scat','post','int');
$lcat=arg('lcat','post','int');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'special\'})"><input class="btn" type="button" value="高 级" onclick="search_more({log:\'special\',lang:\''.$tcz['desc'].'\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'special_edit'),'<input type="button" value="新建专题" class="btn1" onclick="openshow({log:\'special\',lang:\''.$tcz['desc'].'\'})">').goif(ispower($admin_group,'special_del'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'special\'})">').'</div>';
$res.='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:50px">排序</td>
<td>标题/名称</td>
<td style="width:150px">栏目分类</td>
<td style="width:150px">创建时间</td>
<td style="width:50px">状态</td>
<td style="width:50px">评论</td>
<td style="width:50px">封面</td>
<td style="width:50px">星标</td>
<td style="width:50px">电脑</td>
<td style="width:50px">手机</td>
</tr>';
$sql='select * from '.tabname('special').' where webid='.$website['webid'].goif($tcz['bcat'],' and bcat='.$tcz['bcat']).goif($scat,' and scat='.$scat).goif($lcat,' and lcat='.$lcat).goif($keyword!='',' and LOCATE("'.$keyword.'",`title`)>0').goif($star,goif($star==9,' and star>0',' and star='.$star));
$sql.=' order by sort desc,time_add desc,dataid desc';
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$cata='-';
	if($val['scat']){
		$smod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$val['scat']);
		if($smod)$cata=$smod['title'];
		if($val['lcat']){
			$lmod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$val['lcat']);
			if($lmod)$cata.=' - '.$lmod['title'];
			}
		}
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'special\',id:'.$val['id'].'})">
<td class="cen">'.$val['dataid'].'</td>
<td class="cen">'.$val['sort'].'</td>
<td>'.$title.'</td>
<td><span class="list_green">'.$cata.'</span></td>
<td class="cen">'.date('Y-m-d H:i:s',$val['time_add']).'</td>
<td class="cen">'.goif($val['isok']==0,'<span class="list_gray">正常</span>','<span class="list_red">隐藏</span>').'</td>
<td class="cen">'.goif($val['comment'],'<span class="list_blue">'.$val['comment'].'</span>','<span class="list_gray">-</span>').'</td>
<td class="cen">'.goif($val['cover']=='','<span class="list_gray">-</span>','<span class="list_blue">有</span>').'</td>
<td class="cen">'.goif($val['star'],'<span class="list_red">'.getchinesenum($val['star']).'星</span>','<span class="list_gray">-</span>').'</td>
<td class="cen">'.goif($val['computer'],'<span class="list_red">╳</span>','<span class="list_gray">√</span>').'</td>
<td class="cen">'.goif($val['mobile'],'<span class="list_red">╳</span>','<span class="list_gray">√</span>').'</td>
</tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);