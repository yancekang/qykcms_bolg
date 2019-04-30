<?php
$keytype=arg('keytype','post','url');
if($keytype=='')$keytype='a';
$btn='<div class="btnsear"><span class="txt">显示模式：</span><input type="hidden" id="sear_keyword" value="'.$tcz['desc'].'"><select id="sear_keytype"><option value="a">所有栏目</option><option value="e"'.goif($keytype=='e',' selected').'>仅启用栏目</option><option value="b"'.goif($keytype=='b',' selected').'>仅显示导航</option></select><input class="btn" type="button" value="确 定" onclick="search({log:\'module\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'module_edit'),'<input type="button" value="创建栏目" class="btn1" onclick="openshow({log:\'module\',lang:\''.$tcz['desc'].'\'})">').goif(ispower($admin_group,'module_del'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'module\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td>名称</td>
<td style="width:100px">级别</td>
<td style="width:120px">标识</td>
<td style="width:60px">语言</td>
<td style="width:100px">类型</td>
<td style="width:120px">副标题</td>
<td style="width:60px">排序</td>
</tr>';
$sql='select * from '.tabname('module').' where webid='.$website['webid'].' and languages="'.$tcz['desc'].'"';
//if($keytype=='b')
$sql.=' and (menutype=0 or menutype=999)';
if($keytype=='e')$sql.=' and isok=0';
else if($keytype=='c')$sql.=' and computer=0';
else if($keytype=='d')$sql.=' and mobile=0';
$sql.=' order by menutype asc,sort asc,classid asc';
//ajaxreturn(1,$sql);
//$list=db_getpage($sql,setup_am_page,$tcz['page']);
$list=db_getlist($sql);
foreach($list as $val){
	$rank='<span class="list_blue">导航</span>';
	if($val['menutype']==999)$rank='<span class="list_orange">其它栏目</span>';
	else if($val['menutype']>0)$rank='<span class="list_green">'.getchinesenum($val['menutype']).'级分类</span>';
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'module\',id:'.$val['id'].',lang:\''.$tcz['desc'].'\'})">
<td class="cen">'.$val['classid'].'</td>
<td>'.goif($val['isok'],'<span class="list_red">'.$val['title'].'（隐藏）</span>','<span class="list_blue">'.$val['title'].'</span>').goif($val['modfile']!='','<span class="list_orange">（'.$val['modfile'].'）</span>').'</td>
<td class="cen">'.$rank.'</td>
<td class="cen">'.goif($val['mark']!='',$val['mark'],'--').'</td>
<td class="cen">'.$val['languages'].'</td>
<td class="cen">'.getmoduletype($val['modtype']).'</td>
<td>'.goif($val['title_en']!='',$val['title_en'],'--').'</td>
<td class="cen"><span class="list_blue">'.$val['sort'].'</span></td>
</tr>';
	if($keytype!='b'){
		$slist=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and bcat='.$val['classid'].' and menutype=1 order by sort asc,classid asc');
		foreach($slist as $sval){
			$res.='<tr dataid="'.$sval['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'module\',id:'.$sval['id'].',lang:\''.$tcz['desc'].'\'})">
<td class="cen">'.$sval['classid'].'</td>
<td colspan=5 style="text-indent:2em">'.goif($sval['isok'],'<span class="list_red">├─　'.$sval['title'].'（隐藏）</span>','<span class="list_green">├─　'.$sval['title'].'</span>').goif($sval['modfile']!='','<span class="list_orange">（'.$sval['modfile'].'）</span>').'</td>
<td>'.goif($sval['title_en']!='',$sval['title_en'],'--').'</td>
<td class="cen">'.$sval['sort'].'</td>
</tr>';
			$llist=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and scat='.$sval['classid'].' and menutype=2 order by sort asc,classid asc');
			foreach($llist as $lval){
				$res.='<tr dataid="'.$lval['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\'module\',id:'.$lval['id'].',lang:\''.$tcz['desc'].'\'})">
<td class="cen">'.$lval['classid'].'</td>
<td colspan=5 style="text-indent:6em">'.goif($lval['isok'],'<span class="list_red">├─　'.$lval['title'].'（隐藏）</span>','├─　'.$lval['title']).goif($lval['modfile']!='','<span class="list_orange">（'.$lval['modfile'].'）</span>').'</td>
<td>'.goif($lval['title_en']!='',$lval['title_en'],'--').'</td>
<td class="cen">'.$lval['sort'].'</td>
</tr>';
				}
			}
		}
	}
$res.='</table>';
if(!$list)$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn);