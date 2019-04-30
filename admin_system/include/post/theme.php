<?php
if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
$auto=arg('auto','post','int');
$article=arg('article','post','int');
$datatype=arg('datatype','post','int');
$tj=array(
	'changeid'=>0,
	'changecls'=>0,
	'addcls'=>0
	);
$path='../'.$website['upfolder'].setup_uptemp.'qyk_theme_new/';
$path2='../'.setup_webfolder.$website['webid'].'/';
if(!file_exists($path.setup_themefile))ajaxreturn(1,'未找到安装文件，请确认是否上传成功');
$themedata=readtemp_admin($path.setup_themefile,'主题信息读取失败，可能是以下原因导致：<br>1、主题安装包文件制作不规范或不完整<br>2、版本不兼容，请尽量选择推荐版本与系统版本相近的主题<br>3、php环境不能很好地支持ZipArchive扩展类');
@$arr=unserialize($themedata);
if(!is_array($arr)||empty($arr)){
	ajaxreturn(1,setup_themefile.' 文件内容不规范');
	}
if(count($arr['module_user'])>0){
	if(!file_exists($path.'theme_table.txt'))ajaxreturn(1,'该主题包含自定义模块数据，但主题安装包缺少该数据表结构文件');
	}
//整理文章
if($article){
	if(!$auto)ajaxreturn(1,'您勾选了“导入文章数据”，必须勾选“导入栏目分类”以确保栏目兼容');
	if(!$datatype){
		db_upshow('article','webid=0','webid='.$website['webid']);
		$module_user_old=db_getall('module_user','*','webid='.$website['webid']);
		foreach($module_user_old as $om){
			$otname='article_'.$website['webid'].'_'.$om['dataid'];
			db_run('TRUNCATE TABLE `'.tabname($otname).'`');
			}
		}
}else{
	$datatype=1;
	}
deldir_admin('../'.$website['upfolder'].'label/',false);
deldir_admin('../'.$website['upfolder'].'myphoto/',false);
if($auto)deldir_admin('../'.$website['upfolder'].'module/',false);
if($article&&!$datatype)deldir_admin('../'.$website['upfolder'].'article/',false);
$mydir=dir($path);
while($file=$mydir->read()){
	if($file=='.'||$file=='..'||$file=='upload')continue;
	if(is_dir($path.$file)){
		if(is_dir($path2.$file))deldir_admin($path2.$file);
		@rename($path.$file,$path2.$file);
		}
	}
$mydir->close();
$path_upload=$path.'upload/';
if(is_dir($path_upload.'advert'))@rename($path_upload.'advert',$path_upload.'myphoto');
$path_upload2='../'.$website['upfolder'];
if(is_dir($path_upload)){
	$mydir=dir($path_upload);
	while($file=$mydir->read()){
		if(!is_dir($path_upload.$file))continue;
		if($file=='.'||$file=='..')continue;
		if($file=='label'||$file=='myphoto'||($auto&&$file=='module')||($article&&$file=='article')){
			if($datatype){
				copydir($path_upload.$file,$path_upload2.$file);
			}else{
				if(is_dir($path_upload2.$file))deldir_admin($path_upload2.$file);
				@rename($path_upload.$file,$path_upload2.$file);
				}
			}
		}
	}
//自定义模块
foreach($arr['module_user'] as $umod){
	$umod['webid']=$website['webid'];
	$umod_old=db_getshow('module_user','*','webid='.$website['webid'].' and dataid='.$umod['dataid']);
	if($umod_old){
		$tname='article_'.$website['webid'].'_'.$umod['dataid'];
		$newdataid=getdataid($website['webid'],'module_user','dataid');
		if($newdataid<10)$newdataid=11;
		$tname_new='article_'.$website['webid'].'_'.$newdataid;
		db_upshow('module','modtype='.$newdataid,'webid='.$website['webid'].' and modtype='.$umod['dataid']);
		db_upshow('module_user','dataid='.$newdataid,'webid='.$website['webid'].' and dataid='.$umod['dataid']);
		db_upshow('module_field','modid='.$newdataid,'webid='.$website['webid'].' and modid='.$umod['dataid']);
		db_upshow($tname,'modtype='.$newdataid,'webid='.$website['webid'].' and modtype='.$umod['dataid']);
		db_run("ALTER TABLE `".tabname($tname)."` RENAME TO `".tabname($tname_new)."`");
		}
	db_intoarr('module_user',$umod);
	}
//自定义模块数据库
if(file_exists($path.'theme_table.txt')){
	$setuptable=readtemp_admin($path.'theme_table.txt','none');
	$upstatus=checktable($setuptable,'theme',$website['webid']);
	if(!$upstatus)ajaxreturn(1,'无法创建自定义模块数据表结构，请检查主题安装包内 theme_table.txt 文件是否损坏');
	sleep(1);
	}
//自定义模块字段
foreach($arr['module_field'] as $ufie){
	$ufie['webid']=$website['webid'];
	db_intoarr('module_field',$ufie);
	}
if($auto){	//开始整理栏目
	db_upshow('module','isok=1','webid='.$website['webid']);	//先隐藏所有栏目
	foreach($arr['module'] as $bv){
		$bv['webid']=$website['webid'];
		$bcls=db_getshow('module','id,classid,mark,menutype','webid='.$website['webid'].' and classid='.$bv['classid'].' order by classid asc,id asc');
		if($bcls){
			if($bcls['mark']!=$bv['mark']||($bcls['menutype']!=0&&$bcls['menutype']!=9)){
				chclsid($website['webid'],$bv['classid']);	//转移有参数冲突的分类
				$tj['changeid']++;
				}
			}
		$bmod=db_getshow('module','*','webid='.$website['webid'].' and (menutype=0 or menutype=999) and mark="'.$bv['mark'].'"');
		$bv_arr=$bv;
		$bv_arr=deltable($bv_arr,'smod');
		if($bmod){
			$ys=db_uparr('module',$bv_arr,'id='.$bmod['id'],true);
			if($ys)$tj['changecls']++;
			$bsql_art='';
			$bsql_mod='';
			if($bv['languages']!=$bmod['languages']){
				$bsql_art.=goif($bsql_art!='',',').'languages="'.$bv['languages'].'"';
				$bsql_mod.=goif($bsql_mod!='',',').'languages="'.$bv['languages'].'"';
				}
			if($bv['modtype']!=$bmod['modtype'])$bsql_mod.=goif($bsql_mod!='',',').'modtype="'.$bv['modtype'].'"';
			if($bsql_art!='')db_upshow('article',$bsql_art,'webid='.$website['webid'].' and bcat='.$bv['classid']);
			if($bsql_mod!='')db_upshow('module',$bsql_mod,'webid='.$website['webid'].' and bcat='.$bv['classid']);
		}else{
			$tj['addcls']++;
			db_intoarr('module',$bv_arr);
			}
		//一级子类
		foreach($bv['smod'] as $sv){
			$sv['webid']=$website['webid'];
			$scls=db_getshow('module','id,classid,mark,menutype','webid='.$website['webid'].' and classid='.$sv['classid'].' order by classid asc,id asc');
			if($scls){
				if($scls['mark']!=$sv['mark']||$scls['menutype']!=1){
					chclsid($website['webid'],$sv['classid']);	//转移有参数冲突的分类
					$tj['changeid']++;
					}
				}
			$smod=db_getshow('module','*','webid='.$website['webid'].' and menutype=1 and isok=1 and mark="'.$sv['mark'].'"');
			$sv_arr=$sv;
			$sv_arr=deltable($sv_arr,'lmod');
			if($smod){
				$ys=db_uparr('module',$sv_arr,'id='.$smod['id'],true);
				if($ys)$tj['changecls']++;
			}else{
				$tj['addcls']++;
				db_intoarr('module',$sv_arr);
				}
			//二级子类
			foreach($sv['lmod'] as $lv){
				$lv['webid']=$website['webid'];
				$lcls=db_getshow('module','id,classid,mark,menutype','webid='.$website['webid'].' and classid='.$lv['classid'].' order by classid asc,id asc');
				if($lcls){
					if($lcls['mark']!=$lv['mark']||$lcls['menutype']!=2){
						chclsid($website['webid'],$lv['classid']);	//转移有参数冲突的分类
						$tj['changeid']++;
						}
					}
				$lmod=db_getshow('module','*','webid='.$website['webid'].' and menutype=2 and isok=1 and mark="'.$lv['mark'].'"');
				if($lmod){
					$ys=db_uparr('module',$lv,'id='.$lmod['id'],true);
					if($ys)$tj['changecls']++;
				}else{
					$tj['addcls']++;
					db_intoarr('module',$lv);
					}
				}
			}
		}
	}
//文章
if($article){
	foreach($arr['article'] as $art){
		$artid=getdataid($website['webid'],'article','dataid');
		$art['dataid']=$artid;
		$art['webid']=$website['webid'];
		$art['user_admin']=$tcz['admin'];
		$oldart=db_getshow('article','id','webid=0');
		if($oldart)db_uparr('article',$art,'id='.$oldart['id']);
		else db_intoarr('article',$art);
		}
	foreach($arr['article_user'] as $utab){
		$tname='article_'.$website['webid'].'_'.$utab['modid'];
		foreach($utab['data'] as $uart){
			$uart['webid']=$website['webid'];
			$uart['user_admin']=$tcz['admin'];
			db_intoarr($tname,$uart);
			}
		}
	}
db_del('article','webid=0');
//广告，旧记录将删除
db_upshow('advert','webid=0','webid='.$website['webid']);
foreach($arr['advert'] as $ad){
	$ad['webid']=$website['webid'];
	$lid=db_getone('advert','id','webid=0 order by id asc');
	if($lid)db_uparr('advert',$ad,'id='.$lid);
	else db_intoarr('advert',$ad);
	}
db_del('advert','webid=0');
//标签，旧记录将删除
db_upshow('label','webid=0','webid='.$website['webid']);
foreach($arr['label'] as $lab){
	$lab['webid']=$website['webid'];
	$lid=db_getone('label','id','webid=0 order by id asc');
	if($lid)db_uparr('label',$lab,'id='.$lid);
	else db_intoarr('label',$lab);
	}
db_del('label','webid=0');
//作者信息
$savearr=array('title'=>$arr['title'],'author'=>$arr['author']);
$themedata=serialize($savearr);
db_upshow("websetup","themedata='".$themedata."'","webid=".$website['webid']);
//语言
$lang='';
$lang_def='';
foreach($arr['languages'] as $v){
	if($lang=='')$lang_def=$v['en'];
	else $lang.=',';
	$lang.=$v['en'].'|'.$v['cn'];
	updatacofig($website['webid'],$v['en']);	//生成网站配置
	}
$lang_old=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"');
if($lang_old!=$lang){
	db_upshow('config','varval="'.$lang.'"','webid='.$website['webid'].' and varname="setup_language"');
	db_upshow('config','varval="'.$lang_def.'"','webid='.$website['webid'].' and varname="setup_language_def"');
	}
//主题设置，旧的设置将被删除
db_upshow('config','contype=2','webid='.$website['webid'].' and cata="theme" and contype=1');
foreach($arr['config'] as $conf){
	$conf['webid']=$website['webid'];
	$cid=db_getone('config','id','webid='.$website['webid'].' and cata="theme" and varname="'.$conf['varname'].'" order by id asc');
	if($cid)db_uparr('config',$conf,'webid='.$website['webid'].' and cata="theme"  and id='.$cid);
	else db_intoarr('config',$conf);
	}
db_del('config','webid='.$website['webid'].' and contype=2 and cata="theme"');
//重新生成配置
updatacofig($website['webid'],'global');
//清理
@unlink('../'.$website['upfolder'].setup_uptemp.'qyk_theme_new.zip');
deldir_admin($path);
infoadminlog($website['webid'],$tcz['admin'],24,'完成安装主题：'.$arr['title']);
ajaxreturn(0,'已成功完成主题安装：'.$arr['title'].'<br>栏目统计：'.goif($auto&&($tj['changecls']+$tj['changeid']+$tj['addcls']>0),'修改（'.$tj['changecls'].' 个）　迁移（'.$tj['changeid'].' 个）　创建（'.$tj['addcls'].' 个）','未修改栏目结构'));