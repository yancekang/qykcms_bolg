<?php
$morelang=array('cn'=>'中文','en'=>'英文','mn'=>'蒙古','jp'=>'日文','ko'=>'韩文','tr'=>'俄文','el'=>'希腊','sa'=>'梵文','other'=>'其它');
$langlist=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"');
if($langlist=='')$langlist='cn|中文';
$langlist=','.$langlist.',';
$boxlist='';
foreach($morelang as $en=>$cn){
	$boxlist.='<input title="'.$cn.'" class="inp_box" type="checkbox" value="'.$en.'" id="post_language_'.$en.'" text="'.$cn.'（'.$en.'）"'.goif(strstr($langlist,','.$en.'|'),' checked').'>';
	}
$res='<div class="win_ajax ajax_edit"><div class="ui_point">勾选对应的语言版本，后台将自动开启该语言版网站的管理功能，而网站前台需要有该语言的模板才有效果，本功能主要提供给主题设计人员使用</div>
<table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_3">'.$boxlist.'</td></tr>
</table></div>';