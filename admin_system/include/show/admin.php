<?php
$admin=db_getshow('admin','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$admin){
	$admin=array(
		'user_admin'=>'',
		'user_pass'=>'',
		'user_loginkey'=>'',
		'user_addkey'=>'',
		'user_email'=>'',
		'user_phone'=>'',
		'user_head'=>'',
		'login_ip'=>'',
		'login_num'=>0,
		'time_add'=>time(),
		'time_login'=>time(),
		'config_group'=>0,
		'config_type'=>0
		);
	}
$group=db_getlist('select * from '.tabname('admin_group').' where webid='.$website['webid'].' order by id asc');
$group_select='<select id="config_group">';
foreach($group as $val){
	$group_select.='<option value="'.$val['groupid'].'"'.goif($admin['config_group']==$val['groupid'],' selected').'>'.$val['group_name'].'</option>';
	}
$group_select.='</select>';
$res='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'admin_show\',1,\'win_show_admin\');return false" hidefocus="true">基本信息</a><a href="javascript:" class="out" onclick="ajaxcata(this,\'admin_show\',2,\'win_show_admin\');return false" hidefocus="true"'.goif($tcz['id']==0,' style="display:none"').'>帐号统计</a>
</div>
<table id="admin_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">登录帐号</td><td class="td2"><input type="text" id="user_admin" value="'.$admin['user_admin'].'"'.goif($tcz['id'],' class="inp_no" readonly',' class="inp"').'></td><td class="td1">帐号状态</td><td class="td2"><select id="config_type"><option value="1">正常</option><option value="2"'.goif($admin['config_type']==2,' selected').'>冻结</option></select></td></tr>
<tr><td class="td1">登录密码</td><td class="td2"><input type="password" class="inp" id="user_pass"></td><td class="td1">确认密码</td><td class="td2"><input type="password" class="inp" id="user_pass2"></td></tr>
<tr><td class="td1">电子邮箱</td><td class="td2"><input type="text" class="inp" id="user_email" value="'.$admin['user_email'].'"></td><td class="td1">手机号码</td><td class="td2"><input type="text" class="inp" id="user_phone" value="'.$admin['user_phone'].'"></td></tr>
<tr><td class="td1">管理组</td><td class="td2">'.$group_select.'</td><td class="td1">上传头像</td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'admin_head\'})" class="inp_up" id="user_head">'.$admin['user_head'].'</textarea></td></tr>
</table>
<table id="admin_show_2" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">创建时间</td><td class="td2">'.goif($admin['time_add'],date('Y-m-d H:i:s',$admin['time_add']),'--').'</td><td class="td1">登录次数</td><td class="td2">'.$admin['login_num'].' 次</td></tr>
<tr><td class="td1">最近登录时间</td><td class="td2">'.goif($admin['time_login'],date('Y-m-d H:i:s',$admin['time_login']),'--').'</td><td class="td1">登录IP</td><td class="td2">'.$admin['login_ip'].'</td></tr>
</table></div>';