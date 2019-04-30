<?php
$conf=db_getlist('select * from '.tabname('config').' where webid=1 and cata="basic" and isview=0 order by contype asc,sort asc,id asc');
if(!$conf)ajaxreturn(1,'未找到可设置项目');
$res2=getargsform($conf);
$res='<div class="win_ajax ajax_user">
<table class="ajax_tablist" cellpadding="12" cellspacing="1">'.$res2.'</table></div>';