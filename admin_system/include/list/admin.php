<?php
$keyword=arg('keyword','post','txt');
$btn='<div class="btnsear"><span class="txt">管理员：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><input class="btn" type="button" value="搜 索" onclick="search({log:\'admin\'})"></div>
<div class="btnright"><input type="button" value="创建帐号" class="btn1" onclick="openshow({log:\'admin\'})"><input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'admin\'})"></div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td style="width:60px">状态</td>
<td>帐号</td>
<td style="width:110px">管理组</td>
<td style="width:110px">手机号码</td>
<td style="width:160px">邮箱地址</td>
<td style="width:80px">登录数</td>
<td style="width:150px">最近登录</td>
<td style="width:60px">头像</td>
</tr>';
$sql='select * from '.tabname('admin').' where webid='.$website['webid'].goif($keyword!='',' and user_admin="'.$keyword.'"').' order by id desc';
//echo $sql;
$list=db_getpage($sql,setup_am_page,$tcz['page']);
$time_now=time();
foreach($list['list'] as $val){
	$group=db_getshow('admin_group','*','webid='.$website['webid'].' and groupid='.$val['config_group']);
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'admin\',id:'.$val['id'].'})">
<td class="cen">'.$val['id'].'</td>
<td class="cen">'.goif($val['config_type']==1,'正常','<span class="list_red">冻结</span>').'</td>
<td><span class="list_green">'.$val['user_admin'].'</span></td>
<td>'.goif($group['config_super']==0,'<span class="list_blue">'.$group['group_name'].'</span>',$group['group_name']).'</td>
<td>'.goif($val['user_phone']!='',$val['user_phone'],'--').'</td>
<td>'.goif($val['user_email']!='',$val['user_email'],'--').'</td>
<td class="cen">'.$val['login_num'].'</td>
<td class="cen">'.date('Y-m-d H:i:s',$val['time_login']).'</td>
<td class="cen">'.goif($val['user_head']=='','<span class="list_red">无</span>','有').'</td></tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);