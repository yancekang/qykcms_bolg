<?php
$art=db_getshow('article','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$art){
	$art=array(
		'id'=>0,
		'showfile'=>'',
		'parameters'=>'',
		'title'=>'',
		'source'=>'本站',
		'description'=>'',
		'keyword'=>'',
		'isok'=>0,
		'bcat'=>$tcz['bcat'],
		'scat'=>0,
		'lcat'=>0,
		'special_id'=>'',
		'sort'=>1,
		'star'=>0,
		'cover'=>'',
		'linkurl'=>'',
		'time_top'=>0,
		'time_color'=>'',
		'time_add'=>time(),
		'content'=>'',
		'content1'=>'',
		'content2'=>'',
		'content3'=>'',
		'content4'=>'',
		'computer'=>0,
		'mobile'=>0,
		'mood_1'=>0,
		'mood_2'=>0,
		'mood_3'=>0,
		'mood_4'=>0,
		'mood_5'=>0
		);
	}
$time_top=date('Y-m-d H:i:s',strtotime(date('Y-m-d 18:00:00').'+1 day'));
$additional='';
$modtype=0;
$mark='';
if($art['lcat']){
	$lmod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$art['lcat']);
	if($lmod){
		$additional=$lmod['additional'];
		$modtype=$lmod['modtype'];
		$mark=$lmod['mark'];
		}
	}
if($art['scat']){
	$smod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$art['scat']);
	if($smod){
		if($additional=='')$additional=$smod['additional'];
		if(!$modtype)$modtype=$smod['modtype'];
		if($mark=='')$mark=$smod['mark'];
		}
	}
if($art['bcat']){
	$bmod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$art['bcat']);
	if($bmod){
		if($modtype==1&&$art['title']==''&&count($module)==0)$art['title']=$bmod['title'];
		if($additional=='')$additional=$bmod['additional'];
		if(!$modtype)$modtype=$bmod['modtype'];
		if($mark=='')$mark=$bmod['mark'];
		}
	}
$themefolder=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_theme_folder"');
if(!$themefolder)ajaxreturn(1,'缺少主题模板文件夹名称参数');
$path='../'.setup_webfolder.$website['webid'].'/'.$bmod['languages'].'/'.$themefolder.'/';
if(!is_dir($path))ajaxreturn(1,'模板路径不存在，请确定是否已安装主题');
//选择模板文件
$showfile_sel='<select id="art_showfile" tag="postinp">
<option value="">自动</option>';
$mydir=dir($path);
while($file=$mydir->read()){
	if(is_dir($path.$file))continue;
	$file=iconv("gb2312","utf-8",$file);
	$showfile_sel.='<option value="'.$file.'"'.goif($file==$art['showfile'],'selected').'>'.$file.'</option>';
	}
$mydir->close();
$showfile_sel.='</select>';
//获取分类
$modlist=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$art['bcat'].' and menutype=1 order by sort asc,classid asc');
$module_select='<select id="art_cata" tag="postinp"'.goif($modtype==1&&$art['id'],' isedit="no"').'>';
if($modtype>1)$module_select.='<option value="'.$art['bcat'].'_0_0">未归类</option>';
if($modlist){
	foreach($modlist as $k=>$val){
		$module_select.='<option value="'.$art['bcat'].'_'.$val['classid'].'_0"'.goif($art['scat']==$val['classid']&&!$art['lcat'],' selected').'>'.$val['title'].'</option>';
		$smod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$art['bcat'].' and scat='.$val['classid'].' order by sort asc,classid asc');
		if($smod){
			foreach($smod as $kk=>$sval){
				$module_select.='<option value="'.$art['bcat'].'_'.$val['classid'].'_'.$sval['classid'].'"'.goif($art['lcat']==$sval['classid'],' selected').'>├─　'.$sval['title'].'</option>';
				}
			}
		}
}else if($modtype==1){
	$module_select.='<option value="'.$art['bcat'].'_0_0">未归类</option>';
	}
$module_select.='</select>';
$res='<input type="hidden" value="'.$bmod['classid'].'" id="post_bcat"><input type="hidden" value="'.$modtype.'" id="art_modtype">';
switch($modtype){
	case 1:
		$res.='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_article\');return false" hidefocus="true">内容介绍</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',3,\'win_show_article\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td><td class="td1">显示状态</td><td class="td2"><select id="art_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></tr>
<tr><td class="td1">页面标题</td><td class="td2"><input type="text" class="inp_no" id="art_title" value="'.$art['title'].'" placeholder="自动，默认为当前分类名" readonly></td><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="art_sort" value="'.$art['sort'].'"></td></tr>
</td></tr></table>
<div id="article_show_2" class="ajax_content" style="display:none"><script id="art_content" type="text/plain" style="display:none">'.getreset_admin($art['content']).'</script></div>
<table id="article_show_3" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="art_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="当前记录内容页面所调用的主题模板文件，<br>1、当选择为自动时，将逐级继承所在栏目的设置，如果所在栏目均为自动，则为：标识_show.html（标识为所在栏目的唯一标识）">模板文件</span></td><td class="td2">'.$showfile_sel.'</td></tr>
<tr><td class="td1"><span class="help" title="用于直接跳转到指定的地址">外部链接</span></td><td class="td2"><input type="text" class="inp" id="art_linkurl" value="'.$art['linkurl'].'"></td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="art_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="art_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
<tr><td class="td1">所属专题</td><td class="td2"><input style="width:192px" type="text" class="inp" id="art_special_id" value="'.$art['special_id'].'" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'art_special_id\'})"></td><td class="td1">赞一下统计</td><td class="td2"><input style="width:50px" min=0 type="number" class="inp" id="art_mood_1" value="'.$art['mood_1'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_2" value="'.$art['mood_2'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_3" value="'.$art['mood_3'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_4" value="'.$art['mood_4'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_5" value="'.$art['mood_5'].'"></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_description">'.$art['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_keyword">'.$art['keyword'].'</textarea></td></tr>
</table>
</div>';
	break;
	case 2:
		$tagarr=explode('|',$additional.'|||');
		$res.='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_article\');return false" hidefocus="true">内容介绍</a>
'.goif($tagarr[0]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',3,\'win_show_article\');return false" hidefocus="true">'.$tagarr[0].'</a>')
.goif($tagarr[1]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',4,\'win_show_article\');return false" hidefocus="true">'.$tagarr[1].'</a>')
.goif($tagarr[2]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',5,\'win_show_article\');return false" hidefocus="true">'.$tagarr[2].'</a>')
.goif($tagarr[3]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',6,\'win_show_article\');return false" hidefocus="true">'.$tagarr[3].'</a>').'
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',7,\'win_show_article\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">文章标题</td><td class="td2"><input type="text" class="inp" id="art_title" value="'.$art['title'].'"></td><td class="td1">显示状态</td><td class="td2"><select id="art_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">封面图片</td><td class="td2"><textarea onfocus="this.blur()" onclick="uploadimg({log:\'start\',types:\'art_cover\'})" class="inp_up" id="art_cover" placeholder="单击开始上传">'.$art['cover'].'</textarea></td><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td></tr>
<tr><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td5" colspan=3><input type="text" class="inp" id="art_sort" value="'.$art['sort'].'"></td></tr>
</table>
<div id="article_show_2" class="ajax_content" style="display:none"><script id="art_content" type="text/plain" style="display:none">'.getreset_admin($art['content']).'</script></div>
<div id="article_show_3" class="ajax_content" style="display:none"><script id="art_content_1" type="text/plain" style="display:none">'.getreset_admin($art['content1']).'</script></div>
<div id="article_show_4" class="ajax_content" style="display:none"><script id="art_content_2" type="text/plain" style="display:none">'.getreset_admin($art['content2']).'</script></div>
<div id="article_show_5" class="ajax_content" style="display:none"><script id="art_content_3" type="text/plain" style="display:none">'.getreset_admin($art['content3']).'</script></div>
<div id="article_show_6" class="ajax_content" style="display:none"><script id="art_content_4" type="text/plain" style="display:none">'.getreset_admin($art['content4']).'</script></div>
<table id="article_show_7" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="art_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="art_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($art['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($art['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($art['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($art['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($art['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">置顶状态</td><td class="td2"><select id="art_time_top_check"><option value="no">关闭置顶</option><option value="ok"'.goif($art['time_top']>time(),' selected').'>开启置顶</option></select><input style="width:140px;margin-left:10px" type="text" tag="time" id="art_time_top" '.goif($art['time_top']>time(),'class="inp" value="'.date('Y-m-d H:i:s',$art['time_top']).'"','class="inp_no" value="'.$time_top.'"').' readonly></td><td class="td1">标题颜色</td><td class="td2"><select id="art_time_color" color="yes"><option value="">默认颜色</option><option value="#ff0000"'.goif($art['time_color']=='#ff0000',' selected').'>红色</option><option value="#0000ff"'.goif($art['time_color']=='#0000ff',' selected').'>宝蓝</option><option value="#ff00ff"'.goif($art['time_color']=='#ff00ff',' selected').'>粉色</option><option value="#009900"'.goif($art['time_color']=='#009900',' selected').'>草绿</option><option value="#ff6600"'.goif($art['time_color']=='#ff6600',' selected').'>橙色</option><option value="#0066ff"'.goif($art['time_color']=='#25acd9',' selected').'>天空蓝</option><option value="#663399"'.goif($art['time_color']=='#663399',' selected').'>紫色</option></select></td></tr>
<tr><td class="td1"><span class="help" title="用于直接跳转到指定的地址">外部链接</span></td><td class="td2"><input type="text" class="inp" id="art_linkurl" value="'.$art['linkurl'].'"></td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="art_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="art_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="如果是转载，可以是转载网页地址或第三方网站名称">信息来源</span></td><td class="td2"><input type="text" class="inp" id="art_source" value="'.$art['source'].'"></td><td class="td1">模板文件</td><td class="td2">'.$showfile_sel.'</td></tr>
<tr><td class="td1">所属专题</td><td class="td2"><input style="width:192px" type="text" class="inp" id="art_special_id" value="'.$art['special_id'].'" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'art_special_id\'})"></td><td class="td1">赞一下统计</td><td class="td2"><input style="width:50px" min=0 type="number" class="inp" id="art_mood_1" value="'.$art['mood_1'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_2" value="'.$art['mood_2'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_3" value="'.$art['mood_3'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_4" value="'.$art['mood_4'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_5" value="'.$art['mood_5'].'"></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_description">'.$art['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_keyword">'.$art['keyword'].'</textarea></td></tr>
</table>
</div>';
	break;
	case 3:
		if($art['parameters']=='')$art['parameters']='不详';
		$tagarr=explode('|',$additional.'|||');
		$res.='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_article\');return false" hidefocus="true">内容介绍</a>
'.goif($tagarr[0]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',3,\'win_show_article\');return false" hidefocus="true">'.$tagarr[0].'</a>')
.goif($tagarr[1]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',4,\'win_show_article\');return false" hidefocus="true">'.$tagarr[1].'</a>')
.goif($tagarr[2]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',5,\'win_show_article\');return false" hidefocus="true">'.$tagarr[2].'</a>')
.goif($tagarr[3]!='','<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',6,\'win_show_article\');return false" hidefocus="true">'.$tagarr[3].'</a>').'
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',7,\'win_show_article\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">产品名称</td><td class="td2"><input type="text" class="inp" id="art_title" value="'.$art['title'].'"></td><td class="td1">显示状态</td><td class="td2"><select id="art_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">封面图片</td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'art_cover\'})" class="inp_up" id="art_cover">'.$art['cover'].'</textarea></td><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td></tr>
<tr><td class="td1"><span class="help" title="如果网站页面中没有显示价格，则此项可以留空或默认即可">产品价格</span></td><td class="td2"><input type="text" class="inp" id="art_parameters" value="'.$art['parameters'].'"></td><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="art_sort" value="'.$art['sort'].'"></td></tr>
</table>
<div id="article_show_2" class="ajax_content" style="display:none"><script id="art_content" type="text/plain" style="display:none">'.getreset_admin($art['content']).'</script></div>
<div id="article_show_3" class="ajax_content" style="display:none"><script id="art_content_1" type="text/plain" style="display:none">'.getreset_admin($art['content1']).'</script></div>
<div id="article_show_4" class="ajax_content" style="display:none"><script id="art_content_2" type="text/plain" style="display:none">'.getreset_admin($art['content2']).'</script></div>
<div id="article_show_5" class="ajax_content" style="display:none"><script id="art_content_3" type="text/plain" style="display:none">'.getreset_admin($art['content3']).'</script></div>
<div id="article_show_6" class="ajax_content" style="display:none"><script id="art_content_4" type="text/plain" style="display:none">'.getreset_admin($art['content4']).'</script></div>
<table id="article_show_7" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="art_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="art_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($art['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($art['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($art['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($art['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($art['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">置顶状态</td><td class="td2"><select id="art_time_top_check"><option value="no">关闭置顶</option><option value="ok"'.goif($art['time_top']>time(),' selected').'>开启置顶</option></select><input style="width:140px;margin-left:10px" type="text" tag="time" id="art_time_top" '.goif($art['time_top']>time(),'class="inp" value="'.date('Y-m-d H:i:s',$art['time_top']).'"','class="inp_no" value="'.$time_top.'"').' readonly></td><td class="td1">标题颜色</td><td class="td2"><select id="art_time_color" color="yes"><option value="">默认颜色</option><option value="#ff0000"'.goif($art['time_color']=='#ff0000',' selected').'>红色</option><option value="#0000ff"'.goif($art['time_color']=='#0000ff',' selected').'>宝蓝</option><option value="#ff00ff"'.goif($art['time_color']=='#ff00ff',' selected').'>粉色</option><option value="#009900"'.goif($art['time_color']=='#009900',' selected').'>草绿</option><option value="#ff6600"'.goif($art['time_color']=='#ff6600',' selected').'>橙色</option><option value="#0066ff"'.goif($art['time_color']=='#25acd9',' selected').'>天空蓝</option><option value="#663399"'.goif($art['time_color']=='#663399',' selected').'>紫色</option></select></td></tr>
<tr><td class="td1"><span class="help" title="用于直接跳转到指定的地址">外部链接</span></td><td class="td2"><input type="text" class="inp" id="art_linkurl" value="'.$art['linkurl'].'"></td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="art_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="art_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="如果是转载，可以是转载网页地址或第三方网站名称">信息来源</span></td><td class="td2"><input type="text" class="inp" id="art_source" value="'.$art['source'].'"></td><td class="td1">模板文件</td><td class="td2">'.$showfile_sel.'</td></tr>
<tr><td class="td1">所属专题</td><td class="td2"><input style="width:192px" type="text" class="inp" id="art_special_id" value="'.$art['special_id'].'" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'art_special_id\'})"></td><td class="td1">赞一下统计</td><td class="td2"><input style="width:50px" min=0 type="number" class="inp" id="art_mood_1" value="'.$art['mood_1'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_2" value="'.$art['mood_2'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_3" value="'.$art['mood_3'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_4" value="'.$art['mood_4'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_5" value="'.$art['mood_5'].'"></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成，通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_description">'.$art['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置，多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_keyword">'.$art['keyword'].'</textarea></td></tr>
</table>
</div>';
	break;
	case 4:
		$res.='<div class="win_ajax ajax_user"><div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_article\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">图片标题</td><td class="td2"><input type="text" class="inp" id="art_title" value="'.$art['title'].'"></td><td class="td1">显示状态</td><td class="td2"><select id="art_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">上传图片</td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'art_cover\'})" class="inp_up" id="art_cover">'.$art['cover'].'</textarea></td><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td></tr>
<tr><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td5" colspan=3><input type="text" class="inp" id="art_sort" value="'.$art['sort'].'"></td></tr>
<tr><td class="td1">图片描述</td><td class="td5" colspan=3><textarea style="height:120px" class="inp_tex" id="art_content2">'.$art['content'].'</textarea></td></tr>
</table>
<table id="article_show_2" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="art_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="art_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($art['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($art['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($art['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($art['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($art['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">置顶状态</td><td class="td2"><select id="art_time_top_check"><option value="no">关闭置顶</option><option value="ok"'.goif($art['time_top']>time(),' selected').'>开启置顶</option></select><input style="width:140px;margin-left:10px" type="text" tag="time" id="art_time_top" '.goif($art['time_top']>time(),'class="inp" value="'.date('Y-m-d H:i:s',$art['time_top']).'"','class="inp_no" value="'.$time_top.'"').' readonly></td><td class="td1">标题颜色</td><td class="td2"><select id="art_time_color" color="yes"><option value="">默认颜色</option><option value="#ff0000"'.goif($art['time_color']=='#ff0000',' selected').'>红色</option><option value="#0000ff"'.goif($art['time_color']=='#0000ff',' selected').'>宝蓝</option><option value="#ff00ff"'.goif($art['time_color']=='#ff00ff',' selected').'>粉色</option><option value="#009900"'.goif($art['time_color']=='#009900',' selected').'>草绿</option><option value="#ff6600"'.goif($art['time_color']=='#ff6600',' selected').'>橙色</option><option value="#0066ff"'.goif($art['time_color']=='#25acd9',' selected').'>天空蓝</option><option value="#663399"'.goif($art['time_color']=='#663399',' selected').'>紫色</option></select></td></tr>
<tr><td class="td1"><span class="help" title="用于直接跳转到指定的地址">外部链接</span></td><td class="td2"><input type="text" class="inp" id="art_linkurl" value="'.$art['linkurl'].'"></td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="art_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="art_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="如果是转载，可以是转载网页地址或第三方网站名称">信息来源</span></td><td class="td2"><input type="text" class="inp" id="art_source" value="'.$art['source'].'"></td><td class="td1">模板文件</td><td class="td2">'.$showfile_sel.'</td></tr>
<tr><td class="td1">所属专题</td><td class="td2"><input style="width:192px" type="text" class="inp" id="art_special_id" value="'.$art['special_id'].'" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'art_special_id\'})"></td><td class="td1">赞一下统计</td><td class="td2"><input style="width:50px" min=0 type="number" class="inp" id="art_mood_1" value="'.$art['mood_1'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_2" value="'.$art['mood_2'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_3" value="'.$art['mood_3'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_4" value="'.$art['mood_4'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_5" value="'.$art['mood_5'].'"></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成，通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_description">'.$art['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置，多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_keyword">'.$art['keyword'].'</textarea></td></tr>
</table></div>';
	break;
	case 5:
		$art['content']=str_replace(array("\r\n","\n","\r"),'\r\n',$art['content']);
		$res.='<div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_article\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">标题名称</td><td class="td2"><input type="text" class="inp" id="art_title" value="'.$art['title'].'"></td><td class="td1">显示状态</td><td class="td2"><select id="art_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="art_sort" value="'.$art['sort'].'"></td></tr>
<tr><td class="td1">简短内容</td><td class="td5" colspan=3><textarea style="height:120px" class="inp_tex" id="art_content2">'.$art['content'].'</textarea></td></tr>
</table>
<table id="article_show_2" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="art_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="art_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($art['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($art['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($art['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($art['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($art['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">置顶状态</td><td class="td2"><select id="art_time_top_check"><option value="no">关闭置顶</option><option value="ok"'.goif($art['time_top']>time(),' selected').'>开启置顶</option></select><input style="width:140px;margin-left:10px" type="text" tag="time" id="art_time_top" '.goif($art['time_top']>time(),'class="inp" value="'.date('Y-m-d H:i:s',$art['time_top']).'"','class="inp_no" value="'.$time_top.'"').' readonly></td><td class="td1">标题颜色</td><td class="td2"><select id="art_time_color" color="yes"><option value="">默认颜色</option><option value="#ff0000"'.goif($art['time_color']=='#ff0000',' selected').'>红色</option><option value="#0000ff"'.goif($art['time_color']=='#0000ff',' selected').'>宝蓝</option><option value="#ff00ff"'.goif($art['time_color']=='#ff00ff',' selected').'>粉色</option><option value="#009900"'.goif($art['time_color']=='#009900',' selected').'>草绿</option><option value="#ff6600"'.goif($art['time_color']=='#ff6600',' selected').'>橙色</option><option value="#0066ff"'.goif($art['time_color']=='#25acd9',' selected').'>天空蓝</option><option value="#663399"'.goif($art['time_color']=='#663399',' selected').'>紫色</option></select></td></tr>
<tr><td class="td1"><span class="help" title="用于直接跳转到指定的地址">外部链接</span></td><td class="td2"><input type="text" class="inp" id="art_linkurl" value="'.$art['linkurl'].'"></td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="art_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="art_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="如果是转载，可以是转载网页地址或第三方网站名称">信息来源</span></td><td class="td2"><input type="text" class="inp" id="art_source" value="'.$art['source'].'"></td><td class="td1">模板文件</td><td class="td2">'.$showfile_sel.'</td></tr>
<tr><td class="td1">所属专题</td><td class="td2"><input style="width:192px" type="text" class="inp" id="art_special_id" value="'.$art['special_id'].'" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'art_special_id\'})"></td><td class="td1">赞一下统计</td><td class="td2"><input style="width:50px" min=0 type="number" class="inp" id="art_mood_1" value="'.$art['mood_1'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_2" value="'.$art['mood_2'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_3" value="'.$art['mood_3'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_4" value="'.$art['mood_4'].'"><input min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="art_mood_5" value="'.$art['mood_5'].'"></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成，通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_description">'.$art['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置，多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="art_keyword">'.$art['keyword'].'</textarea></td></tr>
</table>
</div>';
	break;
	case 6:
		$res.='<div class="win_ajax ajax_user"><div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\',2,\'win_show_article\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">链接名称</td><td class="td2"><input type="text" class="inp" id="art_title" value="'.$art['title'].'"></td><td class="td1">显示状态</td><td class="td2"><select id="art_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">链接地址</td><td class="td2"><input type="text" class="inp" id="art_linkurl" value="'.$art['linkurl'].'"></td><td class="td1"><span class="help" title="如果网站页面中没有显示图标，可以不用上传">图标LOGO</span></td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'art_cover\'})" class="inp_up" id="art_cover">'.$art['cover'].'</textarea></td></tr>
<tr><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td><td class="td1"><span class="help" title="数字越大，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="art_sort" value="'.$art['sort'].'"></td></tr>
</table>
<table id="article_show_2" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="art_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="art_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($art['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($art['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($art['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($art['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($art['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">置顶状态</td><td class="td2"><select id="art_time_top_check"><option value="no">关闭置顶</option><option value="ok"'.goif($art['time_top']>time(),' selected').'>开启置顶</option></select><input style="width:140px;margin-left:10px" type="text" tag="time" id="art_time_top" '.goif($art['time_top']>time(),'class="inp" value="'.date('Y-m-d H:i:s',$art['time_top']).'"','class="inp_no" value="'.$time_top.'"').' readonly></td><td class="td1">标题颜色</td><td class="td2"><select id="art_time_color" color="yes"><option value="">默认颜色</option><option value="#ff0000"'.goif($art['time_color']=='#ff0000',' selected').'>红色</option><option value="#0000ff"'.goif($art['time_color']=='#0000ff',' selected').'>宝蓝</option><option value="#ff00ff"'.goif($art['time_color']=='#ff00ff',' selected').'>粉色</option><option value="#009900"'.goif($art['time_color']=='#009900',' selected').'>草绿</option><option value="#ff6600"'.goif($art['time_color']=='#ff6600',' selected').'>橙色</option><option value="#0066ff"'.goif($art['time_color']=='#25acd9',' selected').'>天空蓝</option><option value="#663399"'.goif($art['time_color']=='#663399',' selected').'>紫色</option></select></td></tr>
<tr><td class="td1">支持平台</td><td class="td5" colspan=3><input class="inp_box" id="art_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="art_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
</table>
</div>';
	break;
	}