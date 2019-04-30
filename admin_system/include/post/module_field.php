<?php
if(!ispower($admin_group,'module_user'))ajaxreturn(1,'权限不足，操作失败');
$arr=array(
	'webid'=>$website['webid'],
	'modid'=>arg('modid','post','int'),
	'title'=>arg('title','post','txt'),
	'varname'=>arg('varname','post','url'),
	'varval'=>arg('varval','post','txt'),
	'infotype'=>arg('infotype','post','url'),
	'infosel'=>arg('infosel','post','url'),
	'content'=>arg('content','post','txt'),
	'isedit'=>arg('isedit','post','int'),
	'sort'=>arg('sort','post','int')
	);
$usermod=db_getshow('module_user','*','webid='.$website['webid'].' and dataid='.$arr['modid']);
if(!$usermod)ajaxreturn(1,'不存在的自定义模块');
$tname='article_'.$website['webid'].'_'.$arr['modid'];
//$isdata=db_getshow('module_field','*','webid='.$website['webid'].' and modid='.$arr['modid'].' and id!='.$tcz['id'].' and varname="'.$arr['varname'].'"');
//if($isdata)ajaxreturn(1,'同一模块中的字段名称是不可重复的：'.$arr['varname']);
$data=db_getshow('module_field','*','webid='.$website['webid'].' and id='.$tcz['id']);
if($data){
	$zdsql="ALTER TABLE `".tabname($tname)."` CHANGE `".$data["varname"]."` `".$arr["varname"]."`";
	if($arr["varname"]!=$data["varname"]){
		$zd=mysql_num_rows(mysql_query("DESCRIBE `".tabname($tname)."` `".$arr['varname']."`"));
		if($zd)ajaxreturn(1,'无法创建“'.$arr['varname'].'”字段，该字段已被系统内置或已创建');
		if($arr['infotype']=='editor'){
			$artmod=db_getall('module','id,classid','webid='.$website['webid'].' and modtype='.$data['modid']);
			foreach($artmod as $amod){
				$artlist=db_getall($tname,'id,dataid,piclist,'.$data["varname"],'webid='.$website['webid'].' and bcat='.$amod['classid']);
				foreach($artlist as $art){
					$picarr=unserialize(htmlspecialchars_decode($art['piclist']));
					if(!is_array($picarr)||empty($picarr))continue;
					if(array_key_exists($data["varname"],$picarr)){
						$picarr[$arr['varname']]=$picarr[$data["varname"]];
						$picarr=deltable($picarr,$data["varname"]);
						$piclist=htmlspecialchars(serialize($picarr));
						db_upshow($tname,'piclist="'.$piclist.'"','id='.$art['id']);
						}
					}
				}
			}
		}
	if($arr["infotype"]!=$data["infotype"]){
		if($arr['infotype']=='up_file'||$arr['infotype']=='up_cover'){
			$artmod=db_getall('module','id,classid','webid='.$website['webid'].' and modtype='.$data['modid']);
			foreach($artmod as $amod){
				db_upshow($tname,$data["varname"].'=""','webid='.$website['webid'].' and bcat='.$amod['classid']);
				}
			}
		if($data['infotype']=='editor'||$data['infotype']=='up_file'||$data['infotype']=='up_cover'){
			$artmod=db_getall('module','id,classid','webid='.$website['webid'].' and modtype='.$data['modid']);
			foreach($artmod as $amod){
				$artlist=db_getall($tname,'id,dataid,piclist,'.$data["varname"],'webid='.$website['webid'].' and bcat='.$amod['classid']);
				foreach($artlist as $art){
					switch($data['infotype']){
						case 'up_cover':
							$delpath1='../'.$website['upfolder'].str_replace('{size}','b',$art[$data["varname"]]);
							@unlink($delpath1);
							$delpath2='../'.$website['upfolder'].str_replace('{size}','s',$art[$data["varname"]]);
							@unlink($delpath2);
							db_upshow($tname,$data["varname"].'=""','id='.$art['id']);
						break;
						case 'up_file':
							$delpath1='../'.$website['upfolder'].$art[$data["varname"]];
							@unlink($delpath1);
							db_upshow($tname,$data["varname"].'=""','id='.$art['id']);
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
			}
		}
	switch($arr['infotype']){
		case 'text':case 'editor':
			$zdsql.=" text;";
		break;
		default:
			$zdsql.=" varchar(200)";
			$zdsql.=" default '".$arr['varval']."';";
		break;
		}
	$status=$db->query($zdsql);
	db_uparr('module_field',$arr,'id='.$data['id']);
	infoadminlog($website['webid'],$tcz['admin'],12,'编辑自定义模块-字段“'.$arr['title'].'”（ID='.$data['id'].'）');
}else{
	$zd=mysql_num_rows(mysql_query("DESCRIBE `".tabname($tname)."` `".$arr['varname']."`"));
	if($zd)ajaxreturn(1,'无法创建“'.$arr['varname'].'”字段，该字段已被系统内置或已创建');
	$zdsql="ALTER TABLE `".tabname($tname)."` add `".$arr['varname']."`";
	switch($arr['infotype']){
		case 'text':case 'editor':
			$zdsql.=" text;";
		break;
		default:
			$zdsql.=" varchar(200)";
			$zdsql.=" default '".$arr['varval']."';";
		break;
		}
	$status=$db->query($zdsql);
	$newid=db_intoarr('module_field',$arr,true);
	infoadminlog($website['webid'],$tcz['admin'],12,'新建自定义模块-字段“'.$arr['title'].'”（ID='.$newid.'）');
	ajaxreturn(0);
	}
ajaxreturn(0,'已成功保存标签信息');