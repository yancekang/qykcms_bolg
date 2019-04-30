<?php
switch($tcz['desc']){
	case 'del':
		if(!ispower($admin_group,'uploadzip_del'))ajaxreturn(1,'权限不足，无法删除附件');
		$file=arg('file','post','url');
		$folder=arg('folder','post','url');
		$list=explode('|',$file);
		$deltip='';
		$delnum=0;
		foreach($list as $f){
			if($f=='')continue;
			$delnum++;
			if($delnum<=10)$deltip.=goif($deltip!='','，').$f;
			$path='../'.$website['upfolder'].'down/'.$f;
			$path=iconv("utf-8","gb2312",$path);
			@unlink($path);
			}
		infoadminlog($website['webid'],$tcz['admin'],23,'删除'.$delnum.'个附件：'.$deltip.goif($delnum>10,'...'));
		if($folder!=''){
			$isclear=true;
			$path='../'.$website['upfolder'].'down/'.$folder.'/';
			$path=iconv("utf-8","gb2312",$path);
			$mydir=dir($path);
			while($file=$mydir->read()){
				if($file=='..'||$file=='.')continue;
				else{
					$isclear=false;
					break;
					}
				}
			if($isclear){
				rmdir($path);
				ajaxreturn(0,'已成功删除附件','clear');
				}
			}
		ajaxreturn(0,'已成功删除附件');
	break;
	case 'list':
		$folder=arg('folder','post','url');
		$folder=goif($folder!='',$folder.'/');
		$path='../'.$website['upfolder'].'down/'.$folder;
		$path=iconv("utf-8","gb2312",$path);
		$mydir=dir($path);
		$list1='';
		$list2='';
		if($folder!='')$list1.='<li class="out" title="返回上级" ondblclick="uploadzip({log:\'start\'})"><div class="cover pre"></div><div class="fname">返回上级</div></li>';
		while($file=$mydir->read()){
			$fname=iconv("gb2312","utf-8",$file);
			if($file=='..'||$file=='.')continue;
			if(is_dir($path.$file)){
				$list1.='<li class="out" title="'.$fname.'" tag="folder" ondblclick="uploadzip({log:\'start\',folder:\''.$folder.$fname.'\'})"><div class="cover folder"></div><div class="fname">'.tipshort($fname,6).'</div></li>';
			}else{
				@$ftype=strtolower(end(explode('.',$fname)));
				$list2.='<li class="out" title="'.$folder.$fname.'" fname="'.$fname.'" ftype="'.$ftype.'" tag="file" onclick="uploadzip({log:\'choose\',obj:this})"><div class="cover '.$ftype.'"></div><div class="fname">'.tipshort($fname,6).'</div></li>';
				}
			}
		$mydir->close();
		$list=$list1.$list2;
		if($list==''){
			if(ispower($admin_group,'uploadzip_edit'))$list='<li class="out" onclick="uploadzip({log:\'upload\'})"><div class="cover upload"></div><div class="fname">上传文件</div></li>';
			else $list='<li class="out"><div class="cover"></div><div class="fname">暂无附件</div></li>';
			}
		$res='<ul>'.$list.'</ul>';
		$other='|';
		if(!ispower($admin_group,'uploadzip_edit'))$other.='uploadzip_edit|';
		if(!ispower($admin_group,'uploadzip_del'))$other.='uploadzip_del|';
		ajaxreturn(0,$res,$other);
	break;
	}