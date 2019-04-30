<?php
$data=db_getshow('website','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'webid'=>$website['webid'],
		'setup_weburl'=>'',
		'setup_record'=>'',
		'isdef'=>0,
		'isadmin'=>0
		);
	}
$domainmax=db_getone('websetup','domainmax','webid='.$website['webid']);
$res='<div class="win_ajax ajax_edit"><div class="ui_point">注意事项：请先将域名解析到当前网站服务器，再在这里绑定域名后才有效果，当前站点最多可绑定 <span class="blue">'.$domainmax.'</span> 个域名，如您不了解此功能请勿使用</div><table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1"><span class="help" title="前面不用加 http//，后面不用加 /，示例：www.qingyunke.com">域名地址</span></td><td class="td6_2"><input type="text" class="inp" id="post_setup_weburl" value="'.$data['setup_weburl'].'"></td></tr>
<tr><td class="td6_1">备案号</td><td class="td6_2"><input type="text" class="inp" id="post_setup_record" value="'.$data['setup_record'].'"></td></tr>
<tr><td class="td6_1"><span class="help" title="访问次域名会跳转到主域名，并且只能通过主域名登录网站后台，只能设定一个主域名，通常建议设置 www 开头的顶级域名为主域名">是否主域名</span></td><td class="td6_2"><select id="post_isdef" tag="postinp"'.goif($data['isdef'],' isedit="no"').'><option value="0">否（次域名）</option><option value="1"'.goif($data['isdef'],' selected').'>是（主域名）</option></select></td></tr>
</table></div>';