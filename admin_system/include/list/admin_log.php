<?php
$excel=arg('excel','all','txt');
$keyword=arg('keyword','post','txt');
$admincata=arg('admincata','post','int');
$start=arg('start','post','txt');
$end=arg('end','post','txt');
$btn='<div class="btnsear"><span class="txt">管理员：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'admin_log\'})"><input class="btn" type="button" value="高 级" onclick="search_more({log:\'admin_log\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'sys_adminlog'),'<input type="button" value="导出数据" class="btn1" onclick="downexcel({log:\'admin_log\'})">').goif(ispower($admin_group,'super'),'<input type="button" value="清理日志" class="btn1" onclick="userpost({log:\'admin_log\'})">').'</div>';
$res.='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:100px">事件类型</td>
<td style="width:100px">管理员</td>
<td style="width:120px">操作IP</td>
<td>操作描述</td>
<td style="width:150px">操作时间</td>
</tr>';
$sql='select * from '.tabname('admin_log').' where webid='.$website['webid'].goif($admincata,' and oper_type='.$admincata).goif($keyword!='',' and user_admin="'.$keyword.'"');
if($start!=''){
	$start=strtotime($start.' 00:00:00');
	$sql.=' and time_add>='.$start;
	}
if($end!=''){
	$end=strtotime($end.' 23:59:59');
	$sql.=' and time_add<='.$end;
	}
$sql.=' order by time_add desc,id desc';
if($excel=='ok')$list=array('list'=>db_getlist($sql));
else $list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')"'.goif($excel!='ok',' ondblclick="openshow({log:\'admin_log\',id:'.$val['id'].'})"').'>
<td class="cen">'.$val['id'].'</td>
<td class="cen">'.getadmincata($val['oper_type']).'</td>
<td><span class="list_green">'.$val['user_admin'].'</span></td>
<td>'.$val['user_ip'].'</td>
<td>'.$val['content'].'</td>
<td class="cen">'.date('Y-m-d H:i:s',$val['time_add']).'</td></tr>';
	}
$res.='</table>';
if($excel=='ok')excelreturn($res,'admin_log');
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);