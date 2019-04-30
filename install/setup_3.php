<?php
$domain=$_SERVER['SERVER_NAME'];
if($domain=='')$domain=$_SERVER['HTTP_HOST'];
$res='<div class="title" tag="install_start">第二步：初始化设置</div>
<div class="cont" tag="install_start">
	<div class="item2"><span class="cname">站点域名：</span><span class="inp"><input id="post_domain" class="text" type="text" value="'.$domain.'"></span><span class="tips">使用该域名登录后台才有高级管理权限</span></div>
	<div class="item2"><span class="cname">MySQL服务器：</span><span class="inp"><input id="post_dbhost" class="text" type="text" value="localhost"></span><span class="tips">数据库与网站同一台服务器上通常填 localhost</span></div>
	<div class="item2"><span class="cname">数据库名称：</span><span class="inp"><input id="post_dbname" class="text" type="text"></span><span class="tips">如果数据库不存在系统将尝试自动创建</span></div>
	<div class="item2"><span class="cname">数据库用户名：</span><span class="inp"><input id="post_dbuser" class="text" type="text" value="root"></span><span class="tips">MySQL数据库默认的用户名为root，请根据站点情况输入</span></div>
	<div class="item2"><span class="cname">数据库密码：</span><span class="inp"><input id="post_dbpass" class="text" type="text"></span></div>
	<div class="item2"><span class="cname">表名前缀：</span><span class="inp"><input id="post_prefix" class="text" type="text" value="qyk_"></span><span class="tips">通常不用修改，同一数据库安装多个QYKCMS系统时请更改</span></div>
	<div class="item2"><span class="cname">网站管理员账号：</span><span class="inp"><input id="post_user" class="text" type="text"></span><span class="tips">设置一个初始的超级管理员账号用于管理网站</span></div>
	<div class="item2"><span class="cname">网站管理员密码：</span><span class="inp"><input id="post_pass" class="text" type="password"></span><span class="tips">5~20位密码</span></div>
	<div class="item2" id="item_dbdata"><span class="cname">安装模式：</span><span class="inp"><label><input onchange="if(this.checked){$(\'#item_theme\').slideDown();$(\'#post_theme\').attr(\'checked\',true)}else{$(\'#item_theme\').slideUp();$(\'#post_theme\').attr(\'checked\',false)}" id="post_clean" class="box" type="checkbox" value="ok" checked>全新安装模式</label></span><span class="tips">如果勾选此项，有相同的数据表时会删除重新创建</span></div>
	<div class="item2" id="item_theme"><span class="cname">安装内置主题：</span><span class="inp"><label><input id="post_theme" class="box" type="checkbox" value="ok" checked>'.install_theme_name.'</label></span><span class="tips">如果不安装默认主题，安装完请先登录后台安装主题　<a target=_blank href="http://cms.qingyunke.com/">更多主题</a></span></div>
	<div style="clear:both"></div>
</div>
<div class="btn" tag="install_start"><input type="button" value="开始安装" onclick="install()">　<input type="button" value="返回上一步" onclick="location.href=\'index.php?setup=2\'"></div>
<div class="title" tag="install_success" style="display:none">第三步：完成安装</div>
<div class="cont" tag="install_success" style="display:none">
	<div style="width:100%;height:200px;text-align:center"><img src="images/success.gif"></div>
	<div style="width:780px;margin:auto;line-height:40px;clear:both">
	◆ 请记住您刚刚设置的网站管理员账号：<span id="admin_user" style="color:#ccc">...</span>，密码：<span id="admin_pass" style="color:#ccc">...</span>
	<br>◆ 管理QYKCMS网站需要使用<a target=_blank href="http://cms.qingyunke.com/" class="red">QYKCMS后台软件</a>，通常在安装包内的exe文件夹，没有请进入官网下载
	<br>◆ 后台文件夹默认名称为 admin_system，系统允许自由修改该文件夹名称，登录后台时请输入新的名称
	<br>◆ 请检查系统是否正常，如有异常可尝试重新安装，完成后建议删除网站根目录下的install文件夹
	<br>◆ 如果您对QYKCMS有任何建议或意见，欢迎反馈给我们：<a target=_blank href="http://cms.qingyunke.com/feedback/">反馈意见</a>
	</div>
	<div style="width:780px;text-align:center;margin:auto;clear:both;padding:20px 0 50px 0"><a target=_blank href="/" style="color:#009900">浏览您的网站</a>　<span style="color:#ccc;font-size:10px">|</span>　<a target=_blank href="http://cms.qingyunke.com" style="color:#009900">QYKCMS官网</a>　<span style="color:#ccc;font-size:10px">|</span>　<a target=_blank href="http://bbs.qingyunke.com" style="color:#009900">技术论坛</a></div>
</div>';
echo $res;
?>