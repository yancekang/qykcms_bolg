<?php
$downurl=arg('downurl','post','url');
$filename=arg('filename','post','url');
$file=downfile($downurl.$filename,'../'.$website['upfolder'].setup_uptemp,$filename);
if(!$file)ajaxreturn(1,'下载升级文件失败，可能由于以下原因导致无法下载：<br>1、服务器繁忙，请稍候再重新尝试升级<br>2、PHP环境不支持远程 file_get_contents 函数');
ajaxreturn(0,$website['upfolder'].setup_uptemp,$file);