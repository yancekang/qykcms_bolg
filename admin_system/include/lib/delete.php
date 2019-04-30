<?php
$idlist=arg('idlist','post','url');
$idarr=explode(',',$idlist);
$idlen=count($idarr);
$idlen_ok=0;	//成功删除的记录数
$gly=$tcz['admin'];
if($idlen<1)ajaxreturn(1,'请先选择要删除的记录，按住CTRL键支持多选');
if($tcz['desc']=='template'){
	$file=arg('file','post','url');
	$filearr=explode(',',$file);
}else if($tcz['desc']=='websetup'){
	if(!$website['isadmin']||!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
	if($idlen>1)ajaxreturn(1,'站点不支持批量删除');
	$webdel=db_getone('config','varval','webid=1 and cata="basic" and varname="setup_websetup_del"');
	if($webdel=='false')ajaxreturn(1,'系统已禁用删除站点功能，请先开启：高级管理 &raquo; 基础配置');
}else if($tcz['desc']=='article_user'){
	if(!ispower($admin_group,'art_del'))ajaxreturn(1,'权限不足，操作失败');
	$bmod=db_getshow('module','*','webid='.$website['webid'].' and classid='.$tcz['bcat']);
	if(!$bmod)ajaxreturn(1,'不存在的栏目');
	$tname='article_'.$website['webid'].'_'.$bmod['modtype'];
	$conf_file=db_getall('module_field','*','webid='.$website['webid'].' and modid='.$bmod['modtype'].' and (infotype="up_cover" or infotype="up_file") order by sort asc');
	}
foreach($idarr as $k=>$id){
	switch($tcz['desc']){
			case 'websetup':
				$set=db_getshow('websetup','*','id='.$id);
				if(!$set)ajaxreturn(1,'站点不存在：'.$set['webid']);
				if($set['webid']==$website['webid'])ajaxreturn(1,'不能删除当前登录的主站点');
				$path_up='../'.setup_upfolder.$set['webid'].'/';
				$path_temp='../'.setup_webfolder.$set['webid'].'/';
				deldir_admin($path_up);
				deldir_admin($path_temp);
				$moduser=db_getall('module_user','*','webid='.$set['webid']);
				foreach($moduser as $mr){
					$tname='article_'.$set['webid'].'_'.$mr['dataid'];
					$result=mysql_num_rows(mysql_query("SHOW TABLES LIKE '".tabname($tname)."'"));
					if($result)$result=mysql_query("DROP TABLE `".tabname($tname)."`");
					}
				db_del('admin','webid='.$set['webid']);
				db_del('admin_group','webid='.$set['webid']);
				db_del('admin_log','webid='.$set['webid']);
				db_del('advert','webid='.$set['webid']);
				db_del('article','webid='.$set['webid']);
				db_del('config','webid='.$set['webid']);
				db_del('customer','webid='.$set['webid']);
				db_del('feedback','webid='.$set['webid']);
				db_del('label','webid='.$set['webid']);
				db_del('limit','webid='.$set['webid']);
				db_del('login','webid='.$set['webid']);
				db_del('module','webid='.$set['webid']);
				db_del('module_file','webid='.$set['webid']);
				db_del('module_user','webid='.$set['webid']);
				db_del('select','webid='.$set['webid']);
				db_del('special','webid='.$set['webid']);
				db_del('special_list','webid='.$set['webid']);
				db_del('tool_email','webid='.$set['webid']);
				db_del('tool_email_address','webid='.$set['webid']);
				db_del('websetup','webid='.$set['webid']);
				db_del('website','webid='.$set['webid']);
				$idlen_ok++;
			break;
			case 'special':
				if(!ispower($admin_group,'special_del'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('special','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['cover']!=''){
					$cover_b=str_replace('{size}','b',$data['cover']);
					$cover_s=str_replace('{size}','s',$data['cover']);
					$data['piclist'].='|'.$cover_b.'|'.$cover_s;
					}
				if($data['piclist']!=''){
					$pic=explode('|',$data['piclist']);
					foreach($pic as $val){
						if($val!=''){
							$delpath='../'.$website['upfolder'].$val;
							@unlink($delpath);
							}
						}
					}
				db_del('special_list','webid='.$website['webid'].' and special_id='.$id);
				db_del('special','webid='.$website['webid'].' and id='.$id);
				$idlen_ok++;
			break;
			case 'config_domain':
				if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('website','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['isdef'])ajaxreturn(1,'域名 '.$data['setup_weburl'].' 为站点主域名，请先设置其它域名为主域名再删除');
				$idlen_ok++;
				db_del('website','id='.$id);
			break;
			case 'config_args_theme':
				if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('config','*','webid='.$website['webid'].' and contype=1 and cata="theme" and id='.$id);
				if(!$data)continue;
				$idlen_ok++;
				db_del('config','id='.$id);
				infoadminlog($website['webid'],$gly,26,'删除主题参数“'.$data['varname'].'”（ID='.$id.'）');
			break;
			case 'config_args':
				if(!$website['isadmin']||!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('config','*','webid=1 and contype=1 and id='.$id);
				if(!$data)continue;
				$idlen_ok++;
				db_del('config','id='.$id);
				infoadminlog($website['webid'],$gly,26,'删除参数“'.$data['varname'].'”（ID='.$id.'）');
			break;
			case 'template':
				if(!ispower($admin_group,'skin_del'))ajaxreturn(1,'权限不足，操作失败');
				if(strstr($filearr[$k],'./'))continue;
				$idlen_ok++;
				$fpath=iconv("utf-8","gb2312",'../'.setup_webfolder.$website['webid'].'/'.$filearr[$k]);
				@unlink($fpath);
			break;
			case 'module_field':
				if(!ispower($admin_group,'module_user'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('module_field','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				$tname='article_'.$website['webid'].'_'.$data['modid'];
				$artmod=db_getall('module','id,classid','webid='.$website['webid'].' and modtype='.$data['modid']);
				foreach($artmod as $amod){
					$artlist=db_getall($tname,'id,dataid,piclist,'.$data["varname"],'webid='.$website['webid'].' and bcat='.$amod['classid']);
					foreach($artlist as $art){
						$vname=$data["varname"];
						if($art[$vname]=='')continue;
						switch($data['infotype']){
							case 'up_cover':
								$delpath1='../'.$website['upfolder'].str_replace('{size}','b',$art[$vname]);
								@unlink($delpath1);
								$delpath2='../'.$website['upfolder'].str_replace('{size}','s',$art[$vname]);
								@unlink($delpath2);
							break;
							case 'up_file':
								$delpath1='../'.$website['upfolder'].$art[$vname];
								@unlink($delpath1);
							break;
							case 'editor':
								$picarr=unserialize(htmlspecialchars_decode($art['piclist']));
								if(!is_array($picarr)||empty($picarr))continue;
								if(array_key_exists($data["varname"],$picarr)){
									$pic=explode('|',$picarr[$data["varname"]]);
									foreach($pic as $p2){
										if($p2!=''){
											$delpath='../'.$website['upfolder'].$p2;
											@unlink($delpath);
											}
										}
									$picarr=deltable($picarr,$data["varname"]);
									$piclist=htmlspecialchars(serialize($picarr));
									db_upshow($tname,'piclist="'.$piclist.'"','id='.$art['id']);
									}
							break;
							}
						}
					}
				$tname=db_tabfirst.'article_'.$website['webid'].'_'.$data['modid'];
				$delsql="ALTER TABLE `".$tname."` DROP `".$data['varname']."`;";
				$status=$db->query($delsql);
				db_del('module_field','id='.$id);
				infoadminlog($website['webid'],$gly,12,'删除自定义模块-字段“'.$data['title'].'”（ID='.$id.'）');
				$idlen_ok++;
			break;
			case 'module_user':
				if(!ispower($admin_group,'module_user'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('module_user','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				$data_count=db_count('module','webid='.$website['webid'].' and modtype='.$data['dataid']);
				if($data_count){
					ajaxreturn(1,'“'.$data['title'].'”无法删除，请先删除使用该模块的栏目分类');
					}
				$tname='article_'.$website['webid'].'_'.$data['dataid'];
				$artlist=db_getall($tname,'*','webid='.$website['webid']);
				$conf_file=db_getall('module_field','*','webid='.$website['webid'].' and modid='.$data['dataid'].' and (infotype="up_cover" or infotype="up_file") order by sort asc');
				foreach($artlist as $data2){
					foreach($conf_file as $val){
						$vname=$val['varname'];
						if($data2[$vname]=='')continue;
						switch($val['infotype']){
							case 'up_cover':
								$delpath1='../'.$website['upfolder'].str_replace('{size}','b',$data2[$vname]);
								@unlink($delpath1);
								$delpath2='../'.$website['upfolder'].str_replace('{size}','s',$data2[$vname]);
								@unlink($delpath2);
							break;
							case 'up_file':
								$delpath1='../'.$website['upfolder'].$data2[$vname];
								@unlink($delpath1);
							break;
							}
						}
					if($data2['piclist']!=''){
						$picarr=unserialize(htmlspecialchars_decode($data2['piclist']));
						if(!is_array($picarr)||empty($picarr))$picarr=array();
						foreach($picarr as $pic){
							$picarr2=explode('|',$pic);
							foreach($picarr2 as $val){
								if($val!=''){
									$delpath='../'.$website['upfolder'].$val;
									@unlink($delpath);
									}
								}
							}
						}
					db_del('feedback','webid='.$website['webid'].' and bcat='.$data2['bcat'].' and dataid='.$data2['dataid']);
					}
				$result=mysql_num_rows(mysql_query("SHOW TABLES LIKE '".tabname($tname)."'"));
				if($result)$result=mysql_query("DROP TABLE `".tabname($tname)."`");
				db_del('module_field','webid='.$website['webid'].' and modid='.$data['dataid']);
				db_del('module_user','id='.$id);
				infoadminlog($website['webid'],$gly,12,'删除自定义模块“'.$data['title'].'”（ID='.$data['dataid'].'）');
				$idlen_ok++;
				sleep(1);
			break;
			case 'feedback':
				if(!ispower($admin_group,'book_del'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('feedback','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['attachment']!=''){
					$delpath='..'.getfile_admin('file',$data['attachment']);
					if(file_exists($delpath))unlink($delpath);
					}
				if($data['dataid']){
					$tname='article';
					$bmod=db_getshow('module','modtype','webid='.$website['webid'].' and classid='.$data['bcat']);
					if($bmod){
						if($bmod['modtype']>10)$tname.='_'.$website['webid'].'_'.$bmod['modtype'];
						}
					db_upshow($tname,'comment=comment-1','webid='.$website['webid'].' and dataid='.$data['dataid']);
					}
				db_del('feedback','id='.$id);
				infoadminlog($website['webid'],$gly,18,'删除留言评论“'.$data['name'].'”（ID='.$id.'）');
				$idlen_ok++;
			break;
			case 'advert':
				if(!ispower($admin_group,'advert_del'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('advert','title,id,fileurl','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['fileurl']!=''){
					$delpath='..'.getfile_admin('pic',$data['fileurl']);
					if(file_exists($delpath))unlink($delpath);
					}
				db_del('advert','id='.$id);
				infoadminlog($website['webid'],$gly,17,'删除广告“'.$data['title'].'”（ID='.$id.'）');
				$idlen_ok++;
			break;
			case 'customer':
				if(!ispower($admin_group,'customer_del'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('customer','name,id,head','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['head']!=''){
					$delpath='..'.getfile_admin('pic',$data['head']);
					if(file_exists($delpath))unlink($delpath);
					}
				db_del('customer','id='.$id);
				infoadminlog($website['webid'],$gly,16,'删除客服“'.$data['name'].'”（ID='.$id.'）');
				$idlen_ok++;
			break;
			case 'admin':
				if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('admin','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['user_admin']==$gly)continue;
				if($data['user_head']!=''){
					$delpath='..'.getfile_admin('pic',$data['user_head']);
					if(file_exists($delpath))unlink($delpath);
					}
				db_del('admin','id='.$id);
				infoadminlog($website['webid'],$gly,2,'删除管理员“'.$data['user_admin'].'”（ID='.$id.'）');
				$idlen_ok++;
			break;
			case 'admin_group':
				if(!ispower($admin_group,'super'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('admin_group','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				$group_count=db_count('admin','webid='.$website['webid'].' and config_group='.$data['groupid'])+0;
				if($group_count)continue;
				db_del('admin_group','id='.$id);
				infoadminlog($website['webid'],$gly,2,'删除管理组“'.$data['group_name'].'”（ID='.$data['groupid'].'）');
				$idlen_ok++;
			break;
			case 'tool_email':
				$data=db_getshow('tool_email','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				db_del('tool_email','id='.$id);
				infoadminlog($website['webid'],$gly,2,'删除群发任务“'.$data['title'].'”（ID='.$id.'）');
				$idlen_ok++;
			break;
			case 'tool_email_address':
				$data=db_getshow('tool_email_address','*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				$data_count=db_count('tool_email','webid='.$website['webid'].' and addressid='.$id)+0;
				if($data_count)continue;
				db_del('tool_email_address','id='.$id);
				infoadminlog($website['webid'],$gly,2,'删除发信邮箱“'.$data['name'].'”（ID='.$data['addressid'].'）');
				$idlen_ok++;
			break;
			case 'option':
				if(!ispower($admin_group,'skin_option'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('select','id,title,types','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				db_del('select','id='.$id);
				infoadminlog($website['webid'],$gly,27,'删除自定义选项“'.$data['title'].'”（ID='.$data['id'].'）');
				$idlen_ok++;
			break;
			case 'label':
				if(!ispower($admin_group,'skin_label'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('label','id,dataid,title,piclist','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['piclist']!=''){
					$pic=explode('|',$data['piclist']);
					foreach($pic as $val){
						if($val!=''){
							$delpath='../'.$website['upfolder'].$val;
							@unlink($delpath);
							}
						}
					}
				db_del('label','id='.$id);
				infoadminlog($website['webid'],$gly,20,'删除标签“'.$data['title'].'”（ID='.$data['dataid'].'）');
				$idlen_ok++;
			break;
			case 'article_user':
				$data=db_getshow($tname,'*','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				foreach($conf_file as $val){
					$vname=$val['varname'];
					if($data[$vname]=='')continue;
					switch($val['infotype']){
						case 'up_cover':
							$delpath1='../'.$website['upfolder'].str_replace('{size}','b',$data[$vname]);
							@unlink($delpath1);
							$delpath2='../'.$website['upfolder'].str_replace('{size}','s',$data[$vname]);
							@unlink($delpath2);
						break;
						case 'up_file':
							$delpath1='../'.$website['upfolder'].$data[$vname];
							@unlink($delpath1);
						break;
						}
					}
				if($data['piclist']!=''){
					$picarr=unserialize(htmlspecialchars_decode($data['piclist']));
					if(!is_array($picarr)||empty($picarr))$picarr=array();
					foreach($picarr as $pic){
						$picarr2=explode('|',$pic);
						foreach($picarr2 as $val){
							if($val!=''){
								$delpath='../'.$website['upfolder'].$val;
								@unlink($delpath);
								}
							}
						}
					}
				db_del('feedback','webid='.$website['webid'].' and bcat='.$data['bcat'].' and dataid='.$data['dataid']);
				db_del('special_list','webid='.$website['webid'].' and modtype='.$data['modtype'].' and dataid='.$data['dataid']);
				db_del($tname,'id='.$id);
				infoadminlog($website['webid'],$gly,13,'删除文章“'.$data['title'].'”（ID='.$data['dataid'].',ClassID='.$data['bcat'].'）');
				$idlen_ok++;
			break;
			case 'article':
				if(!ispower($admin_group,'art_del'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('article','id,dataid,bcat,title,cover,piclist,modtype','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['cover']!=''){
					$cover_b=str_replace('{size}','b',$data['cover']);
					$cover_s=str_replace('{size}','s',$data['cover']);
					$data['piclist'].='|'.$cover_b.'|'.$cover_s;
					}
				if($data['piclist']!=''){
					$pic=explode('|',str_replace('$','|',$data['piclist']));
					foreach($pic as $val){
						if($val!=''){
							$delpath='../'.$website['upfolder'].$val;
							@unlink($delpath);
							}
						}
					}
				db_del('feedback','webid='.$website['webid'].' and bcat='.$data['bcat'].' and dataid='.$data['dataid']);
				db_del('special_list','webid='.$website['webid'].' and modtype='.$data['modtype'].' and dataid='.$data['dataid']);
				db_del('article','id='.$id);
				infoadminlog($website['webid'],$gly,13,'删除文章“'.$data['title'].'”（ID='.$data['dataid'].',ClassID='.$data['bcat'].'）');
				$idlen_ok++;
			break;
			case 'module':
				if(!ispower($admin_group,'module_del'))ajaxreturn(1,'权限不足，操作失败');
				$data=db_getshow('module','id,classid,cover,modtype,title','webid='.$website['webid'].' and id='.$id);
				if(!$data)continue;
				if($data['cover']!=''){
					$delpath='..'.getfile_admin('pic',$data['cover'],'s');
					if(file_exists($delpath))@unlink($delpath);
					$delpath='..'.getfile_admin('pic',$data['cover'],'b');
					if(file_exists($delpath))@unlink($delpath);
					}
				$nextmod=db_count('module','webid='.$website['webid'].' and (bcat='.$data['classid'].' or scat='.$data['classid'].')')+0;	//判断有下级分类
				if($nextmod)ajaxreturn(1,'无法删除“'.$data['title'].'”，请先删除该栏目的下一级分类');
				$tname='article';
				if($data['modtype']>10)$tname.='_'.$website['webid'].'_'.$data['modtype'];
				$havedata=db_count($tname,'webid='.$website['webid'].' and (bcat='.$data['classid'].' or scat='.$data['classid'].' or lcat='.$data['classid'].')')+0;	//判断有数据
				if($havedata)ajaxreturn(1,'无法删除“'.$data['title'].'”，请先删除该栏目下的所有文章或记录');
				db_del('module','id='.$id);
				infoadminlog($website['webid'],$gly,12,'删除网站模块“'.$data['title'].'”（ID='.$data['classid'].'）');
				$idlen_ok++;
			break;
			default:
				ajaxreturn(1,'未知的记录类型，删除失败');
				return;
			break;
			}
	};
$idlen_error=$idlen-$idlen_ok;
countcapacity($website['webid']);
ajaxreturn(0,'成功删除 '.goif($idlen_ok,'<b class="blue">','<b>').$idlen_ok.'</b> 条记录，失败 '.goif($idlen_error,'<b class="red">','<b>').$idlen_error.'</b> 条',goif($idlen_error==0,'success','alert'));
