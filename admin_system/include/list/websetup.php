<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'websetup\'})"></div>
<div class="btnright"><input type="button" value="新建站点" class="btn1" onclick="openshow({log:\'websetup\'})"><input type="button" value="删除站点" class="btn1" onclick="deldata({log:\'websetup\'})"></div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:80px">ID</td>
<td>站点名称</td>
<td style="width:200px">主要域名</td>
<td style="width:80px">可绑域名</td>
<td style="width:120px">空间大小</td>
<td style="width:160px">空间占用</td>
<td style="width:100px">创建日期</td>
<td style="width:60px">状态</td>
</tr>';
$sql='select * from '.tabname('websetup').goif($keyword!='',' where title like "%'.$keyword.'%"').' order by time_add desc';
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$kjbfb_text='';
	if($val['capacity_max']){
		$kjbfb=sprintf('%.2f',$val['capacity_have']/$val['capacity_max']*100);
		if($kjbfb>80)$kjbfb_text='<span class="list_red">'.$kjbfb.' %</span>';
		else $kjbfb_text=$kjbfb.' %';
		}
	$mydomain=db_getone('website','setup_weburl','webid='.$val['webid'].' order by isdef desc,isadmin desc,id desc');
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'websetup\',id:'.$val['id'].'})">
<td class="cen">'.$val['webid'].'</td>
<td><span class="list_green">'.$title.'</span></td>
<td><span class="list_green">'.$mydomain.'</span></td>
<td>'.$val['domainmax'].' 个</td>
<td>'.goif($val['capacity_max']>0,sprintf('%.2f',$val['capacity_max']/1024).' MB','未设置').'</td>
<td>'.sprintf('%.2f',$val['capacity_have']/1024).'MB'.goif($kjbfb_text!='','（'.$kjbfb_text.'）').'</td>
<td class="cen">'.date('Y-m-d',$val['time_add']).'</td>
<td class="cen">'.goif($val['status'],'<span class="red">关闭</span>','正常').'</td>
</tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);