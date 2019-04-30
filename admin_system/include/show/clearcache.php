<?php
$tempsize=sprintf('%.2f',getdirsize($website['upfolder'].setup_uptemp)/1024/1024);
$appsuisize=sprintf('%.2f',getdirsize(setup_webfolder.$website['webid'].'/runtime/temp/')/1024/1024);
$datasize=sprintf('%.2f',(getdirsize(setup_webfolder.$website['webid'].'/runtime/cache/')+getdirsize(setup_webfolder.$website['webid'].'/runtime/temp/'))/1024/1024);
$res='<div class="win_ajax ajax_edit">
<table id="admin_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1">操作类型</td><td class="td6_2"><select id="post_cachetype" tag="postinp"><option value="datacache">清除网站所有缓存'.goif($datasize>0.01,'（'.$datasize.' MB）').'</option><option value="tempcache">仅清除模板缓存'.goif($appsuisize>0.01,'（'.$appsuisize.' MB）').'</option><option value="tempfile">删除临时文件'.goif($tempsize>0.01,'（'.$tempsize.' MB）').'</option></select></td></tr>
</table></div>';