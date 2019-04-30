<?php
function deldir_install($dir,$delpar=true){
if(!preg_match('/\/$/',$dir))$dir.='/';
if(!is_dir($dir))return;
$dh=opendir($dir);
while($file=readdir($dh)){
	if($file!="."&&$file!=".."){
		$fullpath=$dir.$file;
		if(!is_dir($fullpath)){
			@unlink($fullpath);
		}else{
			@deldir_install($fullpath,true);
			}
		}
	}
closedir($dh);
if($delpar){
	if(rmdir($dir)){
		return true;
	}else{
		return false;
		}
	}else return true;
}
function credir_install(){
$path_temp='template/'.install_webid.'/';
$path_up='upload/'.install_webid.'/';
createDirs($path_temp.'config/');
createDirs($path_temp.'runtime/cache/');
createDirs($path_temp.'runtime/temp/');
createDirs($path_up.'admin/');
createDirs($path_up.'myphoto/');
createDirs($path_up.'article/');
createDirs($path_up.'config/');
createDirs($path_up.'customer/');
createDirs($path_up.'down/');
createDirs($path_up.'label/');
createDirs($path_up.'temp/');
createDirs($path_up.'tool/');
createDirs($path_up.'feedback/');
createDirs($path_up.'special/');
}
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
function updateallweb($webid=0,$savefile=false){
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
	if($savefile)updatacofig($w['webid']);
	foreach($langarr as $l){
		$en=current(explode('|',$l));
		if($savefile)updatacofig($w['webid'],$en);
		}
	}
db_del('config','contype=2 and webid>1');
}
function updatacofig($webid,$uptype='global'){
$website=db_getshow('website','*','webid='.$webid.' order by isdef desc,isadmin desc');
switch($uptype){
	case 'global':
		$post_file='template/'.$website['webid'].'/config/global.php';
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
		$post_file='template/'.$website['webid'].'/config/'.$uptype.'.php';
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
function updateallconf(){
$post_file='include/config.php';
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
function checktable($sqldata,$cata='',$webid=0,$clean='no'){
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
		if($clean=="ok"){	//删除表
			$result=mysql_query("DROP TABLE `".$tname."`");
		}else{
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