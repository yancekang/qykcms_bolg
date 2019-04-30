<?php
$data=db_getshow('feedback','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data)ajaxreturn(1,'不存在的数据');
if(!$data['time_view']){
	$other='view';
	}
$datacont='';
if($data['dataid']){
	$static_val=true;
	$static=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_static"');
	if($static=='false')$static_val=false;
	define('setup_static',$static_val);
	$tname='article';
	$modtype=db_getone('module','modtype','webid='.$website['webid'].' and classid='.$data['bcat']);
	if($modtype>10)$tname.='_'.$website['webid'].'_'.$modtype;
	$art=db_getshow($tname,'mark,dataid,title','webid='.$website['webid'].' and dataid='.$data['dataid']);
	if($art)$datacont='<div class="ui_point">该条评论来自：<a href="javascript:openurl(\'http://'.$website['setup_weburl'].getlink('log='.$art['mark'].'&id='.$art['dataid']).'\')">'.tipshort($art['title'],20).'</a></div>';
	}
$res='<div class="win_ajax ajax_user">'.$datacont.'<table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">称呼</td><td class="td2 green">'.$data['name'].'</td><td class="td1">IP地址</td><td class="td2">'.$data['user_ip'].goif($data['user_iptext']!='','（'.$data['user_iptext'].'）').'</td></tr>
'.goif(!$data['dataid'],'<tr><td class="td1">邮箱地址</td><td class="td2">'.$data['email'].'</td><td class="td1">电话号码</td><td class="td2">'.$data['phone'].'</td></tr>').'<tr><td class="td1">发表时间</td><td class="td2">'.date('Y-m-d H:i:s',$data['time_add']).'</td><td class="td1">阅读记录</td><td class="td2">'.goif($data['time_view'],$data['user_admin'].' 于 '.date('Y-m-d H:i:s',$data['time_view']),'<span class="red">未标记阅读</span>').'</td></tr>';
if($data['attachment']!=''){
	$data['attachment']=getfile_admin('file',$data['attachment']);
	$res.='<tr><td class="td1">相关附件</td><td class="td3" colspan=3><a class="url" onclick="openurl(\'http://'.$website['setup_weburl'].$data['attachment'].'\')">'.$data['attachment'].'</a></td></tr>';
	}
$res.='<tr><td class="td_scro" colspan=4><div class="scro">'.getcomment_admin($data['content']).'</div></td></tr>
<tr><td class="td1"><span class="help" title="如果网站中有公开展示留言评论，则这里的回复会显示到网站，不会直接回复到对方邮箱">回复留言</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="post_reply" placeholder="请输入回复内容（回复不会直接回复到对方邮箱，仅用于网站展示）">'.preg_replace('/<br(.*?)>/i','\n',$data['reply']).'</textarea></td></tr>
</table></div>';