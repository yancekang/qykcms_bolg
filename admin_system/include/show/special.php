<?php
$languages=arg('lang','post','url');
$data=db_getshow('special','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'title'=>'',
		'description'=>'',
		'keyword'=>'',
		'isok'=>0,
		'bcat'=>0,
		'scat'=>0,
		'lcat'=>0,
		'sort'=>1,
		'star'=>0,
		'cover'=>'',
		'time_add'=>time(),
		'content'=>'',
		'computer'=>0,
		'mobile'=>0
		);
	}
//获取分类
$bmod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and (menutype=0 or menutype=999) and (modtype>10 or (modtype>1 and modtype<8)) order by sort asc,classid asc');
$module_select='<select id="post_cata" tag="postinp"><option value="0_0_0">未归类</option>';
if($bmod){
	foreach($bmod as $k=>$val){
		$module_select.='<option value="'.$val['classid'].'_0_0"'.goif($data['bcat']==$val['classid']&&!$data['scat']&&!$data['lcat'],' selected').'>★ '.$val['title'].'</option>';
		$smod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$val['classid'].' and scat=0 order by sort asc,classid asc');
		if($smod){
			foreach($smod as $sval){
				$module_select.='<option value="'.$val['classid'].'_'.$sval['classid'].'_0"'.goif($data['scat']==$sval['classid']&&!$data['lcat'],' selected').'>├─　'.$sval['title'].'</option>';
				$lmod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and scat='.$sval['classid'].' order by sort asc,classid asc');
				if($lmod){
					foreach($lmod as $lval){
						$module_select.='<option value="'.$val['classid'].'_'.$sval['classid'].'_'.$lval['classid'].'"'.goif($data['lcat']==$lval['classid'],' selected').'>　　├─　'.$lval['title'].'</option>';
						}
					}
				}
			}
		}
	}
$module_select.='</select>';
$res.='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_special\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_special\');return false" hidefocus="true">内容介绍</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',3,\'win_show_special\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1"><input type="hidden" id="post_languages" value="'.$languages.'" tag="postinp">专题名称</td><td class="td2"><input tag="postinp" type="text" class="inp" id="post_title" value="'.$data['title'].'"></td><td class="td1">显示状态</td><td class="td2"><select id="post_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($data['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">封面图片</td><td class="td2"><textarea onfocus="this.blur()" class="inp_up" tag="postinp" id="post_cover" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'cover\',obj:$(this)})">'.$data['cover'].'</textarea></td><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td></tr>
<tr><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td5" colspan=3><input tag="postinp" min=0 type="number" class="inp" id="post_sort" value="'.$data['sort'].'"></td></tr>
</table>
<div id="article_show_2" class="ajax_content" style="display:none"><script tag="editor" id="post_content" type="text/plain" style="display:none">'.getreset_admin($data['content']).'</script></div>
<table id="article_show_3" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="post_time_add" value="'.date('Y-m-d H:i:s',$data['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="post_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($data['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($data['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($data['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($data['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($data['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">支持平台</td><td class="td5" colspan=3><input class="inp_box" id="post_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$data['computer'],' checked').'><input class="inp_box" id="post_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$data['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" tag="postinp" id="post_description">'.$data['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea tag="postinp" class="inp_tex" id="post_keyword">'.$data['keyword'].'</textarea></td></tr>
</table>
</div>';