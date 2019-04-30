<?php
define("install_file_table","install/data/install_table.txt");
define("install_file_config","install/data/install_config.txt");
define("install_file_theme","install/data/theme_data.txt");
define("install_file_theme_table","install/data/theme_table.txt");
define("install_webname","QYKCMS默认站点");
define('setup_am_setup_cata','setup|系统综合,tool|侧栏工具,upload|上传设置,smtp|邮件设置,theme|主题设置');
define("install_webid",10001);
$domain=strtolower(trim(arg("domain","post","url")));
$dbhost=arg("dbhost","post","url");
$dbname=arg("dbname","post","url");
$dbuser=arg("dbuser","post","url");
$dbpass=arg("dbpass","post","url");
$prefix=arg("prefix","post","url");
$user=strtolower(trim(arg("user","post","url")));
$pass=arg("pass","post","url");
$theme=arg("theme","post","url");
$clean=arg("clean","post","url");
$userlen=strlen($user);
checkstr($user,"enint",5,20,$err='网站管理员账号',1);
checkstr($user,"none",5,20,$err='网站管理员密码',1);
define("db_hostname",$dbhost);
define("db_database","");
define("db_tabfirst",$prefix);
define("db_username",$dbuser);
define("db_password",$dbpass);
define("db_charset","utf8");
include("install/lib.php");
include("include/class_mysql.php");
$db=new mysql(3);
$dblink=$db->select_db($dbname);
//保存数据库信息设置文件
$content="<"."?php
/*-------------数据库--------------*/
define('db_hostname','".$dbhost."');		//MYSQL数据库服务器
define('db_database','".$dbname."');		//MYSQL数据库名称
define('db_tabfirst','".$prefix."');			//数据表,表名前缀
define('db_username','".$dbuser."');			//数据库帐号
define('db_password','".$dbpass."');		//数据库密码
define('db_charset','utf8');			//数据库编码
?".">";
@$file=fopen("include/config_db.php","w");
if(!$file)ajaxreturn(1,"数据库设置文件include/config_db.php无法写入");
fwrite($file,$content);
fclose($file);
//创建数据库结构
if(!$dblink){
	$status=$db->query("CREATE DATABASE `".$dbname."`");
	if(!$status)ajaxreturn(1,"系统尝试创建“".$dbname."”数据库失败，请手动创建数据库");
	$dblink=$db->select_db($dbname);
	if(!$dblink)ajaxreturn(1,"系统尝试创建“".$dbname."”数据库失败，请手动创建数据库");
	}
@$sqldata=file_get_contents(install_file_table) or ajaxreturn(1,goif(1,"file_get_contents 读取 ".install_file_table." 文件失败"));
@$sqlarr=unserialize($sqldata);
if(!is_array($sqlarr)||empty($sqlarr))ajaxreturn(1,"载入数据结构失败，请检查文件完整性：".install_file_table);
if(!count($sqlarr))ajaxreturn(1,"载入数据结构失败，请检查文件完整性：".install_file_table);
checktable($sqldata,'',install_webid,$clean);
//版本信息
$isrecord=db_getshow('version','*');
$data=array(
	'version'=>install_version,
	'version_front'=>install_version_front,
	'uptype'=>'auto',
	'upmd5'=>'',
	'uptime'=>time(),
	'model'=>1
	);
if(!$isrecord){
	db_intoarr('version',$data);
}else{
	db_uparr('version',$data);
	}
//创建一个默认站点
$isrecord=db_getshow('websetup','*','webid='.install_webid);
if(!$isrecord){
	$data=array(
		'webid'=>install_webid,
		'status'=>0,
		'title'=>install_webname,
		'domainmax'=>2,
		'capacity_max'=>0,
		'capacity_have'=>0,
		'themedata'=>'',
		'visit'=>0,
		'time_add'=>time()
		);
	db_intoarr('websetup',$data);
	}
	
$isrecord=db_getshow('website','*','webid='.install_webid.' and setup_weburl="'.$domain.'"');
if(!$isrecord){
	$data=array(
		'webid'=>install_webid,
		'isdef'=>1,
		'isadmin'=>1,
		'setup_weburl'=>$domain,
		'setup_record'=>''
		);
	db_intoarr('website',$data);
	}
//初始化配置信息
@$confdata=file_get_contents(install_file_config) or ajaxreturn(1,goif(1,"file_get_contents 读取 ".install_file_config." 文件失败"));
$confarr=unserialize($confdata);
if(!is_array($confarr)||empty($confarr))ajaxreturn(1,"初始化配置信息失败，请检查文件完整性：".install_file_config);
if(!count($confarr))ajaxreturn(1,"初始化配置信息失败，请检查文件完整性：".install_file_config);
db_upshow('config','contype=2','webid=1 and contype=0');
foreach($confarr as $v){
	$isconf=db_getshow('config','*','webid=1 and contype=2 order by id asc');
	if($isconf){
		db_uparr('config',$v,'webid=1 and contype=2 and id='.$isconf['id']);
	}else{
		db_intoarr('config',$v);
		}
	}
db_del('config','contype=2 and webid=1');

//创建管理员
$isrecord=db_getshow('admin_group','*','webid='.install_webid.' and config_super=1');
if($isrecord){
	$groupid=$isrecord['groupid'];
}else{
	$groupid=db_getone('admin_group','groupid','webid='.install_webid.' order by groupid desc,id desc');
	if(!$groupid)$groupid=1;
	else $groupid=$groupid+1;
	$data=array(
		'webid'=>install_webid,
		'groupid'=>$groupid,
		'group_name'=>'超级管理员',
		'config_super'=>1,
		'config_rank'=>''
		);
	db_intoarr('admin_group',$data);
	}
$user_ip=getip();
$user_addkey=randomkeys(12,2);
$user_pass=md5($pass.$user_addkey);
$isrecord=db_getshow('admin','*','webid='.install_webid.' and user_admin="'.$user.'"');
$data=array(
	'webid'=>install_webid,
	'user_admin'=>$user,
	'user_pass'=>$user_pass,
	'user_addkey'=>$user_addkey,
	'login_ip'=>$user_ip,
	'time_add'=>time(),
	'config_group'=>$groupid,
	'config_type'=>1
	);
if(!$isrecord)db_intoarr('admin',$data);
else db_uparr('admin',$data,'webid="'.install_webid.'" and id='.$isrecord['id']);

if($theme=="no"||$clean=="no"){
	credir_install();
	updateallweb(0,true);
	@$lock=fopen("install/install_lock.php","w");
	fclose($lock);
	ajaxreturn(0);
	}
//安装主题
if(!is_dir("install/theme/"))ajaxreturn(1,"install安装目录缺少主题安装包文件，请重新上传install目录覆盖");
if(file_exists("install/theme/theme_data.txt")){
	rename("install/theme/theme_data.txt",install_file_theme);
}else if(!file_exists("install/data/theme_data.txt")){
	ajaxreturn(1,"install安装目录主题文件不完整，请重新上传install目录覆盖");
	}
if(file_exists("install/theme/theme_table.txt")){
	rename("install/theme/theme_table.txt",install_file_theme_table);
	}
@unlink("install/theme/cover.jpg");
$webpath="template/".install_webid."/";
deldir_install($webpath,true);
sleep(1);
credir_install();
if(!is_dir($webpath))ajaxreturn(1,"请检查template/目录是否有写权限");
//同步站点设置
updateallweb(install_webid);
//转移主题文件
$path="install/theme/";
$path2="template/".install_webid."/";
$mydir=dir($path);
while($file=$mydir->read()){
	if($file=='.'||$file=='..'||$file=='upload')continue;
	if(is_dir($path.$file)){
		if(is_dir($path2.$file))deldir_install($path2.$file);
		@rename($path.$file,$path2.$file);
		}
	}
$mydir->close();
//转移文章中的图片等文件
$path_upload=$path.'upload/';
$path_upload2="upload/".install_webid."/";
if(is_dir($path_upload)){
	$mydir=dir($path_upload);
	while($file=$mydir->read()){
		if(!is_dir($path_upload.$file))continue;
		if($file=='.'||$file=='..')continue;
		if($file=='label'||$file=='module'||$file=='article'||$file=='advert'||$file=='myphoto'){
			$file2=$file;
			if($file2=='advert')$file2='myphoto';
			if(is_dir($path_upload2.$file2))deldir_install($path_upload2.$file2);
			@rename($path_upload.$file,$path_upload2.$file2);
			}
		}
	}
@$themedata=file_get_contents(install_file_theme) or ajaxreturn(1,goif(1,"file_get_contents 读取 ".install_file_theme." 文件失败"));
//记录主题信息
$arr=unserialize($themedata);
if(!is_array($arr)||empty($arr))ajaxreturn(1,"默认主题信息读取失败，请检查文件完整性：".install_file_theme);
if(!count($arr))ajaxreturn(1,"初始化配置信息失败，请检查文件完整性：".install_file_theme);
db_upshow("config","varval='".$arr['title']."'","webid=".install_webid." and (varname='setup_webname' or varname='setup_webname_page')");
$savearr=array('title'=>$arr['title'],'author'=>$arr['author']);
$data=serialize($savearr);
db_upshow("websetup","themedata='".$data."'","webid=".install_webid);
//栏目类别
foreach($arr['module'] as $bv){
	$bv['webid']=install_webid;
	$bv_arr=$bv;
	$bv_arr=deltable($bv_arr,'smod');
	db_intoarr('module',$bv_arr);
	foreach($bv['smod'] as $sv){
		$sv['webid']=install_webid;
		$sv_arr=$sv;
		$sv_arr=deltable($sv_arr,'lmod');
		db_intoarr('module',$sv_arr);
		foreach($sv['lmod'] as $lv){
			$lv['webid']=install_webid;
			db_intoarr('module',$lv);
			}
		}
	}
//自定义模块
foreach($arr['module_user'] as $umod){
	$umod['webid']=install_webid;
	$tname=$prefix.'article_'.install_webid.'_'.$umod['dataid'];
	$result=mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$tname."'"));
	if($result==1){
		$result=mysql_query("DROP TABLE `".$tname."`");
		}
	db_intoarr('module_user',$umod);
	}
//自定义模块数据库
if(file_exists(install_file_theme_table)){
	@$themetable=file_get_contents(install_file_theme_table) or ajaxreturn(1,goif(1,"file_get_contents 读取 ".install_file_theme_table." 文件失败"));
	$themetable_status=checktable($themetable,'theme',install_webid,'ok');
	if(!$themetable_status)ajaxreturn(1,'无法创建自定义模块数据表结构');
	sleep(1);
}else if(count($arr['module_user'])>0){
	ajaxreturn(1,'该主题包含自定义模块数据，但主题安装包缺少该数据表结构文件');
	}
//自定义模块字段
foreach($arr['module_field'] as $ufie){
	$ufie['webid']=$website['webid'];
	db_intoarr('module_field',$ufie);
	}
//自定义模块文章
foreach($arr['article_user'] as $utab){
	$tname='article_'.install_webid.'_'.$utab['modid'];
	foreach($utab['data'] as $uart){
		$uart['webid']=install_webid;
		$uart['user_admin']=$user;
		db_intoarr($tname,$uart);
		}
	}
//系统自带模块文章
db_upshow('article','webid=0','webid='.install_webid);
foreach($arr['article'] as $art){
	$art['webid']=install_webid;
	$art['user_admin']=$user;
	$lid=db_getone('article','id','webid=0 order by id asc');
	if($lid)db_uparr('article',$art,'id='.$lid);
	else db_intoarr('article',$art);
	}
db_del('article','webid=0');
//整理广告
db_upshow('advert','webid=0','webid='.install_webid);
foreach($arr['advert'] as $ad){
	$ad['webid']=install_webid;
	$lid=db_getone('advert','id','webid=0 order by id asc');
	if($lid)db_uparr('advert',$ad,'id='.$lid);
	else db_intoarr('advert',$ad);
	}
db_del('advert','webid=0');
//整理标签
db_upshow('label','webid=0','webid='.install_webid);
foreach($arr['label'] as $lab){
	$lab['webid']=install_webid;
	$lid=db_getone('label','id','webid=0 order by id asc');
	if($lid)db_uparr('label',$lab,'id='.$lid);
	else db_intoarr('label',$lab);
	}
db_del('label','webid=0');
//语言
$lang='';
$lang_def='';
foreach($arr['languages'] as $v){
	if($lang=='')$lang_def=$v['en'];
	else $lang.=',';
	$lang.=$v['en'].'|'.$v['cn'];
	updatacofig(install_webid,$v['en']);
	}
db_upshow('config','varval="'.$lang.'"','webid='.install_webid.' and varname="setup_language"');
db_upshow('config','varval="'.$lang_def.'"','webid='.install_webid.' and varname="setup_language_def"');
//主题设置
foreach($arr['config'] as $conf){
	$conf['webid']=install_webid;
	$isconf=db_getshow('config','*','webid='.install_webid.' and cata="theme" and varname="'.$conf['varname'].'"');
	if($isconf)db_uparr('config',$conf,'id='.$isconf['id']);
	else db_intoarr('config',$conf);
	}
updatacofig(install_webid,'global');
updateallconf();
@$lock=fopen("install/install_lock.php","w");
if($lock){
	fwrite($lock,"如需重新安装QYKCMS，请删除本文件");
	fclose($lock);
	}
ajaxreturn(0);
?>