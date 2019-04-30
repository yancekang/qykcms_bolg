<?php
$keyword=arg('keyword','post','txt');
$keytype=arg('keytype','post','url');
$setupcata=explode(',',setup_am_setup_cata);
$order_xu='';
$cata_arr=array('web'=>'网站配置');
if($tcz['log']=='config_args'){
	$keytypelist='<select id="sear_keytype"><option value="">全部参数</option>';
	foreach($setupcata as $sort=>$s){
		$n=explode('|',$s);
		$keytypelist.='<option value="'.$n[0].'"'.goif($keytype==$n[0],' selected').'>'.$n[1].'</option>';
		$xu=100-$sort;
		$order_args=explode('|',$s);
		$cata_arr[$order_args[0]]=$order_args[1];
		$order_xu.=goif($order_xu!='','+').'if(cata="'.$order_args[0].'",'.$xu.',0)';
		}
	$cata_arr['basic']='基础配置';
	$keytypelist.='<option value="web"'.goif($keytype=='web',' selected').'>网站配置</option><option value="basic"'.goif($keytype=='basic',' selected').'>基础配置</option></select>';
}else{
	$keytypelist='<input type="hidden" id="sear_keytype" value="">';
	}
$btn='<div class="btnsear"><span class="txt">关键字：</span><input class="inp" type="text" id="sear_keyword" value="'.$keyword.'" onfocus="this.select()">'.$keytypelist.'<input class="btn" type="button" value="搜 索" onclick="search({log:\''.$tcz['log'].'\'})"></div>
<div class="btnright"><input type="button" value="添加参数" class="btn1" onclick="openshow({log:\''.$tcz['log'].'\'})"><input type="button" value="删除参数" class="btn1" onclick="deldata({log:\''.$tcz['log'].'\'})">'.goif($tcz['log']=='config_args','<input type="button" value="同步站点" class="btn1" onclick="PZ.e({msg:\'确定要将当前参数结构同步到所有站点吗？\',btn:[{text:\'确定同步\',css:\'out2\',close:\'ok\',callback:function(){userpost({log:\'config_all\'})}},{text:\'取 消\',close:\'ok\'}]})"><input type="button" value="导出设置" class="btn1" onclick="userpost({log:\'config_args_import\'})">').'</div>';
$res='<table class="ui_tablist" cellpadding="12" cellspacing="1">
<tr class="tr1">
<td style="width:80px">归类</td>
<td style="width:160px">常量名</td>
<td style="width:120px">标题</td>
<td style="width:90px">初始值</td>
<td style="width:60px">方式</td>
<td>描述</td>
<td style="width:50px">显示</td>
<td style="width:50px">系统</td>
<td style="width:50px">排序</td>
</tr>';
$infotype=array('inp'=>'单行','text'=>'多行','select'=>'<span class="list_blue">下拉</span>','upload'=>'<span class="list_orange">上传</span>','pass'=>'<span class="list_red">密码</span>');
$sql='select *,'.goif($order_xu!='','('.$order_xu.') as xu','1 as xu').' from '.tabname('config').' where webid='.goif($tcz['log']=='config_args',1,$website['webid']).goif($keyword!='',' and (LOCATE("'.$keyword.'",`title`)>0 or LOCATE("'.$keyword.'",`content`)>0 or varname="'.$keyword.'")').goif($tcz['log']=='config_args_theme',' and cata="theme"',goif($keytype!='',' and cata="'.$keytype.'"')).' order by contype asc,xu desc,cata desc,sort asc,id asc';
$list=db_getpage($sql,setup_am_page,$tcz['page']);
foreach($list['list'] as $val){
	$title=$val['title'];
	$content=$val['content'];
	if($keyword!=''){
		$title=str_replace($keyword,'<span class="list_red">'.$keyword.'</span>',$title);
		$content=str_replace($keyword,'<span class="list_red">'.$keyword.'</span>',$content);
		if($keyword==$val['varname'])$val['varname']='<span class="list_red">'.$val['varname'].'</span>';
		}
	if(isset($cata_arr[$val['cata']]))$val['cata']=$cata_arr[$val['cata']];
	$res.='<tr dataid="'.$val['id'].'" class="tr2" onmouseover="changelist(this,\'on\')" onmouseout="changelist(this,\'out\')" onmousedown="changelist(this,\'down\')" ondblclick="openshow({log:\''.$tcz['log'].'\',id:'.$val['id'].'})">
<td class="cen">'.$val['cata'].'</td>
<td><span class="list_green">'.$val['varname'].'</span></td>
<td><span class="list_green">'.$title.'</span></td>
<td>'.goif($val['infotype']=='pass',goif($val['varval']!='','********'),$val['varval']).'</td>
<td class="cen">'.$infotype[$val['infotype']].'</td>
<td>'.$content.'</td>
<td class="cen">'.goif(!$val['isview'],'<span class="list_gray">是</span>','<span class="list_red">否</span>').'</td>
<td class="cen">'.goif(!$val['contype'],'<span class="list_gray">是</span>','<span class="list_red">否</span>').'</td>
<td class="cen">'.$val['sort'].'</td>
</tr>';
	}
$res.='</table>';
if(!$list['num'])$res.='<div class="ui_none">未找到相关记录</div>';
//else $res.=$list['page'];
ajaxreturn(0,$res,$btn,$list['page']);