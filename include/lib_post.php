<?php
$res='';
switch($tcz['desc']){
	case 'login':
		$uid=arg('uid','post','int');
		$ucode=arg('ucode','post','url');
		$uname=arg('uname','post','url');
		$sig=arg('sig','post','url');
		$sig_new=md5(setup_qyk_appid.$uid.$ucode.setup_qyk_appsecret.date('ymd'));
		if($sig!=$sig_new)ajaxreturn(1,'参数错误，同步登录通行证失败');
		$login_key=md5(randomkeys(20));
		$login=db_getshow('login','*','user_id='.$uid);
		if(!$login){
			$tab='user_id,login_key,login_time';
			$val=$uid.',"'.$login_key.'",'.time();
			db_intoshow('login',$tab,$val);
		}else{
			db_upshow('login','login_key="'.$login_key.'",login_time='.time(),'user_id='.$uid);	
			}
		setcook($uid,$ucode,$uname,$login_key);
		ajaxreturn(0,'success');
	break;
	case 'logout':
		clearcook();
		ajaxreturn(0);
	break;
	case 'getarticlehits':
		$modtype=arg('modtype','post','int');
		$dataid=arg('dataid','post','int');
		$tname='article';
		if($modtype>10)$tname.='_'.$web['id'].'_'.$modtype;
		$hits=db_getone($tname,'hits','webid='.$web['id'].' and isok=0 and dataid='.$dataid);
		if(!$hits)ajaxreturn(1);
		if(setup_datacache){
			db_upshow($tname,'hits=hits+1','dataid='.$dataid);
			$hits++;
			}
		ajaxreturn(0,$hits);
	break;
	case 'comment_list':
		$bcat=arg('bcat','post','int');
		$dataid=arg('dataid','post','int');
		$size=arg('size','post','int');
		$mod=arg('mod','post','int');
		if(!$size)$size=setup_comment_size;
		$comm=array('num'=>0,'list'=>'','page'=>'');
		$sql='select * from '.tabname('feedback').' where webid='.$web['id'].' and isok=0';
		if($dataid){
			if(!$bcat)ajaxreturn(1);
			$sql.=' and dataid='.$dataid;
		}else{
			$sql.=' and dataid=0';
			}
		$sql.=' order by '.goif(setup_feedback_order==1,'time_add desc,id desc','time_add asc,id asc');
		$comm['num']=$db->num_rows($db->query($sql));
		$start=$tcz['page']*$size-$size;
		$comm['list']=$db->fetch_all($db->query($sql.' limit '.$start.','.$size));
		$page=new pagelist();
		$comm['page']=$page->show($comm['num'],$size,'javascript:PZ.comment({mod:'.$mod.',page:{P},size:'.$size.',scro:true,load:true})',goif($web['mobiletemp'],'mobile','full'),true,'');
		$res='';
		foreach($comm['list'] as $val){
			$res.='<li class="comm_li"><div class="comm_list_top">
<div class="comm_list_top_left"><span>'.$val['name'].'</span>';
			if(!$web['mobiletemp'])$res.='　（'.getlang('comment','from').goif($val['user_iptext']!='',$val['user_iptext'],hideaddress($val['user_ip'],'ip')).'）';
			$res.='</div>
<div class="comm_list_top_right">'.getimes($val['time_add']).'</div>
</div><div class="comm_list_desc">'.getcomment($val['content']).'</div>'.goif($val['reply']!='','<div class="comm_list_reply"><span>回复：</span>'.$val['reply'].'</div>').'</li>';
			}
		if($res=='')$res='<li class="comm_none">'.getlang('comment','nodata').'</li>';
		ajaxreturn(0,$res,$comm['page'],$comm['num']);
	break;
	case 'comment':
		$fromurl=$_SERVER["HTTP_REFERER"];
		if(!strstr($fromurl,setup_weburl))ajaxreturn(3,'未知的URL来源，请刷新重新提交或联系工作人员');
		$user_ip=getip();
		if(setup_feedback_limit){
			$ltime=time()-setup_feedback_limit;
			$ftime=db_count('feedback','webid='.$web['id'].' and time_add>'.$ltime.' and user_ip="'.$user_ip.'"');
			if($ftime){
				$errtip=sprintf(getlang('comment','limiterr'),setup_feedback_limit);
				ajaxreturn(1,$errtip);
				}
			}
		$content=arg('content','post','txt');
		$dataid=arg('dataid','post','int');
		$bcat=arg('bcat','post','int');
		if(setup_feedback_cn){
			if(!preg_match("/[\x7f-\xff]/",$content)){
				$errtip=getlang('send','notcn');
				ajaxreturn(1,$errtip);
				}
			}
		if(setup_errkey!=''){
			if(!checkerrkey($content)){
				$errtip=getlang('send','errkey');
				ajaxreturn(1,$errtip);
				}
			}
		if($dataid){
			$tname='article';
			$modtype=db_getone('module','modtype','webid='.$web['id'].' and classid='.$bcat);
			if($modtype>10)$tname.='_'.$web['id'].'_'.$modtype;
			$art=db_getshow($tname,'*','webid='.$web['id'].' and dataid='.$dataid);
			if(!$art)ajaxreturn(1,'不存在的记录，请刷新页面重试');
		}else{
			$bcat=0;
			}
		$name=arg('name','post','txt');
		if($name=='')$name=$cook['uname'];
		else{
			setcook(0,'',$name,'',1440);
			}
		require_once('include/class_ip.php');
		$ipdata=new IpLocation();
		$add=$ipdata->getlocation($user_ip);
		$user_iptext=$add['country'];
		$isok=0;
		$isok_res='yes';
		if(setup_feedback_isok){
			$isok=1;
			$isok_res='no';
			}
		$data=array(
			'webid'=>$web['id'],
			'bcat'=>$bcat,
			'dataid'=>$dataid,
			'languages'=>'',
			'name'=>$name,
			'email'=>'',
			'phone'=>'',
			'user_ip'=>$user_ip,
			'user_iptext'=>$user_iptext,
			'content'=>$content,
			'attachment'=>'',
			'time_add'=>time(),
			'time_view'=>0,
			'user_admin'=>'',
			'isok'=>$isok
			);
		db_intoarr('feedback',$data);
		if($dataid){
			$comment=db_count('feedback','webid='.$web['id'].' and bcat='.$bcat.' and dataid='.$dataid);
			db_upshow($tname,'comment='.$comment,'webid='.$web['id'].' and dataid='.$dataid);
			}
		if(setup_smtp_email!=''){
			$content1=getcomment($content,3);
			include('include/class_smtp.php');
			$mailbody='姓名称呼：'.$name.'<br>留言时间：'.date('Y-m-d H:i:s').'<br>==== 留言内容 ====<br>'.$content1;
			sendmail(setup_smtp_email,'网站留言（'.tipshort($name,50).'）-'.setup_shortname,$mailbody,'网站留言');
			}
		ajaxreturn(0,$isok_res);
	break;
	case 'feedback':
		$fromurl=$_SERVER["HTTP_REFERER"];
		if(!strstr($fromurl,setup_weburl))ajaxreturn(3,'未知的URL来源，请刷新重新提交或联系工作人员');
		$lang=strtolower(arg('lang','post','url'));
		$name=arg('name','post','txt');
		$email=arg('email','post','url');
		$phone=arg('phone','post','url');
		$content=arg('content','post','txt');
		$attachment=arg('attachment','post','url');
		checkstr($name,'none',1,50,'您的称呼');
		if($email!='')checkstr($email,'email',1,200,'电子邮箱');
		if($phone!='')checkstr($phone,'none',1,20,'联系电话');
		checkstr($content,'none',1,1000,'留言内容');
		if(setup_feedback_cn){
			if(!preg_match("/[\x7f-\xff]/",$content)){
				$errtip=getlang('send','notcn');
				ajaxreturn(1,$errtip);
				}
			}
		if(setup_errkey!=''){
			if(!checkerrkey($content)){
				$errtip=getlang('send','errkey');
				ajaxreturn(1,$errtip);
				}
			}
		$user_ip=getip();
		if(setup_feedback_limit){
			$ltime=time()-setup_feedback_limit;
			$ftime=db_count('feedback','webid='.$web['id'].' and time_add>'.$ltime.' and user_ip="'.$user_ip.'"');
			if($ftime){
				$errtip=sprintf(getlang('comment','limiterr'),setup_feedback_limit);
				ajaxreturn(1,$errtip);
				}
			}
		require_once('include/class_ip.php');
		$ipdata=new IpLocation();
		$add=$ipdata->getlocation($user_ip);
		$user_iptext=$add['country'];
		$attachment_new='';
		if($attachment!=''){
			$attachment_new='feedback/'.$attachment;
			@rename(setup_upfolder.$web['id'].'/'.setup_uptemp.$attachment,setup_upfolder.$web['id'].'/'.$attachment_new);
			}
		$content1=nl2br($content);
		$content2=str_replace('<br>','{br}',$content1);
		$tab='webid,dataid,languages,name,email,phone,user_ip,user_iptext,content,attachment,time_add';
		$val=$web['id'].',0,"'.$lang.'","'.$name.'","'.$email.'","'.$phone.'","'.$user_ip.'","'.$user_iptext.'","'.$content2.'","'.$attachment_new.'",'.time();
		db_intoshow('feedback',$tab,$val);
		if(setup_smtp_email!=''){	//发送到邮箱
			include('include/class_smtp.php');
			$mailbody='姓名称呼：'.$name.'<br>邮箱地址：'.$email.'<br>联系电话：'.$phone.'<br>留言时间：'.date('Y-m-d H:i:s').'<br>==== 留言内容 ====<br>'.$content1;
			sendmail(setup_smtp_email,'网站留言（'.tipshort($name,50).'）-'.setup_shortname,$mailbody,'网站留言');
			}
		ajaxreturn(0);
	break;
	case 'calendar':
		include('class_calendar.php');
		$ym=arg('ym','post','url');
		$did=arg('did','post','url');
		$mark=arg('mark','post','txt');
		if($mark=='')ajaxreturn(1);
		$tname='article';
		$mod=db_getshow('module','modtype,classid','webid='.$web['id'].' and bcat=0 and mark="'.$mark.'"');
		if(!$mod)ajaxreturn(1);
		if($mod['modtype']>10)$tname.='_'.$web['id'].'_'.$mod['modtype'];
		$yy=date('Y');
		$mm=date('m');
		if($ym!=''){
			if(!preg_match('/^([0-9]+)-([0-9]+)$/',$ym))ajaxreturn(1);
			$ym=explode('-',$ym);
			$yy=$ym[0];
			$mm=$ym[1];
			if($mm<1){$mm=12;$yy--;}
			if($mm>12){$mm=1;$yy++;}
			}
		$yy=(int)$yy;$mm=(int)$mm;
		$ym2=$yy.'-'.($mm+1);
		$ym3=$yy.'-'.($mm-1);
		$dd=date('d');
		$dd_all=date('t',strtotime($yy.'-'.$mm.'-1'));
		$carr=array();
		for($i=1;$i<=$dd_all;$i++){
			$t1=strtotime($yy.'-'.$mm.'-'.$i.' 00:00:00');
			$t2=strtotime($yy.'-'.$mm.'-'.$i.' 23:59:59');
			if($t1>time())$carr[$i]=0;
			else{
				$cnum=db_count($tname,'webid='.$web['id'].' and bcat='.$mod['classid'].' and isok=0 and time_add>='.$t1.' and time_add<='.$t2);
				$carr[$i]=$cnum;
				}
			}
		$calc=new calendar($yy,$mm,$carr,'log='.$mark.'&seartype=date&word=');
		$res='<div class="cale_tit"><span>'.sprintf(getlang('time','ym'),$yy,goif($mm<10,'0').$mm).'</span><a href="javascript:" onclick="PZ.calendar({mark:\''.$mark.'\',id:\''.$did.'\',ym:\''.$ym2.'\'})">→</a><a href="javascript:" onclick="PZ.calendar({mark:\''.$mark.'\',id:\''.$did.'\',ym:\''.$ym3.'\'})">←</a></div>'.$calc->showCalendar();
		ajaxreturn(0,$res);
	break;
	case 'expmood':
		$other='';
		$xu=arg('xu','post','int');
		$tcz['id']=arg('id','post','int');
		$bcat=arg('bcat','post','int');
		$tname='article';
		$modtype=db_getone('module','modtype','webid='.$web['id'].' and classid='.$bcat);
		if($modtype>10)$tname.='_'.$web['id'].'_'.$modtype;
		if($xu){
			if($xu<1||$xu>5)ajaxreturn(1,'未知的参数(1)');
			$user_ip=ip2long(getip());
			$tt=strtotime(date('Y-m-d 00:00:01'));
			if(setup_mood_limit){
				$islimit=db_count('limit','webid='.$web['id'].' and types=1 and bcat='.$bcat.' and dataid='.$tcz['id'].' and time_add>='.$tt.' and user_ip='.$user_ip);
				if($islimit)ajaxreturn(1,getlang('mood','islimit'));
				}
			db_upshow($tname,'mood_'.$xu.'=mood_'.$xu.'+1','dataid='.$tcz['id']);
			$larr=array(
				'webid'=>$web['id'],
				'types'=>1,
				'bcat'=>$bcat,
				'dataid'=>$tcz['id'],
				'user_ip'=>$user_ip,
				'time_add'=>time()
				);
			$isgq=db_getshow('limit','id','types=1 and time_add<'.$tt);
			if($isgq)db_uparr('limit',$larr,'id='.$isgq['id']);
			else db_intoarr('limit',$larr);
			}
		$show=db_getshow($tname,'id,dataid,mood_1,mood_2,mood_3,mood_4,mood_5','dataid='.$tcz['id']);
		if(!$show)ajaxreturn(1,'不存在的记录');
		$mood_all=$show['mood_1']+$show['mood_2']+$show['mood_3']+$show['mood_4']+$show['mood_5'];
		$show['mood_1_scale']=0;$show['mood_2_scale']=0;$show['mood_3_scale']=0;$show['mood_4_scale']=0;$show['mood_5_scale']=0;
		if($mood_all){
			$show['mood_1_scale']=round($show['mood_1']/$mood_all*100);
			$show['mood_2_scale']=round($show['mood_2']/$mood_all*100);
			$show['mood_3_scale']=round($show['mood_3']/$mood_all*100);
			$show['mood_4_scale']=round($show['mood_4']/$mood_all*100);
			$show['mood_5_scale']=100-$show['mood_1_scale']-$show['mood_2_scale']-$show['mood_3_scale']-$show['mood_4_scale'];
			}
		$res='[{"num":'.$show['mood_1'].',"scale":"'.$show['mood_1_scale'].'%"},{"num":'.$show['mood_2'].',"scale":"'.$show['mood_2_scale'].'%"},{"num":'.$show['mood_3'].',"scale":"'.$show['mood_3_scale'].'%"},{"num":'.$show['mood_4'].',"scale":"'.$show['mood_4_scale'].'%"},{"num":'.$show['mood_5'].',"scale":"'.$show['mood_5_scale'].'%"}]';
		ajaxreturn(0,$res,$other);
	break;
	default:
		ajaxreturn(99,'未知参数，请刷新页面重试！');
	break;
	}
?>