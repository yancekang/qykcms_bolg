<?php
if(!$website['isadmin']||!ispower($admin_group,'super'))ajaxreturn(9,'权限不足，操作失败');
$backtype=arg('backtype','post','url');
switch($backtype){
	case 'clear':
		$dir='backup/';
		$istype=clearDirs($dir);
		infoadminlog($website['webid'],$tcz['admin'],19,'删除数据库备份文件：'.$dir);
		countcapacity($website['webid']);
		ajaxreturn(0,'已删除数据库备份：后台目录/'.$dir);
	break;
	case 'backup':	// 备份数据库
	case 'backup_clear':	// 备份数据库，备份前清除
		if($backtype=='backup_clear'){
			$dir='backup/';
			$istype=clearDirs($dir);
			}
		$host=db_hostname;
		$user=db_username; //数据库账号
		$password=db_password; //数据库密码
		$dbname=db_database; //数据库名称
		//这里的账号、密码、名称都是从页面传过来的
		if(!mysql_connect($host, $user, $password))ajaxreturn(1,'数据库连接失败，请核对后再试');
		if(!mysql_select_db($dbname))ajaxreturn(1,'数据库“'.$dbname.'”不存在');
		mysql_query("set names 'utf8'");
		$mysql = "set charset utf8;\r\n";
		$q1=mysql_query("show tables");
		while ($t = mysql_fetch_array($q1)){
		$table = $t[0];
		$q2 = mysql_query("show create table `$table`");
		$sql = mysql_fetch_array($q2);
		$mysql .= $sql['Create Table'] . ";\r\n";
		$q3 = mysql_query("select * from `$table`");
		while ($data = mysql_fetch_assoc($q3)){
			$keys = array_keys($data);
			$keys = array_map('addslashes', $keys);
			$keys = join('`,`', $keys);
			$keys = "`" . $keys . "`";
			$vals = array_values($data);
			$vals = array_map('addslashes', $vals);
			$vals = join("','", $vals);
			$vals = "'" . $vals . "'";
			$mysql .= "insert into `$table`($keys) values($vals);\r\n";
			}
		}
		//存放路径，默认存放到项目最外层
		$filename='backup/#'.$dbname.'_'.date('YmdHis').'_'.randomkeys(12,2).'.sql';
		$fp = fopen($filename, 'w');
		fputs($fp, $mysql);
		fclose($fp);
		infoadminlog($website['webid'],$tcz['admin'],19,'备份数据库，'.goif($backtype=='backup_clear','删除旧备份文件','保留旧备份文件'));
		countcapacity($website['webid']);
		ajaxreturn(0,'备份成功，建议下载“后台/backup/”下的备份文件到本机保存');
	break;
	case 'table':
		$content=importtable();
		$post_file=$website['upfolder'].setup_uptemp.'install_table.zip';
		if(file_exists('../'.$post_file))@unlink('../'.$post_file);
		$zip=new ZipArchive();
		if($zip->open('../'.$post_file,ZipArchive::OVERWRITE)===TRUE){
			$zip->addFromString('install_table.txt',$content);
			$zip->close();
		}else{
			$post_file=$website['upfolder'].setup_uptemp.'install_table.txt';
			@$file=fopen('../'.$post_file,'w');
			if(!$file)ajaxreturn(1,'保存文件失败');
			else{
				fwrite($file,$content);
				fclose($file);
				}
			}
		countcapacity($website['webid']);
		$downurl='http://'.$website['setup_weburl'].'/'.$post_file;
		ajaxreturn(0,'数据库结构已导出，请留意下载提示',$downurl);
	break;
	}