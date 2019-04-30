<?php
$data=db_getshow('module_user','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'webid'=>1,
		'dataid'=>0,
		'title'=>'',
		'isok'=>0
		);
	$res='<div class="win_ajax ajax_edit">
	<table id="module_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
	<tr><td class="td6_1"><span class="help" title="设置一个名称方便自己区分，如：下载模块、产品模块">模块名称</span></td><td class="td6_2"><input type="text" class="inp" id="post_title" value="'.$data['title'].'"></td></tr>
	<tr><td class="td6_1">启用状态</td><td class="td6_2"><select id="post_isok" tag="postinp"><option value="0">启用</option><option value="1"'.goif($data['isok']==1,' selected').'>不启用</option></select></td></tr>
	</table></div>';
}else{
	$fielist='';
	$fiearr=db_getall('module_field','*','webid='.$website['webid'].' and modid='.$data['dataid'].' order by sort asc,id asc');
	foreach($fiearr as $val){
		$fielist.='<tr><td class="td6"><input name="id" style="width:60px;text-align:center" type="text" class="inp_no" value="'.$val['id'].'" title="ID" readonly>';
		$fielist.='<input name="title" style="width:160px;margin-left:10px" type="text" class="inp" value="'.$val['title'].'" title="字段标题" maxlength="30">';
		$fielist.='<input name="varname" style="width:160px;margin-left:10px" type="text" class="inp_no" value="'.$val['varname'].'" title="字段名称" maxlength="50" readonly>';
		$fielist.=selecthtml('post_infotype_'.$val['id'],getusermodinfotype('',true),$val['infotype'],'tag="postinp" sw=160 isedit="no" ml=10');
		$fielist.='<input name="sort" style="width:100px;margin-left:10px;text-align:center" type="number" min="1" max="9999" class="inp" value="'.$val['sort'].'" title="排序数字，越小越靠前"><input style="margin-left:10px" class="inp_btn" type="button" value="修改" onclick="openshow({log:\'module_field\',id:'.$val['id'].'})"><input style="margin-left:10px" class="inp_btn" type="button" value="删除" onclick="deldata({log:\'module_field\',id:'.$val['id'].'})"></td></tr>';
		}
	if($fielist=='')$fielist='<span class="green">请点击下方“添加字段”按钮设置字段，系统已内置必备字段：</span><br>标题（title），来源（source），关键词（keyword），描述（description），排序（sort），外部链接（linkurl）<br><br><span class="green">除此之外，部分系统级的设置功能也已内置，如：</span><br>所属分类、模板文件、发布时间、置顶时间、支持PC端/移动端、状态、星标等级、标题颜色等';
	$res='<input type="hidden" id="post_moduser_id" value="'.$data['id'].'"><input type="hidden" id="post_moduser_dataid" value="'.$data['dataid'].'"><div class="win_ajax ajax_user">
	<table id="module_user_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
	<tr><td class="td1"><span class="help" title="设置一个名称方便自己区分，如：下载模块、产品模块">模块名称</span></td><td class="td2"><input type="text" class="inp" id="post_title" value="'.$data['title'].'"></td><td class="td1">启用状态</td><td class="td2"><select id="post_isok" tag="postinp"><option value="0">启用</option><option value="1"'.goif($data['isok']==1,' selected').'>不启用</option></select></td></tr>
	<tr><td class="td0" colspan=4>内置字段：标题（title），来源（source），关键词（keyword），描述（description），排序（sort），外部链接（linkurl）</td></tr>
	<tr><td class="td_scro" colspan=4>
	<div class="scro ui_morechoose" style="height:350px">
		<table id="module_user_show_2" class="ajax_tablist" cellpadding="12" cellspacing="1">'.$fielist.'</table>
	</div>
	</td></tr>
	</table></div>';
	}