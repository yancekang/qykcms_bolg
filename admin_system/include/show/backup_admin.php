<?php
$res='<div class="win_ajax ajax_edit">
<table id="admin_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1"><span class="help" title="这里的备份与上面的“备份与恢复”不相同，这里将导出所有站点数据为sql，并且为云端备份，不支持直接下载，如需恢复可能需要相关技术人员操作，如您没有开设多个站点或并不了解sql及其操作原理，建议您使用上面的“备份与恢复”即可">备份选项</span></td><td class="td6_2"><select id="post_backtype_admin" tag="postinp"><option value="backup_clear">备份数据库，删除旧备份文件</option><option value="backup">备份数据库，保留旧备份文件</option><option value="clear">删除所有备份文件</option><option value="table">导出数据库结构（不含数据）</option></select></td></tr>
</table></div>';