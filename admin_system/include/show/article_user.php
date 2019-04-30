<?php
$bmod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$tcz['bcat']);
if(!$bmod)ajaxreturn(1,'不存在的栏目');
$usermod=db_getshow('module_user','*','webid='.$website['webid'].' and dataid='.$bmod['modtype']);
if(!$usermod)ajaxreturn(1,'不存在的自定义模块');
$modtype=$bmod['modtype'];
$mark=$bmod['mark'];
$tname='article_'.$website['webid'].'_'.$modtype;
$art=db_getshow($tname,'*','webid='.$website['webid'].' and id='.$tcz['id']);
$conf=db_getall('module_field','*','webid='.$website['webid'].' and modid='.$modtype.' order by sort asc');
$conf_edinum=db_count('module_field','webid='.$website['webid'].' and modid='.$modtype.' and infotype="editor" order by sort asc')+0;
if(!$art){
	$art=array(
		'id'=>0,
		'showfile'=>'',
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
		'linkurl'=>'',
		'time_top'=>0,
		'time_color'=>'',
		'time_add'=>time(),
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
$themefolder=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_theme_folder"');
if(!$themefolder)ajaxreturn(1,'缺少主题模板文件夹名称参数');
$path='../'.setup_webfolder.$website['webid'].'/'.$bmod['languages'].'/'.$themefolder.'/';
if(!is_dir($path))ajaxreturn(1,'模板路径不存在，请确定是否已安装主题');
//选择模板文件
$showfile_sel='<select id="post_showfile" tag="postinp">
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
$module_select='<select id="post_cata" tag="postinp"'.goif($modtype==1&&$art['id'],' isedit="no"').'>';
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

$inplist='';
$catalist='';	//选项卡
$catacont='';
$catalist_xu=2;
$conf_len=count($conf)-$conf_edinum;
$k=0;
foreach($conf as $v){
	if(!$art['id'])$art[$v['varname']]=$v['varval'];
	if($v['infotype']=='editor'){
		$catalist.='<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\','.$catalist_xu.',\'win_show_article_user\');return false" hidefocus="true">'.$v['title'].'</a>';
		$catacont.='<div id="article_show_'.$catalist_xu.'" class="ajax_content" style="display:none"><script tag="editor" id="post_'.$v['varname'].'" type="text/plain" style="display:none">'.getreset_admin($art[$v['varname']]).'</script></div>';
		$catalist_xu++;
		continue;
		}
	$conf_len--;
	if($k==0)$inplist.='<tr>';
	else if($v['infotype']=='text'){
		$inplist.='<td class="td3" colspan=2>&nbsp;</td></tr><tr>';
		}
	$inplist.='<td class="td1"><span class="help" title="'.goif($v['content']!='',$v['content'].'<br>').'字段名：'.$v['varname'].'">'.$v['title'].'</span></td>';
	if($conf_len==0&&$k==0){
		$inplist.='<td class="td5" colspan=3>';
	}else if($v['infotype']=='text'){
		$k++;
		$inplist.='<td class="td5" colspan=3>';
	}else{
		$inplist.='<td class="td2">';
		}
	//$inplist.=goif($conf_len%2!=0&&$k==($conf_len-1),'<td class="td5" colspan=3>','<td class="td2">');
	switch($v['infotype']){
		case 'range':
		$inplist.='<input tag="postinp" id="post_'.$v['varname'].'" title="'.$art[$v['varname']].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'type="text" class="inp_no" readonly','type="range" class="inp_range"').' onchange="this.title=this.value">';
		break;
		case 'text':
			$inplist.='<textarea tag="postinp" id="post_'.$v['varname'].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp_tex"').'>'.$art[$v['varname']].'</textarea>';
		break;
		case 'pass':
			$inplist.='<input tag="postinp" type="password" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp"').'>';
		break;
		case 'up_cover':
			$inplist.='<textarea onfocus="this.blur()" class="inp_up" tag="postinp" id="post_'.$v['varname'].'" '.goif($v['isedit']==1,'placeholder="已禁用上传"',' placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'cover\',obj:$(this)})"').'>'.$art[$v['varname']].'</textarea>';
		break;
		case 'up_file':
			$inplist.='<textarea onfocus="this.blur()" class="inp_up" tag="postinp" id="post_'.$v['varname'].'" '.goif($v['isedit']==1,'placeholder="已禁用上传"',' placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'none\',obj:$(this),ctype:\'所有文件|*\'})"').'>'.$art[$v['varname']].'</textarea>';
		break;
		case 'select':
			$opt=explode(',',$v['infosel']);
			$inplist.='<select tag="postinp" id="post_'.$v['varname'].'" isedit="'.goif($v['isedit']==1,'no').'">';
			foreach($opt as $s){
				$stext=$s;
				$sval=$s;
				if(strstr($s,'|')){
					$sarr=explode('|',$s);
					$stext=$sarr[1];
					$sval=$sarr[0];
					}
				$inplist.='<option value="'.$sval.'"'.goif($art[$v['varname']]==$sval,' selected').'>'.$stext.'</option>';
				}
			$inplist.='</select>';
		break;
		case 'option':
			$opt=db_getall('select','*','webid='.$website['webid'].' and types="'.$v['infosel'].'"');
			$inplist.='<select tag="postinp" id="post_'.$v['varname'].'" isedit="'.goif($v['isedit']==1,'no').'">';
			foreach($opt as $s2){
				$s=$s2['title'];
				$stext=$s;
				$sval=$s;
				if(strstr($s,'|')){
					$sarr=explode('|',$s);
					$stext=$sarr[1];
					$sval=$sarr[0];
					}
				$inplist.='<option value="'.$sval.'"'.goif($art[$v['varname']]==$sval,' selected').'>'.$stext.'</option>';
				}
			$inplist.='</select>';
		break;
		case 'option':
			$opt=db_getall('select','*','webid='.$website['webid'].' and types="'.$v['infosel'].'"');
			$inplist.='<select tag="postinp" id="post_'.$v['varname'].'" isedit="'.goif($v['isedit']==1,'no').'">';
			foreach($opt as $s2){
				$s=$s2['title'];
				$stext=$s;
				$sval=$s;
				if(strstr($s,'|')){
					$sarr=explode('|',$s);
					$stext=$sarr[1];
					$sval=$sarr[0];
					}
				$inplist.='<option value="'.$sval.'"'.goif($art[$v['varname']]==$sval,' selected').'>'.$stext.'</option>';
				}
			$inplist.='</select>';
		break;
		case 'num':
			$inplist.='<input tag="postinp" type="number" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp"').'>';
		break;
		case 'time':
			$inplist.='<input tag="postinp" timetype="yyyy-MM-dd HH:mm" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" type="text"','class="inp" type="time"').' readonly>';
		break;
		case 'time-long2':
			$inplist.='<input tag="postinp" timetype="yyyy-MM-dd HH:mm:ss" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" type="text"','class="inp" type="time"').' readonly>';
		break;
		case 'date':
			$inplist.='<input tag="postinp" timetype="yyyy-MM-dd" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" type="text"','class="inp" type="time"').' readonly>';
		break;
		case 'date-short':
			$inplist.='<input tag="postinp" timetype="MM-dd" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" type="text"','class="inp" type="time"').' readonly>';
		break;
		case 'month':
			$inplist.='<input tag="postinp" timetype="yyyy-MM" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" type="text"','class="inp" type="time"').' readonly>';
		break;
		case 'option_more':
			$inplist.='<input tag="postinp" type="text" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" readonly>','class="inp" style="width:192px" readonly><input class="inp_btn" type="button" value="选择" onclick="optioncheck(\'post_'.$v['varname'].'\')">');
		break;
		default:	//case 'inp':
			$inplist.='<input tag="postinp" type="text" id="post_'.$v['varname'].'" value="'.$art[$v['varname']].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp"').'>';
		break;
		}
	$inplist.='</td>';
	if($k==1)$inplist.='</tr>';
	$k++;
	if($k>=2)$k=0;
	}
$res.='<input tag="postinp" type="hidden" value="'.$bmod['classid'].'" id="post_bcat"><div class="win_ajax ajax_user">
<div class="ajax_cata">
<a href="javascript:" class="on" onclick="ajaxcata(this,\'article_show\',1,\'win_show_article_user\');return false" hidefocus="true">基本信息</a>
'.$catalist.'<a href="javascript:" class="out" onclick="ajaxcata(this,\'article_show\','.$catalist_xu.',\'win_show_article_user\');return false" hidefocus="true">高级选项</a>
</div>
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1"><span class="help" title="字段名：title">文章标题</span></td><td class="td2"><input tag="postinp" type="text" class="inp" id="post_title" value="'.$art['title'].'"></td><td class="td1"><span class="help" title="字段名：status">显示状态</span></td><td class="td2"><select id="post_isok" tag="postinp"><option value="0">正常显示</option><option value="1"'.goif($art['isok'],' selected').'>不显示</option></select></td></tr>
<tr><td class="td1">所属分类</td><td class="td2">'.$module_select.'</td><td class="td1"><span class="help" title="排序数字，越大越靠前<br>字段名：sort">排 序</span></td><td class="td2"><input tag="postinp" type="number" class="inp" id="post_sort" value="'.$art['sort'].'"></td></tr>
'.$inplist.'</table>
'.$catacont.'<table id="article_show_'.$catalist_xu.'" class="ajax_tablist" cellpadding="12" cellspacing="1" style="display:none">
<tr><td class="td1">发布时间</td><td class="td2"><input tag="time" type="text" class="inp" id="post_time_add" value="'.date('Y-m-d H:i:s',$art['time_add']).'" readonly></td><td class="td1"><span class="help" title="通常用于特别关注或推荐的信息">星标等级</span></td><td class="td2"><select id="post_star" tag="postinp"><option value="0">0 - ☆☆☆☆☆</option><option value="1"'.goif($art['star']==1,' selected').'>1 - ★☆☆☆☆</option><option value="2"'.goif($art['star']==2,' selected').'>2 - ★★☆☆☆</option><option value="3"'.goif($art['star']==3,' selected').'>3 - ★★★☆☆</option><option value="4"'.goif($art['star']==4,' selected').'>4 - ★★★★☆</option><option value="5"'.goif($art['star']==5,' selected').'>5 - ★★★★★</option></select></td></tr>
<tr><td class="td1">置顶状态</td><td class="td2"><select id="post_time_top_check" tag="postinp" sw=112><option value="no">关闭置顶</option><option value="ok"'.goif($art['time_top']>time(),' selected').'>开启置顶</option></select><input style="width:140px;margin-left:10px" type="text" tag="time" id="post_time_top" '.goif($art['time_top']>time(),'class="inp" value="'.date('Y-m-d H:i:s',$art['time_top']).'"','class="inp_no" value="'.$time_top.'"').' readonly></td><td class="td1">标题颜色</td><td class="td2"><select id="post_time_color" color="yes" tag="postinp"><option value="">默认颜色</option><option value="#ff0000"'.goif($art['time_color']=='#ff0000',' selected').'>红色</option><option value="#0000ff"'.goif($art['time_color']=='#0000ff',' selected').'>宝蓝</option><option value="#ff00ff"'.goif($art['time_color']=='#ff00ff',' selected').'>粉色</option><option value="#009900"'.goif($art['time_color']=='#009900',' selected').'>草绿</option><option value="#ff6600"'.goif($art['time_color']=='#ff6600',' selected').'>橙色</option><option value="#0066ff"'.goif($art['time_color']=='#25acd9',' selected').'>天空蓝</option><option value="#663399"'.goif($art['time_color']=='#663399',' selected').'>紫色</option></select></td></tr>
<tr><td class="td1"><span class="help" title="用于直接跳转到指定的地址">外部链接</span></td><td class="td2"><input tag="postinp" type="text" class="inp" id="post_linkurl" value="'.$art['linkurl'].'"></td><td class="td1">支持平台</td><td class="td2"><input class="inp_box" id="post_computer" type="checkbox" value="ok" text="电脑版"'.goif(!$art['computer'],' checked').'><input class="inp_box" id="post_mobile" type="checkbox" value="ok" text="移动端"'.goif(!$art['mobile'],' checked').'></td></tr>
<tr><td class="td1"><span class="help" title="如果是转载，可以是转载网页地址或第三方网站名称">信息来源</span></td><td class="td2"><input tag="postinp" type="text" class="inp" id="post_source" value="'.$art['source'].'"></td><td class="td1">模板文件</td><td class="td2">'.$showfile_sel.'</td></tr>
<tr><td class="td1">所属专题</td><td class="td2"><input tag="postinp" style="width:192px" type="text" class="inp" id="post_special_id" value="'.$art['special_id'].'" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'post_special_id\'})"></td><td class="td1">赞一下统计</td><td class="td2"><input tag="postinp" style="width:50px" min=0 type="number" class="inp" id="post_mood_1" value="'.$art['mood_1'].'"><input tag="postinp" min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="post_mood_2" value="'.$art['mood_2'].'"><input tag="postinp" min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="post_mood_3" value="'.$art['mood_3'].'"><input tag="postinp" min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="post_mood_4" value="'.$art['mood_4'].'"><input tag="postinp" min=0 style="width:50px;margin-left:3px" type="number" class="inp" id="post_mood_5" value="'.$art['mood_5'].'"></td></tr>
<tr><td class="td1"><span class="help" title="页面描述，用于meta description标签，对搜索引擎比较友好，留空自动生成通常为200个汉字内，不能超过400个汉字（800个字符）">页面描述</span></td><td class="td5" colspan=3><textarea tag="postinp" class="inp_tex" id="post_description">'.$art['description'].'</textarea></td></tr>
<tr><td class="td1"><span class="help" title="页面关键词，用于meta keyword标签，对搜索引擎比较友好，留空则调用网站配置中的设置多个关键词用英文逗号分隔，通常为200个汉字内，不能超过400个汉字（800个字符）">关键词</span></td><td class="td5" colspan=3><textarea tag="postinp" class="inp_tex" id="post_keyword">'.$art['keyword'].'</textarea></td></tr>
</table>
</div>';