<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'advert\',lang:\''.$tcz['desc'].'\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'advert_edit'),'<input type="button" value="添加广告" class="btn1" onclick="openshow({log:\'advert\',lang:\''.$tcz['desc'].'\'})">').goif(ispower($admin_group,'advert_del'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'advert\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:60px">语言</td>
<td style="width:60px">排序</td>
<td>标题名称</td>
<td style="width:100px">分组</td>
<td style="width:180px">附加参数</td>
<td style="width:80px">状态</td>
</tr>';
$sql='select * from '.tabname('advert').' where webid='.$website['webid'].goif($tcz['desc']!='',' and languages="'.$tcz['desc'].'"').goif($keyword!='',' and title like "%'.$keyword.'%"').' order by adtype asc,status asc,sort asc,id asc';
//echo $sql;
$list=db_getpage($sql,setup_am_page,$tcz['page']);
$time_now=time();
foreach($list['list'] as $val){
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'advert\',id:'.$val['id'].'})">
<td class="cen">'.$val['id'].'</td>
<td class="cen">'.$val['languages'].'</td>
<td class="cen">'.$val['sort'].'</td>
<td><span class="list_green">'.$title.'</span></td>
<td class="cen">第 '.$val['adtype'].' 组</td>
<td>'.$val['other'].'</td>
<td class="cen">'.goif($val['status']==1,'正常','<span class="list_red">隐藏</span>').'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);