<?php
$data=db_getshow('tool_email','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$data)ajaxreturn(1,'群发任务不存在');
if($data['addressid']){
	$email=db_getshow('tool_email_address','*','webid='.$website['webid'].' and id='.$data['addressid']);
}else{
	$email=db_getshow('tool_email_address','*','webid='.$website['webid'].' and isok=1 order by rand() limit 1');
	}
if(!$email)ajaxreturn(1,'未找到合适的发件箱');
$allemail_arr=explode(',',$data['emailto']);
$allemail=count($allemail_arr);
$allpage=$allemail;
$start=$tcz['page']-1;
$end=$start+1;
if($data['sendtype']==2){ //组发模式
	$start=$tcz['page']*$data['sendnum']-$data['sendnum'];
	$end=$start+$data['sendnum'];
	$allpage=ceil($allemail/$data['sendnum']);
	}
if($end>$allemail)$end=$allemail;
if($tcz['desc']=='start'){
	db_upshow('tool_email','results=results+1,time_send='.time(),'id='.$data['id']);
	$res='<div class="ui_sendemail">
<div class="load">
<div class="nums"><span id="sendemail_load">0%</span></div>
<div class="bg bg1"><div></div></div>
<div class="bg bg2" style="height:0px" id="sendemail_loadbg"><div></div></div>
</div>
<div class="rtext">
<span class="etitle">'.$data['title'].'</span>
<br>本次发出：<span id="sendemail_emailme">尚未发送</span>
<br>发送状态：<span id="sendemail_page">0</span> / '.$allpage.'，错误 <span id="sendemail_error">0</span> 次
<br>下次执行：<span id="sendemail_timesup"></span>
</div>
</div>';
	ajaxreturn(0,$res,5);
}else{
	$mailbody=getreset_admin($data['content']);
	$html='<table border="0" cellspacing="0" cellpadding="1" align="center" style="width:820px;table-layout:fixed;border:1px solid #ddd;font-family:Microsoft YaHei,Tahoma,STHeiti,Arial,sans-serif;box-shadow:0 0 20px #777;">
<tr><td style="text-indent:20px;color:#ffffff;font-weight:bold;font-size:28px;height:60px;background:#319400;overflow:hidden">'.$data['title'].'</td></tr>
<tr><td style="white-space:normal !important;padding:20px;font-size:16px;word-wrap:break-word !important;line-height:200%;background:#ffffff">'.$mailbody.'</td></tr>
</table>';
	preg_match_all("/[^\x80-\xff]{2}|[^\x80-\xff]|[\x80-\xff]{3}/",$html,$arr);
	$html2='';
	$randnum_arr=array(0=>'',1=>array(0=>1,1=>5),2=>array(0=>3,1=>8),3=>array(0=>5,1=>15));
	$randnum=$randnum_arr[$data['randnum']];
	foreach($arr[0] as $t){
		$ilen=rand($randnum[0],$randnum[1]);
		$ilen_1=floor($ilen/2);
		$ilen_2=ceil($ilen/2);
		$html2.=preg_replace("/([\x7f-\xff]+)/",infocntext($ilen_1)."$1".infocntext($ilen_2),$t);
		}
	$emailto='';
	for($i=$start;$i<$end;$i++){
		$em=$allemail_arr[$i];
		if(preg_match('/^[a-zA-Z0-9_.]+@([a-zA-Z0-9_]+.)+[a-zA-Z]{2,3}$/',$em)){
			$emailto.=goif($emailto!='',',').$em;
			//$list.='-'.$data['content'];
			//$data['content']='这是一封测试邮件';
			//sendmail_admin($email['sendtype'],$email['server'],$email['port'],$email['code'],$email['pass'],$email['email'],$emailto,$data['title'],$data['content'],$mailtip='');
			}
		}
	//事件{fg}发件人{fg}收件人{fg}邮件服务器{fg}用户名{fg}密码{fg}邮件标题
	$other='ok{fg}'.$email['email'].'{fg}'.$emailto.'{fg}'.$email['server'].'{fg}'.$email['code'].'{fg}'.$email['pass'].'{fg}'.$data['title'];
	$alljz=$tcz['page']/$allpage*100;
	if($alljz>99&&$alljz<100)$alljz=99;
	$alljz=ceil($alljz);
	//百分比|第几页|共几页|当前发送邮箱
	$seconds=$data['seconds'];
	if($seconds==9999)$seconds=rand(5,30);
	else if($seconds==9998)$seconds=rand(60,300);
	$res=$seconds.'|'.$alljz.'|'.$tcz['page'].'|'.$allpage.'|'.$email['email'];
	ajaxreturn(0,$res,$other,$html2);
	}