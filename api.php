<?php
ini_set("max_execution_time","1800");
include('include/config.php');
include('include/function.php');
include('include/class_mysql.php');
nocache();
$tcz=array(
'log'=>arg('log','all','url')
);
$db=new mysql();
switch($tcz['log']){
	case 'skin':
		$change=arg('change','get','url');
		$times=time()+86400*365;
		setcookie('web_templog',$change,$times,'/');
		$_COOKIE['web_templog']=$change;
		@$back=$_SERVER['HTTP_REFERER'];
		if($back=='')$back='/';
		header('Location:'.$back);
	break;
	case 'lang':
		$change=arg('change','get','url');
		if($change=='')tipmsg('未知的语言类型',true);
		$times=time()+86400*365;
		setcookie('web_templang',$change,$times,'/');
		$_COOKIE['web_templang']=$change;
		$back='/';
		header('Location:'.$back);
	break;
	case 'reg':
	case 'login':
		@$back=$_SERVER['HTTP_REFERER'];
		if($back=='')$back='/';
		$back=urlencode($back);
		$sig=md5(setup_qyk_appid.setup_qyk_appsecret.$back.date('ymd'));
		$url=setup_qyk_api.'api.php?log=qyk'.$tcz['log'].'&appid='.setup_qyk_appid.'&back='.$back.'&sig='.$sig;
		header('Location:'.$url);
		echo '正在跳转，请稍候...';
	break;
	case 'gopc':
		setcookie('web_tempview','pc',0,'/');
		$url='/';
		header('Location:'.$url);
	break;
	case 'feedback_upload':
		@$webdomain=$_SERVER['SERVER_NAME'];
		$website=db_getshow('website','*','setup_weburl="'.$webdomain.'"');
		echo $website['webid'];
		if(!$website){
			die('error');
			exit;
			}
		if($_FILES["file"]["error"]>0){
			echo '<script type="text/javascript">parent.PZ.sendfeedback_upload({log:"error",msg:"未知的附件类型，请重新尝试"});</script>';
			exit;
		}else{
			$fsize=$_FILES['file']['size']/1024;
			if($fsize>5120){
				echo '<script type="text/javascript">parent.PZ.sendfeedback_upload({log:"error",msg:"请将附件控制在5MB以内"});</script>';
				exit;
				}
			$typename=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
			if(!in_array($typename,array('zip','rar','doc','jpg','gif','png'))){
				echo '<script type="text/javascript">parent.PZ.sendfeedback_upload({log:"error",msg:"不支持的附件类型，请尽量压缩后上传（01）"});</script>';
				exit;
				}
			$filename=date('dHis').'_'.randomkeys(6).'.'.$typename;
			$path=setup_upfolder.$website['webid'].'/'.setup_uptemp.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'],$path);
			@$fdata=file_get_contents($path);
			if(preg_match("/<\%|<\?|%\>|\?\>/is",$fdata)){
				@unlink($path);
				echo '<script type="text/javascript">parent.PZ.sendfeedback_upload({log:"error",msg:"不支持的附件类型，请尽量压缩后上传（02）"});</script>';
				exit;
				}
			$req="'<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
			if(preg_match("/".$req."/is",$fdata)==1){
				@unlink($path);
				echo '<script type="text/javascript">parent.PZ.sendfeedback_upload({log:"error",msg:"不支持的附件类型，请尽量压缩后上传（03）"});</script>';
				exit();
				}
			echo '<script type="text/javascript">parent.PZ.sendfeedback_upload({log:"success",file:"'.$filename.'"});</script>';
			}
	break;
	default:
		$url=$_SERVER["QUERY_STRING"];
		if($url!=''){
			$url=preg_replace('/^\//','',$url);
			gotourl($url);
			}
	break;
	}
?>