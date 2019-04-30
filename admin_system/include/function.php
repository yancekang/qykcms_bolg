<?php
//写入管理员日志
function infoadminlog($webid,$user,$oper,$content){
$user_ip=getip();
$times=time()-1800;
$logcount=db_count('admin_log','webid='.$webid.' and time_add>='.$times.' and oper_type='.$oper.' and user_admin="'.$user.'" and user_ip="'.$user_ip.'" and content="'.$content.'"')+0;
if($logcount)return;
$tab='webid,user_admin,user_ip,oper_type,content,time_add';
$val=$webid.',"'.$user.'","'.$user_ip.'",'.$oper.',"'.$content.'",'.time();
db_intoshow('admin_log',$tab,$val);
}
//清除缓存文件
function cleardatacache($log,$data){
$file='../'.setup_webfolder.$data['webid'].'/runtime/cache/'.$data['languages'].'/'.$data['mark'].'/article_'.$data['dataid'].'.html';
if(file_exists($file))@unlink($file);
$file_mobile='../'.setup_webfolder.$data['webid'].'/runtime/cache/'.$data['languages'].'_mobile/'.$data['mark'].'/article_'.$data['dataid'].'.html';
if(file_exists($file))@unlink($file_mobile);
}
//在数据库中创建字段
function addfield($tname,$fieldname,$insql=''){
global $db;
$fie=mysql_num_rows(mysql_query("DESCRIBE ".tabname($tname)." `".$fieldname."`"));
if($fie)return false;
$sql="ALTER TABLE `".tabname($tname)."` ADD `".$fieldname."` ".goif($insql=="","VARCHAR(200) DEFAULT ''",$insql);
db_run($sql);
return true;
}
//删除字段
function delfield($tname,$fieldname){
global $db;
$list=$db->fetch_all($db->query('SHOW COLUMNS FROM '.tabname($tname)));
foreach($list as $f){
	if(strtolower($f['Field'])==strtolower($fieldname)){
		$sql="ALTER TABLE `".tabname($tname)."` DROP `".$fieldname."`;";
		db_run($sql);
		return true;
		break;
		}
	}
return false;
}
//更新全局系统设置
function updateglobal($data){
$arr=unserialize($data);
if(!is_array($arr)||empty($arr))return false;
if(!count($arr))return false;
db_upshow('config','contype=2','webid=1 and contype=0');
foreach($arr as $v){
	$conf=db_getshow('config','*','webid=1 and contype=2 and varname="'.$v['varname'].'" order by id asc');
	if($conf){
		if($v['vartype']==$conf['vartype'])$v=deltable($v,'varval');
		db_uparr('config',$v,'webid=1 and contype=2 and id='.$conf['id']);
	}else{
		db_intoarr('config',$v);
		}
	}
db_del('config','contype=2 and webid=1');
return true;
}
//同步站点全局系统设置
function updateallweb($webid=0){
$web=db_getall('websetup','*',goif($webid>1,'webid='.$webid));
$conf=db_getall('config','*','webid=1 and cata!="basic"');
db_upshow('config','contype=2',goif($webid>1,'webid='.$webid,'webid>1').' and contype=0');
foreach($web as $w){
	$lang=db_getone('config','varval','webid='.$w['webid'].' and varname="setup_language"');
	if($lang)$langarr=explode(',',$lang);
	else $langarr=array('cn|中文');
	foreach($conf as $c){
		$c=deltable($c,'id');
		$c=deltable($c,'webid');
		if($c['cata']=='basic')continue;
		if($c['cata']=='web'){
			foreach($langarr as $l){
				$en=current(explode('|',$l));
				$c2=$c;
				$c2['cata']=$en;
				$web_conf=db_getshow('config','*','webid='.$w['webid'].' and cata="'.$en.'" and varname="'.$c['varname'].'"');
				if($web_conf){
					$c2=deltable($c2,'varval');
					db_uparr('config',$c2,'id='.$web_conf['id']);
				}else{
					$c2['webid']=$w['webid'];
					db_intoarr('config',$c2);
					}
				}
		}else{
			$web_conf=db_getshow('config','*','webid='.$w['webid'].' and varname="'.$c['varname'].'"');
			if($web_conf){
				$c=deltable($c,'varval');
				db_uparr('config',$c,'id='.$web_conf['id']);
			}else{
				$c['webid']=$w['webid'];
				db_intoarr('config',$c);
				}
			}
		}
	updatacofig($w['webid']);
	foreach($langarr as $l){
		$en=current(explode('|',$l));
		updatacofig($w['webid'],$en);
		}
	}
db_del('config','contype=2 and webid>1');
}
//更新全局基础配置
function updateallconf(){
$post_file='../include/config.php';
$conf=db_getall('config','*','webid=1 and cata="basic"');
if(!$conf)return false;
$html="<"."?php
include('config_db.php');
include('config_qyk.php');
/*----基础配置更新于".date('Y-m-d H:i:s')."----*/";
foreach($conf as $v){
	$pv=$v['varval'];
	$html.="
define('".$v['varname']."',".goif($v['vartype']==1,"'".$pv."'",$pv).");";
	}
$html.="
?".">";
@$file=fopen($post_file,'w');
if(!$file)return false;
fwrite($file,$html);
fclose($file);
return true;
}
//更新系统设置
function updatacofig($webid,$uptype='global'){
$website=db_getshow('website','*','webid='.$webid.' order by isdef desc,isadmin desc');
switch($uptype){
	case 'global':
		$post_file='../'.setup_webfolder.$website['webid'].'/config/global.php';
		$html="<"."?php
/*----系统设置更新于".date('Y-m-d H:i:s')."----*/
define('setup_weburl','".$website['setup_weburl']."');
define('setup_record','".$website['setup_record']."');";
		$catalist=explode(',',setup_am_setup_cata);
		foreach($catalist as $cv){
			$iv=explode('|',$cv);
			$conf=db_getlist('select * from '.tabname('config').' where webid='.$website['webid'].' and cata="'.$iv[0].'" and contype<2 order by sort asc,id asc');
			foreach($conf as $v){
				$pv=$v['varval'];
				$html.="
define('".$v['varname']."',".goif($v['vartype']==1,"'".$pv."'",$pv).");";
				}
			}
		$html.="
?".">";
		@$file=fopen($post_file,'w');
		if(!$file)return false;
		fwrite($file,$html);
		fclose($file);
	break;
	default:	//cn、en等
		$post_file='../'.setup_webfolder.$website['webid'].'/config/'.$uptype.'.php';
		$html="<?php
/*----网站配置更新于".date('Y-m-d H:i:s')."----*/";
		$conf=db_getlist('select * from '.tabname('config').' where webid='.$website['webid'].' and cata="'.$uptype.'" and contype<2 order by sort asc,id asc');
		foreach($conf as $v){
			$pv=$v['varval'];
			$html.="
define('".$v['varname']."',".goif($v['vartype']==1,"'".$pv."'",$pv).");";
			}
		$html.="
?>";
		@$file=fopen($post_file,'w');
		if(!$file)return false;
		fwrite($file,$html);
		fclose($file);
	break;
	}
return true;
}
//返回参数表单，仅tr无table
function getargsform($conf){
global $website;
$res='';
$conf_len=count($conf);
$k=0;
foreach($conf as $v){
	$conf_len--;
	if($k==0)$res.='<tr>';
	else if($v['infotype']=='text'){
		$res.='<td class="td3" colspan=2>&nbsp;</td></tr><tr>';
		}
	$res.='<td class="td1"><span class="help" title="'.goif($v['content']!='',$v['content'].'<br>').'调用标签：<span class=\'blue\'>'.setup_prefix.$v['varname'].setup_suffix.'</span>">'.$v['title'].'</span></td>';
	if($conf_len==0&&$k==0){
		$res.='<td class="td5" colspan=3>';
	}else if($v['infotype']=='text'){
		$k++;
		$res.='<td class="td5" colspan=3>';
	}else{
		$res.='<td class="td2">';
		}
	//$res.=goif($conf_len%2!=0&&$k==($conf_len-1),'<td class="td5" colspan=3>','<td class="td2">');
	switch($v['infotype']){
		case 'inp':
			switch($v['varname']){
				case 'setup_language':
					$res.='<input style="width:192px" tag="postinp" type="text" id="post_'.$v['varname'].'" value="'.$v['varval'].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp"').'><input type="button" class="inp_btn" value="设置" onclick="openshow({log:\'config_admin_language\'})">';
				break;
				default:
					$res.='<input tag="postinp" type="text" id="post_'.$v['varname'].'" value="'.$v['varval'].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp"').'>';
				break;
				}
		break;
		case 'text':
			$res.='<textarea tag="postinp" id="post_'.$v['varname'].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp_tex"').'>'.$v['varval'].'</textarea>';
		break;
		case 'pass':
			$res.='<input tag="postinp" type="password" id="post_'.$v['varname'].'" value="'.$v['varval'].'" '.goif($v['isedit']==1,'class="inp_no" readonly','class="inp"').'>';
		break;
		case 'upload':
			$res.='<textarea onfocus="this.blur()" class="inp_up" tag="postinp" id="post_'.$v['varname'].'" '.goif($v['isedit']==1,'placeholder="已禁用上传"',' placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'none\',obj:$(this)})"').'>'.$v['varval'].'</textarea>';
		break;
		case 'select':
			if($v['varname']=='setup_language_def'){
				$v['infosel']=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"');
				}
			$opt=explode(',',$v['infosel']);
			$btn='';
			$res.='<select tag="postinp" id="post_'.$v['varname'].'" isedit="'.goif($v['isedit']==1,'no').'"';
			switch($v['varname']){
				case 'setup_tool_customer':
				case 'setup_tool_skype':
					$langdef=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language_def"');
					$res.=' sw=192>';
					$btn='<input type="button" class="inp_btn" value="设置" onclick="closepar({callback:function(){infomenu_click({log:\'lmenu_customer_'.$langdef.'\'})}})">';
				break;
				}
			$res.='>';
			foreach($opt as $sxu=>$s){
				$stext=$s;
				$sval=$s;
				if(strstr($s,'|')){
					$sarr=explode('|',$s);
					$stext=$sarr[1];
					$sval=$sarr[0];
					}
				//if($v['varname']=='setup_web_close'&&$sxu==3&&$sval!=3)$res.='';
				if($v['varname']=='setup_web_close'){
					if($v['varval']==3&&$sxu!=3)continue;
					if($sxu==3&&$v['varval']!=3)continue;
					}
				$res.='<option value="'.$sval.'"'.goif($v['varval']==$sval,' selected').'>'.$stext.'</option>';
				}
			$res.='</select>'.$btn;
		break;
		}
	$res.='</td>';
	if($k==1)$res.='</tr>';
	$k++;
	if($k>=2)$k=0;
	}
if($res=='')$res='<tr><td class="td7_2"><div class="ui_none">当前栏目暂无可设置参数</div></td></tr>';
return $res;
}
//在所有站点的上传目录下创建文件夹
function createallupfolder($folder){
$web=db_getall('websetup','*');
foreach($web as $w){
	$path='../'.setup_upfolder.$w['webid'].'/'.$folder.'/';
	createDirs($path);
	}
}
//生成唯一ID
function getdataid($webid,$tname,$tab='dataid'){
$dataid=db_getone($tname,$tab,goif($webid,'webid='.$webid,'1=1').' order by '.$tab.' desc');
if(!$dataid)$dataid=0;
$dataid=$dataid+1;
return $dataid;
}
//改变classid
function chclsid($webid,$classid){
$mod=db_getshow('module','id,menutype','webid='.$webid.' and classid='.$classid);
if(!$mod)return;
$newid=getdataid($webid,'module','classid');
db_upshow('module','classid='.$newid,'id='.$mod['id']);
switch($mod['menutype']){
	case 0:
		db_upshow('module','bcat='.$newid,'webid='.$webid.' and bcat='.$classid);
		db_upshow('article','bcat='.$newid,'webid='.$webid.' and bcat='.$classid);
	break;
	case 1:
		db_upshow('module','scat='.$newid,'webid='.$webid.' and scat='.$classid);
		db_upshow('article','scat='.$newid,'webid='.$webid.' and scat='.$classid);
	break;
	case 2:
		db_upshow('article','lcat='.$newid,'webid='.$webid.' and lcat='.$classid);
	break;
	}
}
//统计空间占用情况
function countcapacity($webid,$save=true){
$ver=db_getshow('version','*');
if($ver['model']==1){
	$have1=getdirsize(setup_webfolder.$webid.'/')/1024;	//程序占用空间大小
	$have2=getdirsize(setup_upfolder.$webid.'/')/1024;	//上传文件占用空间大小
	$capacity_have=$have1+$have2;
}else{
	$capacity_have=getdirsize()/1024;	//如果是单用户版本则直接统计根目录
	}
if($save){
	$capacity_have=ceil($capacity_have);
	db_upshow('websetup','capacity_have='.$capacity_have,'webid='.$webid);
	}
return $capacity_have;
}
//判断是否有权限
function ispower($group,$rank){
if($group['config_super']==1)return true;
else if($rank=='super')return false;
if(strstr($group['config_rank'],'|'.$rank.'|'))return true;
return false;
}
//管理日志分类
function getadmincata($types,$relst=false){
$arr=array(
	'1'=>'后台登录',
	'2'=>'管理权限',
	'11'=>'备份恢复',
	'12'=>'栏目分页',
	'13'=>'网站内容',
	'14'=>'缓存管理',
	'15'=>'系统设置',
	'16'=>'客服资料',
	'17'=>'广告管理',
	'18'=>'留言管理',
	'19'=>'高级管理',
	'20'=>'自定义标签',
	'21'=>'邮箱群发',
	'22'=>'修改头像',
	'23'=>'附件管理',
	'24'=>'安装主题',
	'25'=>'域名绑定',
	'26'=>'参数设置',
	'27'=>'自定义选项',
	'28'=>'专题管理'
	);
if($relst)return $arr;
return $arr[$types];
}
//栏目数据类型,大于等于10是自定义模块
function getmoduletype($types,$relst=false){
$arr=array(
	1=>'单页内容',
	2=>'文章列表',
	3=>'产品列表',
	4=>'照片列表',
	5=>'简易列表',
	6=>'链接列表',
	8=>'独立页面',
	9=>'指定链接'
	);
if($relst)return $arr;
if($types>10)return '自定义模块';
return $arr[$types];
}
//用户状态
function getadminusertype($types,$relst=false){
$arr=array(
	'0'=>'待审',
	'1'=>'正常',
	'2'=>'冻结'
	);
if($relst)return $arr;
return $arr[$types];
}
//自定义模块输入类型
function getusermodinfotype($types,$relst=false){
$arr=array(
	'inp'=>'单行文本框',
	'text'=>'多行文本框',
	'num'=>'数字输入框',
	'up_cover'=>'上传图片',
	'up_file'=>'上传文件（不限类型）',
	'pass'=>'密码输入框',
	'editor'=>'文章编缉器',
	'select'=>'下拉菜单',
	'option'=>'自定义选项',
	'option_more'=>'自定义选项（多选）',
	'range'=>'滑块条范围',
	'date'=>'日期（年-月-日）',
	'date-short'=>'短日期（月-日）',
	'month'=>'月份（年-月）',
	'time'=>'长时间1（年-月-日 时:分）',
	'time-long'=>'长时间2（年-月-日 时:分:秒）'
	);
if($relst)return $arr;
return $arr[$types];
}
//用户昵称_后台用
function getadminusername($uid,$gtype='ucode'){
if(!$uid)return '系统';
$member=db_getshow('member','user_code,user_name','id='.$uid);
if(!$member)return '用户'.$uid;
if($gtype=='ucode')return $member['user_code'];
$uname=$member['user_name'];
//if($uname==$member['user_code'])$uname=uname_short($uname);
return $uname;
}
//评论留言
function getcomment_admin($str){
global $website;
$str=preg_replace('/{img:([^\}]+)}/isU','<img name="face" src="http://'.$website['setup_weburl'].'/\\1">',$str);
$str=preg_replace('/{br}/isU','<br>',$str);
return $str;
}
//返回链接地址
function getadminlink($link){
global $webdomain;
$url='http://'.$webdomain.'/index.php?'.$link;
return $url;
}
//保存excel
function excelreturn($res,$downfile='excel'){
global $website;
$downfile=$downfile.'_'.date('Ymd_His').'_'.randomkeys(8).'.xls';
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:filename=".$downfile);
header("Content-type:text/html; charset=utf-8");
$res=preg_replace('/<table([^\>]+)>/','<table border=1 cellpadding=10 cellspacing=10>',$res);
$res=preg_replace('/<tr([^\>]+)>/','<tr>',$res);
$res=preg_replace('/<td([^\>]+)>/','<td>',$res);
$res='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$res;
$filepath=setup_upfolder.$website['webid'].'/'.setup_uptemp.$downfile;
@$file=fopen('../'.$filepath,'w');
if(!$file)ajaxreturn(1,'站点目录权限不足，生成导出文件失败');
fwrite($file,$res);
fclose($file);
ajaxreturn(0,'http://'.$website['setup_weburl'].'/'.$filepath);
exit;
}
//计算指定文件夹占用空间大小
function getdirsize($path=''){
$dir=new RecursiveDirectoryIterator('../'.$path);
$totalSize=0;
foreach(new RecursiveIteratorIterator($dir) as $file){
	@$ss=$file->getSize();
	@$totalSize+=$ss;
	}
return $totalSize;
}
//问候语
function gethello(){
$strTimeToString = "000111222334455556666667";
$strWenhou = array('夜已深，该休息了','凌晨了，还在奋斗吗','早上好，新的一天开始了！','上午好！','中午好！','下午好！','晚上好！','夜已深，该休息了');
return $strWenhou[(int)$strTimeToString[(int)date('G',time())]];
}
//处理编辑器内容
function handleImage($cont,$oldimg='',$ml='article'){
global $website;
$newimg='';
preg_match_all('/<img(.+?)src(.+?)"([^\"\\\]+)([^\>]+)>/',$cont,$pic);
$newnum=count($pic[3]);
if($newnum>100)ajaxreturn(1,'您上传的图片过多，请控制在100张图片以内');
if($newnum>0){
	foreach($pic[3] as $val){
		$img=strtolower($val);
		if(strstr($img,'/'.setup_upfolder.$website['webid'].'/'.setup_uptemp)){	//如果该图片在临时目录，则转存到正式目录
			$filepath=$ml.'/'.date('Y_m').'/';
			//createDirs('../'.setup_upfolder.$filepath);
			$fname=getstr($img.'|','/'.setup_upfolder.$website['webid'].'/'.setup_uptemp,'|');
			$tempfile='../'.setup_upfolder.$website['webid'].'/'.setup_uptemp.$fname;
			if(file_exists($tempfile)){
				copy($tempfile,'../'.setup_upfolder.$website['webid'].'/'.$filepath.$fname);	//保存图片
				$newimg.='|'.$filepath.$fname;
				$cont=str_replace($img,setup_uploadtag.$filepath.$fname,$cont);
				}
		}else if(strstr($img,'http://'.$website['setup_weburl'].'/'.setup_upfolder.$website['webid'].'/')){
			$imgurl=str_replace('http://'.$website['setup_weburl'].'/'.setup_upfolder.$website['webid'].'/','',$img);
			$cont=str_replace($img,setup_uploadtag.$imgurl,$cont);
			$newimg.='|'.$imgurl;
			}
		}
	}
if($oldimg!=''&&$oldimg!=$newimg){
	$arr2=explode('|',$oldimg);
	for($i=1;$i<count($arr2);$i++){
		if(!strstr($newimg,$arr2[$i])){
			$delpath='../'.setup_upfolder.$website['webid'].'/'.$arr2[$i];
			if(file_exists($delpath))unlink($delpath);	//删除图片
			}
		}
	}
$cont=preg_replace('/<i?frame(.*?)ueditor_baidumap(.*?)#center\=([^&]+)&zoom\=([^&]+)&width\=([^&]+)&height\=([^&]+)&markers\=([^&]+)&([^\>]+)><\/i?frame>/i','{map=$3,zoom=$4,width=$5,height=$6,markers=$7}',$cont);
$cont=preg_replace_callback('/<pre([^\>]+)>(.+?)<\/pre>/i',"getpre_in",$cont);
$cont=htmlspecialchars($cont);
return array('img'=>$newimg,'cont'=>$cont);
}
//代码入库整理
function getpre_in($mat){
$str=preg_replace('/<br>/i','\r\n',$mat[0]);
return $str;
}
//代码显示整理
function getpre($mat){
$str=preg_replace('/\n/i','<br>',$mat[0]);
return $str;
}
//内容还原
function getreset_admin($str){
global $website;
if($str!=''){
	$str=htmlspecialchars_decode($str);
	$str=str_replace(setup_uploadtag,'http://'.$website['setup_weburl'].'/'.setup_upfolder.$website['webid'].'/',$str);
	$str=preg_replace('/{map=([\.\0-9\,]+),zoom=([^,]+),width=([^,]+),height=([^,]+),markers=([^\}]+)}/isU','<iframe class="ueditor_baidumap" src="dialogs/map/show.html#center=$1&zoom=$2&width=$3&height=$4&markers=$5&markerStyles=l,A" frameborder="0" width="$3" height="$4"></iframe>',$str);
	//正则
	$str=preg_replace('/<(\/?)(script|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU','&lt;\\1\\2\\3&gt;',$str);
	//$str=preg_replace('/<pre class="brush:([^\>]+)>((.*?)(\n)(.*?))+<\/pre>/i','<pre$1>$3$4$5</pre>',$str);
	$str=preg_replace_callback('/<pre([^\>]+)>([^<]+)<\/pre>/i',"getpre",$str);
	//$str=preg_replace('/\n/i','<br>',$str);
	@$str=preg_replace_callback('/<[a-zA-Z\/][^>]*>/',tosafehtmlcallback,$str);
	$str=str_replace(array("\r\n","\n","\r"),"",$str);
	$str=str_replace(PHP_EOL,'', $str);
	$str= preg_replace("/([\s]{2,})/","",$str);
	$str=trim($str);
	}
return $str;
}
//获取文件路径 $size=s、m、b
function getfile_admin($gtype,$furl,$size='s'){
global $website;
if($furl==''){
	switch($gtype){
		case "head":		//60,120,200
			$furl='images/ui/nohead_{size}.gif';
		break;
		case "pic":
			$furl='images/ui/nopic.gif';
		break;
		}
}else{
	$furl=setup_upfolder.$website['webid'].'/'.$furl;
	}
if($size!='none')$furl=str_replace('{size}',$size,$furl);
$furl='/'.$furl;
return $furl;
}
//根据数组生成下拉菜单
function selecthtml($sid,$arr,$value='',$selhtml=''){
$res='<select id="'.$sid.'"'.goif($selhtml!='',' '.$selhtml).'>';
foreach($arr as $key=>$val){
	$res.='<option value="'.$key.'"'.goif($value==$key,' selected').'>'.$val.'</option>';
	}
$res.='</select>';
return $res;
}
//随机生成指字个数的汉字
function infocntext($n){
if(!$n)return '';
$cnstr='的一是在了不和有大这主中人上为们地个用工时要动国产以我到他会作来分生对于学下级就年阶义发成部民可出能方进同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批如应形想制心样干都向变关点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫康遵牧遭幅园腔订香肉弟屋敏恢忘衣孙龄岭骗休借丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩';
$newStr=preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $str);
$text='';
for($i=0;$i<$n;$i++){
	$randnum=rand(0,mb_strlen($cnstr,'utf-8')-1);
	$text.=mb_substr($cnstr,$randnum,1,'utf-8');
	}
return '<span style="widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;display:inline !important;font:0px/0px arial;white-space:normal;orphans:2;float:none;letter-spacing:normal;color:#F37545;word-spacing:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;">'.$text.'</span>';
}
//群发邮件
function sendmail_admin($sendtype,$smtp_server,$smtp_port,$smtp_user,$smtp_pass,$mailme,$mailto,$mailtitle,$mailbody,$mailtip=''){
switch($sendtype){
	case 1:	//smtp方式发送
		include('../include/class_smtp.php');
		if($mailtip=='')$mailtip=$mailtitle;
		$mailbody=getreset_admin($mailbody);
		$html='<table border="0" cellspacing="0" cellpadding="1" align="center" style="width:820px;border:1px solid #ddd;font-family:Microsoft YaHei,Tahoma,STHeiti,Arial,sans-serif;box-shadow:0 0 20px #777;">
<tr><td style="text-indent:20px;color:#ffffff;font-weight:bold;font-size:32px;background:#319400" height="60">'.$mailtip.'</td></tr>
<tr><td style="padding:20px;font-size:16px;word-wrap:break-word;line-height:200%;background:#ffffff">'.$mailbody.'</td></tr>
</table>';
		$smtp=new smtp($smtp_server,$smtp_port,true,$smtp_user,$smtp_pass);
		//$smtp->debug = false;//是否显示发送的调试信息
		$smtp->sendmail($mailto,$mailme,$mailtitle,$html,'HTML');
	break;
	}
}
//删除指定数组值
function deltable($arr,$keys){
$i=0;
foreach($arr as $sort=>$val){
	if($sort==$keys){
		array_splice($arr,$i,1);
		break;
		}
	$i++;
	}
return $arr;
}
//读取文件
function readtemp_admin($tempurl,$errmsg=''){
if($errmsg!='none'){
	@$temphtml=file_get_contents($tempurl) or ajaxreturn(1,goif($errmsg!='',$errmsg,'文件不存在：'.$tempurl));
}else{
	@$temphtml=file_get_contents($tempurl);
	}
return $temphtml;
}
//复制文件夹
function copydir($strSrcDir, $strDstDir){
$dir=opendir($strSrcDir);
if(!$dir)return false;
if(!is_dir($strDstDir)){
	if(!mkdir($strDstDir))return false;
	}
while(false!==($file = readdir($dir))){
	if (($file!='.') && ($file!='..')){
		if (is_dir($strSrcDir.'/'.$file)){
			if(!copydir($strSrcDir.'/'.$file, $strDstDir.'/'.$file))return false;
		}else{
			if(!copy($strSrcDir.'/'.$file, $strDstDir.'/'.$file))return false;
			}
		}
	}
closedir($dir);
return true;
}
//删除文件夹及子目录文件
function deldir_admin($dir,$delpar=true){
if(!preg_match('/\/$/',$dir))$dir.='/';
if(!is_dir($dir))return;
$dh=opendir($dir);
while($file=readdir($dh)){
	if($file!="."&&$file!=".."){
		$fullpath=$dir.$file;
		if(!is_dir($fullpath)){
			unlink($fullpath);
		}else{
			deldir_admin($fullpath,true);
			}
		}
	}
closedir($dh);
if($delpar){
	@$status=rmdir($dir);
	if($status)return true;
	else{
		return false;
		}
	}
return true;
}
//下载保存
function downfile($url,$savepath='',$filename=''){
if(@fopen($url,'r')){
	$savepath=$savepath.$filename;
	$file=file_get_contents($url);
	if(preg_match("/404/", $file)){
		return false;
		}
	file_put_contents($savepath,$file);
	return $filename;
}else{
	return false;
	}
}
//压缩包
function addFileToZip($path,$zip,$par=''){
if(!is_dir($path))return false;
$handler=opendir($path);
while(($filename=readdir($handler))!==false){
	if($filename=="."||$filename=="..")continue;
	if(is_dir($path."/".$filename)){
		addFileToZip($path."/".$filename, $zip,$par);
	}else if(file_exists($path."/".$filename)){
		if($par!='')$zip->addFile($path."/".$filename,$par.$path."/".$filename);
		else $zip->addFile($path."/".$filename);
		}
	}
@closedir($path);
}
//导出当前数据库结构
function importtable($tabdata=''){
global $db;
$tabarr=array();
if($tabdata==''){
	$tablist=$db->fetch_all($db->query('SHOW TABLES FROM `'.db_database.'`'));
	foreach($tablist as $tab){
		$tname=$tab['Tables_in_'.db_database];
		$tname2=preg_replace('/^'.db_tabfirst.'/i','qyk_',$tname);
		if(preg_match('/^qyk_article_([0-9]+)_([0-9]+)$/i',$tname2))continue;
		$field=$db->fetch_all($db->query('SHOW COLUMNS FROM `'.$tname.'`'));
		$arr=array('tab'=>$tname2,'fie'=>array());
		if($field){
			$arr['fie']=$field;
			}
		array_push($tabarr,$arr);
		}
}else{
	$tablist=explode(',',$tabdata);
	foreach($tablist as $tab){
		$tname=tabname($tab);
		$field=$db->fetch_all($db->query('SHOW COLUMNS FROM `'.$tname.'`'));
		$tname2=preg_replace('/^'.db_tabfirst.'/i','qyk_',$tname);
		$tname2=preg_replace('/^qyk_article_([0-9]+)_([0-9]+)$/i','qyk_article_{webid}_$2',$tname2);
		$arr=array('tab'=>$tname2,'fie'=>array());
		if($field)$arr['fie']=$field;
		array_push($tabarr,$arr);
		}
	}
$sqldata=serialize($tabarr);
return $sqldata;
}
//恢复数据库结构
function checktable($sqldata,$cata='',$webid=0){
@$sqlarr=unserialize($sqldata);
if(!is_array($sqlarr)||empty($sqlarr))return false;
if(!count($sqlarr))return false;
foreach($sqlarr as $list){
	if($cata=='theme'){
		if(!preg_match('/^qyk_article/',$list["tab"]))continue;
		$list['tab']=str_replace('{webid}',$webid,$list['tab']);
		}
	$tname=str_replace('qyk_',db_tabfirst,$list["tab"]);
	$result=mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$tname."'"));
	if($result==1){
		$prezd="";
		foreach($list['fie'] as $fie){
			$zd=mysql_num_rows(mysql_query("DESCRIBE `".$tname."` `".$fie['Field']."`"));
			if(!$zd){
				$zdsql="ALTER TABLE `".$tname."` add `".$fie['Field']."` ".$fie['Type'].goif($fie['Null']=="NO"," NOT NULL");
				if($fie["Extra"]=="auto_increment"){
					$zdsql.=" AUTO_INCREMENT PRIMARY KEY";
				}else if($fie['Default']!="" || strstr($fie['Type'],"varchar") ||strstr($fie['Type'],"text")){
					$zdsql.=" DEFAULT '".$fie['Default']."'";
					}
				if($prezd!="")$zdsql.=" AFTER `".$prezd."`";
				else $zdsql.=" FIRST";
				$status=db_run($zdsql);
				}
			$prezd=$fie["Field"];
			}
		}
	$tabsql="CREATE TABLE IF NOT EXISTS `".$tname."` (";
	foreach($list['fie'] as $fie){
		$tabsql.="`".$fie['Field']."` ".$fie['Type'].goif($fie['Null']=="NO"," NOT NULL");
		if($fie["Extra"]=="auto_increment"){
			$tabsql.=" AUTO_INCREMENT";
		}else if($fie['Default']!="" || strstr($fie['Type'],"varchar") ||strstr($fie['Type'],"text")){
			$tabsql.=" DEFAULT '".$fie['Default']."'";
			}
		$tabsql.=",";
		}
	$tabsql.="PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	$status=db_run($tabsql);
	}
return true;
}
?>