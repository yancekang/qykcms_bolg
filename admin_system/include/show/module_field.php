<?php
$data=db_getshow('module_field','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'modid'=>$tcz['bcat'],
		'title'=>'',
		'varname'=>'',
		'varval'=>'',
		'infotype'=>'inp',
		'infosel'=>'',
		'content'=>'',
		'isedit'=>0,
		'sort'=>1
		);
	}
$res='<input type="hidden" id="post_modid" value="'.$data['modid'].'"><div class="win_ajax ajax_user">
'.goif($data['id'],'<div class="ui_point"><span class="blue">注意：修改具有上传功能字段的“字段名称”可能会对系统产生较大压力，并且修改这类型字段的“输入方式”将会丢失该字段所在列数据、图片或文件</span></div>').'<table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1"><span class="help" title="简短的标题，表达此字段的意义，通常在8个汉字内">字段标题</span></td><td class="td2"><input type="text" class="inp" id="post_fietitle" value="'.$data['title'].'"></td><td class="td1"><span class="help" title="由字母及数字下划线组成，并且必须以字母开头，在每个自定义模块中不可重复">字段名称</span></td><td class="td2"><input type="text" class="inp" id="post_varname" value="'.$data['varname'].'"></td></tr>
<tr><td class="td1">输入方式</td><td class="td2">'.selecthtml('post_infotype',getusermodinfotype('',true),$data['infotype'],'tag="postinp"').'</td><td class="td1"><span class="help" title="1、当输入方式为下拉菜单时，这里可设置下拉菜单选项<br>格式：值|选项文本,值|选项文本...（值和选项文本相同时，可忽略选项文本）<br>示例：1|中国,2|美国,3|韩国<br>示例：100,200,300,400,500<br>2、当输入方式为自定义选项时，这里请输入自定义选项的类型标识">下拉选项</span></td><td class="td2"><input type="text" id="post_infosel" value="'.$data['infosel'].'" '.goif($data['infotype']!='select'&&$data['infotype']!='option'&&$data['infotype']!='option_more','class="inp_no" readonly','class="inp"').'></td></tr>
<tr><td class="td1">默认值</td><td class="td2"><input type="text" id="post_varval" value="'.$data['varval'].'" class="inp"></td><td class="td1"><span class="help" title="数字越<span class=\'blue\'>小</span>，排序越靠前">状态 / 排序</span></td><td class="td2"><select tag="postinp" id="post_isedit" sw=160><option value=0>允许编缉</option><option value=1'.goif($data['isedit']==1,' selected').'>禁用编缉</option></select><input style="margin-left:10px;width:90px" type="number" min=1 max=9999 class="inp" id="post_sort" value="'.$data['sort'].'"></td></tr>
<tr><td class="td1"><span class="help" title="详细说明该参数的用途，不需要可以为空">说明描述</span></td><td class="td3" colspan=3><input style="width:701px" type="text" class="inp" id="post_content" value="'.$data['content'].'"></td></tr>
</table></div>';