<?php
if(!ispower($admin_group,'module_user'))ajaxreturn(1,'权限不足，操作失败');
$data=db_getshow('module_user','*','webid='.$website['webid'].' and id='.$tcz['id']);
$arr=array(
	'webid'=>$website['webid'],
	'title'=>arg('title','post','txt'),
	'isok'=>arg('isok','post','int')
	);
if($data){
	$fie=arg('fie','post','none');
	if($fie!=''){
		$fiearr=explode(',',$fie);
		foreach($fiearr as $val){
			$f=explode('|',$val);
			$fid=floatval($f[0]);
			$arr2=array(
				'title'=>htmlspecialchars(urldecode($f[1])),
				'varname'=>$f[2],
				'sort'=>intval($f[3])
				);
			db_uparr('module_field',$arr2,'webid='.$website['webid'].' and modid='.$data['dataid'].' and id='.$fid);
			}
		}
	db_uparr('module_user',$arr,'id='.$data['id']);
	infoadminlog($website['webid'],$tcz['admin'],12,'编辑自定义模块“'.$arr['title'].'”（ID='.$data['dataid'].'）');
}else{
	$dataid=getdataid($website['webid'],'module_user','dataid');
	if($dataid<=10)$dataid+=10;
	//创建表
	$tname=db_tabfirst.'article_'.$website['webid'].'_'.$dataid;
	$result=mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$tname."'"));
	if($result==1)ajaxreturn(1,'数据表“'.$tname.'”已存在，无法直接创建，如果该表无实际用途请手动删除');
	$tabsql="CREATE TABLE IF NOT EXISTS `".$tname."` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`webid` int(10) unsigned NOT NULL DEFAULT '".$website['webid']."',
`dataid` int(10) unsigned NOT NULL DEFAULT '0',
`mark` varchar(50) DEFAULT '',
`showfile` varchar(100) DEFAULT '',
`modtype` int(10) DEFAULT '0',
`bcat` int(10) DEFAULT '0',
`scat` int(10) DEFAULT '0',
`lcat` int(10) unsigned DEFAULT '0',
`special_id` varchar(1000) DEFAULT '',
`languages` varchar(20) DEFAULT 'cn',
`title` varchar(200) DEFAULT '',
`source` varchar(1000) DEFAULT '本站',
`keyword` varchar(1000) DEFAULT '',
`description` varchar(1000) DEFAULT '',
`sort` int(10) unsigned DEFAULT '1',
`star` int(2) DEFAULT '0',
`piclist` text,
`linkurl` varchar(500) DEFAULT '',
`isok` int(2) DEFAULT '0',
`hits` int(10) DEFAULT '0',
`comment` int(10) DEFAULT '0',
`time_add` int(10) unsigned DEFAULT '0',
`time_update` int(10) unsigned DEFAULT '0',
`time_top` int(10) unsigned DEFAULT '0',
`time_color` varchar(20) NOT NULL DEFAULT '',
`mood_1` int(10) unsigned NOT NULL DEFAULT '0',
`mood_2` int(10) unsigned NOT NULL DEFAULT '0',
`mood_3` int(10) unsigned NOT NULL DEFAULT '0',
`mood_4` int(10) unsigned NOT NULL DEFAULT '0',
`mood_5` int(10) unsigned NOT NULL DEFAULT '0',
`user_admin` varchar(50) DEFAULT '',
`computer` int(2) NOT NULL DEFAULT '0',
`mobile` int(2) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$status=$db->query($tabsql);
	$arr['dataid']=$dataid;
	$newid=db_intoarr('module_user',$arr,true);
	infoadminlog($website['webid'],$tcz['admin'],12,'新建自定义模块“'.$arr['title'].'”（ID='.$arr['dataid'].'）');
	ajaxreturn(0,$newid);
	}
ajaxreturn(0,'已成功保存模块信息');