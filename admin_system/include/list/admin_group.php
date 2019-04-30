<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键词：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'admin_group\'})"></div>
<div class="btnright"><input type="button" value="添加分组" class="btn1" onclick="openshow({log:\'admin_group\'})"><input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'admin_group\'})"></div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td>分组名称</td>
<td style="width:180px">管理权限</td>
</tr>';
$sql='select * from '.tabname('admin_group').' where webid='.$website['webid'];
$sql.=goif($keyword!='',' and group_name like "%'.$keyword.'%"');
$sql.=' order by config_super desc,id asc';
//echo $sql;
$list=db_getpage($sql,setup_am_page,$tcz['page']);
$time_now=time();
foreach($list['list'] as $val){
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'admin_group\',id:'.$val['id'].'})">
<td class="cen">'.$val['groupid'].'</td>
<td><span class="list_green">'.str_replace($keyword,'<span class="list_red">'.$keyword.'</span>',$val['group_name']).'</span></td>
<td class="cen">'.goif($val['config_super']==1,'<span class="list_blue">超级权限</span>','分配权限').'</td>
</tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);