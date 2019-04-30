<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>青云客网站管理系统 - Powered by QYKCMS 4.0</title><META HTTP-EQUIV="Pragma" CONTENT="no-cache"><META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"><META HTTP-EQUIV="Expires" CONTENT="0"><script type="text/javascript" src="../js/min.js"></script>
<script type="text/javascript" src="../js/lang/cn.js"></script>
<script type="text/javascript" src="../js/tczAppsui.js"></script>
<link href="../images/style.css" rel="stylesheet" type="text/css" />
<link href="images/global.css?t=<?php echo time();?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var tczAppsui={path:"/"};
function gostart(){
if($(".item1>.text>.no").length>0){
	PZ.e({ico:"error",msg:"检测到有不太符合系统要求的项目，您确定要忽略提示继续安装吗？",btn:[
		{text:"继续安装",css:"out2",close:"ok",callback:function(){
			location.href="index.php?setup=3";
			}},
		{text:"重新检测",close:"ok",callback:function(){
			location.reload();
			}}
		]});
}else{
	location.href="index.php?setup=3";
	};
};
function getnewver(){
$.ajax({
	type:"POST",
	url:"index.php?log=getnewver",
	cache:false,
	dataType:"json",
	success:function(res){
		//alert(res);return;
		switch(res.status){
			case 0:
				$("#qyk_newver").html(res.data).attr(res.other);
			break;
			default:
				$("#qyk_newver").html("无法获取");
			break;
			};
		}
	});
};
function install(){
var domain=$("#post_domain").val();
var dbhost=$("#post_dbhost").val();
var dbname=$("#post_dbname").val();
var dbuser=$("#post_dbuser").val();
var dbpass=$("#post_dbpass").val();
var prefix=$("#post_prefix").val();
var clean="no";
var theme="no";
var user=$("#post_user").val();
var pass=$("#post_pass").val();
if($("#post_clean").is(":checked"))clean="ok";
if($("#post_theme").is(":checked"))theme="ok";
if(domain==""){PZ.fly({log:"addone",obj:$("#post_domain"),msg:"站点域名不能为空"});return;};
if(dbhost==""){$("#post_dbhost").val("localhost");PZ.tip({log:"addone",obj:$("#post_dbhost"),msg:"MySql数据库服务器不能为空"});return;};
if(dbname==""){PZ.tip({log:"addone",obj:$("#post_dbname"),msg:"MySql数据库名称不能为空"});return;};
if(dbuser==""){PZ.tip({log:"addone",obj:$("#post_dbuser"),msg:"数据库用户名不能为空"});return;};
if(prefix==""){$("#post_prefix").val("qyk_");PZ.tip({log:"addone",obj:$("#post_prefix"),msg:"表名前缀不能为空"});return;};
if(user==""){PZ.tip({log:"addone",obj:$("#post_user"),msg:"管理员账号不能为空"});return;};
if(pass==""){PZ.tip({log:"addone",obj:$("#post_pass"),msg:"管理员密码不能为空"});return;};
var data="domain="+PZ.en(domain);
data+="&dbhost="+PZ.en(dbhost);
data+="&dbname="+PZ.en(dbname);
data+="&dbuser="+PZ.en(dbuser);
data+="&dbpass="+PZ.en(dbpass);
data+="&prefix="+PZ.en(prefix);
data+="&user="+PZ.en(user);
data+="&pass="+PZ.en(pass);
data+="&theme="+theme;
data+="&clean="+clean;
var gofunc=function(){
	PZ.load({log:"open",msg:"正在安装，请耐心等待..."});
	$.ajax({
	type:"POST",
	url:"index.php?log=install",
	data:data,
	cache:false,
	dataType:"json",
	success:function(res){
		//PZ.load({log:"close"});alert(res);return;
		switch(res.status){
			case 0:
				$("[tag='install_start']").fadeOut(200,function(){
					PZ.load({log:"close"});
					$("[tag='install_start']").remove();
					$("[tag='install_success']").fadeIn(200,function(){
						$("#admin_user").html(user);
						$("#admin_pass").html(pass);
						});
					});
			break;
			default:
				PZ.load({log:"close"});
				PZ.e({ico:"error",msg:res.data});
			break;
			};
		}
	});
	};
$("html,body").scrollTop(0);
gofunc();
};
/*---------------出错提示---------------*/
window.onerror=function(sMessage,sUrl,sLine){
return true;
var str="页面脚本运行出错，请截图联系工作人员，谢谢您的合作！\n\n";
str+="信息："+sMessage+"\n\n";
str+="地址："+sUrl+"\n\n";
str+="行数："+sLine;
alert(str);
return true;
};
</script>
</head><body>
<div class="ui_win">
	<div class="header">
		<a class="logo" href="http://cms.qingyunke.com" target=_blank><img src="images/logo.gif"></a>
		<div class="link"><a href="http://cms.qingyunke.com" target=_blank>QYKCMS官网</a><a href="http://blog.qingyunke.com/" target=_blank>官方博客</a><a href="http://bbs.qingyunke.com/" target=_blank>技术论坛</a><a href="http://www.qingyunke.com" target=_blank>青云客软件</a></div>
	</div>
	<div class="bodyout">
	<noscript><div class="noscript">禁用Javascript脚本运行，将会导致QYKCMS无法正常安装，请先设置浏览器允许运行Javascript脚本</div></noscript>