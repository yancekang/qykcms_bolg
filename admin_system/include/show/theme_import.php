<?php
$themedata=db_getone('websetup','themedata','webid='.$website['webid']);
if($themedata=='')ajaxreturn(1,'请先安装主题');
@$themearr=unserialize($themedata);
if(!is_array($themearr)||empty($themearr)){
	$themearr=array(
		"title"=>"未命名",
		"author"=>array("name"=>"无名氏","qq"=>"","email"=>"","homepage"=>'http://'.$webdomain)
		);
	}
$res='<div class="win_ajax ajax_edit">
<div class="ui_point" tag="import_more2" style="display:none">提示：如对主题未进行大幅修改，请尊重他人保留原作者信息</div>
<table id="admin_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr tag="import_more1"><td class="td6_1">当前主题</td><td class="td6_2">'.$themearr['title'].'</td></tr>
<tr tag="import_more1"><td class="td1">设计人</td><td class="td6_2"><a href="javascript:openurl(\''.$themearr['author']['homepage'].'\')" class="links" title="'.$themearr['author']['homepage'].'">'.$themearr['author']['name'].'</a></td></tr>
<tr><td class="td6_1"><span class="help" title="1、建议包含分类结构，每款主题的网站栏目都可能不相同，如果网站安装了不含栏目结构的主题，极有可能与原网站栏目不兼容<br>2、通常不必勾选“包含文章数据”，勾选此项时系统将会同时把网站内容打包到主题，文章记录数超过1000条则不支持此选项">导出选项</span></td><td class="td6_2"><input class="inp_box" id="post_import_auto" type="checkbox" value="auto" text="包含分类结构" checked><input class="inp_box" id="post_import_article" type="checkbox" value="auto" text="包含文章数据"></td></tr>
<tr><td class="td6_1">作者信息</td><td class="td6_2"><input class="inp_box" id="post_import_zdy" type="checkbox" value="ok" text="自定义"></td></tr>
<tr tag="import_more2" style="display:none"><td class="td6_1">主题名称</td><td class="td6_2"><input class="inp" id="post_title" type="text" value="'.$themearr['title'].'"></td></tr>
<tr tag="import_more2" style="display:none"><td class="td6_1">设计人</td><td class="td6_2"><input class="inp" id="post_name" type="text" value="'.$themearr['author']['name'].'"></td></tr>
<tr tag="import_more2" style="display:none"><td class="td6_1">作者QQ</td><td class="td6_2"><input class="inp" id="post_qq" type="text" value="'.$themearr['author']['qq'].'"></td></tr>
<tr tag="import_more2" style="display:none"><td class="td6_1">作者邮箱</td><td class="td6_2"><input class="inp" id="post_email" type="text" value="'.$themearr['author']['email'].'"></td></tr>
<tr tag="import_more2" style="display:none"><td class="td6_1">作者主页</td><td class="td6_2"><input class="inp" id="post_homepage" type="text" value="'.$themearr['author']['homepage'].'"></td></tr>
</table></div>';