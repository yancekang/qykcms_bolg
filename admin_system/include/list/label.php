<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'label\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'skin_label'),'<input type="button" value="添加标签" class="btn1" onclick="openshow({log:\'label\'})">').goif(ispower($admin_group,'skin_label'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'label\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:60px">排序</td>
<td>标签名称</td>
<td style="width:150px">调用标签</td>
<td style="width:150px">内容长度</td>
<td style="width:150px">创建时间</td>
</tr>';
$sql='select * from '.tabname('label').' where webid='.$website['webid'].goif($keyword!='',' and title like "%'.$keyword.'%"').' order by sort desc,dataid desc,time_add desc';
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'label\',id:'.$val['id'].'})">
<td class="cen">'.$val['dataid'].'</td>
<td class="cen">'.goif($val['sort'],$val['sort'],'<span class="list_red">禁用</span>').'</td>
<td><span class="list_green">'.$title.'</span></td>
<td>'.setup_prefix.'label='.$val['dataid'].setup_suffix.'</td>
<td>'.sprintf('%.2f',strlen($val['content'])/1024).' KB</td>
<td class="cen">'.date('Y-m-d H:i:s',$val['time_add']).'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);