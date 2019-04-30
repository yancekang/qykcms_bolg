<?php
$keyword=arg('keyword','post','txt');
if($tcz['desc']==''){
	$tcz['desc']=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language_def"');
	}
$btn='<div class="btnsear"><span class="txt">称呼：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'customer\',lang:\''.$tcz['desc'].'\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'customer_edit'),'<input type="button" value="添加客服" class="btn1" onclick="openshow({log:\'customer\',lang:\''.$tcz['desc'].'\'})">').goif(ispower($admin_group,'customer_del'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'customer\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:60px">语言</td>
<td style="width:60px">排序</td>
<td>姓名称呼</td>
<td style="width:120px">分组</td>
<td style="width:150px">软件帐号</td>
<td style="width:100px">职务头衔</td>
<td style="width:80px">头像</td>
<td style="width:50px">显示</td>
</tr>';
$bcatarr=explode(',',setup_customer_bcat);
$sql='select * from '.tabname('customer').' where webid='.$website['webid'].goif($tcz['desc']!='',' and languages="'.$tcz['desc'].'"').goif($keyword!='',' and name like "%'.$keyword.'%"').' order by bcat asc,sort asc,id asc';
//echo $sql;
$list=db_getpage($sql,setup_am_page,$tcz['page']);
$time_now=time();
foreach($list['list'] as $val){
	$name=$val['name'];
	if($keyword!='')$name=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$name);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'customer\',id:'.$val['id'].'})">
<td class="cen">'.$val['id'].'</td>
<td class="cen">'.$val['languages'].'</td>
<td class="cen">'.$val['sort'].'</td>
<td><span class="list_green">'.$name.'</span></td>
<td>'.$bcatarr[$val['bcat']].'</td>
<td>'.$val['qqnum'].'</td>
<td>'.$val['pos'].'</td>
<td class="cen">'.goif($val['head']=='','<span class="list_red">未上传</span>','已上传').'</td>
<td class="cen">'.goif($val['isok'],'<span class="list_red">否</span>','<span class="list_gray">是</span>').'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);