<?php
$themelist='';
$path='../res/theme/';
$mydir=dir($path);
while($file=$mydir->read()){
	if(is_dir($path.$file))continue;
	$file=iconv("gb2312","utf-8",$file);
	$farr=explode('.', $file);
	$ftype=strtolower(end($farr));
	if($ftype!='zip')continue;
	$themelist.='<option value="'.$file.'">内置主题：'.$file.'</option>';
	}
$mydir->close();
$res='<div class="win_ajax ajax_edit" id="theme_show_1"><table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1">安装方式</td><td class="td6_2"><select id="post_themetype" tag="postinp"><option value="a">上传主题安装包</option>'.$themelist.'</select></td></tr>
<tr tag="theme_tr1"><td class="td6_1"><span class="help" title="主题安装包可以在官网或有提供QYKCMS主题的第三方网站上下载，制作自己的主题安装包也相当简单，只需随意安装一个主题按自己的设计去修改，完成后导出当前主题即可">上传安装包</span></td><td class="td6_2"><textarea onfocus="this.blur()" placeholder="主题安装包为.zip格式" onclick="uploadimg({log:\'start\',types:\'theme\'})" class="inp_up" id="post_theme_file"></textarea></td></tr>
</table></div>
<div class="win_ajax ajax_user" id="theme_show_2" style="display:none">
<table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">主题名称</td><td class="td2 green" id="post_title">...</td><td class="td3 cover" rowspan=9><img src="images/ui/nopic.png" id="post_cover" onclick="openurl(this.src)" title="单击查看大图" style="max-height:328px"></td></tr>
<tr><td class="td1"><span class="help" title="制作该主题的前台系统版本，不代表其它版本不兼容">前台系统版本</span></td><td class="td2" id="post_version">...</td></tr>
<tr><td class="td1">支持语言</td><td class="td2" id="post_languages">...</td></tr>
<tr><td class="td1">制作时间</td><td class="td2" id="post_time">...</td></tr>
<tr><td class="td1">设计人</td><td class="td2" id="post_author_name">...</td></tr>
<tr><td class="td1">作者QQ</td><td class="td2" id="post_author_qq">...</td></tr>
<tr><td class="td1">作者邮箱</td><td class="td2" id="post_author_email">...</td></tr>
<tr><td class="td1">作者主页</td><td class="td2" id="post_author_homepage">...</td></tr>
<tr><td class="td1">额外内容</td><td class="td2" id="post_otherdata">...</td></tr>
<tr><td class="td1"><span class="help" title="1、勾选导入分类结构，安装后将可能会改变您当前网站分类结构，如果您不勾选此项，那么该主题很可能与现在的网站分类不兼容，导致网站页面显示异常<br>2、勾选导入文章数据，如果该主题包含文章数据将会一起被导入，但如果您当前的网站中已有文章，系统将会提示您先清空所有文章才能安装该主题">安装选项</span></td><td class="td5" colspan=3><input class="inp_box" id="post_import_auto" type="checkbox" value="auto" text="导入分类结构" checked><input class="inp_box" id="post_import_article" type="checkbox" value="auto" text="导入文章数据"></td></tr>
<tr tag="import_datatype_tr" style="display:none"><td class="td1"><span class="help" title="如果不勾选“保留现有数据”，则在导入文章数据时会覆盖原有的数据">数据导入方式</span></td><td class="td5" colspan=3><input class="inp_box" id="post_import_datatype" type="checkbox" value="auto" text="保留现有数据" checked></td></tr>
</table></div>';