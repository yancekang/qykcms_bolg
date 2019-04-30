<?php
$auto=arg('auto','post','int');
$title=trim(arg('title','post','txt'));
$name=trim(arg('name','post','txt'));
$qq=arg('qq','post','url');
$email=arg('email','post','url');
$homepage=arg('homepage','post','url');
$cover=arg('cover','post','url');
$article=arg('article','post','int');	//是否包含文章数据 0不包含1包含
if($title==''||$name=='')ajaxreturn(1,'请输入完整参数');
$lang=explode(',',db_getone('config','varval','webid='.$website['webid'].' and varname="setup_language"'));
$langarr=array();
foreach($lang as $lv){
	$lvarr=explode('|',$lv);
	$en=$lvarr[0];
	$cn=goif($lvarr[1]!='',$lvarr[1],$en);
	array_push($langarr,array('en'=>$en,'cn'=>$cn));
	}
$qyk_theme_data=array(
	'title'=>$title,
	'version'=>$ver['version_front'],
	'languages'=>$langarr,
	'time'=>date('Y-m-d H:i:s'),
	'auto'=>1,
	'author'=>array('name' =>$name,'qq'=>$qq,'email'=>$email,'homepage'=>$homepage),
	'config'=>array(),
	'module'=>array(),
	'module_user'=>array(),
	'module_field'=>array(),
	'label'=>array(),
	'advert'=>array(),
	'article'=>array(),
	'article_user'=>array()
	);
$themedata=serialize(array('title'=>$title,'author'=>$qyk_theme_data['author']));
db_upshow("websetup","themedata='".$themedata."'","webid=".$website['webid']);
$conf=db_getall('config','*','webid='.$website['webid'].' and cata="theme" and contype!=2 order by sort asc,id asc');
foreach($conf as $cval){
	$cval=deltable($cval,'id');
	$cval=deltable($cval,'webid');
	array_push($qyk_theme_data['config'],$cval);
	}
if($auto){
	$bcat=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and (menutype=0 or menutype=999) order by languages asc,menutype asc,id asc');
	foreach($bcat as $bval){
		$bid=$bval['classid'];
		$bval=deltable($bval,'id');
		$bval=deltable($bval,'webid');
		$bval['smod']=array();

		$smod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and menutype=1 and bcat='.$bid.' order by sort asc,id asc');
		foreach($smod as $sval){
			$sid=$sval['classid'];
			$sval=deltable($sval,'id');
			$sval=deltable($sval,'webid');
			$sval['lmod']=array();

			$lmod=db_getlist('select * from '.tabname('module').' where webid='.$website['webid'].' and menutype=2 and scat='.$sid.' order by sort asc,id asc');
			foreach($lmod as $lval){
				$lval=deltable($lval,'id');
				$lval=deltable($lval,'webid');
				array_push($sval['lmod'],$lval);
				}
			array_push($bval['smod'],$sval);
			}
		array_push($qyk_theme_data['module'],$bval);
		}
	}
$article_count=0;
//自定义模块
$usertablist='';
$module_user=db_getall('module_user','*','webid='.$website['webid'].' and isok=0');
foreach($module_user as $umod){
	$usertablist.=goif($usertablist!='',',').'article_'.$website['webid'].'_'.$umod['dataid'];
	$umod=deltable($umod,'id');
	$umod=deltable($umod,'webid');
	array_push($qyk_theme_data['module_user'],$umod);
	$module_field=db_getall('module_field','*','webid='.$website['webid'].' and modid='.$umod['dataid']);
	foreach($module_field as $ufie){
		$ufie=deltable($ufie,'id');
		$ufie=deltable($ufie,'webid');
		array_push($qyk_theme_data['module_field'],$ufie);
		}
	//自定义模块文章
	if($article){
		$artimgsize=getdirsize($website['upfolder'].'/article/');
		$artimgsize=$artimgsize/1024/1024;
		if($artimgsize>5)ajaxreturn(1,'文章数据中包含的上传文件大于5M无法打包进主题，请取消“包含文章数据”');
		$tname='article_'.$website['webid'].'_'.$umod['dataid'];
		$artdata=db_getall($tname,'*','modtype='.$umod['dataid']);
		$article_count=count($artdata);
		if($article_count>setup_theme_artnum){
			ajaxreturn(1,'文章记录数超过'.setup_theme_artnum.'条无法打包到主题，转移网站数据请使用备份与恢复');
			}
		$article_user_arr=array('modid'=>$umod['dataid'],'data'=>array());
		foreach($artdata as $aval){
			$aval=deltable($aval,'id');
			$aval=deltable($aval,'webid');
			$aval['comment']=0;
			$aval['hits']=0;
			array_push($article_user_arr['data'],$aval);
			}
		array_push($qyk_theme_data['article_user'],$article_user_arr);
		}
	}
//内置模块文章
if($article){
	$artdata=db_getall('article','*','webid='.$website['webid'].' order by sort asc,id asc');
	if(count($artdata)+$article_count>setup_theme_artnum){
		ajaxreturn(1,'文章记录数超过'.setup_theme_artnum.'条无法打包到主题，转移网站数据请使用备份与恢复');
		}
	foreach($artdata as $aval){
		$aval=deltable($aval,'id');
		$aval=deltable($aval,'webid');
		$aval['comment']=0;
		$aval['hits']=0;
		array_push($qyk_theme_data['article'],$aval);
		}
	}
//导出标签
$label=db_getall('label','*','webid='.$website['webid'].' order by sort asc,id asc');
foreach($label as $aval){
	$aval=deltable($aval,'id');
	$aval=deltable($aval,'webid');
	array_push($qyk_theme_data['label'],$aval);
	}
//导出advert
$advert=db_getall('advert','*','webid='.$website['webid'].' order by sort asc,id asc');
foreach($advert as $aval){
	$aval=deltable($aval,'id');
	$aval=deltable($aval,'webid');
	array_push($qyk_theme_data['advert'],$aval);
	}
$filecontent=serialize($qyk_theme_data);
$downurl=$website['upfolder'].setup_uptemp.'qyk_theme.zip';
if(!method_exists('ZipArchive','open')){
	ajaxreturn(1,'主题导出失败，请检查php环境是否支持 ZipArchive');
	}
$zip=new ZipArchive();
if($zip->open('../'.$downurl,ZipArchive::OVERWRITE)===TRUE){
	if($usertablist!=''){
		$txtfile=importtable($usertablist);
		$zip->addFromString('theme_table.txt',$txtfile);
		}
	$zip->addFromString(setup_themefile,$filecontent);
	if($cover!=''){
		if(file_exists('../'.$website['upfolder'].setup_uptemp.$cover)){
			@$ftype=end(explode('.',$cover));
			$zip->addFile('../'.$website['upfolder'].setup_uptemp.$cover,'cover.'.$ftype);
			}
		}
	chdir('../'.$website['upfolder'].'/');
	addFileToZip('label',$zip,'upload/');
	addFileToZip('module',$zip,'upload/');
	addFileToZip('myphoto',$zip,'upload/');
	if($article)addFileToZip('article',$zip,'upload/');
	chdir('../../'.setup_webfolder.$website['webid'].'/');
	$mydir=dir('.');
	while($file=$mydir->read()){
		if($file=='.'||$file=='..')continue;
		if($file=='config'||$file=='runtime')continue;
		addFileToZip($file,$zip);
		}
	$mydir->close();
	$zip->close();
	chdir('../');	//返回到与后台目同一级的地方，计算空间占用函数才可执行
	countcapacity($website['webid'],true);
	ajaxreturn(0,'主题安装包已成功导出，请点击按钮下载<br><span class="red">提示：“缓存管理”-“删除临时文件”可清理服务端生成的主题文件</span>','http://'.$webdomain.'/'.$downurl);
}else{
	ajaxreturn(1,'请检查php运行环境是否支持ZipArchive');
	}