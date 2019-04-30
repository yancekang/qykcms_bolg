<?php
$setup=db_getshow('websetup','*','webid='.$website['webid']);
$setup_tip_tempfile=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_tip_tempfile"');
$langlist=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"');
$langico='';
$lang=explode(',',$langlist);
foreach($lang as $k=>$v){
	$langarr=explode('|',$v);
	$langico.='<a href="javascript:" onclick="infomenu_click({log:\'lmenu_module_'.$langarr[0].'\'});return false" class="item"><div class="ico"><img src="images/welcome/setup.png"></div><div class="desc">栏目与分类（'.$langarr[1].'）</div></a>';
	//$langico.=goif(ispower($admin_group,'advert_list'),'<a href="javascript:" onclick="infomenu_click({log:\'lmenu_advert_'.$langarr[0].'\'});return false" class="item"><div class="ico"><img src="images/welcome/article.png"></div><div class="desc">广告图片（'.$langarr[1].'）</div></a>');
	//$langico.=goif(ispower($admin_group,'customer_list'),'<a href="javascript:" onclick="infomenu_click({log:\'lmenu_customer_'.$langarr[0].'\'});return false" class="item"><div class="ico"><img src="images/welcome/article.png"></div><div class="desc">客服信息（'.$langarr[1].'）</div></a>');
	$langico.=goif(ispower($admin_group,'sys_config'),'<a href="javascript:" onclick="infomenu_click({log:\'lmenu_setup_'.$langarr[0].'\'});return false" class="item"><div class="ico"><img src="images/welcome/setup.png"></div><div class="desc">网站配置（'.$langarr[1].'）</div></a>');
	}
$capacity_max='不限';
if($setup['capacity_max']){
	$capacity_max=floatval($setup['capacity_max']/1024);
	$capacity_max=sprintf('%.2f',$capacity_max);
	}
$capacity_have=$setup['capacity_have'];
$capacity_have=floatval($setup['capacity_have']/1024);
$capacity_have=sprintf('%.2f',$capacity_have);
$havebfb='已使用 '.$capacity_have.' MB';
$havewidth=0;
if($setup['capacity_max']>0){
	$havebfb=floatval($setup['capacity_have']/$setup['capacity_max']*100);
	$havebfb=sprintf('%.2f',$havebfb).' %';
	$havewidth=round($havebfb);
	if($havewidth>100)$havewidth=100;
	}
$ver=db_getshow('version','*');
$tempsize=sprintf('%.2f',getdirsize($website['upfolder'].setup_uptemp)/1024/1024);
$cachetips=0;
if($tempsize>$setup_tip_tempfile)$cachetips++;
$feedback_new=db_count('feedback','webid='.$website['webid'].' and time_view=0')+0;
$res='<div class="ui_welcome">
<div class="manage">
<div class="head"><img src="http://'.$website['setup_weburl'].getfile_admin('head',$admin_check['user_head'],200).'"></div>
<div class="hello">
<div class="weltext">'.$tcz['admin'].'，'.gethello().'</div>
<div class="weltip">当前站点：<span class="green">'.$setup['title'].'</span>　<a href="javascript:" onclick="openurl(\'http://'.$webdomain.'\')" class="gray">'.$webdomain.'</a></div>
<div class="weltip">您是第 '.$admin_check['login_num'].' 次登录系统，所属管理组为'.$admin_group['group_name'].'</div>
</div>
<div class="count">
<div class="ico ico1"><span class="cname">后台版本：</span><span class="desc">version '.$ver['version'].'</span></div>
<div class="ico ico2"><span class="cname">前台版本：</span><span class="desc" title="更新于'.date('Y年m月d日 H时i分',$ver['uptime']).'">version '.$ver['version_front'].'</span></div>
<div class="ico ico3"><span class="cname">空间容量：</span><span class="desc"><span class="line" title="已使用 '.$capacity_have.' MB，'.goif($capacity_max>0,'剩余 '.sprintf('%.2f',$capacity_max-$capacity_have).' MB，总容量 '.$capacity_max.' MB','总容量未设置上限').'"><span class="have" style="width:'.$havewidth.'%"></span><span class="num">'.$havebfb.'</span></span></span></div>
<div class="ico ico4"><span class="cname">站点编号：</span><span class="desc">'.$setup['webid'].'</span></div>
</div>
</div>
<div class="iconlist">
<a href="javascript:" onclick="opensys({load:\'open\'});return false" class="item" id="tcz_qyk_aboutsys"><div class="ico"><img src="images/welcome/notice.png"></div><div class="desc">关于系统<span class="none" id="tcz_qyk_notice">0</span></div></a>
<!--a href="javascript:" onclick="winapp({log:\'webpage\',url:\'http://'.$webdomain.'\'});return false" class="item"><div class="ico"><img src="images/welcome/home.png"></div><div class="desc">浏览主页<span class="none">0</span></div></a-->'
.goif(ispower($admin_group,'sys_pass'),'<a href="javascript:" onclick="openshow({log:\'editpass\'});return false" class="item"><div class="ico"><img src="images/welcome/lock.png"></div><div class="desc">修改登录密码<span class="none">0</span></div></a>')
.goif(ispower($admin_group,'sys_head'),'<a href="javascript:" onclick="openshow({log:\'edithead\'});return false" class="item"><div class="ico"><img src="images/welcome/user.png"></div><div class="desc">修改头像'.goif($admin_check['user_head']=='','<span class="tips" id="tcz_qyk_head">1</span>','<span class="none" id="tcz_qyk_head">0</span>').'</div></a>')
.goif(ispower($admin_group,'sys_cache'),'<a href="javascript:" onclick="openshow({log:\'clearcache\'});return false" class="item"><div class="ico"><img src="images/welcome/del.png"></div><div class="desc">缓存管理'.goif($cachetips,'<span class="tips" id="tcz_qyk_clearcache">'.$cachetips.'</span>','<span class="none">0</span>').'</div></a>')
.goif(ispower($admin_group,'sys_uploadzip'),'<a href="javascript:" onclick="openshow({log:\'uploadzip\'});return false" class="item"><div class="ico"><img src="images/welcome/down.png"></div><div class="desc">附件管理</div></a>')
.goif(ispower($admin_group,'sys_backup'),'<a href="javascript:" onclick="openshow({log:\'backup\'});return false" class="item"><div class="ico"><img src="images/welcome/setup.png"></div><div class="desc">备份与恢复<span class="none">0</span></div></a>')
.goif(ispower($admin_group,'book_list'),'<a href="javascript:" onclick="infomenu_click({log:\'lmenu_feedback\'});return false" class="item"><div class="ico"><img src="images/welcome/article.png"></div><div class="desc">留言与评论'.goif($feedback_new,'<span class="tips">'.$feedback_new.'</span>').'</div></a>')
.'<a href="javascript:" onclick="infomenu_click({log:\'lmenu_admin_log\'});return false" class="item"><div class="ico"><img src="images/welcome/memo.png"></div><div class="desc">管理员日志<span class="none">0</span></div></a>'
.$langico
.goif(ispower($admin_group,'sys_setup'),'<a href="javascript:" onclick="openshow({log:\'config_admin\'});return false" class="item"><div class="ico"><img src="images/welcome/setup.png"></div><div class="desc">系统设置<span class="none">0</span></div></a>')
.goif(ispower($admin_group,'super'),'<a href="javascript:" onclick="infomenu_click({log:\'lmenu_admin\'});return false" class="item"><div class="ico"><img src="images/welcome/lock.png"></div><div class="desc">后台账号管理<span class="none">0</span></div></a>').'
</div>
</div>';
ajaxreturn(0,$res);