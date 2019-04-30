<?php
$path=strtolower(arg('path','post','url'));
if(strstr($path,setup_webfolder.$website['webid'].'/')){
	$file=explode(setup_webfolder.$website['webid'].'/',$path);
	$file=$file[1];
}else{
	$file=$path;
	if(!$website['isadmin'])die('{error:文件不存在}');
	}
$content='';
$path2=iconv("utf-8","gb2312",$path);
if(is_dir($path2)){
	die('{error:暂未支持直接创建文件，请通过上传方式创建}');
}else{
	@$ftype=end(explode('.',$path));
	@$content=file_get_contents($path2) or ajaxreturn(1,'文件无法读取，请检查读写权限');
	$content=str_replace('</script>','{script_tag_end}',$content);
	$content=str_replace('{filecode}','{filecode_tag}',$content);
	$content=trim($content,'\r\n');
	$content=trim($content,'\r');
	$content=trim($content,'\n');
	}
switch($ftype){
	case 'gif':case 'png':case 'jpg':case 'jpeg':case 'bmp':
		$newpath='http://'.$website['setup_weburl'].'/'.str_replace('../','',$path);
		$res='<div class="ajax_imgview"><img src="'.$newpath.'" onload="this.style.marginTop=((320-this.height)/2)+\'px\'"></div>';
	break;
	case 'html':case 'htm':case 'shtml':case 'php':case 'asp':case 'jsp':case 'txt':case 'css':case 'js':case 'sql':case 'ini':case 'conf':case 'config':case 'htacces':
		$res='<input id="post_file" type="hidden" value="'.$file.'"><input id="post_ftype" type="hidden" value="'.$ftype.'"><script id="post_content" type="text/plain" style="display:none">'.$content.'</script>';
	break;
	default:
		die('{error:不支持编缉'.$ftype.'类型文件}');
	break;
	}
die('{filecode}'.$res.'{filecode}');
exit;