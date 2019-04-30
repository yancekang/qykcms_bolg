<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'option\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'skin_option'),'<input type="button" value="添加选项" class="btn1" onclick="openshow({log:\'option\'})">').goif(ispower($admin_group,'skin_option'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'option\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:150px">类型标识</td>
<td>选项标题</td>
<td style="width:60px">排序</td>
</tr>';
$sql='select * from '.tabname('select').' where webid='.$website['webid'].goif($keyword!='',' and title like "%'.$keyword.'%" or types="'.$keyword.'" order by types asc,sort desc,id desc',' order by sort desc,id desc');
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'option\',id:'.$val['id'].'})">
<td class="cen">'.$val['id'].'</td>
<td><span class="list_orange">'.$val['types'].'</span></td>
<td><span class="list_green">'.$title.'</span></td>
<td class="cen">'.$val['sort'].'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
ajaxreturn(0,$res,$btn,$list['page']);