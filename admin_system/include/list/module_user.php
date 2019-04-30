<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'module_user\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'module_user'),'<input type="button" value="添加模块" class="btn1" onclick="openshow({log:\'module_user\'})">').goif(ispower($admin_group,'module_user'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'module_user\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td>模块名称</td>
<td style="width:120px">字段统计</td>
<td style="width:120px">文章记录</td>
<td style="width:120px">使用状态</td>
<td style="width:80px">启用</td>
</tr>';
$sql='select * from '.tabname('module_user').' where webid='.$website['webid'].goif($keyword!='',' and title like "%'.$keyword.'%"').' order by dataid desc,id desc';
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$modnum=db_count('module','webid='.$website['webid'].' and modtype='.$val['dataid'])+0;
	$fienum=db_count('module_field','webid='.$website['webid'].' and modid='.$val['dataid'])+0;
	$tname='article_'.$website['webid'].'_'.$val['dataid'];
	
	$artnum=db_count($tname)+0;
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'module_user\',id:'.$val['id'].'})">
<td class="cen">'.$val['dataid'].'</td>
<td><span class="list_green">'.$title.'</span></td>
<td class="cen">'.goif(!$fienum,'<span class="list_gray">尚未设置</span>','<span class="list_blue">'.$fienum.' 个</span>').'</td>
<td class="cen">'.goif(!$artnum,'<span class="list_gray">暂无记录</span>','<span class="list_blue">'.$artnum.' 条</span>').'</td>
<td class="cen">'.goif(!$modnum,'<span class="list_red">未使用</span>','<span class="list_blue">'.$modnum.' 个</span>').'</td>
<td class="cen">'.goif(!$val['isok'],'正常','<span class="list_red">未启用</span>').'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);