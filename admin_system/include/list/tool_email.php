<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'tool_email\'})"></div>
<div class="btnright"><input type="button" value="新建任务" class="btn1" onclick="openshow({log:\'tool_email\'})"><input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'tool_email\'})"></div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td>邮件标题</td>
<td style="width:200px">发件邮箱</td>
<td style="width:100px">接收邮箱</td>
<td style="width:80px">发送模式</td>
<td style="width:120px">发送统计</td>
<td style="width:150px">发送时间</td>
</tr>';
$sql='select * from '.tabname('tool_email').' where webid='.$website['webid'].goif($keyword!='',' and title like "%'.$keyword.'%"').' order by id desc,time_add desc';
//echo $sql;
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$addressid_text='自动随机';
	if($val['addressid'])$addressid_text=db_getone('tool_email_address','email','webid='.$website['webid'].' and addressid='.$val['addressid']);
	$emailto_text=count(explode(',',$val['emailto'])).'个';
	$title=$val['title'];
	if($keyword!='')$title=str_replace($keyword,'<span class="red">'.$keyword.'</span>',$title);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'tool_email\',id:'.$val['id'].'})">
<td class="cen">'.$val['id'].'</td>
<td><span class="list_green">'.$title.'</span></td>
<td>'.$addressid_text.'</td>
<td>'.$emailto_text.'</td>
<td class="cen">'.goif($val['sendtype']==1,'单发','组发').'</td>
<td>'.goif($val['results'],'已发送'.$val['results'].'次','<span class="list_orange">从未发送</span>').'</td>
<td class="cen">'.goif($val['time_send'],date('Y-m-d H:i:s',$val['time_send']),'--').'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);