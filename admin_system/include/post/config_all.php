<?php
if(!$website['isadmin']||!ispower($admin_group,'super'))ajaxreturn(9,'权限不足，操作失败');
updateallweb();
infoadminlog($website['webid'],$tcz['admin'],19,'同步系统参数');
ajaxreturn(0,'系统参数同步完成');