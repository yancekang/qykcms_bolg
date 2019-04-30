<?php
@$domain=$_SERVER['SERVER_NAME'];
if($domain=='')$domain=$_SERVER['HTTP_HOST'];
if($domain=='')$domain='<a class="no" title="无法使用SERVER_NAME及HTTP_HOST获得域名，可能会影响部分功能">无法获取</a>';
else $domain='<a class="yes">'.$domain.'</a>';
	
$ports=(int)$_SERVER['SERVER_PORT'];
if($ports!=80)$ports='<a class="no">'.$ports.'</a>';
else $ports='<a class="yes">'.$ports.'</a>';

$runpath=$_SERVER['PHP_SELF'];
if(!preg_match('/^\/install\//',$runpath))$runpath='<a class="no">子目录</a>';
else $runpath='<a class="yes">根目录</a>';

$phpver=phpversion();
if(!preg_match('/^5|^6/',$phpver))$phpver.='<a class="no" title="请安装5.2及以上PHP版本">'.$phpver.'</a>';
else $phpver='<a class="yes">'.$phpver.'</a>';

$upmax=strtoupper(ini_get('upload_max_filesize'));
$upmax_size=(int)$upmax;
if(!strstr($upmax,'M'))$upmax_size=$upmax_size/1024;
if($upmax_size>=5)$upmax='<a class="yes">'.$upmax.'</a>';

$filegetcontents=file_get_contents(__FILE__)?'<a class="yes">支持</a>':'<a class="no" title="重要，不支持将无法加载主题模板">NO</a>';

if(function_exists('zip_open'))$zip1='<a class="yes">支持</a>';
else $zip1='<a class="no" title="系统自动升级模块需要">不支持</a>';

if(function_exists('disk_free_space')){
	@$diskspace=(float)disk_free_space('/');
	if($diskspace){
		$diskspace=ceil($diskspace/1024/1024);
		if($diskspace>=20)$diskspace='<a class="yes">'.$diskspace.'M</a>';
		else $diskspace='<a class="no">'.$diskspace.'M</a>';
	}else $diskspace='无法获取';
}else $diskspace='无法获取';
$zip2='<a class="no" title="导出主题等功能需要">不支持</a>';
if(method_exists('ZipArchive','open')){
	$zip=new ZipArchive();
	if($zip->open('test.zip',ZipArchive::OVERWRITE)===TRUE){
		$zip2='<a class="yes">支持</a>';
		}
	}
$filelist=array(
	array('path'=>'install/','text'=>"安装目录"),
	array('path'=>'include/config.php','text'=>"基础配置文件"),
	array('path'=>'include/config_db.php','text'=>"数据库配置文件"),
	array('path'=>'template/','text'=>"主题模板目录"),
	array('path'=>'upload/','text'=>"上传目录"),
	array('path'=>'admin_system/backup/','text'=>"备份目录"),
	array('path'=>'admin_system/update/','text'=>"自动升级文件目录")
	);
function installcheckfile($fpath){
$isok=false;
if(is_dir($fpath)){
	if(is_writable($fpath))$isok=true;
}else{
	if(is_writable($fpath)){
		@$f=fopen($fpath,'r+');
		if($f)$isok=true;
		fclose($f);
		}
	}
return $isok;
}
$filecheck='';
foreach($filelist as $f){
	$fs=installcheckfile($f['path']);
	if($fs)$fstext='<a class="yes">可写</a>';
	else $fstext='<a class="no">不可写或不存在</a>';
	$filecheck.='<div class="item1"><span class="cname">'.$f['path'].'</span><span class="text">'.$fstext.'</span><span class="text2">可写</span><span class="tips">'.$f['text'].'</span></div>';
	}

$res='<div class="title">第一步：安装环境检测</div>
<div class="cont">
	<div class="item1 item"><span class="cname">环境检测</span><span class="text">当前状态</span><span class="text2">系统要求</span><span class="tips">相关说明</span></div>
	<div class="item1"><span class="cname">站点域名</span><span class="text">'.$domain.'</span><span class="text2">无限制</span></div>
	<div class="item1"><span class="cname">QYKCMS前台版本</span><span class="text"><a class="yes">version '.install_version_front.'</a></span><span class="text2">无限制</span><span class="tips">官方推荐版本：<a id="qyk_newver" target=_blank href="http://cms.qingyunke.com" class="orange">...</a></span></div>
	<div class="item1"><span class="cname">端口</span><span class="text">'.$ports.'</span><span class="text2">80</span><span class="tips">非80端口无法完美运行</span></div>
	<div class="item1"><span class="cname">安装路径</span><span class="text">'.$runpath.'</span><span class="text2">根目录</span><span class="tips">当前版本仅支持根目录运行</span></div>
	<div class="item1"><span class="cname">PHP版本</span><span class="text">'.$phpver.'</span><span class="text2">5.2及以上版本</span><span class="tips">版本过旧可能会产生错误</span></div>
	<div class="item1"><span class="cname">磁盘空间</span><span class="text">'.$diskspace.'</span><span class="text2">20M以上</span><span class="tips">如此项为“无法获取”不会影响安装</span></div>
	<div class="item1"><span class="cname">附件上传</span><span class="text">'.$upmax.'</span><span class="text2">推荐5M以上</span></div>
	<div class="item1"><span class="cname">file_get_contents</span><span class="text">'.$filegetcontents.'</span><span class="text2">必须支持</span><span class="tips">不支持将无法运行系统</span></div>
	<div class="item1"><span class="cname">ZipArchive</span><span class="text">'.$zip2.'</span><span class="text2">建议支持</span><span class="tips">导出主题、数据备份、自动更新...</span></div>
	<div class="item1"><span class="cname">zip_open</span><span class="text">'.$zip1.'</span><span class="text2">建议支持</span><span class="tips">不支持将影响部分后台管理功能</span></div>
	<div class="item1 item"><span class="cname">文件、目录权限检查</span><span class="text">当前状态</span><span class="text2">所需状态</span><span class="tips">相关说明</span></div>
	'.$filecheck.'
	<div style="clear:both"></div>
</div>
<div class="btn"><input type="button" value="重新检测" onclick="location.reload()">　<input type="button" value="下一步" onclick="gostart()">　<input type="button" value="返回上一步" onclick="location.href=\'index.php?setup=1\'"></div>
<script>
$(document).ready(function(){
	getnewver()
	});
</script>
';
echo $res;
?>