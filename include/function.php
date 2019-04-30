<?php
date_default_timezone_set("PRC");
function nocache(){
header("Expires:Mon,26Jul199705:00:00GMT");
header("Pragma:no-cache");
header("Content-Type:text/html;charset=utf-8");
}
function setcook($uid=0,$ucode='',$uname='',$head='',$loginkey='',$times=0){
if($times){
	$times=$times*60;
	$times=time()+$times;
	if($uid){
		setcookie('cook_uid',$uid,$times,'/');
		$_COOKIE['cook_uid']=$uid;
		}
	if($ucode!=''){
		setcookie('cook_ucode',$ucode,$times,'/');
		$_COOKIE['cook_ucode']=$ucode;
		}
	if($uname!=''){
		setcookie('cook_uname',$uname,$times,'/');
		$_COOKIE['cook_uname']=$uname;
		}
	if($loginkey!=''){
		setcookie('cook_loginkey',$loginkey,$times,'/');
		$_COOKIE['cook_loginkey']=$loginkey;
		}
}else{
	if($uid){
		setcookie('cook_uid',$uid);
		$_COOKIE['cook_uid']=$uid;
		}
	if($ucode!=''){
		setcookie('cook_ucode',$ucode);
		$_COOKIE['cook_ucode']=$ucode;
		}
	if($uname!=''){
		setcookie('cook_uname',$uname);
		$_COOKIE['cook_uname']=$uname;
		}
	if($loginkey!=''){
		setcookie('cook_loginkey',$loginkey);
		$_COOKIE['cook_loginkey']=$loginkey;
		}
	}
}
function clearcook(){
$times=time()-3600;
setcookie('cook_uid',NULL,$times,'/');
setcookie('cook_ucode',NULL,$times,'/');
setcookie('cook_uname',NULL,$times,'/');
setcookie('cook_loginkey',NULL,$times,'/');
}
function checklogin($errmsg=true){
global $db,$cook;
$ltype=false;
if($cook['ucode']!=setup_ucode_guest&&$cook['login']){
	if($cook['ucode']=='none')$cook['ucode']=='';
	$rs=db_getshow('member','id,user_code,login_key','id='.$cook['uid'].' and user_code="'.$cook['ucode'].'" and user_name="'.$cook['uname'].'" and login_key="'.$cook['loginkey'].'"');
	if($rs)$ltype=true;
	}
if(!$ltype){
	clearcook();
	if($errmsg)ajaxreturn(1,'您尚未登录或已掉线，请先登录');
	}
else return $ltype;
}
function getcook(){
if(isset($_COOKIE['cook_uid'])&&isset($_COOKIE['cook_ucode'])&&isset($_COOKIE['cook_uname'])&&isset($_COOKIE['cook_loginkey'])){
	return array('login'=>true,'uid'=>$_COOKIE['cook_uid'],'ucode'=>$_COOKIE['cook_ucode'],'uname'=>$_COOKIE['cook_uname'],'loginkey'=>$_COOKIE['cook_loginkey']);
}else{
	if(isset($_COOKIE['cook_uname']))$uname=$_COOKIE['cook_uname'];
	else{
		$uname=getlang('sys','guest').mt_rand(11111,99999);
		setcook(0,'',$uname,'',1440);
		}
	return array('login'=>false,'uid'=>0,'ucode'=>'guest','uname'=>$uname,'loginkey'=>'');
	}
}
function is_mobile_request(){
$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
$mobile_browser = '0';
if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))$mobile_browser++;
if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))$mobile_browser++;
if(isset($_SERVER['HTTP_X_WAP_PROFILE']))$mobile_browser++;
if(isset($_SERVER['HTTP_PROFILE']))$mobile_browser++;  
$mobile_ua=strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
$mobile_agents=array(
	'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
	'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
	'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
	'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
	'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
	'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
	'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
	'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
	'wapr','webc','winw','winw','xda','xda-'
	);
if(in_array($mobile_ua, $mobile_agents))$mobile_browser++;  
if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)$mobile_browser++;  
if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)$mobile_browser=0;  
if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)$mobile_browser++;  
if($mobile_browser>0)return true;  
else return false;
}
function getyzimg($yzm){
$yzm=strtolower($yzm);
$yzm=preg_replace("/[^0-9a-z]/","",$yzm);
if(strlen($yzm)!=4)return false;
session_start();
if(!isset($_SESSION['session_yzm']))return false;
if($_SESSION['session_yzm']!=$yzm){
	$_SESSION['session_yzm']='';
	return false;
	}
$_SESSION['session_yzm']='';
return true;
}
function getdomain($url){
$weburl=strtolower($url);
$weburl=preg_replace("/^(http:\/\/)|^(https:\/\/)|\/$/","",$weburl);
$weburl=preg_replace("/^(www\.)/","",$weburl);
if(strstr($weburl,'/')){
	$arr=explode('/',$weburl);
	$weburl=$arr[0];
	}
return $weburl;
}
function checkerrkey($str){
$errkey=explode(',',setup_errkey);
for($i=0;$i<count($errkey);$i++){
	$keys=chunk_split($errkey[$i],3,".{0,3}");
	if(preg_match('/'.$keys.'/',$str)){
		return false;
		}
	}
return true;
}
function goif($term,$okhtml='',$nohtml=''){
if($term)return $okhtml;
else return $nohtml;
}
function arg($aname='log',$gtype='post',$atype='string',$len=0){
$val='';
switch($gtype){
	case "get":
		@$val=$_GET[$aname];
	break;
	case "post":
		@$val=$_POST[$aname];
	break;
	case "all":
		@$val=$_GET[$aname];
		if($val=='')@$val=$_POST[$aname];
	break;
	}
switch($atype){
	case 'int':
		if($val=='')$val='0';
		$val=sprintf('%.0f',$val);
	break;
	case 'num':
		$val=floatval($val);
		if($val<1)$val=1;
	break;
	case 'url':
		$val=trim($val);
		StopAttack($aname,$val,$gtype);
		$val=urldecode($val);
	break;
	case 'txt':
		$val=trim($val);
		StopAttack($aname,$val,$gtype);
		$val=urldecode($val);
		$val=htmlspecialchars($val);
	break;
	case 'rate':
		$val=urldecode($val);
		if($val=='')$val=0;
		$val=(float)$val;
		$val=sprintf('%.4f',$val);
	break;
	case 'decimal':
		$val=urldecode($val);
		if($val=='')$val=0;
		$val=(float)$val;
		$val=sprintf('%.'.goif($len,$len,2).'f',$val);
	break;
	case 'none':
	break;
	default:
		StopAttack($aname,$val,$gtype);
		$val=htmlspecialchars($val);
	break;
	}
return $val;
}
function checkstr($str,$ctype,$min=0,$max=0,$err='',$status=99){
$slen=strlen($str);
if(($min>0&&$slen<$min)||($max>0&&$slen>$max))ajaxreturn($status,$err.'长度不符合要求<br>必须在'.$min.'-'.$max.'个字节之间，当前'.$slen.'字节，1个汉字=2字节');
switch($ctype){
	case 'en':
		if(preg_match('/[^a-zA-Z]/',$str))ajaxreturn($status,$err.'错误，必须是大小写字母');
	break;
	case 'enint':
		if(!preg_match('|^[0-9a-zA-Z]+$|',$str))ajaxreturn($status,$err.'错误，必须是数字或字母');
	break;
	case 'email':
		if(!preg_match('/^[a-zA-Z0-9_.]+@([a-zA-Z0-9_]+.)+[a-zA-Z]{2,3}$/',$str))ajaxreturn($status,$err.'，必须是正确的电子邮箱地址');
	break;
	case 'none':
	break;
	}
}
//过滤危险参数，测试脚本，请勿删除
function StopAttack($StrFiltKey,$StrFiltValue,$gtype='get'){
if($gtype=='post')$req="^\\+\/v(8|9)|\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
else $req="'|<[^>]*?>|^\\+\/v(8|9)|\\b(and|or)\\b.+?(>|<|=|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$StrFiltValue=arr_foreach($StrFiltValue);
if (preg_match("/".$req."/is",$StrFiltValue)==1){
	//slog("<br><br>操作IP: ".$_SERVER["REMOTE_ADDR"]."<br>操作时间: ".strftime("%Y-%m-%d %H:%M:%S")."<br>操作页面:".$_SERVER["PHP_SELF"]."<br>提交方式: ".$_SERVER["REQUEST_METHOD"]."<br>提交参数: ".$StrFiltKey."<br>提交数据: ".$StrFiltValue);
	//print "<div style=\"position:fixed;top:0px;width:100%;height:100%;background-color:white;color:green;font-weight:bold;border-bottom:5px solid #999;\"><br>您的提交带有不合法参数,谢谢合作!</div>";
	//tipmsg('必须为string类型，存在非法的URL参数',true);
	//ajaxreturn(999,'必须为string类型，存在非法的URL参数');
	//exit();
	}
if(preg_match("/".$req."/is",$StrFiltKey)==1){   
	//slog("<br><br>操作IP: ".$_SERVER["REMOTE_ADDR"]."<br>操作时间: ".strftime("%Y-%m-%d %H:%M:%S")."<br>操作页面:".$_SERVER["PHP_SELF"]."<br>提交方式: ".$_SERVER["REQUEST_METHOD"]."<br>提交参数: ".$StrFiltKey."<br>提交数据: ".$StrFiltValue);
	//print "<div style=\"position:fixed;top:0px;width:100%;height:100%;background-color:white;color:green;font-weight:bold;border-bottom:5px solid #999;\"><br>您的提交带有不合法参数,谢谢合作!</div>";
	//tipmsg('必须为string类型，存在非法的URL参数',true);
	//ajaxreturn(999,'必须为string类型，存在非法的URL参数');
	//exit();
	}
}
function arr_foreach($arr) {
static $str;
if (!is_array($arr))return $arr;
foreach ($arr as $key => $val ) {
	if (is_array($val)) {
		arr_foreach($val);
	}else{
	  $str[] = $val;
		}
	}
return implode($str);
}
function tosafehtmlcallback($content){
$okcontent=substr($content[0],1);
$okcontent=preg_replace("/\son[a-zA-Z]+\s*=/"," ",$okcontent);
$okcontent=str_replace("<","&lt;",$okcontent);
return "<".$okcontent;
}
function getreset_img($ms){
if($ms[1]=='')$ms[1]=' ';
if(preg_match('/(onclick)+/i',$ms[0])){
	return '<img qykdelay="qyk_delayload"'.$ms[1].'src="'.$ms[3].'"'.$ms[5].'>';
}else{
	return '<img qykdelay="qyk_delayload" qykphoto="qyk_photobox"'.$ms[1].'src="'.$ms[3].'"'.$ms[5].'>';
	}
}
function getreset($str,$view=true,$showcode=false){
global $web;
if($str=='')return $str;
$str=htmlspecialchars_decode($str);
$str=str_replace(setup_uploadtag,'/'.setup_upfolder.$web['id'].'/',$str);
$str=preg_replace('/<(\/?)(script|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU','&lt;\\1\\2\\3&gt;',$str);
//$str=preg_replace_callback('/<[a-zA-Z\/][^>]*>/','tosafehtmlcallback',$str);
//$str=preg_replace('/<img([^\>]+)\>/isU','<img name="delayload" class="qyk_photobox"\\1>',$str);
$str=preg_replace_callback('/<img(.*)src=([\'\"].?)(.+)([\'\"].?)([^\>]+)\>/isU','getreset_img',$str);
$str=preg_replace('/{download\=([^\}]+)\}/isU','/'.setup_upfolder.$web['id'].'/down/$1',$str);
$str=preg_replace('/{map=([\.\0-9\,]+),zoom=([^,]+),width=([^,]+),height=([^,]+),markers=([^\}]+)}/isU','<iframe class="ueditor_baidumap" src="/res/map.html#center=$1&zoom=$2&markers=$5&markerStyles=l,A" frameborder="0" width="$3" height="$4"></iframe>',$str);
$str=preg_replace('/<a([^\>]+)\>/isU','<a\\1 onclick="return PZ.openlink(this)">',$str);
if($showcode){
	if(preg_match('/<pre class="brush:([^\>]+)>/i',$str)){
		$str.='<script>PZ.ready({callback:function(){PZ.showcode()}})</script>';
		}
	}
return $str;
}
function getcomment($str,$types=1){
switch($types){
	case 1:
		$str=preg_replace('/{img:([^\}]+)}/isU','<img name="face" src="/\\1">',$str);
		$str=preg_replace('/{br}/isU','<br>',$str);
	break;
	case 2:
		$str=preg_replace('/{img:([^\}]+)}/isU','{表情}',$str);
		$str=preg_replace('/{br}/isU',' ',$str);
	break;
	case 3:
		$str=preg_replace('/{img:([^\}]+)}/isU','<img name="face" src="http://'.setup_weburl.'/\\1">',$str);
		$str=preg_replace('/{br}/isU','<br>',$str);
	break;
	}
return $str;
}
function readtemp($tempurl,$errmsg='文件不存在：',$viewurl=true){
if($viewurl)$errmsg.=$tempurl;
@$temphtml=file_get_contents($tempurl) or tipmsg($errmsg,true);
return $temphtml;
}
function getfile($gtype,$furl,$size='s',$ml=true){
global $web;
if($furl==''){
	switch($gtype){
		case "head":
			$furl='images/ui/nohead_{size}.gif';
		break;
		case "pic":
			$furl='images/ui/nopic_{size}.gif';
		break;
		}
}else{
	$furl=setup_upfolder.$web['id'].'/'.$furl;
	}
if($size!='none')$furl=str_replace('{size}',$size,$furl);
if($ml)$furl='/'.$furl;
return $furl;
}
function getimes($times){
$s=time()-$times;
if(date('Y')==date('Y',$times)){
	if(date('m-d')==date('m-d',$times)){
		$i=ceil($s/60);
		$h=ceil($i/60);
		if($i<240){
			if($s<60){
				$times='<span class="blue">'.$s.getlang('time','be_second').'</span>';
			}else if($i<60){
				$times='<span class="blue">'.$i.getlang('time','be_minute').'</span>';
			}else if($i<120){
				$times='<span class="blue">2'.getlang('time','be_hour').'</span>';
			}else if($i<180){
				$times='<span class="blue">3'.getlang('time','be_hour').'</span>';
			}else{
				$times='<span class="blue">4'.getlang('time','be_hour').'</span>';
				}
		}else if(date('H',$times)<9)$times=date(getlang('time','morning').' H:i',$times);
		else if(date('H',$times)<12)$times=date(getlang('time','am').' H:i',$times);
		else if(date('H',$times)<18)$times=date(getlang('time','pm').' H:i',$times);
		else $times=date(getlang('time','night').' H:i',$times);
		$times='<span class="blue">'.$times.'</span>';
	}else if($times>=mktime(0,0,0,date("m"),date("d")-1,date("Y"))){
		$times='<span class="red">'.date(getlang('time','yesterday').' H:i',$times).'</span>';
	}else if($times>=mktime(0,0,0,date("m"),date("d")-6,date("Y"))){
		$times='<span class="green">'.ceil(abs($s/(3600*24))).getlang('time','be_day').'</span>';
	}else if($times>=mktime(0,0,0,date("m"),date("d")-10,date("Y"))){
		$times='<span class="green">'.getlang('time','be_week').'</span>';
	}else{
		$times=sprintf(getlang('time','short'),date('m',$times),date('d',$times));
		}
}else{
	$times=sprintf(getlang('time','long'),date('Y',$times),date('m',$times),date('d',$times));
	}
return $times;
}
function randomkeys($length,$type=1){
$pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
switch($type){
	case 2:
		$pattern='1234567890abcdefghijklmnopqrstuvwxyz';
	break;
	}
$key='';
for($i=0;$i<$length;$i++){
	$key.= $pattern{mt_rand(0,35)};
	}
return $key;
}
function createDirs($path){
$dirs=explode('/',$path);
$adir='';
for($i=0;$i<count($dirs)-1;$i++){
	$adir.=$dirs[$i].'/';
	if(!file_exists($adir)){
		@mkdir($adir);
		@chmod($adir,0777);
		}
	}
}
function clearDirs($dir){
if(!file_exists($dir))return false;
if(!is_empty_dir($dir)){
	$list=dir($dir);
	while($ml=$list->read()){
		if(is_file($dir.'/'.$ml)&&($ml!='.')&&($ml!='..'))unlink($dir.'/'.$ml);		//如果是文件直接删除
		if(is_dir($dir.'/'.$ml)&&($ml!='.')&& ($ml!='..')){	//如果是目录
			if(!is_empty_dir($dir.'/'.$ml))clearDirs($dir.'/'.$ml);
			}
		}
	}
return true;
}
function is_empty_dir($pathdir){
$d=opendir($pathdir);
$i=0;
while($a=readdir($d))$i++;   
closedir($d);
if($i>2){return false;}
else return true;
}
function getFiletype($path,$iscz=true){
if($iscz){
	$type_list = array("1"=>"gif","2"=>"jpg","3"=>"png","4"=>"swf","5" => "psd","6"=>"bmp","15"=>"wbmp");
	$img_info=@getimagesize($path);
	if(isset($type_list[$img_info[2]])){
		Return $type_list[$img_info[2]];
		}
	}else{
	return strtolower(pathinfo($path,PATHINFO_EXTENSION));
	}
}
function getchinesenum($num){
$arr=array('零','一','二','三','四','五','六','七','八','九','十');
return $arr[$num];
}
function tipshort($str,$len,$filter=true,$gl='...'){
if($str=='')return '';
if($filter){
	$str=str_replace('&nbsp;','',$str);
	$str=str_replace('&mdash;','—',$str);
	$str=str_replace("\r\n",'',$str);
	$str=str_replace('&ldquo;','“',$str);
	$str=str_replace("&rdquo;","”",$str);
	$str=str_replace("&mdash;","—",$str);
	$str=str_replace("&middot;","·",$str);
	$str=str_replace(PHP_EOL,' ', $str);
	$str=preg_replace('/\s*/', '', $str);
	$str=preg_replace('/<\/*[^>]*?>/','',$str);
	}
preg_match_all("/[^\x80-\xff]{2}|[^\x80-\xff]|[\x80-\xff]{3}/",$str,$arr);
if(count($arr[0])>$len){
	return join('',array_splice($arr[0],0,$len)).$gl;
}else{
	return join('',$arr[0]);
	}
}
function getstr($str,$one,$two,$xu=0){
if($str=='')return '';
if($one!=''){
	if(!strstr($str,$one))return '';
	}
$str2=explode($one,$str);
$val=explode($two,$str2[1]);
return $val[$xu];
}
function getip(){
if(!empty($_SERVER["HTTP_CLIENT_IP"]))$cip = $_SERVER["HTTP_CLIENT_IP"];
else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
else if(!empty($_SERVER["REMOTE_ADDR"]))$cip = $_SERVER["REMOTE_ADDR"];
else $cip = "127.0.0.1";
return $cip;
}
function hideaddress($string,$types='phone'){
switch($types){
	case 'ip':
		return preg_replace('/\.([0-9]+)$/','.*',$string);
	break;
	case 'email':
		$em=explode('@',$string);
		$emlen=strlen($em[0]);
		if($emlen>1){
			$str=substr($em[0],0,1);
			if($emlen>2)$str.='***'.substr($em[0],-1,1);
		}else{
			$str='*';
			}
		$str.='@'.$em[1];
		return $str;
	break;
	case 'phone':
		$pattern="/(1\d{1,2})\d\d(\d{0,3})/";
		$replacement="\$1*****\$3";
		return preg_replace($pattern,$replacement,$string);
	break;
	case 'bank':
		$str=substr($string,0,2);
		$str.='**********'.substr($string,-4);
		return $str;
	break;
	case 'idcard':
		$str=substr($string,0,2);
		$str.='**********'.substr($string,-4);
		return $str;
	break;
	case 'name':
		$arr=str_split($string,3);
		$arrlen=count($arr);
		$str='＊';
		for($i=1;$i<$arrlen;$i++){
			$str.=$arr[$i];
			}
		return $str;
	break;
	}
}
function getdatacache($webid,$folder){
global $tcz;
$file=setup_webfolder.$webid.'/runtime/cache/'.$folder.'/';
switch($tcz['log']){
	case 'index':
		$file.='index.html';
	break;
	case 'special':
		$file.=$tcz['log'].'/';
		if($tcz['id'])$file.='special_'.$tcz['id'].goif($tcz['page']>1,'_'.$tcz['page']).'.html';
		else if($tcz['lcat'])$file.='lcat_'.$tcz['lcat'].'_'.$tcz['page'].'.html';
		else if($tcz['scat'])$file.='scat_'.$tcz['scat'].'_'.$tcz['page'].'.html';
		else if($tcz['bcat'])$file.='bcat_'.$tcz['bcat'].'_'.$tcz['page'].'.html';
		else if($tcz['page'])$file.='index_'.$tcz['page'].'.html';
	break;
	default:
		$file.=$tcz['log'].'/';
		if($tcz['id'])$file.='article_'.$tcz['id'].goif($tcz['page']>1,'_'.$tcz['page']).'.html';
		else if($tcz['lcat'])$file.='lcat_'.$tcz['lcat'].'_'.$tcz['page'].'.html';
		else if($tcz['scat'])$file.='scat_'.$tcz['scat'].'_'.$tcz['page'].'.html';
		else if($tcz['page'])$file.='index_'.$tcz['page'].'.html';
	break;
	}
return $file;
}
function getlink($link){
if(!setup_static)return '/?'.$link;
$link=preg_replace('/^\?/','',$link);
$url='&'.$link.'&';
$log=urlargs($url,'log');
$word=urlargs($url,'word');
$seartype=urlargs($url,'seartype');
$scat=(int)urlargs($url,'scat');
$lcat=(int)urlargs($url,'lcat');
$id=(int)urlargs($url,'id');
$page=(int)urlargs($url,'page');
switch($log){
	case 'index':
		$link='/';
	break;
	default:
		$link='/'.$log.'/';
		if($id)$link.='article_'.$id.goif($page>1,'_'.$page).'.html';
		else if($lcat)$link.='lcat_'.$lcat.goif($page>1,'_'.$page).'.html';
		else if($scat)$link.='scat_'.$scat.goif($page>1,'_'.$page).'.html';
		else if($word!='')$link.='so?word='.$word.goif($seartype!='','&seartype='.$seartype).goif($page>1,'&page='.$page);
		else if($page>1)$link.='index_'.$page.'.html';
	break;
	}
return $link;
}
function urlargs($str,$aname){
if(!strstr($str,'&'.$aname.'='))return '';
$str2=explode('&'.$aname.'=',$str);
$val=explode('&',$str2[1]);
return $val[0];
}
function gotourl($link){
$link=getlink($link);
header('Location:'.$link);
exit;
}
function ajaxreturn($status=0,$data='',$other='',$cont='',$isheader=true){
if($data!=''){
	$data=str_replace(array("\r\n","\n","\r"),"",$data);
	$data=str_replace('"','\"',$data);
	}
if($other!=''){
	$other=str_replace(array("\r\n","\n","\r"),"",$other);
	$other=str_replace('"','\"',$other);
	}
if($cont!=''){
	$cont=str_replace(array("\r\n","\n","\r"),"",$cont);
	$cont=str_replace('"','\"',$cont);
	}
$res='{"status":'.$status.',"data":"'.$data.'","other":"'.$other.'","cont":"'.$cont.'"}';
if($isheader)header("Content-type: text/html; charset=utf-8");
die($res);
exit;
}
function getlang($barg,$sarg=''){
global $qyklang;
$lang=$qyklang[$barg];
if($sarg!='')$lang=$lang[$sarg];
return $lang;
}
function getwebclosemess($xu){
$arr=array(
	'1'=>'很抱歉，站点已暂停访问，请稍候再来',
	'2'=>'很抱歉，正在进行系统维护，请稍候再来',
	'3'=>'很抱歉，网站已被关闭'
	);
return $arr[$xu];
}
function tipmsg($msg='',$echo=false,$title='系统提示',$url=''){
$homepage='http://'.$_SERVER['SERVER_NAME'];
@$preurl=$_SERVER['HTTP_REFERER'];
if($preurl!=''){
	$preurl=preg_replace('/\/$/','',$preurl);
	}
$html='<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv=content-type content="text/html; charset=UTF-8" />
<title>'.$title.'</title>
<style>
body,html{color:#333;font-family:"Microsoft Yahei","Verdana","arial","宋体";padding:0px;margin:0px;width:100%;height:100%;background:#e7eaf4;overflow-x:hidden;TABLE-LAYOUT:fixed;WORD-BREAK:break-all;}
p,font,td{font-size:14px;color:#333}
a{color:#006600;text-decoration:none}
a:hover{color:#009900;text-decoration:underline}
.out{background:#fff;border:0px solid #0f9d58;padding:30px;width:680px;min-height:180px;margin:100px auto;border-radius:8px}
.icon{float:left;width:150px;height:180px;display:inline;overflow:hidden}
.cont{float:right;display:inline;width:500px}
.cont .msg{line-height:250%;width:100%;min-height:88px;font-size:16px}
.cont .golink{margin-top:30px;width:100%;display:inline;float:left;clear:both}
.cont .golink{width:100%;padding:0;margin:0}
.cont .golink li{list-style:none;padding:0;margin:0}
.cont .golink .tips{color:#006600;padding-top:30px;font-size:14px}
.cont .golink .links{width:100%;padding-top:12px;font-size:14px;color:#999}
.cont .golink .links a{font-size:14px;color:#999}
</style>
</head><body>
<div class="out">
	<div class="icon"><img width=150 src="/images/ui/tip_error.gif"></div>
	<div class="cont">
		<div class="msg">'.$msg.'</div>
		<ul class="golink">
<li class="tips">您还可以尝试其它操作</li>
'.goif($url!='','<li class="links">●　相关链接：<a href="'.$url.'">'.tipshort($url,20).'</a></li>').'
<li class="links">●　进入首页：<a href="'.$homepage.'">'.$homepage.'</a></li>
'.goif($preurl!=''&&$preurl!=$homepage,'<li class="links">●　返回上页：<a href="'.$preurl.'">'.tipshort($preurl,20).'</a></li>').'
		</ul>
	</div>
	<div style="clear:both"></div>
</div>
</body></html>';
if($echo){
	die($html);
}else{
	return $html;
	}
}
?>