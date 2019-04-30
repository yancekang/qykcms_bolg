<?php
$excel=arg('excel','all','txt');
$keyword=arg('keyword','post','txt');
$keytype=arg('keytype','post','url');
$start=arg('start','post','txt');
$end=arg('end','post','txt');
$dataid=arg('dataid','post','int');
$sear_dataid=0;
if($dataid){
	$keytype='data';
	$tname='article';
	$modtype=db_getone('module','modtype','webid='.$website['webid'].' and classid='.$tcz['bcat']);
	if($modtype>10)$tname.='_'.$website['webid'].'_'.$modtype;
	$art=db_getshow($tname,'id,title,dataid','webid='.$website['webid'].' and id='.$dataid);
	if(!$art)ajaxreturn(1,'评论对应的文章记录不存在');
	$dataid=$art['dataid'];
	$sear_dataid=$art['id'];
	}
$btn='<div class="btnsear"><span class="txt">关键字：</span><input type="hidden" id="sear_dataid" value="'.$sear_dataid.'"><input type="hidden" id="sear_bcat" value="'.$tcz['bcat'].'"><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()"><select id="sear_keytype"><option value="">类型不限</option><option value="book"'.goif($keytype=='book',' selected').'>所有留言</option><option value="comment"'.goif($keytype=='comment'&&!$dataid,' selected').'>所有评论</option>'.goif($dataid,'<option value="data" selected>指定评论（ID='.$dataid.'）</option>').'</select><input class="btn" type="button" value="搜 索" onclick="search({log:\'feedback\'})"><input class="btn" type="button" value="高 级" onclick="search_more({log:\'feedback\'})"></div>
<div class="btnright">'.goif(ispower($admin_group,'book_import'),'<input type="button" value="导出数据" class="btn1" onclick="downexcel({log:\'feedback\'})">').goif(ispower($admin_group,'book_del'),'<input type="button" value="删除所选" class="btn1" onclick="deldata({log:\'feedback\'})">').goif(ispower($admin_group,'book_view'),'<input type="button" value="标记所选" class="btn1" onclick="PZ.e({msg:\'确定要将选择的留言/评论标记为已阅读状态吗？\',btn:[{text:\'确 定\',close:\'ok\',css:\'out2\',callback:function(){userpost({log:\'feedback_view\',types:\'view\'})}},{text:\'取 消\',close:\'ok\'}]})">').goif(ispower($admin_group,'book_isok'),'<input type="button" value="审核所选" class="btn1" onclick="PZ.e({msg:\'确定要将选择的留言/评论设置为已审核状态吗？\',btn:[{text:\'确 定\',close:\'ok\',css:\'out2\',callback:function(){userpost({log:\'feedback_view\',types:\'isok\'})}},{text:\'取 消\',close:\'ok\'}]})">').'</div>';
$res.='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:60px">ID</td>
<td>称呼</td>
<td style="width:60px">类型</td>
'.goif($keytype=='comment'||$keytype=='data','<td style="width:280px">相关文章</td>','<td style="width:150px">邮箱地址</td><td style="width:100px">电话号码</td>').'
<td style="width:120px">地区</td>
<td style="width:150px">留言时间</td>
<td style="width:90px">阅读</td>
<td style="width:70px">审核</td>
</tr>';
$sql='select * from '.tabname('feedback').' where webid='.$website['webid'].goif($tcz['desc']!='',' and languages="'.$tcz['desc'].'"').goif($keyword!='',' and (name="'.$keyword.'" or LOCATE("'.$keyword.'",`content`)>0)').goif($keytype!=''&&$keytype!='data',goif($keytype=='comment',' and dataid>0',' and dataid=0')).goif($tcz['bcat'],' and bcat='.$tcz['bcat']).goif($dataid,' and dataid='.$dataid);
if($start!=''){
	$start=strtotime($start.' 00:00:00');
	$sql.=' and time_add>='.$start;
	}
if($end!=''){
	$end=strtotime($end.' 23:59:59');
	$sql.=' and time_add<='.$end;
	}
$sql.=' order by time_add desc,id desc';
if($excel=='ok')$list=array('list'=>db_getlist($sql));
else $list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')"'.goif($excel!='ok',' ondblclick="openshow({log:\'feedback\',id:'.$val['id'].'})"').'>
<td class="cen">'.$val['id'].'</td>
<td><span class="list_green">'.$val['name'].'</span></td>
<td class="cen">'.goif($val['dataid'],'<span class="list_blue">评论</span>','<span class="list_orange">留言</span>').'</td>';
	if($keytype=="comment"||$keytype=="data"){
		if(!$dataid){
			$tname='article';
			$modtype=db_getone('module','modtype','webid='.$website['webid'].' and classid='.$val['bcat']);
			if($modtype>10)$tname.='_'.$website['webid'].'_'.$modtype;
			$art=db_getshow($tname,'title','webid='.$website['webid'].' and dataid='.$val['dataid']);
			if(!$art)$art['title']='<span class="list_red">文章不存在</span>';
			}
		$res.='<td>'.$art['title'].'</td>';
	}else{
		$res.='<td>'.$val['email'].'</td><td>'.$val['phone'].'</td>';
		}
	$res.='<td>'.goif($val['user_iptext']!='',$val['user_iptext'],$val['user_ip']).'</td>
<td class="cen">'.date('Y-m-d H:i:s',$val['time_add']).'</td>
<td class="cen">'.goif($val['time_view'],date('Y-m-d',$val['time_view']),'<span class="list_red">未阅读</span>').'</td>
<td class="cen">'.goif($val['isok'],'<span class="list_red">待审核</span>','<span class="list_gray">已审核</span>').'</td></tr>';
	}
$res.='</table>';
if($excel=='ok')excelreturn($res,'admin_log');
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);