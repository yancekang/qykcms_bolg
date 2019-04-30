<?php
$lab=db_getshow('label','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$lab){
	$lab=array(
		'id'=>0,
		'title'=>'',
		'edtype'=>2,
		'sort'=>1,
		'content'=>'',
		'tag'=>'自动生成'
		);
}else{
	$lab['tag']=setup_prefix.'label='.$lab['dataid'].setup_suffix;
	if($lab['edtype']==1){
		//$lab['content']=str_replace(PHP_EOL,'------',$lab['content']);
		$lab['content']=str_replace(array("\r\n","\n","\r"),'\r\n',$lab['content']);
		}
	}
$res='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'label_show\',1,\'win_show_label\');return false" hidefocus="true">基本信息</a>
'.goif($lab['id'],'<a href="javascript:" class="out" onclick="ajaxcata(this,\'label_show\',2,\'win_show_label\');return false" hidefocus="true">标签内容</a>').'
</div>
<table id="label_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">标签名称</td><td class="td2"><input type="text" class="inp" id="post_title" value="'.$lab['title'].'"></td><td class="td1">编辑模式</td><td class="td2"><select id="post_edtype"><option value="1">纯文本框</option><option value="2"'.goif($lab['edtype']==2,' selected').'>HTML编辑器</option></select></td></tr>
<tr><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="post_sort" value="'.$lab['sort'].'"></td><td class="td1"><span class="help" title="在模板页面中插入标签代码，即可显示标签中的内容">调用标签</span></td><td class="td2"><input type="text" class="inp_no" value="'.$lab['tag'].'" readonly></td></tr>
</table>
<div id="label_show_2" class="ajax_content" style="display:none">'.goif($lab['id']&&$lab['edtype']==1,'<textarea class="tex" id="post_content">'.$lab['content'].'</textarea>','<script id="post_content" type="text/plain" style="display:none">'.getreset_admin($lab['content']).'</script>').'</div>
</div>';