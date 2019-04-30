<?php
$languages=arg('lang','post','url');
if($languages=='')ajaxreturn(1,'未知的语言版本');
$conf=db_getlist('select * from '.tabname('config').' where webid='.$website['webid'].' and cata="'.$languages.'" and isview=0 order by contype asc,sort asc,id asc');
if(!$conf)ajaxreturn(1,'未找到可设置项目');
$res2=getargsform($conf);
$res='<input type="hidden" id="post_languages" value="'.$languages.'"><div class="win_ajax ajax_user">
<div class="ui_point">温馨提示：频繁修改网站配置信息对搜索引擎不友好，如您不了解本功能请勿随意修改</div>
<table class="ajax_tablist" cellpadding="12" cellspacing="1">'.$res2.'</table></div>';