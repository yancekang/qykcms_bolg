<?php
$file=arg('file','post','url');
if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
$cover='';
$dir='../'.$website['upfolder'].setup_uptemp;
$dir_new='qyk_theme_new';
$path=$dir.'qyk_theme_new.zip';
if($tcz['desc']=='theme_open_def'){
	$defpath='../res/theme/'.$file;
	$defpath=iconv("utf-8","gb2312",$defpath);
	if(!file_exists($defpath))ajaxreturn(1,'系统内置的主题安装包已丢失：res/theme/'.$file);
	copy($defpath,$path);
	}
if(!file_exists($path))ajaxreturn(1,'未找到主题安装包，请确认是否上传成功');
if(is_dir($dir.$dir_new.'/')){
	$dr=deldir_admin($dir.$dir_new.'');
	if(!$dr)ajaxreturn(1,'无法删除旧安装文件导致主题安装失败，请手动删除该目录及文件<br>'.$dir.$dir_new);
	}
if(!method_exists('ZipArchive','open')){
	ajaxreturn(1,'主题解压失败，请检查php环境是否支持 ZipArchive');
	}
$zip=new ZipArchive;
$otype=$zip->open($path);
if(!$otype)ajaxreturn(1,'主题解压失败，请检查php环境是否支持 ZipArchive');
$zip->extractTo($dir.$dir_new.'/');
$zip->close();
$theme_file=$dir.$dir_new.'/'.setup_themefile;
if(!file_exists($theme_file))ajaxreturn(1,'安装包内缺少 '.setup_themefile.' 文件');
$themedata=readtemp_admin($theme_file,'主题信息读取失败，可能是以下原因导致：<br>1、主题安装包文件制作不规范或不完整<br>2、版本不兼容，请尽量选择推荐版本与系统版本相近的主题<br>3、php环境不能很好地支持ZipArchive扩展类');
@$arr=unserialize($themedata);
if(!is_array($arr)||empty($arr)){
	ajaxreturn(1,setup_themefile.' 文件内容不规范');
	}
if(file_exists($dir.$dir_new.'/cover.jpg'))$cover='http://'.$website['setup_weburl'].getfile_admin('pic',setup_uptemp.$dir_new.'/cover.jpg');
$arr['otherdata']='导航栏目（'.count($arr['module']).'）　自定义模块（'.count($arr['module_user']).'）';
$arr=deltable($arr,'config');
$arr=deltable($arr,'module');
$arr=deltable($arr,'module_user');
$arr=deltable($arr,'module_field');
$arr=deltable($arr,'article');
$arr=deltable($arr,'article_user');
$arr=deltable($arr,'label');
$arr=deltable($arr,'advert');
$jn=json_encode($arr);
ajaxreturn(0,$jn,$cover);