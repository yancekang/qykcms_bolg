<?php
$types=arg('types','all','url');
if($_FILES["file"]["error"]>0){
	ajaxreturn(1,'error');
}else{
	$gifstatus=false;
	$upgif=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_upgif"');
	if($upgif=='true')$gifstatus=true;
	$coversize=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_bigcover_size"');
	if(!$coversize)$coversize='0,0';
	$coversize=explode(',',$coversize.',0');
	$res='';
	$fsize=$_FILES['file']['size']/1024;	//获取到上传文件大小 KB
	$capacity_have=countcapacity($website['webid'],false);
	$capacity_have=ceil($capacity_have+$fsize);
	$setup=db_getshow('websetup','*','webid='.$website['webid']);
	if($setup['capacity_max']>0){
		if($setup['capacity_max']<$capacity_have){
			countcapacity($website['webid']);
			ajaxreturn(1,'error2');
			}
		}
	//ajaxreturn(0,'http://'.setup_weburl.'/'.setup_upfolder.setup_uptemp.'|'.$types);
	switch($types){
		case 'template':
			if(!ispower($admin_group,'skin_upload'))ajaxreturn(1,'error4');
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=strtolower($_FILES['file']['name']);
			if(!in_array($typename,array('html','css','js','jpg','gif','png')))ajaxreturn(1,'error');
			$path=arg('path','all','url');
			$filepath='../'.setup_webfolder.$website['webid'].'/'.$path.'/'.$filename;
			$isup=false;
			$path2=$filepath;
			for($i=1;$i<=10000;$i++){
				if(file_exists($path2)){
					$path2=preg_replace('/\.'.$typename.'$/','_'.$i.'.'.$typename.'',$filepath);
				}else{
					$filepath=$path2;
					$isup=true;
					break;
					}
				}
			if($isup){
				move_uploaded_file($_FILES['file']['tmp_name'],$filepath);
				infoadminlog($website['webid'],$tcz['admin'],24,'上传模板文件：'.$path.'/'.$filename);
				$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
			}else{
				ajaxreturn(1,'error3');	//文件重名
				}
		break;
		case 'none':
			//$oldname=strtolower($_FILES['file']['name']);
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=date('dHis').'_'.randomkeys(6).'.'.$typename;
			$path='../'.$website['upfolder'].setup_uptemp.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
		break;
		case 'theme':
			if(!ispower($admin_group,'super'))ajaxreturn(1,'error4');
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			if($typename!='zip')ajaxreturn(1,'error');
			$dir='../'.$website['upfolder'].setup_uptemp;
			$filename='qyk_theme_new.zip';
			$path=$dir.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			infoadminlog($website['webid'],$tcz['admin'],24,'上传主题安装包');
			$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
		break;
		case 'uploadzip':
			if(!ispower($admin_group,'uploadzip_edit'))ajaxreturn(1,'error4');
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=strtolower($_FILES['file']['name']);
			$folder=date('Y_m');
			$dir='../'.$website['upfolder'].'down/'.$folder.'/';
			createDirs($dir);
			$path=$dir.$filename;
			$isup=false;
			$path2=$path;
			for($i=1;$i<=10000;$i++){
				if(file_exists($path2)){
					$path2=preg_replace('/\.'.$typename.'$/','_'.$i.'.'.$typename.'',$path);
				}else{
					$path=$path2;
					$isup=true;
					break;
					}
				}
			if($isup){
				move_uploaded_file($_FILES['file']['tmp_name'],$path);
				infoadminlog($website['webid'],$tcz['admin'],23,'上传附件：'.$folder.'/'.$filename);
				$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename.'|'.$folder;
			}else{
				ajaxreturn(1,'error3');	//文件重名
				}
		break;
		case 'advert':
			$adstatus=false;
			$upadvert=db_getone('config','varval','webid='.$website['webid'].' and varname="setup_upadvert"');
			if($upadvert=='true')$adstatus=true;
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=date('dHis').'_'.randomkeys(6).'.'.$typename;
			$path='../'.$website['upfolder'].setup_uptemp.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			if($adstatus){
				$ps=new photo();
				$ps->setOpenpath($path);
				$ps->setSavepath($path);
				$ps->setImgresize(false);
				$ps->setImgquality(90);
				$ps->setImgsize(9999,9999,1,1);
				$ps->createImg();
				if($typename=='png'){	//如果是PNG则转换成JPG
					$path2=str_replace('.png','.jpg',$path);
					rename($path,$path2);
					$filename=str_replace('.png','.jpg',$filename);
					}
				}
			$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
		break;
		case 'customer_head':
		case 'admin_head':
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=date('dHis').'_'.randomkeys(6).'.'.$typename;
			$path='../'.$website['upfolder'].setup_uptemp.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			$ps=new photo();
			$ps->setOpenpath($path);
			$ps->setSavepath($path);
			$ps->setImgresize(true);
			$ps->setImgquality(90);
			$ps->setImgsize(200,200,200,200);
			$ps->createImg();
			if($typename=='png'){	//如果是PNG则转换成JPG
				$path2=str_replace('.png','.jpg',$path);
				rename($path,$path2);
				$filename=str_replace('.png','.jpg',$filename);
				}
			$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
		break;
		case 'editor':
		case 'editor_post_content':
		case 'editor_art_content':
		case 'editor_art_content_1':
		case 'editor_art_content_2':
		case 'editor_art_content_3':
		case 'editor_art_content_4':
			@require_once('../'.setup_webfolder.$website['webid'].'/config/global.php');
			//ajaxreturn(0,'abc/|'.setup_markimg);
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=date('dHis').'_'.randomkeys(6).'.'.$typename;
			$path='../'.$website['upfolder'].setup_uptemp.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			if(!$gifstatus||$typename!='gif'){
				$ps=new photo();
				$ps->setOpenpath($path);
				$ps->setSavepath($path);
				$ps->setImgresize(false);
				$ps->setImgquality(90);
				$ps->setImgsize(1600,9999,20,20);
				if(setup_marktype&&setup_markimg!='')$ps->setImgmark('..'.getfile_admin('pic',setup_markimg),setup_markxy,setup_markalpha,setup_markside,setup_markmin);
				$ps->createImg();
				if($typename=='png'){	//如果是PNG则转换成JPG
					$path2=str_replace('.png','.jpg',$path);
					rename($path,$path2);
					$filename=str_replace('.png','.jpg',$filename);
					}
				}
			$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
		break;
		case 'theme_cover':
			$coversize=array(480,9999);
		case 'cover':
		case 'art_cover':
		case 'mod_cover':
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			$filename=date('dHis').'_'.randomkeys(6).'_{size}.'.$typename;
			//ajaxreturn(0,$_FILES['file']['name']);
			$path='../'.$website['upfolder'].setup_uptemp.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			if(!$gifstatus||$typename!='gif'){
				$ps=new photo();
				$ps->setOpenpath($path);
				$ps->setSavepath($path);
				$ps->setImgresize(false);
				$ps->setImgquality(90);
				$ps->setImgsize($coversize[0],$coversize[1],1,1);
				$ps->createImg();
				if($typename=='png'){	//如果是PNG则转换成JPG
					$path2=str_replace('.'.$typename,'.jpg',$path);
					rename($path,$path2);
					$filename=str_replace('.'.$typename,'.jpg',$filename);
					}
				}
			$res='http://'.$website['setup_weburl'].'/'.$website['upfolder'].setup_uptemp.'|'.$filename;
		break;
		default:
			ajaxreturn(1,'error');
		break;
		}
	db_upshow('websetup','capacity_have='.$capacity_have,'webid='.$website['webid']);
	ajaxreturn(0,$res);
	}