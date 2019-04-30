<?php
$data=db_getshow('select','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'bcat'=>0,
		'types'=>'',
		'title'=>'',
		'sort'=>1
		);
	}
$res='<div class="win_ajax ajax_edit">
<table id="option_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1">选项标题</td><td class="td6_2"><input type="hidden" id="post_bcat" value="'.$data['bcat'].'"><input type="text" class="inp" id="post_title" value="'.$data['title'].'"></td></tr>
<tr><td class="td6_1"><span class="help" title="字母或数字组成，按标识区分选项是否属同一组">类型标识</span></td><td class="td6_2"><input type="text" class="inp" id="post_types" value="'.$data['types'].'"></td></tr>
<tr><td class="td6_1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td6_2"><input type="number" class="inp" id="post_sort" value="'.$data['sort'].'"></td></tr>
</table></div>';