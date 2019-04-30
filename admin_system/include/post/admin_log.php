<?php
if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
$deltime=strtotime('-3 month');
$res=db_del('admin_log','webid='.$website['webid'].' and time_add<'.$deltime,true);
ajaxreturn(0,'本次操作共清理 '.$res.' 条日志');