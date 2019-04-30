<?php
$data=db_getshow('websetup','*','id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'webid'=>0,
		'title'=>'',
		'domainmax'=>2,
		'capacity_max'=>100,
		'capacity_have'=>0,
		'status'=>0,
		'time_add'=>time(),
		'domainlen'=>0,
		'domain'=>''
		);
	}else{
		$data['capacity_max']=ceil($data['capacity_max']/1024);
		$data['domain']=db_getone('website','setup_weburl','webid='.$data['webid'].' order by isdef desc,isadmin desc,id desc');;
		}
$res='<div class="win_ajax ajax_edit">
<table id="websetup_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1"><span class="help" title="这里的站点名称仅作内部标记用，前台网站title进入该站后台可自由设定">站点名称</span></td><td class="td6_2"><input type="text" class="inp" id="post_title" value="'.$data['title'].'"></td></tr>
<tr><td class="td6_1">状 态</td><td class="td6_2"><select id="post_status" tag="postinp"><option value="0">正常</option><option value="1"'.goif($data['status']==1,' selected').'>关闭</option></select></td></tr>
<tr><td class="td6_1"><span class="help" title="单位:MB，空间到达上限后将无法再上传文件">空间上限</a></td><td class="td6_2"><input type="text" class="inp" id="post_capacity_max" value="'.$data['capacity_max'].'"></td></tr>
<tr><td class="td6_1"><span class="help" title="设定站点可绑定的域名数量">可绑域名数量</a></td><td class="td6_2"><input type="text" id="post_domainmax" value="'.$data['domainmax'].'" class="inp"></td></tr>
<tr><td class="td6_1"><span class="help" title="该网站最主要的域名，通常以 www 开头，站点创建后，登录该站点后台可自行绑定域名或设置主域名，除主域名外的其它域名为次域名，访问次域名会自动跳转至主域名，使用次域名不可登录后台<br><span class=\'blue\'>示例：设置 www.xxx.com 为主域名，再绑定一个次域名 xxx.com，那么访问 xxx.com 将会跳转到 www.xxx.com</span>">主域名</span></td><td class="td6_2"><input type="text" id="post_domain" value="'.$data['domain'].'" '.goif($data['id'],'class="inp_no" readonly','class="inp"').'></td></tr>
<tr><td class="td6_1">创建时间</td><td class="td6_2"><input tag="time" type="text" class="inp" id="post_time_add" value="'.date('Y-m-d H:i:s',$data['time_add']).'"></td></tr>
'.goif($data['id'],'<tr><td class="td6_1"><span class="help" title="勾选此项则系统会重置 admin 的密码，如果账号不存在会自动创建">重置密码</span></td><td class="td6_2"><input class="inp_box" type="checkbox" id="post_pass" value="ok" text="重置“admin”的管理密码"></td></tr>').'
</table>
<div id="websetup_show_2" class="ajax_content" style="display:none">--</div>
</div>';