<?php
$data=db_getshow('admin_group','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data){
	$data=array(
		'group_name'=>'',
		'config_super'=>0,
		'config_rank'=>''
		);
	}
$res='<div class="win_ajax ajax_user">
<table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">分组名称</td><td class="td2"><input class="inp" type="text" id="post_group_name" value="'.$data['group_name'].'"></td><td class="td1"><span class="help" title="选择“超级管理”则拥有所有管理权限，下面的分配权限无效">管理权限</span></td><td class="td2"><select id="post_config_super"><option value="1">超级管理</option><option value="2"'.goif($data['config_super']==2,' selected').'>分配权限</option></select></td></tr>
<tr><td class="td0" colspan=4><a href="javascript:morechoose({log:\'clear\',obj:$(\'#tcz_adminchoose\')})">全不选</a><a href="javascript:morechoose({log:\'all\',obj:$(\'#tcz_adminchoose\')})">全选</a>分配权限（管理权限为“超级管理”时以下设置无效）</td></tr>
<tr><td class="td_scro" colspan=4>
<div class="scro ui_morechoose" style="height:350px" id="tcz_adminchoose"><input type="hidden" id="post_config_rank" value="'.$data['config_rank'].'">
<table cellpadding="0" cellspacing="0">
<tr class="item" id=""><td class="log">首页秘书：</td><td class="desc btn"><a class="out" data="sys_login">登录系统</a><a class="out" data="sys_pass">修改密码</a><a class="out" data="sys_head">修改头像</a><a class="out" data="sys_adminlog">导出管理员日志</a></td></tr>
<tr class="item" id=""><td class="log">网站内容：</td><td class="desc btn"><a class="out" data="art_list">查询列表</a><a class="out" data="art_all">所有记录</a><a class="out" data="art_edit">新建编缉</a><a class="out" data="art_del">删除记录</a></td></tr>
<tr class="item" id=""><td class="log">栏目分类：</td><td class="desc btn"><a class="out" data="module_list">查询列表</a><a class="out" data="module_edit">新建编缉</a><a class="out" data="module_del">删除记录</a><a class="out" data="module_user">自定义模块</a></td></tr>
<tr class="item" id=""><td class="log">专题管理：</td><td class="desc btn"><a class="out" data="special_list">查询列表</a><a class="out" data="special_edit">新建编缉</a><a class="out" data="special_del">删除记录</a></td></tr>
<tr class="item" id=""><td class="log">广告图片：</td><td class="desc btn"><a class="out" data="advert_list">查询列表</a><a class="out" data="advert_edit">新建编缉</a><a class="out" data="advert_del">删除记录</a></td></tr>
<tr class="item" id=""><td class="log">留言评论：</td><td class="desc btn"><a class="out" data="book_list">查询列表</a><a class="out" data="book_edit">浏览阅读</a><a class="out" data="book_import">导出数据</a><a class="out" data="book_del">删除记录</a><a class="out" data="book_view">标记阅读</a><a class="out" data="book_tips">待办提醒</a></td></tr>
<tr class="item" id=""><td class="log">系统综合：</td><td class="desc btn"><a class="out" data="sys_bakeup">备份与恢复</a><a class="out" data="sys_cache">缓存管理</a><a class="out" data="sys_setup">系统设置</a><a class="out" data="sys_config">网站配置</a></td></tr>
<tr class="item" id=""><td class="log">附件管理：</td><td class="desc btn"><a class="out" data="uploadzip_list">附件菜单</a><a class="out" data="uploadzip_edit">上传附件</a><a class="out" data="uploadzip_del">删除附件</a></td></tr>
<tr class="item" id=""><td class="log">客服信息：</td><td class="desc btn"><a class="out" data="customer_list">查询列表</a><a class="out" data="customer_edit">新建编缉</a><a class="out" data="customer_del">删除记录</a></td></tr>
<tr class="item" id=""><td class="log">主题模板：</td><td class="desc btn"><a class="out" data="skin_list">模板菜单</a><a class="out" data="skin_edit">新建编缉</a><a class="out" data="skin_upload">上传文件</a><a class="out" data="skin_del">删除文件</a><a class="out" data="skin_label">自定义标签</a><a class="out" data="skin_option">自定义选项</a></td></tr>
<tr class="item" id=""><td class="log">辅助工具：</td><td class="desc btn"><a class="out" data="tool_list">工具功能</a><a class="out" data="tool_email">邮件群发</a></td></tr>
</table>
</div>
</td></tr>
</table></div>';