<?php
if(!$website['isadmin']||!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
$domain=strtolower(arg('domain','post','txt'));
$repass=arg('pass','post','url');
$data=array(
	'status'=>arg('status','post','int'),
	'title'=>arg('title','post','txt'),
	'domainmax'=>arg('domainmax','post','int'),
	'capacity_max'=>arg('capacity_max','post','int'),
	'time_add'=>strtotime(arg('time_add','post','url'))
	);
if($data['capacity_max'])$data['capacity_max']=$data['capacity_max']*1024;
$set=db_getshow('websetup','*','id='.$tcz['id']);
if($set){
	$user_pass='';
	if($repass=='ok'){
		$user_pass='qyk'.rand(111,999);
		$am=db_getshow('admin','*','webid='.$set['webid'].' and user_admin="admin"');
		$groupid=db_getone('admin_group','groupid','webid='.$set['webid'].' and config_super=1');
		if(!$groupid)$groupid=1;
		if($am){
			$user_pass2=md5($user_pass.$am['user_addkey']);
			db_upshow('admin','user_pass="'.$user_pass2.'",config_group='.$groupid.',config_type=1','id='.$am['id']);
		}else{
			$user_addkey=randomkeys(8,2);
			$admin=array(
				'webid'=>$set['webid'],
				'user_admin'=>'admin',
				'user_pass'=>md5($user_pass.$user_addkey),
				'user_loginkey'=>'',
				'user_addkey'=>$user_addkey,
				'user_email'=>'',
				'user_phone'=>'',
				'user_head'=>'',
				'login_ip'=>'',
				'login_num'=>0,
				'login_version'=>'',
				'time_add'=>time(),
				'time_login'=>0,
				'config_group'=>$groupid,
				'config_type'=>1
				);
			db_intoarr('admin',$admin);
			}
		}
	db_uparr('websetup',$data,'id='.$tcz['id']);
	$webclose=0;
	if($data['status'])$webclose=3;
	db_upshow('config','varval="'.$webclose.'"','webid='.$set['webid'].' and varname="setup_web_close"');
	updatacofig($set['webid'],'global');
	infoadminlog($website['webid'],$tcz['admin'],19,'编缉站点“'.$data['title'].'”'.goif($repass=='ok','，并重置 admin 密码').'（webID='.$set['webid'].'）');
	ajaxreturn(0,'已成功保存站点信息'.goif($repass=='ok','，管理员 admin 密码为：<span class="red">'.$user_pass.'</span>'));
}else{	//新建站点
	if(preg_match('/^http(s*?):\/\/|\/$/i',$domain))ajaxreturn(1,'域名请不要以 http 开头，且不要以 / 结尾，示例：www.qingyunke.com');
	if(preg_match('/\/|\\\/',$domain))ajaxreturn(1,'域名请不要包含正反斜杠符号');
	if(!preg_match('/([a-z0-9_]+)\.([a-z0-9]+)$/',$domain))ajaxreturn(1,'域名不正确，示例：www.qingyunke.com');
	$isdomain=db_count('website','setup_weburl="'.$domain.'"')+0;
	if($isdomain)ajaxreturn(1,'域名 '.$domain.' 已被其它站点使用');
	$webid=getdataid(0,'websetup','webid');
	$data['webid']=$webid;
	$data['capacity_have']=0;
	$data['themedata']='';
	$data['visit']=0;
	db_intoarr('websetup',$data);
	$webdata=array(
		'webid'=>$webid,
		'isdef'=>1,
		'isadmin'=>0,
		'setup_weburl'=>$domain,
		'setup_record'=>''
		);
	db_intoarr('website',$webdata);
	$group=array('webid'=>$webid,'groupid'=>1,'group_name'=>'超级管理员','config_super'=>1,'config_rank'=>'');
	db_intoarr('admin_group',$group);
	$user_pass='qyk'.rand(111,999);
	$user_addkey=randomkeys(8,2);
	$admin=array(
		'webid'=>$webid,
		'user_admin'=>'admin',
		'user_pass'=>md5($user_pass.$user_addkey),
		'user_loginkey'=>'',
		'user_addkey'=>$user_addkey,
		'user_email'=>'',
		'user_phone'=>'',
		'user_head'=>'',
		'login_ip'=>'',
		'login_num'=>0,
		'login_version'=>'',
		'time_add'=>time(),
		'time_login'=>0,
		'config_group'=>1,
		'config_type'=>1
		);
	db_intoarr('admin',$admin);
	$conf=db_getall('config','*,if(cata="web",0,1) as xu','webid=1 order by xu desc,cata desc,sort asc,id asc');
	$lang='en';
	foreach($conf as $c){
		switch($c['varname']){
			case 'setup_language_def':
				$lang=$c['varval'];
			break;
			case 'setup_webname':
			case 'setup_webname_page':
			case 'setup_shortname':
			case 'setup_keyword':
			case 'setup_company':
				$c['varval']=$data['title'];
			break;
			case 'setup_description':
				$c['varval']=$data['title'].'，网站于'.date('Y年m月d日').'上线运行';
			break;
			case 'setup_web_close':
				$c['varval']=goif($data['status'],3,0);
			break;
			}
		if($c['cata']=='web')$c['cata']=$lang;
		$c=deltable($c,'id');
		$c=deltable($c,'xu');
		$c['webid']=$webid;
		db_intoarr('config',$c);
		}
	//创建目录
	$path_temp='../'.setup_webfolder.$webid.'/';
	createDirs($path_temp);
	createDirs($path_temp.'config/');
	createDirs($path_temp.'runtime/');
	createDirs($path_temp.'runtime/cache/');
	createDirs($path_temp.'runtime/temp/');
	$path_up='../'.setup_upfolder.$webid.'/';
	createDirs($path_up);
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
	updatacofig($webid,'global');
	updatacofig($webid,$lang);
	countcapacity($webid);
	infoadminlog($website['webid'],$tcz['admin'],19,'新建站点“'.$data['title'].'”（webID='.$webid.'）');
	ajaxreturn(0,'已成功创建站点，初始管理员：<span class="red">admin</span>，密码：<span class="red">'.$user_pass.'</span><br>1、请牢记管理员密码，登录后尽快修改或更换新的管理账号<br>2、新建的站点未安装主题，请先登录该站点后台安装');
	}