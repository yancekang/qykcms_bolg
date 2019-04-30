<?php
$res="<div class='win_ajax ajax_edit'><table class='ajax_tablist' cellpadding='12' cellspacing='1'>";
switch($tcz['desc']){
	case 'admin_log':
		$choose_select='<select id="sear2_admincata"><option value="0">类型不限</option>';
		$admincata=getadmincata(0,true);
		foreach($admincata as $key=>$val){
			if($key>0)$choose_select.='<option value="'.$key.'">'.$val.'</option>';
			}
		$choose_select.='</select>';
		$res.='<tr><td class="td6_1">事件类型</td><td class="td6_2">'.$choose_select.'</td></tr>';
		$res.='<tr><td class="td6_1">管理员帐号</td><td class="td6_2"><input class="inp" id="sear2_keyword" type="text"></td></tr>';
		$res.='<tr><td class="td6_1">开始时间</td><td class="td6_2"><input class="inp" id="sear2_start" type="text"></td></tr>';
		$res.='<tr><td class="td6_1">结束时间</td><td class="td6_2"><input class="inp" id="sear2_end" type="text"></td></tr>';
	break;
	case 'feedback':
		$res.='<tr><td class="td6_1">关键字</td><td class="td6_2"><input class="inp" id="sear2_keyword" type="text"></td></tr>';
		$res.='<tr><td class="td6_1">类 型</td><td class="td6_2"><select id="sear2_keytype"><option value="">类型不限</option><option value="book">所有留言</option><option value="comment">所有评论</option></select></td></tr>';
		$res.='<tr><td class="td6_1">开始时间</td><td class="td6_2"><input class="inp" id="sear2_start" type="text"></td></tr>';
		$res.='<tr><td class="td6_1">结束时间</td><td class="td6_2"><input class="inp" id="sear2_end" type="text"></td></tr>';
	break;
	case 'article_user':
	case 'article':
		//获取分类
		$mod=db_getshow('module','modtype','webid='.$website['webid'].' and classid='.$tcz['bcat'].' and menutype=0');
		if(!$mod)ajaxreturn('未知的栏目');
		$bmod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$tcz['bcat'].' and menutype=1 order by sort asc,classid asc');
		$module_select='<select id="sear2_cata" tag="taginp">';
		$module_select.='<option value="'.$tcz['bcat'].'_0_0">未归类</option>';
		if($bmod){
			foreach($bmod as $k=>$val){
				$module_select.='<option value="'.$tcz['bcat'].'_'.$val['classid'].'_0">'.$val['title'].'</option>';
				$smod=db_getlist('select * from '.tabname('module').' where bcat='.$tcz['bcat'].' and scat='.$val['classid'].' order by sort asc,classid asc');
				if($smod){
					foreach($smod as $kk=>$sval){
						$module_select.='<option value="'.$tcz['bcat'].'_'.$val['classid'].'_'.$sval['classid'].'">'.$val['title'].' - '.$sval['title'].'</option>';
						}
					}
				}
			}
		$module_select.='</select>';
		$res.='<tr><td class="td6_1">模块分类</td><td class="td6_2">'.$module_select.'</td></tr>';
		$res.='<tr><td class="td6_1">关键字</td><td class="td6_2"><input class="inp" id="sear2_keyword" type="text"></td></tr>';
		if($mod['modtype']!=1)$res.='<tr><td class="td6_1">星标等级</td><td class="td6_2"><select id="sear2_star"><option value="0">不限</option><option value="9">所有星标记录</option><option value="1">1 - ★☆☆☆☆</option><option value="2">2 - ★★☆☆☆</option><option value="3">3 - ★★★☆☆</option><option value="4">4 - ★★★★☆</option><option value="5">5 - ★★★★★</option></select></td></tr>';
		$res.='<tr><td class="td6_1">发布日期</td><td class="td6_2"><input class="inp" id="sear2_start" type="text"></td></tr>';
	break;
	};
$res.='</table></div>';
ajaxreturn(0,$res);