<?php
$data=db_getshow('tool_email_address','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'name'=>'',
		'email'=>'',
		'code'=>'',
		'pass'=>'',
		'content'=>'',
		'sendtype'=>1,
		'emailtype'=>1,
		'server'=>'smtp.qq.com',
		'port'=>25,
		'isok'=>1
		);
	}
$res='<div class="win_ajax ajax_user">
<table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">邮箱地址</td><td class="td2"><input type="text" class="inp" id="post_email" value="'.$data['email'].'"></td><td class="td1">称呼</td><td class="td2"><input type="text" class="inp" id="post_name" value="'.$data['name'].'"></td></tr>
<tr><td class="td1">登录账号</td><td class="td2"><input type="text" class="inp" id="post_code" value="'.$data['code'].'"></td><td class="td1">登录密码</td><td class="td2"><input type="text" class="inp" id="post_pass" value="'.$data['pass'].'"></td></tr>
<tr><td class="td1">发信方式</td><td class="td2"><select id="post_sendtype"><option value="1">smtp（推荐）</option></select></td><td class="td1">邮箱类型</td><td class="td2"><select id="post_emailtype"><option value="1"'.goif($data['emailtype']==1,' selected').'>QQ邮箱</option><option value="2"'.goif($data['emailtype']==2,' selected').'>网易163邮箱</option><option value="3"'.goif($data['emailtype']==3,' selected').'>谷歌Gmail</option><option value="4"'.goif($data['emailtype']==4,' selected').'>新浪邮箱</option><option value="5"'.goif($data['emailtype']==5,' selected').'>126邮箱</option><option value="0">手动设置</option></select></td></tr>
<tr><td class="td1">服务器</td><td class="td2"><input type="text" class="inp" id="post_server" value="'.$data['server'].'"></td><td class="td1">端口</td><td class="td2"><input type="text" class="inp" id="post_port" value="'.$data['port'].'"></td></tr>
<tr><td class="td1">备注信息</td><td class="td2"><input type="text" class="inp" id="post_content" value="'.$data['content'].'"></td><td class="td1">状态</td><td class="td2"><select id="post_isok"><option value="1"'.goif($data['isok']==1,' selected').'>启用</option><option value="2"'.goif($data['isok']==2,' selected').'>不启用</option></select></td></tr>
</table>
</div>';