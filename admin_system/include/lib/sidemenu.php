<?php
$langlist=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"');
$catalist='';
$catalist_skin='{title:"资源脚本文件",ico:"folder",url:"log=template&desc=res",log:"lmenu_template_res"}';
//伪静态规则文件
$staticfile=db_getone('config','varval','varname="setup_static_file"');
if(!$staticfile)$staticfile='httpd.ini';
//移动端设置
$setup_mobile=false;
$setup_mobile_val=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_mobile"');
if($setup_mobile_val){
	if($setup_mobile_val=='true')$setup_mobile=true;
	}
$setup_mobile_url=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_mobile_url"');
if(!$setup_mobile_url)$setup_mobile_url='';
$lang=explode(',',$langlist);
//$lang_text=explode(',',setup_language_text);
$icospan=array('1'=>'edit','2'=>'news','3'=>'product','4'=>'photo','5'=>'list','6'=>'link','7'=>'list','8'=>'list','9'=>'link','10'=>'user');
foreach($lang as $k=>$v){
	$langarr=explode('|',$v);
	$othercata='';
	$menu='';
	$menu2='';
	$list=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and languages="'.$langarr[0].'" and bcat=0 and (modtype<8 or modtype>10) and (menutype=0 or menutype=999) order by sort asc,classid asc');
	foreach($list as $val){
		if($val['modtype']>10)$menuicon=$icospan[10];else $menuicon=$icospan[$val['modtype']];
		if($val['menutype']==0)$menu.=',{title:"'.$val['title'].'",ico:"'.goif($val['isok'],'hide',$menuicon).'",url:"log=article'.goif($val['modtype']>10,'_user').'&bcat='.$val['classid'].'"}';
		else if($val['menutype']==999)$othercata.=goif($othercata!='',',').'{title:"'.$val['title'].'",ico:"'.goif($val['isok'],'hide',$menuicon).'",url:"log=article&bcat='.$val['classid'].'"}';
		}
	if(ispower($admin_group,'module_list')){
		$menu2.=',{title:"栏目与分类",ico:"add",url:"log=module&desc='.$langarr[0].'",log:"lmenu_module_'.$langarr[0].'"}';
		}
	if(ispower($admin_group,'special_list')){
		$menu2.=',{title:"专题管理",ico:"add",url:"log=special&desc='.$langarr[0].'",log:"lmenu_special_'.$langarr[0].'"}';
		}
	$othercata.=goif(ispower($admin_group,'advert_list'),goif($othercata!='',',').'{title:"广告图片",ico:"edit",url:"log=advert&desc='.$langarr[0].'",log:"lmenu_advert_'.$langarr[0].'"}');
	$othercata.=goif(ispower($admin_group,'customer_list'),goif($othercata!='',',').'{title:"客服信息",ico:"edit",url:"log=customer&desc='.$langarr[0].'",log:"lmenu_customer_'.$langarr[0].'"}');
	$othercata.=goif(ispower($admin_group,'sys_config'),goif($othercata!='',',').'{title:"网站配置",ico:"setup",url:"config_web",lang:"'.$langarr[0].'",win:"show",log:"lmenu_setup_'.$langarr[0].'"}');
	$catalist.='
,{"bc":"网站管理（'.$langarr[1].'）","bclist":[{}
'.goif($menu!='',',{"sc":"内容管理","sclist":[{}'.$menu.']}').'
'.goif($menu2!='',',{"sc":"栏目结构","sclist":[{}'.$menu2.']}').'
,{"sc":"其它管理","sclist":['.$othercata.']}
]}';
	$catalist_skin.=',{title:"模板（'.$langarr[1].'-PC端）",ico:"folder",url:"log=template&desc='.urlencode($langarr[0].'/{themepath}').'",log:"lmenu_template"}
,{title:"样式（'.$langarr[1].'-PC端）",ico:"folder",url:"log=template&desc='.urlencode($langarr[0].'/ui').'",log:"lmenu_template"}';
	if($setup_mobile&&$setup_mobile_url==''){
		$catalist_skin.=',{title:"模板（'.$langarr[1].'-移动端）",ico:"folder",url:"log=template&desc='.urlencode($langarr[0].'_mobile/{themepath}').'",log:"lmenu_template"}
,{title:"样式（'.$langarr[1].'-移动端）",ico:"folder",url:"log=template&desc='.urlencode($langarr[0].'_mobile/ui').'",log:"lmenu_template"}';
		}
	}
$sjzl='';
$sjzl.=goif(ispower($admin_group,'sys_cache'),goif($sjzl!='',',').'{title:"缓存管理",ico:"clear",url:"clearcache",win:"show"}');
$sjzl.=goif(ispower($admin_group,'uploadzip_list'),goif($sjzl!='',',').'{title:"附件管理",ico:"link",url:"uploadzip",win:"show"}');
$sjzl.=goif(ispower($admin_group,'sys_backup'),goif($sjzl!='',',').'{title:"备份与恢复",ico:"down",url:"backup",win:"show"}');
$zdysj='';
$zdysj.=goif(ispower($admin_group,'module_user'),',{title:"自定义模块",ico:"list",url:"log=module_user",log:"lmenu_module_user"}');
$zdysj.=goif(ispower($admin_group,'skin_label'),',{title:"自定义标签",ico:"list",url:"log=label",log:"lmenu_label"}');
$zdysj.=goif(ispower($admin_group,'skin_option'),',{title:"自定义选项",ico:"list",url:"log=option",log:"lmenu_option"}');
$res='[
{"bc":"首页秘书台","bclist":[
{"sc":"个人管理","sclist":[
	{title:"欢迎界面",url:"log=welcome",ico:"welcome",log:"lmenu_welcome",open:true}'
	.goif(ispower($admin_group,'sys_pass'),',{title:"修改登录密码",url:"editpass",ico:"pass",win:"show",log:"lmenu_editpass"}')
	.goif(ispower($admin_group,'sys_head'),',{title:"修改头像",url:"edithead",ico:"user",win:"show",log:"lmenu_edithead"}')
	.']},
{"sc":"常用功能","sclist":[
	{title:"管理员日志",url:"log=admin_log",ico:"list",log:"lmenu_admin_log"}
	'.goif(ispower($admin_group,'book_list'),',{title:"留言与评论",ico:"book",url:"log=feedback",log:"lmenu_feedback"}').'
	]}
]}'
.$catalist
.goif(ispower($admin_group,'super'),'
,{"bc":"账号与权限","bclist":[
{"sc":"管理员权限","sclist":[
	{title:"创建账号",ico:"edit",url:"admin",win:"show",log:"lmenu_admin_show"},
	{title:"管理后台账号",ico:"user",url:"log=admin",log:"lmenu_admin"},
	{title:"管理员分组",ico:"list",url:"log=admin_group",log:"lmenu_admin_group"}
	]}
]}')
.goif(ispower($admin_group,'skin_list'),'
,{"bc":"主题模板","bclist":[
	{"sc":"主题风格","sclist":[
	{title:"安装新主题",ico:"add",url:"theme",log:"lmenu_theme_show",win:"show"},
	{title:"导出当前主题",ico:"down",url:"theme_import",log:"lmenu_theme_import",win:"show"},
	{title:"设置当前主题",ico:"edit",url:"config_admin",other:"theme",log:"lmenu_template_theme",win:"show"}
	]}
,{"sc":"修改当前主题","sclist":[
	'.$catalist_skin.'
	,{title:"主题参数设置",ico:"list",url:"log=config_args_theme",log:"lmenu_config_args_theme"}
	]}'
.goif($zdysj!='',',{"sc":"自定义数据","sclist":[{}'.$zdysj.']}').'
]}')
.',{"bc":"系统综合","bclist":['
.goif($sjzl!='','{"sc":"数据整理","sclist":['.$sjzl.']},').'
{"sc":"全局设置","sclist":[{}'
	.goif(ispower($admin_group,'super'),',{title:"域名绑定",ico:"setup",url:"log=config_domain",log:"lmenu_config_domain"}')
	.goif(ispower($admin_group,'sys_setup'),',{title:"系统设置",ico:"setup",url:"config_admin",win:"show",log:"lmenu_setup_admin"}')
	.']}
]}'
.goif(setup_am_more&&$website['isadmin']&&ispower($admin_group,'super'),'
,{"bc":"高级功能","bclist":[
	{"sc":"多站点功能","sclist":[
	{title:"新建站点",ico:"edit",url:"websetup",log:"lmenu_websetup_show",win:"show"},
	{title:"管理站点",ico:"product",url:"log=websetup",log:"lmenu_websetup"}
	]},
{"sc":"其它管理","sclist":[
	{title:"数据库备份",ico:"down",url:"backup_admin",log:"lmenu_backup_admin",win:"show"},
	{title:"编缉 '.$staticfile.'",ico:"edit",url:"editfile",other:"'.$staticfile.'",log:"lmenu_file_http",win:"show"},
	{title:"基础配置",ico:"setup",url:"config_basic",win:"show",log:"lmenu_config_basic"},
	{title:"全局参数设置",ico:"edit",url:"log=config_args",log:"lmenu_config_args"}
	]}
]}')
.goif(ispower($admin_group,'tool_list'),',{"bc":"辅助工具","bclist":[
{"sc":"邮件群发器","sclist":[
	{title:"新建群发",ico:"edit",url:"tool_email",win:"show"},
	{title:"管理群发任务",ico:"list",url:"log=tool_email",log:"lmenu_tool_email"},
	{title:"设置发件邮箱",ico:"list",url:"log=tool_email_address",log:"lmenu_tool_email_address"}
	]}
]}')
.']';
ajaxreturn(0,$res);