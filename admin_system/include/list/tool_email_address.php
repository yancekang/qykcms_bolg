<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'tool_email_address\'})"></div>
<div class="btnright"><input type="button" value="添加邮箱" class="btn1" onclick="openshow({log:\'tool_email_address\'})"><input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'tool_email_address\'})"></div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:200px">称呼</td>
<td>邮箱地址</td>
<td style="width:120px">发信方式</td>
<td style="width:120px">状态</td>
</tr>';
$sql='select * from '.tabname('tool_email_address').' where webid='.$website['webid'].goif($keyword!='',' and (name like "%'.$keyword.'%" or email like "%'.$keyword.'%")').' order by addressid desc,id desc';
//echo $sql;
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$name=$val['name'];
	$email=$val['email'];
	if($keyword!=''){
		$name=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$name);
		$email=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$email);
		}
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'tool_email_address\',id:'.$val['id'].'})">
<td class="cen">'.$val['addressid'].'</td>
<td><span class="list_green">'.goif($name=='','--',$name).'</span></td>
<td><span class="list_green">'.$email.'</span></td>
<td class="cen">SMTP</td>
<td class="cen">'.goif($val['isok']==1,'启用','<span class="list_red">未启用</span>').'</td>
</tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);