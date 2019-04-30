<?php
$modlist=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$tcz['bcat'].' and menutype=1 order by sort asc,classid asc');
$module_select='<select id="post_cata" tag="postinp">';
$module_select.='<option value="'.$tcz['bcat'].'_0_0">未归类</option>';
if($modlist){
	foreach($modlist as $k=>$val){
		$module_select.='<option value="'.$tcz['bcat'].'_'.$val['classid'].'_0">'.$val['title'].'</option>';
		$smod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$tcz['bcat'].' and scat='.$val['classid'].' order by sort asc,classid asc');
		if($smod){
			foreach($smod as $kk=>$sval){
				$module_select.='<option value="'.$tcz['bcat'].'_'.$val['classid'].'_'.$sval['classid'].'">├─　'.$sval['title'].'</option>';
				}
			}
		}
	}
$module_select.='</select>';
$res='<input type="hidden" value="'.$tcz['bcat'].'" id="post_bcat">';
$res.='<div class="win_ajax ajax_edit">
<table id="article_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1">统一设置标题</td><td class="td6_2"><input type="text" class="inp" id="post_title" placeholder="留空则以图片文件名作为标题"></td></tr>
<tr><td class="td6_1">所属分类</td><td class="td6_2">'.$module_select.'</td></tr>
<tr><td class="td6_1">所属专题</td><td class="td6_2"><input style="width:192px" type="text" class="inp" id="post_special_id" readonly><input type="button" class="inp_btn" value="选择" onclick="winchoose({desc:\'special\',inp:\'post_special_id\'})"></td></tr>
<tr><td class="td6_1"><span class="help" title="导入功能主要用于批量导入图片，选择一个文件夹，系统将自动导入该文件夹内的所有图片，支持JPG、GIF、PNG格式文件">文件夹路径</span></td><td class="td6_2"><textarea onfocus="this.blur()" placeholder="单击选择文件夹" onclick="choosepath({callback:function(path){$(\'#post_folder\').val(path)}})" class="inp_up" id="post_folder"></textarea></td></tr>
</table></div>';