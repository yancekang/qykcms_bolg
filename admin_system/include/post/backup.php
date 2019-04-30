<?php
if(!ispower($admin_group,'sys_backup'))ajaxreturn(1,'权限不足，操作失败');
$backtype=arg('backtype','post','url');
switch($backtype){
	case 'a':	//备份
		$size=arg('size','post','int');
		$xu=arg('xu','post','int');
		$page=arg('page','post','int');
		$backpath='../'.$website['upfolder'].setup_uptemp.'backup_'.date('Ymd').'/';
		$backlist=array();
		$tablist=$db->fetch_all($db->query('SHOW TABLES FROM `'.db_database.'`'));
		foreach($tablist as $tab){
			$tname=$tab['Tables_in_'.db_database];
			$tname=preg_replace('/^'.db_tabfirst.'/','',$tname);
			@$fie=mysql_num_rows(mysql_query("DESCRIBE ".tabname($tname)." `webid`"));
			if(!$fie)continue;
			$arr=array('types'=>$tname,'tips'=>$tname);
			array_push($backlist,$arr);
			}
		$allpage=1;
		if(!$page){
			infoadminlog($website['webid'],$tcz['admin'],11,'备份网站数据');
			if(is_dir($backpath))deldir_admin($backpath,false);
			else createDirs($backpath);
		}else{
			$webdata=array('tab'=>$backlist[$xu]['types'],'version'=>$ver['version'],'version_front'=>$ver['version_front'],'data'=>array());
			$sql='select * from '.tabname($backlist[$xu]['types']).' where webid='.$website['webid'];
			$allnum=$db->num_rows($db->query($sql));
			$allpage=1;
			if($size){
				$allpage=ceil($allnum/$size);
				$start=$page*$size-$size;
				$sql.=' limit '.$start.','.$size;
				}
			$data=$db->fetch_all($db->query($sql));
			if($data){
				$webdata['data']=$data;
				$txt=serialize($webdata);
				@$file=fopen($backpath.$backlist[$xu]['types'].'_'.$page.'.txt','w');
				if(!$file)ajaxreturn(1);
				fwrite($file,$txt);
				fclose($file);
				}
			}
		$tips=$backlist[$xu]['tips'];
		if($page>=$allpage){
			$xu++;
			$page=1;
			if($xu>=count($backlist)){
				if(!method_exists('ZipArchive','open')){
					ajaxreturn(1,'备份文件已生成，因php环境不支持ZipArchive无法打包，请手动下载备份：<br>'.$backpath);
					}
				$zip=new ZipArchive();
				$zipfile=$website['webid'].'_'.date('Ymd_His').'_'.randomkeys(12,2).'.zip';
				$downurl=$website['upfolder'].setup_uptemp.$zipfile;
				if($zip->open('../'.$downurl,ZipArchive::OVERWRITE)===TRUE){
					$txtfile='1、本目录为 '.$website['setup_weburl'].' QYKCMS网站系统备份文件，备份于'.date('Y年m月d日H时i分s秒').'，前台系统版本'.$ver['version'].'，后台系统版本'.$ver['version_front'].'；
2、请勿手动编缉本目录中的文件，否则可能损坏备份格式，无法用于网站恢复；
3、备份文件数据为数组形式，无法直接通过phpmyadmin等工具作为sql导入到mysql，如需恢复请通过网站后台操作';
					$zip->addFromString('readme.txt',$txtfile);
					$txtfile2=importtable();
					$zip->addFromString('install_table.txt',$txtfile2);
					chdir($backpath);
					$mydir=dir('.');
					while($file=$mydir->read()){
						if($file=='.'||$file=='..')continue;
						$zip->addFile($file);
						//addFileToZip('backup',$zip);
						}
					$mydir->close();
					$zip->close();
					chdir('../../../');
					deldir_admin($backpath,true);	//删除备份
					countcapacity($website['webid'],true);
					ajaxreturn(0,'{"types":"success"}','http://'.$website['setup_weburl'].'/'.$downurl);
				}else{
					ajaxreturn(1,'备份已完成，因php环境不支持ZipArchive无法打包，请手动下载备份：<br>'.$backpath);
					}
			}else{
				$tips=$backlist[$xu]['tips'];
				}
		}else{
			$page++;
			}
		$res='{"types":"正在备份“'.$tips.'”，第 '.$page.' 页...（ '.($xu+1).' / '.count($backlist).' ）","xu":'.$xu.',"size":'.$size.',"page":'.$page.'}';
		ajaxreturn(0,$res);
	break;
	case 'b':	//恢复
		$tj=array('filenum'=>0);
		$upfile=arg('upfile','post','url');
		$bfpath='../'.$website['upfolder'].setup_uptemp;
		if($upfile=='backup'){
			$dir='backup';
		}else{
			$dir=str_replace('.zip','',$upfile);
			if(!file_exists($bfpath.$upfile))ajaxreturn(1,'没有找到备份文件，请确认是否上传成功');
			if(is_dir($bfpath.$dir.'/')){
				$dr=deldir_admin($bfpath.$dir);
				if(!$dr)ajaxreturn(1,'无法删除旧备份文件导致恢复失败，请手动删除该目录及文件<br>'.$bfpath.$dir.'/');
				}
			if(!method_exists('ZipArchive','open')){
				ajaxreturn(1,'无法通过在线上传备份文件恢复数据，请检查php环境是否支持 ZipArchive<br><span class="red">提示：可通过更改恢复选项，通过FTP或其它工具上传备份文件并在此处执行恢复</span>');
				}
			$zip=new ZipArchive;
			$otype=$zip->open($bfpath.$upfile);
			if(!$otype)ajaxreturn(1,'备份文件解压失败，请检查php环境是否支持 ZipArchive');
			$zip->extractTo($bfpath.$dir.'/');
			$zip->close();
			}
		if(!is_dir($bfpath.$dir.'/'))ajaxreturn(1,'没有找到上传的备份文件，请检查是否已上传或解压成功');
		chdir($bfpath.$dir.'/');
		//恢复数据库结构
		if(file_exists('install_table.txt')){
			$setuptable=readtemp_admin('install_table.txt','none');
			$upstatus=checktable($setuptable);
			if(!$upstatus)ajaxreturn(1,'数据库结构较验失败');
			sleep(1);
			@unlink('install_table.txt');
			}
		$mydir=dir('.');
		while($file=$mydir->read()){
			if($file=='.'||$file=='..'||$file=='readme.txt'||$file=='install_table.txt')continue;
			if(is_dir($file))continue;
			$data=readtemp_admin($file,'备份文件读取失败，可能是以下原因导致：<br>1、备份文件已被损坏，可尝试联系官方技术人员恢复<br>2、备份系统与当前系统版本不兼容，请查看备份文件中的readme.txt并选择合适的系统版本<br>3、php环境不能很好地支持ZipArchive扩展类');
			@$arr=unserialize($data);
			if(!is_array($arr)||empty($arr))ajaxreturn(1,'备份文件“'.$file.'”内容不规范');
			$tname=$arr['tab'];
			$result=mysql_num_rows(mysql_query("SHOW TABLES LIKE '".tabname($tname)."'"));
			if(!$result)ajaxreturn(1,'数据表不存在：'.tabname($tname));
			$tj['filenum']++;
			db_upshow($arr['tab'],'webid=0','webid='.$website['webid']);
			foreach($arr['data'] as $sort=>$v){
				$v=deltable($v,'id');
				$v['webid']=$website['webid'];
				$oldid=db_getone($arr['tab'],'id','webid=0 order by id asc');
				if($oldid)db_uparr($arr['tab'],$v,'id='.$oldid);
				else db_intoarr($arr['tab'],$v);
				}
			db_del($arr['tab'],'webid=0');
			}
		$mydir->close();
		chdir('../../../');
		deldir_admin($bfpath.$dir);
		if($upfile!='backup')@unlink($bfpath.$upfile);
		countcapacity($website['webid'],true);
		infoadminlog($website['webid'],$tcz['admin'],11,'恢复网站数据');
		if($tj['filenum'])ajaxreturn(0,'已完成网站数据恢复，共执行 '.$tj['filenum'].' 次');
		else ajaxreturn(1,'未找到可恢复的备份文件，请检查是否已成功上传');
	break;
	}