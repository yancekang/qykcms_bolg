<?php
$languages=arg('lang','post','url');
if($languages=='')ajaxreturn(1,'未知的语言版本');
$mod=db_getshow('module','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$mod){
	$mod=array(
		'id'=>0,
		'languages'=>$languages,
		'bcat'=>0,
		'scat'=>0,
		'modtype'=>1,
		'title'=>'',
		'title_en'=>'',
		'keyword'=>'',
		'description'=>'',
		'cover'=>'',
		'sort'=>1,
		'mark'=>'',
		'modfile'=>'',
		'showfile'=>'',
		'linkurl'=>'',
		'menutype'=>0,
		'additional'=>'',
		'computer'=>0,
		'mobile'=>0,
		'pagesize'=>12,
		'isok'=>0
		);
	}
$themefolder=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_theme_folder"');
if(!$themefolder)ajaxreturn(1,'缺少主题模板文件夹名称参数');
$path='../'.setup_webfolder.$website['webid'].'/'.$mod['languages'].'/'.$themefolder.'/';
if(!is_dir($path))ajaxreturn(1,'模板路径不存在，请确定是否已安装主题');
$mod_bcat_select='<select id="mod_bcat">';
if($mod['menutype']==0||$mod['menutype']==999){
	$mod_bcat_select.='<option value="0_0">= 无上级模块 =</option>';
}else{
	$list=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and languages="'.$mod['languages'].'" and (modtype<8 or modtype>10) and menutype='.($mod['menutype']-1).' order by bcat asc,sort asc,classid asc');
	foreach($list as $k=>$v){
		$title=$v['title'];
		if($v['menutype']){
			$title_s=db_getone('module','title','webid='.$website['webid'].' and classid='.$v['bcat']);
			$title=$title_s.' - '.$title;
			}
		$mod_bcat_select.='<option value="'.$v['classid'].'_'.$v['modtype'].'"'.goif($mod['bcat']==$v['classid']||$mod['scat']==$v['classid'],' selected').'>'.$title.'</option>';
		}
	}
$mod_bcat_select.='</select>';
$catalog='<option value="0">网站导航</option>';
if($mod['bcat']||$mod['scat']||!$mod['id']){
	for($i=1;$i<=2;$i++){
		$catalog.='<option value="'.$i.'"'.goif($mod['menutype']==$i,' selected').'>'.getchinesenum($i).'级分类</option>';
		}
	}
$catalog.='<option value="999"'.goif($mod['menutype']==999,' selected').'>其它栏目</option>';
$modfile_sel='<select id="mod_modfile" tag="postinp" sw=126'.goif($mod['modtype']==9,' isedit="no"').'><option value="">栏目页：自动</option>';
$showfile_sel='<select id="mod_showfile" tag="postinp" sw=126 ml=10'.goif($mod['modtype']==1||$mod['modtype']>=8,' isedit="no"').'><option value="">内容页：自动</option>';
$mydir=dir($path);
while($file=$mydir->read()){
	if(is_dir($path.$file))continue;
	$file=iconv("gb2312","utf-8",$file);
	$modfile_sel.='<option value="'.$file.'"'.goif($file==$mod['modfile'],'selected').'>'.$file.'</option>';
	$showfile_sel.='<option value="'.$file.'"'.goif($file==$mod['showfile'],'selected').'>'.$file.'</option>';
	}
$mydir->close();
$modfile_sel.='</select>';
$modtypearr=getmoduletype($mod['modtype'],true);
if(!$mod['id']||$mod['modtype']>10){
	$moduser=db_getall('module_user','*','webid='.$website['webid'].' and isok=0');
	foreach($moduser as $mu){
		$modtypearr[$mu['dataid']]='自定义：'.$mu['title'];
		}
	}
$res='<div class="win_ajax ajax_user">
<div class="ui_point">温馨提示：栏目分类涉及网站分类与数据，如果设置与主题不兼容，可能导致网站异常，不熟悉系统请勿随意修改，建议联系技术人员操作</div>
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'module_show\',1,\'win_show_module\');return false" hidefocus="true">基本信息</a>
<a href="javascript:" class="out" onclick="ajaxcata(this,\'module_show\',2,\'win_show_module\');return false" hidefocus="true">高级选项</a>
</div>
<table id="module_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">栏目名称</td><td class="td2"><input type="hidden" id="mod_lang" value="'.$mod['languages'].'"><input type="text" class="inp" id="mod_title" value="'.$mod['title'].'"></td><td class="td1">启用状态</td><td class="td2"><select id="mod_isok"><option value="0">启用</option><option value="1"'.goif($mod['isok']==1,' selected').'>不启用</option></select></td></tr>
<tr><td class="td1">位置级别</td><td class="td2"><select id="mod_menutype">'.$catalog.'</select></td><td class="td1">上一级</td><td class="td2">'.$mod_bcat_select.'</td></tr>
<tr><td class="td1"><span class="help" title="1、单页内容：适合用于创建公司简单、联系方式等<br>2、ＸＸ列表：用于创建可分页的列表<br>3、独立页面：独立的单个页面，不同于“单页内容”，比如网站首页，或任意自行设计的页面<br>4、指定链接：直接链接到一个指定的地址<br>5、自定义，可根据需要设置字段，适合制作更加多元化的栏目<br>6、选择为自定义模块的栏目，以后无法随意转换模块类型">模块类型</span></td><td class="td2">'.selecthtml('mod_modtype',$modtypearr,$mod['modtype']).'<input type="button" class="inp_btn" value="自定义" onclick="PZ.win({id:\'win_show_module\',log:\'close\',callback:function(){openshow({log:\'module_user\'})}})"></td><td class="td1"><span class="help" title="仅对数据类型为外部链接的栏目有效">链接地址</span></td><td class="td2"><input type="text" class="inp" id="mod_linkurl" value="'.$mod['linkurl'].'"></td></tr>
<tr><td class="td1"><span class="help" title="网站上展示每页显示几条记录">每页列表数</span></td><td class="td2"><input type="text" class="inp" id="mod_pagesize" value="'.$mod['pagesize'].'"></td><td class="td1"><span class="help" title="模块附加内容标签，用 | 分隔，不超过4个">附加内容标签</span></td><td class="td2"><input type="text" class="inp" id="mod_additional" value="'.$mod['additional'].'"></td></tr>
<tr><td class="td1"><span class="help" title="任意字母数字，通常是模块名的英文或拼音">唯一标识</span></td><td class="td2"><input type="text" class="inp" id="mod_mark" value="'.$mod['mark'].'"></td><td class="td1"><span class="help" title="数字越<span class=\'blue\'>小</span>，排序越靠前">排 序</span></td><td class="td2"><input type="text" class="inp" id="mod_sort" value="'.$mod['sort'].'"></td></tr>
</table>
<table id="module_show_2" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1"><span class="help" title="该栏目下的列表页（即栏目页）、内容页面所调用的主题模板文件，第一个选项为栏目页模板文件，第二个选项为内容页模板文件<br>1、如果数据类型为单页内容或独立页面，则仅设置第一个栏目页即可<br>2、如果数据类型为外部链接，这里的模板文件设置无效<br>3、当选择为自动时，将继承上一级栏目的设置，如无上一级栏目，则栏目页为：标识.html，内容页为：标识_show.html">模板文件</span></td><td class="td2">'.$modfile_sel.$showfile_sel.'</td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="mod_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$mod['computer'],' checked').'><input class="inp_box" id="mod_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$mod['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="部分主题可能会引用该参数，如果主题中没有调用留空即可">副标题</span></td><td class="td2"><input type="text" class="inp" id="mod_title_en" value="'.$mod['title_en'].'"></td><td class="td1"><span class="help" title="如果主题中没有引用，则无需上传">封面图标</span></td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'mod_cover\'})" class="inp_up" id="mod_cover">'.$mod['cover'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成，通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="mod_description">'.$mod['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置，多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea class="inp_tex" id="mod_keyword">'.$mod['keyword'].'</textarea></td></tr>
</table></div>';