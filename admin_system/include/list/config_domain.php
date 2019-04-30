<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'config_domain\'})"></div>
<div class="btnright"><input type="button" value="新增域名" class="btn1" onclick="openshow({log:\'config_domain\'})"><input type="button" value="删除域名" class="btn1" onclick="deldata({log:\'config_domain\'})"></div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:100px">webID</td>
<td>域名</td>
<td style="width:200px">备案号</td>
<td style="width:80px">主域名</td>
</tr>';
$sql='select * from '.tabname('website').' where webid='.$website['webid'].goif($keyword!='',' and setup_weburl like "%'.$keyword.'%"').' order by isdef desc,id desc';
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$title=$val['setup_weburl'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'config_domain\',id:'.$val['id'].'})">
<td class="cen">'.$val['id'].'</td>
<td class="cen">'.$val['webid'].'</td>
<td><span class="list_green">'.$title.'</span></td>
<td><span class="list_green">'.$val['setup_record'].'</span></td>
<td class="cen">'.goif($val['isdef'],'<span class="blue">是</span>','<span class="gray">否</span>').'</td>
</tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);