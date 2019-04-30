<?php
$data=db_getshow('tool_email','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'title'=>'',
		'emailto'=>'',
		'addressid'=>0,
		'content'=>'',
		'sendnum'=>30,
		'sendtype'=>1,
		'seconds'=>5,
		'randnum'=>1
		);
	}
$addresslist=db_getlist('select id,addressid,email from '.tabname('tool_email_address').' where webid='.$website['webid'].' and isok=1 order by id desc');
if(!$addresslist&&!$data['id'])ajaxreturn(1,'请先设置发件邮箱再进行群发任务');
$addressopt='';
foreach($addresslist as $val){
	$addressopt.='<option value="'.$val['addressid'].'"'.goif($val['addressid']==$data['addressid'],' selected').'>'.$val['email'].'</option>';
	}
$res='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'tool_email_show\',1,\'win_show_tool_email\');return false" hidefocus="true">发送设置</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'tool_email_show\',2,\'win_show_tool_email\');return false" hidefocus="true">邮件内容</a>
</div>
<table id="tool_email_show_1" style="width:100%" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">邮件标题</td><td class="td2"><input type="text" class="inp" id="post_title" value="'.$data['title'].'"></td><td class="td1">发件邮箱</td><td class="td2"><select id="post_addressid"><option value="0">自动随机</option>'.$addressopt.'</select></td></tr>
<tr><td class="td1">发送模式</td><td class="td2"><select id="post_sendtype"><option value="2">组发，每封邮件多个收件人</option><option value="1"'.goif($data['sendtype']==1,' selected').'>单发，每封邮件一个收件人</option></select></td><td class="td1">群发速度</td><td class="td2"><select id="post_seconds"><option value="5">最快速度</option><option value="9999"'.goif($data['seconds']==9999,' selected').'>随机（5~30秒）</option><option value="9998"'.goif($data['seconds']==9998,' selected').'>随机（1~5分钟）</option><option value="10"'.goif($data['seconds']==10,' selected').'>间隔10秒</option><option value="30"'.goif($data['seconds']==30,' selected').'>间隔30秒</option><option value="60"'.goif($data['seconds']==60,' selected').'>间隔1分钟</option><option value="300"'.goif($data['seconds']==300,' selected').'>间隔5分钟</option></select></td></tr>
<tr><td class="td1">每组收件人数</td><td class="td2"><input type="text" id="post_sendnum" value="'.$data['sendnum'].'"'.goif($data['sendtype']==1,' class="inp_no" readonly',' class="inp"').'></td><td class="td1">随机码</td><td class="td2"><select id="post_randnum"><option value="0">不插入（非广告邮件建议选择）</option><option value="1"'.goif($data['randnum']==1,' selected').'>插入（1~3个码，较少）</option><option value="2"'.goif($data['randnum']==2,' selected').'>插入（3~8个码，推荐）</option><option value="3"'.goif($data['randnum']==3,' selected').'>插入（5~15个码，较多）</option></select></td></tr>
<tr><td class="td0" colspan=4>已录入 <span class="red" id="post_emailto_num">0</span> 个接收邮箱（多个邮箱用逗号分隔，例：<span class="green">888@qq.com,abc@163.com</span>）：</td></tr>
<tr><td class="td7_2" colspan=4 style="width:100%;text-align:center"><textarea style="width:852px;height:241px" class="tex_no" id="post_emailto">'.$data['emailto'].'</textarea></td></tr>
</table>
<div id="tool_email_show_2" class="ajax_content" style="display:none"><script id="post_content" type="text/plain" style="display:none">'.getreset_admin($data['content']).'</script></div>
</div>';