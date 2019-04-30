<?php
$data=db_getshow('admin_log','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data)ajaxreturn(1,'不存在的日志记录');
$res='<div class="win_ajax ajax_edit"><table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1">管理员</td><td class="td6_2 green">'.$data['user_admin'].'</td></tr>
<tr><td class="td6_1">IP地址</td><td class="td6_2">'.$data['user_ip'].'</td></tr>
<tr><td class="td6_1">事件类型</td><td class="td6_2">'.getadmincata($data['oper_type']).'</td></tr>
<tr><td class="td6_1">操作时间</td><td class="td6_2">'.date('Y-m-d H:i:s',$data['time_add']).'</td></tr>
<tr><td class="td_scro" colspan=2><div class="scro" style="height:80px">'.$data['content'].'</div></td></tr>
</table></div>';