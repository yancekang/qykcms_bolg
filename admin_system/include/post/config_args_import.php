<?php
$args=db_getall('config','*','webid=1 order by id asc');
$arr=array();
foreach($args as $v){
	$v=deltable($v,'id');
	array_push($arr,$v);
	}
$content=serialize($arr);
$post_file=$website['upfolder'].setup_uptemp.'install_config.zip';
$zip=new ZipArchive();
if($zip->open('../'.$post_file,ZipArchive::OVERWRITE)===TRUE){
	$zip->addFromString('install_config.txt',$content);
	$zip->close();
}else{
	$post_file=$website['upfolder'].setup_uptemp.'install_config.txt';
	@$file=fopen('../'.$post_file,'w');
	if(!$file)ajaxreturn(1,'保存文件失败');
	fwrite($file,$content);
	fclose($file);
	}
countcapacity($website['webid']);
$downurl='http://'.$website['setup_weburl'].'/'.$post_file;
ajaxreturn(0,'参数已导出，请留意下载提示',$downurl);