<?php
$other=arg('other','post','url');
$data=db_getshow('config','*','webid='.goif($tcz['desc']=='config_args_theme',$website['webid'],1).' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'id'=>0,
		'cata'=>goif($tcz['desc']=='config_args_theme','theme'),
		'title'=>'',
		'varname'=>'',
		'vartype'=>1,
		'varval'=>'',
		'infotype'=>'inp',
		'infosel'=>'',
		'content'=>'',
		'contype'=>1,
		'isedit'=>0,
		'isview'=>0,
		'sort'=>1
		);
	}
$cata_option='<select id="post_cata" tag="postinp"'.goif($tcz['desc']=='config_args_theme',' isedit="no"').'><option value="web">网站配置</option>';
$setup_cata=explode(',',setup_am_setup_cata);
foreach($setup_cata as $opt){
	$opt2=explode('|',$opt);
	$cata_option.='<option value="'.$opt2[0].'"'.goif($opt2[0]==$data['cata'],' selected').'>'.$opt2[1].'</option>';
	}
$cata_option.='<option value="basic"'.goif($data['cata']=='basic',' selected').'>基础配置</option></select>';
$res='<div class="win_ajax ajax_user"><div class="ui_point">温馨提示：'.goif($tcz['desc']=='config_args','本功能主要针对需要对系统进行二次开发的技术人员，不建议更改系统原有的参数结构，可能会出现系统异常、无法升级等问题。在此处修改、新增参数默认仅对新增站点有效（基础配置参数除外），如希望现有的全部站点均按此参数结构，请点击列表右上方的“同步站点”按钮。','本功能主要为当前的主题增加可直接调用的参数，如您不了解本功能请勿操作，不当的修改可能导致当前网站主题异常').'</div><table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1"><span class="help" title="参数对应的设置栏目">参数归类</span></td><td class="td2">'.$cata_option.'</td><td class="td1"><span class="help" title="1、系统参数不能直接删除，需设置为否才可删除<br>2、每次升级，系统参数结构都将重新同步或还原，非系统参数不受影响">系统参数</span></td><td class="td2"><select id="post_contype" tag="postinp"><option value="0">是</option><option value="1"'.goif($data['contype'],' selected').'>否</option></select></td></tr>
<tr><td class="td1"><span class="help" title="简短的标题，表达此参数的意义，通常在8个汉字内">标 题</span></td><td class="td2"><input type="text" class="inp" id="post_title" value="'.$data['title'].'"></td><td class="td1"><span class="help" title="由字母及数字下划线组成，并且必须以字母开头，具有唯一性的">常量名称</span></td><td class="td2"><input type="text" id="post_varname" value="'.$data['varname'].'" '.goif(!$data['contype']&&$data['id'],'class="inp_no" readonly','class="inp"').'></td></tr>
<tr><td class="td1">值类型</td><td class="td2"><select tag="postinp" id="post_vartype"><option value=1>文本（任意字符）</option><option value=2'.goif($data['vartype']==2,' selected').'>纯数字（不支持小数点）</option><option value=3'.goif($data['vartype']==3,' selected').'>布尔类型（true、false）、函数</option></select></td><td class="td1">参数初始值</td><td class="td2"><input type="'.goif($data['infotype']=='pass','password','text').'" id="post_varval" value="'.$data['varval'].'" '.goif($tcz['desc']=='config_args_theme'&&$data['id'],'class="inp_no" readonly','class="inp"').'></td></tr>
<tr><td class="td1">输入方式</td><td class="td2"><select tag="postinp" id="post_infotype"><option value="inp">单行输入框</option><option value="text"'.goif($data['infotype']=='text',' selected').'>多行输入框</option><option value="pass"'.goif($data['infotype']=='pass',' selected').'>密码输入框</option><option value="select"'.goif($data['infotype']=='select',' selected').'>下拉菜单</option><option value="upload"'.goif($data['infotype']=='upload',' selected').'>上传控件</option></select></td><td class="td1"><span class="help" title="当输入方式为下拉菜单时，这里可设置下拉菜单选项<br>格式：值|选项文本,值|选项文本...（值和选项文本相同时，可忽略选项文本）<br>示例：1|中国,2|美国,3|韩国<br>示例：100,200,300,400,500">下拉选项</span></td><td class="td2"><input type="text" id="post_infosel" value="'.$data['infosel'].'" '.goif($data['infotype']!='select','class="inp_no" readonly','class="inp"').'></td></tr>
<tr><td class="td1"><span class="help" title="数字越<span class=\'blue\'>小</span>，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="post_sort" value="'.$data['sort'].'"></td><td class="td1"><span class="">参数状态</span></td><td class="td2"><select tag="postinp" id="post_isedit" sw=126><option value=0>状态：正常</option><option value=1'.goif($data['isedit']==1,' selected').'>状态：禁用</option></select><select tag="postinp" id="post_isview" sw=126 ml=10><option value=0>显示：正常</option><option value=1'.goif($data['isview']==1,' selected').'>显示：隐藏</option></select></td></tr>
<tr><td class="td1"><span class="help" title="详细说明该参数的用途，不需要可以为空">说明描述</span></td><td class="td3" colspan=3><input style="width:701px" type="text" class="inp" id="post_content" value="'.$data['content'].'"></td></tr>
</table></div>';