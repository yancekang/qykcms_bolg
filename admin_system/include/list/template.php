<?php
$keyword=arg('keyword','post','txt');
$themepath=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_theme_folder"');
if(!$themepath)$themepath='default';
$tcz['desc']=str_replace('{themepath}',$themepath,$tcz['desc']);
$dirlist=str_replace('/',' → ',$tcz['desc']);
$path='../'.setup_webfolder.$website['webid'].'/'.$tcz['desc'].'/';
if(!is_dir($path))ajaxreturn(1,'路径不存在：'.$tcz['desc'].'/， 尚未创建的模板文件夹');
$isui=false;
if(preg_match('/\/ui$/',$tcz['desc']))$isui=true;	//UI目录
$btn='<div class="btnsear"><span class="txt">当前位置：<span class="green">站点目录 → '.$dirlist.'</span></span></div>
<div class="btnright">'.goif(ispower($admin_group,'skin_upload'),'<input type="button" value="上传文件" class="btn1" onclick="uploadimg({log:\'start\',types:\'template\',path:\''.$tcz['desc'].'\'})">').goif(ispower($admin_group,'skin_del'),'<input type="button" value="删除文件" class="btn1" onclick="deldata({log:\'template\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">序</td>
<td>文件名称</td>
<td style="width:150px">文件大小</td>
</tr>';
//$path=iconv("utf-8","gb2312",$path)
$mydir=dir($path);
$xu=0;
if($isui){
	$xu++;
	$file='style_global.css';
	$res.='<tr dataid="'.$xu.'" file="'.$tcz['desc'].'/'.$file.'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow_code({path:\''.$path.'\',file:\''.$file.'\'})">
<td class="cen">'.$xu.'</td>
<td><span class="list_green">'.$file.'</span>　<span class="list_orange">（css样式文件）</span></td>
<td>'.sprintf('%.2f',filesize($path.$file)/1024).' KB</td>
</tr>';
	}
while($file=$mydir->read()){
	if(is_dir($path.$file))continue;
	if($isui&&$file=='style_global.css')continue;
	$xu++;
	$title=iconv("gb2312","utf-8",$file);
	$res.='<tr dataid="'.$xu.'" file="'.$tcz['desc'].'/'.$title.'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow_code({path:\''.urlencode($path).'\',file:\''.urlencode($title).'\'})">
<td class="cen">'.$xu.'</td>
<td><span class="list_green">'.$title.'</span></td>
<td>'.sprintf('%.2f',filesize($path.$file)/1024).' KB</td>
</tr>';
	}
$mydir->close();
$res.='</table>';
ajaxreturn(0,$res,$btn);