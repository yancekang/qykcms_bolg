var tczAppsui={};
var ready_callback=[];
var PZ=new function(){
this.ie=function(){var sVer=navigator.userAgent;if(sVer.indexOf("MSIE")==-1){return false}else{return true}};
this.s=function(obj,h){obj.scrollTop=h};
this.c=function(obj,n){obj.className=n};
this.d=function(did,t){if(!$(did))return;$(did).style.display=t};
this.v=function(did,en){if(!$(did))alert("不存在的输入框ID："+did);var v=$(did).value;if(en)v=PZ.en(v);return v};
/*---------------ajax post前转码---------------*/
this.en=function(str){
	if(str=="")return str;
	str=str.replace(/\+/g,"%2B");
	str=encodeURIComponent(str);	//escape(str);
	return str;
	};
/*---------------删除左右两端的空格 包括中文空格及英文空格---------------*/
this.trim=function(str){
	str=str.replace(/(^\s*)|(\s*$)/g, "");
	str=str.replace(/(^[\　]*)/g, "");
	str=str.replace(/([\　]*$)/g, "");
	return str;
	};
/*---------------小数---------------*/
this.xs=function(n,len){
	return n;
	};
/*---------------强制转为数字---------------*/
this.n=function(n){
	if(n=="")return 0;
	n=parseInt(n);
	//n=n.replace(/([^0-9]+)/g,"");
	return n;
	};
/*---------------指定长度随机数字---------------*/
this.n1=function(n){
	var sn=parseInt(Math.random().toString().slice(-n));
	if(sn.length!=n)sn+=1;
	return sn;
	};
/*---------------用于缓冲数字---------------*/
this.n2=function(n){
	if(n>0)n=Math.ceil(n);if(n<0)n=Math.floor(n);
	return n;
	};
/*---------------指定范围随机数字---------------*/
this.n3=function(n1,n2){return parseInt(Math.random()*(n2-n1+1)+n1)};
//保留多少位小数
this.formatFloat=function(src,pos){
	return Math.round(src*Math.pow(10,pos))/Math.pow(10,pos);
	};
/*---------------获取字符串长度 字节---------------*/
this.getlen=function(str){
	if(str=="")return 0;
	var len=0;
	for(var i=0;i<str.length;i++){
		if(str.charCodeAt(i)>127){len+=2}else{len++};
		};
	return len;
	};
/*---------------清除HTML标签---------------*/
this.clear=function(str){return str.replace(/<.*?>/g,"")};
/*---------------验证数据合法性---------------*/
this.regular=function(str,rtype){
	var sReg="",errs="";
	if(str=="")return "";
	switch(rtype){
		case "enint":sReg=/([a-zA-Z0-9]+)+$/;errs="字母或数字";break;
		case "txt2":str=str.replace("\r\n","");sReg=/([^'\"]+)+$/;errs="不能有单引号,双引号";break;
		case "txt":sReg=/([0-9\a-z\A-Z\u4E00-\u9FA5]+)+$/;errs="中文,数字或字母";break;
		case "int":sReg=/([0-9]+)+$/;errs="正整数";break;
		case "num":sReg=/([0-9\.]+)+$/;errs="整数或小数";break;
		case "allnum":sReg=/([0-9\.\-]+)+$/;errs="整数、小数、负数";break;
		case "en":sReg=/([a-zA-Z]+)+$/;errs="大小写字母";break;
		case "cn":sReg=/([\u4E00-\u9FA5]+)+$/;errs="中文汉字";break;
		case "email":sReg=/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/;errs="正确的邮箱帐号";break;
		case "date":sReg=/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;errs="日期，如 2011-10-08";break;
		case "time":sReg=/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})( |\/)(\d{1,2})(:|\/)(\d{1,2})(:|\/)(\d{1,2})$/;errs="时间，如 1990-01-01 14:31:20";break;
		case "other":return "";break;
		};
	if(str.replace(sReg,"")!="")return "<span class=\"red\">格式错误，请符合以下格式：</span><br>"+errs;
	return "";
	};
/*---------------loading---------------*/
this.load=function(args){
	var log=args.log || "open";
	var ico=args.ico || "ok";
	var msg=args.msg || "正在载入，请稍候...";
	switch(log){
		case "open":
			if($("#tcz_loading").length>0){
				$("#tcz_loading_tip").html(msg);
				$("#tcz_loading_text").show();
				$("#tcz_loading").show();
				if(ico=="no")$("#tcz_loading_ico").hide();
				else $("#tcz_loading_ico").show();
				var obj=$("#tcz_loading");
			}else{
				var html="<div class='ui_loading' id='tcz_loading'><div class=\"bg\"></div><div class=\"text\" id=\"tcz_loading_text\"><div class=\"sidel\"></div><div class=\"cen\">";
				if(ico=="ok")html+="<img src=\""+tczAppsui.path+"images/b.gif\" id=\"tcz_loading_ico\">";
				html+="<span id=\"tcz_loading_tip\">"+msg+"</span></div><div class=\"sider\"></div></div></div>";
				var obj=$(html);
				$(document.body).append(obj);
				};
			var l=($(window).width()-$("#tcz_loading_text").width())/2+$(document).scrollLeft();
			var t=($(window).height()-$("#tcz_loading_text").height())/2+$(document).scrollTop();
			$("#tcz_loading_text").css({left:l+"px",top:t+"px"});
			obj.css({height:$(document).height()+"px",width:$(document).width()+"px"});
		break;
		case "close":
			$("#tcz_loading_text").fadeOut(500,function(){
				$("#tcz_loading").hide();
				});
		break;
		};
	};
/*---------------倒计时---------------*/
this.timeup=function(args){
	var endtime=args.endtime;
	var diff=args.diff;
	//endtime=new Date(endtime).getTime();	//取结束日期(毫秒值)
	var nowtime=new Date().getTime()/1000+diff;		//今天的日期(秒值)
	var seconds=Math.floor(endtime-nowtime);			//还有几秒
	var minutes=Math.floor(seconds/60);
	var hours=Math.floor(minutes/60);
	var days=Math.floor(hours/24);
	hours=hours%24;
	minutes=minutes%60;
	seconds=Math.floor(seconds%60);		//"%"是取余运算，可以理解为60进一后取余数，然后只要余数。
	var res={status:0,days:days,hours:hours,minutes:minutes,seconds:seconds};
	if(endtime<=nowtime)res.status=1;
	return res;
	};
/*---------------数据输入框---------------*/
this.infonum=function(args){
	var log=args.log || "info";	//add,red,info 增加,减少,输入
	var inpid=args.inpid;
	var min=args.min || 0;
	var max=args.max || 99999999;
	var unit=args.unit || 1;	//增减单位
	var n=$("#"+inpid).val();
	switch(log){
		case "info":if(n=="")return;n=PZ.n(n);break;
		case "add":n=PZ.n(n);n+=unit;break;
		case "red":n=PZ.n(n);n-=unit;break;
		};
	if(n<min)n=min;else if(n>max)n=max;
	$("#"+inpid).val(n);
	if(args.callback)args.callback();
	};
/*---------------飞行提示信息---------------*/
this.fly=function(args){
	var ico=args.ico || "alert";	//none,alert,error,success
	var delay=args.delay || 1000;	//多少毫秒后关闭
	var id=args.id || "fly_"+PZ.n1(8);
	var html="<div id='"+id+"' class='ui_fly'><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"td1\">";
	if(ico!="none")html+="<td class='td2'><img src='"+tczAppsui.path+"images/b.gif' class='"+ico+"'></td>";
	html+="<td class=\"td3\">"+args.msg+"</td>";
	html+="<td class=\"td4\"></td></tr></table></div>";
	var objdiv=$(html);
	$(document.body).append(objdiv);
	var w=$("#"+id).innerWidth();
	var h=$("#"+id).innerHeight();
	var y=$(document).scrollTop()+($(window).height()-h)/2;
	var x=($(window).width()-w)/2;
	$("#"+id).css({width:w+"px",height:h+"px",left:x+"px",top:y+"px"});
	var func=function(){
		if(args.link)$("#"+id).click(function(){
			location.href=args.link;
			});
		window.setTimeout(function(){
			$("#"+id).animate({top:(y-100)+"px",opacity:"toggle"},300,function(){
				$("#"+id).remove();
				if(args.callback)args.callback();
				});
			},delay);
		};
	$("#"+id).hide();
	$("#"+id).fadeIn(400,func);
	return id;
	};
/*---------------模拟警告框---------------*/
this.e=function(args){
	var ico=args.ico || "alert";	//none,alert,error,success
	var title=args.title || qyklang.win.title;
	var close=args.close || "ok";
	var btn=args.btn || [{text:qyklang.win.btn_ok,css:"out2",close:"ok"}];
	var html="<table border=\"0\" cellspacing=\"15\" cellpadding=\"0\" class='win_info_alert'>";
	if(ico!="none")html+="<td class=\"ico\" valign=\"top\"><img src='"+tczAppsui.path+"images/b.gif' class='"+ico+"'></td>";
	html+="<td class=\"text\">"+args.msg+"</table></td>";
	var id=PZ.win({title:title,close:close,type:"html",content:html,btn:btn});
	};
/*---------------模拟窗口---------------*/
this.win=function(args){
	var log=args.log || "open";		//操作方式
	var id=args.id || "win_"+PZ.n1(8);
	var title=args.title || "";
	var content=args.content || "";
	var type=args.type || "html";
	var close=args.close || "ok";	//是否有关闭按钮
	var shadow=args.shadow || 1;	//阴影样式
	var animate=args.animate || 1;	//动画样式 0不启用 1-3
	//var callback=args.callback;
	var w=args.w || 0;		//默认窗口宽 0为自动 iframe载入方式必须指定
	var h=args.h || 0;		//默认窗口高 0为自动
	switch(log){
		case "change":	//改变大小及位置
			var x=args.x || 0;
			var y=args.y || 0;
			$("#"+id+"_info").css({width:"auto",height:"auto"});
			if(w==0){w=$("#"+id+"_info").width()+20};
			if(h==0){h=$("#"+id+"_info").height()+20};
			if(w<180)w=180;if(h<80)h=80;
			var x=($(window).width()-w)/2;
			var y=($(window).height()-h)/2;
			if(x<0)x=0;if(y<0)y=0;
			$("#"+id+"_info").css({width:(w-20)+"px",height:(h-20)+"px"});
			if(animate==99){
				$("#"+id).css({left:x+"px",top:y+"px",width:w+"px",height:h+"px"});
				if(args.callback)args.callback();
			}else{
				$("#"+id+"_close").hide();
				$("#"+id).animate({left:x+"px",top:y+"px",width:w+"px",height:h+"px"},200,function(){
					if(close=="ok")$("#"+id+"_close").show();
					if(args.callback)args.callback();
					});
				};
		break;
		case "close":
			var func=function(){
				$("#"+id).remove();
				if($(".win_block").length==0)$("#tcz_window").remove();
				else{
					var cc=PZ.n($("#tcz_window>div:last").css("z-index"));
					$("#tcz_window_bg").css({zIndex:cc});
					};
				if(args.callback)args.callback();
				};
			$("#"+id+"_close").hide();
			switch(animate){
				case 0:
					func();
				break;
				case 1:	//淡出
					$("#"+id+"_info").fadeOut(200,func);
				break;
				case 2:	//从下到上擦除
					$("#"+id).slideUp(200,func);
				break;
				case 3:	//从大变小
					var t=($(window).height()-2)/2;
					var l=($(window).width()-2)/2;
					$("#"+id).animate({top:t+"px",left:l+"px",width:"2px",height:"2px"},200,func);
				break;
				};
		break;
		};
	if($("#"+id).length>0)return;//如果相同ID的窗口存在则不再创建
	if($("#tcz_window").length==0){
		var objdiv=$("<div class='ui_window' id='tcz_window' style='height:"+$(document).height()+"px'></div>");
		$(document.body).append(objdiv);
		};
	var cc=1;
	if($("#tcz_window>div:last").length>0)cc=PZ.n($("#tcz_window>div:last").css("z-index"))+1;
	if($("#tcz_window_bg").length==0){
		var objdiv2=$("<div class='win_bg' style='z-index:"+cc+"' id='tcz_window_bg'></div>");
		$("#tcz_window").append(objdiv2);
	}else{
		$("#tcz_window_bg").css({zIndex:cc});
		};
	var html="<div class='win_block' id='"+id+"' style='z-index:"+cc+"'>";
	if(close=="ok")html+="<div class='win_close' id='"+id+"_close' onmouseover='this.scrollTop=34' onmousedown='this.scrollTop=68' onmouseout='this.scrollTop=0' onclick=\"this.scrollTop=34;PZ.win({log:'close',id:'"+id+"',animate:"+animate+"})\"><span></span></div>";
	html+="<div class='win_shadow"+shadow+"' id='"+id+"_shadow'></div><div class='win_info' id='"+id+"_info'>";
	if(title!="")html+="<div class='win_info_title'>"+title+"</div>";
	html+=content+"</div>";
	var objdiv3=$(html);
	$("#tcz_window").append(objdiv3);
	if(args.btn){
		btnhtml="<div class='win_info_btn' id='"+id+"_btn'></div>";
		$("#"+id+"_info").append(btnhtml);
		for(var i=0;i<args.btn.length;i++){
			(function(){
				var btn=args.btn[i];
				var css=btn.css || "";
				if(css==""){
					if(args.btn.length==1)css="out2";else css="out1";
					};
				var btnclose=btn.close || "ok";
				//btnhtml="<span id=\""+id+"_btn_"+i+"\" class=\""+css+"\" onmouseover=\"this.className='on'\" onmousedown=\"this.className='down'\" onmouseout=\"this.className='"+css+"'\">"+btn.text+"</span>";
				btnhtml="<input type=\"button\" id=\""+id+"_btn_"+i+"\" class=\""+css+"\" value=\""+btn.text+"\">";
				$("#"+id+"_btn").append(btnhtml);
				$("#"+id+"_btn_"+i).click(function(){
					 if(btn.close=="ok")PZ.win({log:"close",id:id});
					if(btn.callback)btn.callback();
					});
				})();
			};
		};
	if(w==0){w=$("#"+id+"_info").outerWidth()+20};
	if(h==0){h=$("#"+id+"_info").height()+20};
	if(w<180)w=180;if(h<80)h=80;
	//alert(w);
	var l=($(window).width()-w)/2;
	var t=($(window).height()-h)/2;
	if(l<0)l=0;if(t<0)t=0;
	$("#"+id+"_info").css({width:(w-20)+"px",height:(h-20)+"px"});
	if(animate!=3)$("#"+id).css({left:l+"px",top:t+"px",width:w+"px",height:h+"px"});
	var func=function(){
		if(args.callback)args.callback();
		};
	switch(animate){
		case 0:
			func();
		break;
		case 1:	//淡入
			$("#"+id+"_info").hide();
			$("#"+id+"_info").fadeIn(200,func);
		break;
		case 2:	//从上到下擦除
			$("#"+id).hide();
			$("#"+id).slideDown(200,func);
		break;
		case 3:	//由小变大
			//$("#"+id).hide();
			var t2=($(window).height()-2)/2;
			var l2=($(window).width()-2)/2;
			$("#"+id).css({left:l2+"px",top:t2+"px",width:"2px",height:"2px"});
			$("#"+id).animate({top:t+"px",left:l+"px",width:w+"px",height:h+"px"},200,func);
		break;
		};
	return id;
	};
/*---------------选项卡---------------*/
this.cata=function(args){
	var did=args.did;
	var btn=$("#tcz_catalist > .catalist > .left > a");
	var con=$("#tcz_catalist > .catadesc");
	btn.click(function(){
		var xu=$(this).index();
		btn.each(function(){
			if($(this).index()==xu)$(this).removeClass().addClass("on");
			else $(this).removeClass().addClass("out");
			});
		con.each(function(){
			if($(this).index()==xu+1){
				$(this).css({display:""});
				if(args.callback)args.callback($(this),xu);
			}else $(this).css({display:"none"});
			});
		});
	};
/*---------------提示信息---------------*/
this.tip=function(args){
	var msg=args.msg;
	var obj=args.obj;
	var delay=args.delay || 5000;
	var log=args.log || "open";
	var id=args.id || "tip_"+PZ.n1(8);
	switch(log){
		case "open":
			var upclose=args.upclose || "no";
			if($("#"+id).length>0)$("#"+id).remove();
			var tip=$("<div class='ui_tip' id='"+id+"'><div class='arrow'></div>"+msg+"</div>");
			$(document.body).append(tip);
			var w=tip.width();
			var h=tip.innerHeight();
			if(w<80)w=80;if(h<20)h=20;
			var pos=obj.offset();
			var x=pos.left;
			var y=pos.top-h-9;
			var st=$(document).scrollTop();
			if(x<0)x=0;if(y<0)y=0;
			if(st>y)window.scrollTo(0,y);
			tip.css({left:x+"px",top:y+"px",width:w+"px",display:"none"});
			var closefunc=function(){
				if(tip.length>0){
					if(upclose=="ok")$(document.body).off("click",closefunc);
					tip.fadeOut(200,function(){
						if(tip.length>0)tip.remove();
						});
					};
				};
			tip.fadeIn(200,function(){
				if(delay>0)window.setTimeout(closefunc,delay);
				if(upclose=="ok")$(document.body).live("click",closefunc);
				});
			//return id;
		break;
		case "close":
			$("#"+id).fadeOut(200,function(){$("#"+id).remove()});
		break;
		case "addover":
			obj.bind("mouseover",function(){
				PZ.tip({log:"open",obj:obj,id:id,msg:msg,delay:10000});
				});
			obj.bind("mouseleave",function(){
				PZ.tip({log:"close",id:id});
				});
		break;
		case "addfocus":
			obj.bind("focus",function(){
				PZ.tip({log:"open",obj:obj,id:id,msg:msg,delay:30000});
				});
			obj.bind("blur",function(){
				PZ.tip({log:"close",id:id});
				});
		break;
		case "addone":
			var focus=args.focus || obj;
			focus.one("blur",function(){
				PZ.tip({log:"close",id:id});
				});
			PZ.tip({log:"open",obj:obj,id:id,msg:msg,delay:30000});
			focus.focus();
		break;
		};
	};
/*---------------密码输入框---------------*/
this.passinp=function(args){
	var id=args.id;
	var css=$("#"+id).attr("class");
	$("#"+id).val("请输入密码").attr('readonly',true).css({color:"#999",background:"#e8e8e8"});
	var func=function(){
		if($("#"+id).length==0)return;
		if($("#"+id).attr("type")!="text")return;
		$("#"+id).unbind().replaceWith("<input type='password' id='"+id+"' class='"+css+"' onfocus='this.select()'>");
		$("#"+id).focus();
		};
	$("#"+id).bind("click",func);
	window.setTimeout(func,3000);
	};
/*---------------模拟下拉菜单---------------*/
this.select=function(args){
	var log=args.log || "create";
	var sid=args.sid;
	//var id=args.id || "sel_"+PZ.n1(8);
	var id=sid+"_select";
	var w=args.w || 162;
	var child=args.child || [];
	var parent=args.parent || "";
	switch(log){
		case "create":
			if($("#"+sid).length==0)return;
			var def=args.def || "";
			$("#"+sid).hide();
			var text=$("#"+sid+" option:selected").text();
			if(text=="")text=def;
			var sel="<div class='ui_select' id='"+id+"' style='width:"+w+"px' dis='no'><div class='sel_out' id='"+id+"_out' style='width:"+(w-2)+"px'><div id='"+id+"_info' class='sel_info' style='width:"+(w-40)+"px'>"+text+"</div><div class='sel_arrow'></div></div></div>";
			$("#"+sid).before(sel);
			$("#"+id).mouseover(function(){
				if($("#"+id+"_out").attr("class")=="sel_out"){
					$("#"+id+"_out").removeClass().addClass("sel_on");
					};
				});
			$("#"+id).mouseout(function(){
				if($("#"+id+"_out").attr("class")=="sel_on"){
					$("#"+id+"_out").removeClass().addClass("sel_out");
					};
				});
			$("#"+id).click(function(){
				if($("#"+id+"_out").attr("class")=="sel_dis")return;
				if($("#"+id+"_opt").length==0){
					var val=$("#"+sid+" option:selected").val();
					$("#"+id+"_out").removeClass().addClass("sel_down");
					var optlink="";
					if(args.ajax){
						optlink="<div class='loadsel'><img src='"+tczAppsui.path+"images/ui/loading_s.gif'>请稍候...</div>";
					}else{
						var optlen=$("#"+sid+" option").length;
						for(var i=0;i<optlen;i++){
							var css=$("#"+sid+" option")[i].value==val?"on":"out";
							var _text=$("#"+sid+" option")[i].text;
							//if(optlen==1&&_text=="")_text=def;
							optlink+="<a class='"+css+"' href='javascript:' onmouseup=\"PZ.select({log:'check',sid:'"+sid+"',val:'"+$("#"+sid+" option")[i].value+"'})\">"+_text+"</a>";
							};
						};
					var pos=$("#"+id).offset();
					var x=pos.left;
					var y=pos.top+$("#"+id).outerHeight();
					var optdiv=$("<div class='ui_select_opt' id='"+id+"_opt' style='width:"+(w-4)+"px;left:"+x+"px;top:"+y+"px'>"+optlink+"</div>");
					$(document.body).append(optdiv);
					optdiv.hide();
					optdiv.fadeIn(200,function(){
						if(args.ajax){
							var bval="";
							if(parent!=""){
								bval=$("#"+parent).val();
								if(bval==""){
									PZ.tip({obj:$("#"+parent+"_select"),msg:"请先选择该项再选择下一级菜单",delay:1500});
									PZ.select({log:"del",sid:sid});
									return;
									};
								};
							//alert("顶层"+bval)
							$.ajax({
								type:"GET",
								url:"/?log=post&desc=select&bval="+PZ.en(bval)+"&vtype="+args.ajax,
								cache:false,
								dataType:"json",
								success:function(res){
									eval("var data="+res.data);
									optlink="";
									if(res.status==0){
										optlink+="<a class='on' href='#' onmouseup=\"PZ.select({log:'check',sid:'"+sid+"',val:'',text:'"+def+"'})\" onclick=\"return false\">暂无选项</a>";
									}else{
										for(var i=0;i<res.status;i++){
											var css=data[i]==val?"on":"out";
											optlink+="<a class='"+css+"' href='#' onmouseup=\"PZ.select({log:'check',sid:'"+sid+"',text:'"+data[i]+"',val:'"+data[i]+"'})\" onclick=\"return false\">"+data[i]+"</a>";
											};
										};
									optdiv.html(optlink);
									PZ.select({log:"resel",sid:sid});
									}
								});
							}else PZ.select({log:"resel",sid:sid});
						$(document.body).live("mouseup", function(event){
							//alert(event.tagName)
							PZ.select({log:"del",sid:sid});
							});
						});
					//alert("add")
					};
				});
		break;
		case "resel":
			var h=$("#"+sid+"_select_opt").height();
			if(h>380)$("#"+sid+"_select_opt").css({height:"380px",overflow:"auto",overflowX:"hidden"});
		break;
		case "check":
			var val=args.val;
			//$("#"+sid).show();
			try{$("#"+sid).val(val)}catch(e){};
			var text=args.text || "";
			//alert(text)
			if(text=="")text=$("#"+sid+" option:selected").text();
			if(text!=$("#"+sid+" option:selected").text()){
				$("#"+sid).append("<option value='"+val+"' selected>"+text+"</option>");
				};
			//$("#"+sid).hide();
			$("#"+id+"_info").html(text);
			PZ.select({log:"del",sid:sid});
			switch(sid){
				case "post_home_province":
					PZ.select({log:"check",val:"",text:"选择城市",sid:"post_home_city"});
				break;
				case "post_home_city":
					PZ.select({log:"check",val:"",text:"选择区域",sid:"post_home_area"});
				break;
				case "post_now_province":
					PZ.select({log:"check",val:"",text:"选择城市",sid:"post_now_city"});
				break;
				case "post_now_city":PZ.select({log:"check",val:"",text:"选择区域",sid:"post_now_area"});break;
				case "post_bank_province":PZ.select({log:"check",val:"",text:"选择城市",sid:"post_bank_city"});break;
				};
		break;
		case "disabled":
			var type=args.type;
			if(type)$("#"+id+"_out").removeClass().addClass("sel_dis");
			else $("#"+id+"_out").removeClass().addClass("sel_out");
		break;
		case "del":
			$(document.body).off("mouseup");
			$("#"+id+"_out").removeClass().addClass("sel_out");
			$("#"+id+"_opt").fadeOut(200,function(){
				$("#"+id+"_opt").remove();
				});
		break;
		};
	};
/*---------------图片浏览器---------------*/
this.photobox=function(args){
	var css=args.css || ""
	var qykphoto=args.qykphoto || "";
	var arr;
	if(css!="")arr=$("."+css);
	else if(qykphoto!="")arr=$("[qykphoto='qyk_photobox']");
	var xu=args.xu || 0;
	var len=arr.length;
	if(len==0)return;
	var func=function(myxu){
		PZ.load({log:"open"});
		var obj=$(arr[myxu-1]);
		if(obj.is("img")){
			var url=arr[myxu-1].src;
			var big=obj.attr("big");
			if(big!=""&&typeof(big)!="undefined")url=big;
			var title=arr[myxu-1].alt;
		}else{
			var url=arr[myxu-1].href;
			var title=arr[myxu-1].title;
			};
		var xu_pre=myxu-1<1?len:myxu-1;
		var xu_next=myxu+1>len?1:myxu+1;
		var mw=$(window).width()-40;
		var mh=$(window).height()-40;
		var img=new Image();
		img.onload=function(){
			PZ.load({log:"close"});
			var w=img.width;var h=img.height;
			if(w>mw){w=mw;h=w/img.width*img.height};
			if(h>mh){h=mh;w=h/img.height*img.width};
			var html="<div class=\"win_photobox\" id=\"tcz_photobox_out\" style=\"width:"+w+"px;height:"+h+"px\"><img width=\""+w+"\" height=\""+h+"\" src=\""+url+"\"><div class=\"box_foot\" id=\"tcz_photobox_foot\" style=\"display:none\"><div class=\"bg\"></div><div class=\"bg2\"><div class=\"title\">"+title+"&nbsp;</div><div class=\"xu\">"+qyklang.win.photobox+myxu+" / "+len+"</div></div></div>";
			if(len>1){
				html+="<a class=\"box_pre\" href=\"javascript:\" style=\"height:"+h+"px\" onclick=\"PZ.photobox({css:'"+css+"',qykphoto:'"+qykphoto+"',xu:"+xu_pre+"});return false\">&nbsp;</a>";
				html+="<a class=\"box_next\" href=\"javascript:\" style=\"height:"+h+"px\" onclick=\"PZ.photobox({css:'"+css+"',qykphoto:'"+qykphoto+"',xu:"+xu_next+"});return false\">&nbsp;</a>";
				};
			html+="</div>";
			var func2=function(){
				PZ.win({id:"tcz_photobox",shadow:2,animate:1,content:html,callback:function(){
					$("#tcz_photobox_foot").slideDown(200);
					$("#tcz_window_bg").one("click",function(){
						PZ.win({id:"tcz_photobox",log:"close"});
						});
					}});
				
				};
			if($("#tcz_photobox").length>0){
				$("#tcz_photobox_info").html(html);
				$("#tcz_photobox_out").hide();
				PZ.win({log:"change",id:"tcz_photobox",w:w+20,h:h+20,callback:function(){
					$("#tcz_photobox_out").fadeIn(400,function(){
						$("#tcz_photobox_foot").slideDown(200);
						});
					}});
			}else func2();
			};
		img.src=url;
		};
	if(xu>0){func(xu);return};
	arr.each(function(){
		if($(this).is("img")&&$(this).parent().is("a")){
			
		}else{
			$(this).click(function(){
				var myxu=arr.index(this)+1;
				func(myxu);
				return false;
				});
			};
		});
	};
/*---------------链接新窗打开---------------*/
this.openlink=function(obj){
	$(obj).attr("target","_blank");
	return true;
	};
/*---------------显示时间---------------*/
this.viewtime=function(args){
	var obj=args.obj;
	var func=function(){
		var d=new Date(),str="";
		str+=d.getFullYear()+"年";
		str+=d.getMonth()+1+"月";
		str+=d.getDate()+"日 ";
		str+=d.getHours()+"时";
		str+=d.getMinutes()+"分";
		str+=d.getSeconds()+"秒";
		obj.html(str);
		window.setTimeout(func,1000);
		};
	func();
	};
/*---------------获取URL---------------*/
this.getlink=function($url){
	return '/api.php?/'+$url;
	};
/*---------------弹出留言界面---------------*/
this.openfeedback=function(){
	var html="<div class=\"ui_feedback\">"+
		"<div class=\"list\"><span class=\"cname\">"+qyklang.feedback.open.name+"：</span><span class=\"inp\"><input type=\"text\" id=\"post_name\"></span><span class=\"tips\"><b class='red'>*</b></span></div>"+
		"<div class=\"list\"><span class=\"cname\">"+qyklang.feedback.open.email+"：</span><span class=\"inp\"><input type=\"text\" id=\"post_email\"></span><span class=\"tips\"><b class='red'>*</b> "+qyklang.feedback.open.email_tip+"</span></div>"+
		"<div class=\"list\"><span class=\"cname\">"+qyklang.feedback.open.tel+"：</span><span class=\"inp\"><input type=\"text\" id=\"post_phone\"></span></div>"+
		"<div class=\"list\"><span class=\"cname\">"+qyklang.feedback.open.upload+"：</span><span class=\"inp\"><input type='hidden' id='post_attachment'><a href='javascript:' class='upload' id='post_upfile_out'></a></span><span class=\"tips\"><b>*</b> "+qyklang.feedback.open.upload_tip+"</span></div>"+
		"<div class=\"list\" style=\"height:170px\"><span class=\"cname\">"+qyklang.feedback.open.content+"：</span><span class=\"inp2\"><textarea id=\"post_content\"></textarea></span></div>"+
		//"<div class=\"list\" style=\"height:170px\"><span class=\"cname\">"+qyklang.feedback.open.content+"：</span><span class=\"inp2\"><div class='infotext' contentEditable=true id='post_content'></div></span></div>"+
		"</div>";
	PZ.win({id:"win_feedback",title:qyklang.feedback.open.title,content:html,btn:[
		{text:qyklang.feedback.open.btn1,css:"out2",callback:function(){
			PZ.sendfeedback();
			}},
		{text:qyklang.feedback.open.btn2,close:"ok"}
		],callback:function(){
			$("#post_name").focus();
			var uphtml="<form action='/api.php?log=feedback_upload' id='post_feedback_form' name='post_feedback_form' encType='multipart/form-data' method='post' target='hidden_frame'><input type='hidden' name='log' value='feedback_upload'><span id='post_upfile_text'>"+qyklang.feedback.open.upload_start+"</span><input type='file' name='file' id='post_upfile'><iframe name='hidden_frame' id='hidden_frame' style='display:none'></iframe>";
			$("#post_upfile_out").html(uphtml);
			$("#post_upfile_text").live("click",function(){
				$("#post_upfile_out").html(uphtml);
				});
			$("#post_upfile").live("change",function(){
				var file=$("#post_upfile").val();
				if(file!=""){
					var args=file.split(".");
					var ftype=args[args.length-1].toLowerCase();
					if("|rar|zip|doc|".indexOf("|"+ftype+"|")==-1){
						PZ.tip({obj:$("#post_upfile_out"),msg:qyklang.feedback.open.upload_tip,upclose:"ok"});
						$("#post_upfile_out").html(uphtml);
					}else{
						$("#post_upfile_text").css({"color":"#ff0"}).html(qyklang.feedback.open.upload_cancel);
						$("#post_upfile").hide();
						};
				}else{
					$("#post_upfile_text").css({'color':""}).html(qyklang.feedback.open.upload_start);
					};
				});
			}});
	};
/*---------------提交留言---------------*/
this.sendfeedback_upload=function(args){
	var log=args.log || "success";
	switch(log){
		case "success":
			$("#post_attachment").val(args.file);
			PZ.sendfeedback();
		break;
		case "error":
			PZ.load({log:"close"});
			PZ.e({msg:args.msg});
		break;
		};
	};
this.sendfeedback=function(){
	var name=PZ.trim($("#post_name").val());
	var email=PZ.trim($("#post_email").val());
	var phone=PZ.trim($("#post_phone").val());
	var content=PZ.trim($("#post_content").val());
	if(name==""){PZ.tip({log:"addone",obj:$("#post_name"),msg:qyklang.feedback.send.name});return;};
	if(email==""&&phone==""){PZ.tip({log:"addone",obj:$("#post_phone"),msg:qyklang.feedback.send.email});return;};
	if(content==""){PZ.tip({log:"addone",obj:$("#post_content"),msg:qyklang.feedback.send.content});return;};
	var data="lang="+tczAppsui.lang+"&name="+PZ.en(name)+"&email="+PZ.en(email)+"&phone="+PZ.en(phone)+"&content="+PZ.en(content);
	PZ.load({log:"open",msg:qyklang.feedback.send.load});
	var attachment=$("#post_attachment").val();
	if($("#post_upfile").length>0){
		if($("#post_attachment").val()==""&&$("#post_upfile").val()!=""){
			$("#post_feedback_form").submit();
			return;
			};
		data+="&attachment="+PZ.en(attachment);
		};
	$.ajax({
		type:"POST",
		url:"/?log=post&desc=feedback",
		data:data,
		cache:false,
		dataType:"json",
		success:function(res){
			PZ.load({log:"close"});
			//alert(res);return;
			switch(res.status){
				case 0:
					PZ.win({log:"close",id:"win_feedback"});
					PZ.e({ico:"success",close:"no",msg:qyklang.feedback.send.success,btn:[
						{text:qyklang.feedback.send.btn,callback:function(){
							location.reload();
							}}
						]});
				break;
				default:PZ.e({ico:"error",msg:res.data});break;
				};
			}
		});
	};
/*---------------工具栏---------------*/
this.tool=function(args){
	if(!tczAppsui.tool)return;
	var top=args.top || 150;
	var html="<div class='ui_sidetool' id='tcz_sidetool'><div class='con' style='display:none'><div class='arrow'></div><div class='desc'></div></div><div class='win'>";
		if(tczAppsui.tool_customer!="")html+="<a tag='customer' href='javascript:' class='out' title='"+qyklang.tool.customer+"'><div class='bg'></div><div class='ico qq'></div></a>";
		if(tczAppsui.tool_skype!="")html+="<a tag='skype' href='javascript:' class='out' title='"+qyklang.tool.skype+"'><div class='bg'></div><div class='ico skype'></div></a>";
		if(tczAppsui.tool_phone!="")html+="<a tag='phone' href='javascript:' class='out' title='"+qyklang.tool.phone+"'><div class='bg'></div><div class='ico contact'></div></a>";
		if(tczAppsui.tool_weixin!="")html+="<a tag='weixin' href='javascript:' class='out' title='"+qyklang.tool.weixin+"'><div class='bg'></div><div class='ico weixin'></div></a>";
		if(tczAppsui.tool_feedback)html+="<a tag='feedback' href='javascript:' class='out' title='"+qyklang.tool.feedback+"'><div class='bg'></div><div class='ico feedback'></div></a>";
		html+="<a tag='close' href='javascript:' class='out' onclick=\"$('#tcz_sidetool').remove()\" title='"+qyklang.tool.close+"'><div class='bg'></div><div class='ico close'></div></a>";
		html+="</div></div>";
	$("body").append($(html));
	var st=$(document).scrollTop()+top;
	$("#tcz_sidetool").css({top:st+"px"});
	var timer;
	$("#tcz_sidetool>.win>a").click(function(){
		var tag=$(this).attr("tag");
		if(tag=="feedback"){
			PZ.openfeedback();
			return;
			};
		eval("var cont=tczAppsui.tool_"+tag);
		$('#tcz_sidetool>.con>.desc').html(cont);
		var pos=$(this).position();
		var y=pos.top;
		if($('#tcz_sidetool>.con').is(":hidden")){
			$('#tcz_sidetool>.con').css({top:y+"px"}).fadeIn(200);
		}else{
			$('#tcz_sidetool>.con').animate({top:y+"px"},100);
			};
		});
	$('#tcz_sidetool').mouseleave(function(){
		timer=window.setTimeout(function(){
			$("#tcz_sidetool>.con").fadeOut(500);
			},500)
		});
	$('#tcz_sidetool').mouseover(function(){
		window.clearTimeout(timer);
		});
	$(window).bind("scroll",function(){
		var qqfunc=function(){
			if($("#tcz_sidetool").length>0){
				var st=$(document).scrollTop()+top;
				$("#tcz_sidetool").animate({top:st});
				};
			};
		window.clearTimeout(timer);
		timer=window.setTimeout(qqfunc,100);
		});
	};
/*---------------banner---------------*/
this.banner=function(args){
	var obj=args.obj;
	var btn=args.btn || "dot";	//close表示关闭
	var pos=args.pos || "center";
	var animate=args.animate || 5;	//0随机 1无动画
	var order=args.order || 1;	//1正常 2随机
	var target=args.target || "";	//链接打开方式
	var item=obj.find("a");
	var len=item.length;
	if(len==0){
		var w=obj.width();
		if(w==$(window).width())w=1920;
		var h=obj.height();
		obj.html("<p style='padding:10px'>"+qyklang.banner.nodata+w+"x"+h+"</p>");
		return;
		};
	var delay=args.delay || 5000;
	var but="";
	var i=0;
	var list=[];
	var on=1;
	var timer;
	item.each(function(){
		var css=i==0?"on":"out";
		var text=$(this).attr("title");
		var link=$(this).attr("href");
		var img=$(this).find("img:first").attr("src");
		var other=$(this).attr("other");
		list.push({text:text,link:link,img:img,other:other});
		but+="<div xu='"+i+"' class='"+css+"' onclick=\""+(target=="new"?"window.open('"+link+"')":"location.href='"+link+"'")+"\"><span>"+(i+1)+"</span></div>";
		i++;
		});
	var html="<div class='banner'><span style=\"background-image:url('"+list[0].img+"')\"></span></div>"
		+"<div class='click' style=\"background:url('"+tczAppsui.path+"images/b.gif')\"></div>"
		+"<div class='picbtn_"+btn+"'><div class='btnscro'>"+but+"</div></div>";
	obj.html(html);
	var b1=obj.find(".btnscro>div:eq(0)");
	var ow=b1.outerWidth()+parseInt(b1.css("marginLeft"))+parseInt(b1.css("marginRight"));
	ow=ow*len;
	switch(pos){
		case "center":
			obj.find(".picbtn_"+btn).css({width:ow+"px",left:"50%",marginLeft:-(ow/2)+"px"});
		break;
		case "left":
			obj.find(".picbtn_"+btn).css({width:ow+"px",left:"10px",right:"auto"});
		break;
		case "right":
			obj.find(".picbtn_"+btn).css({left:"auto",width:ow+"px",right:"10px"});
		break;
		};
	obj.find(".click").mouseover(function(){
		window.clearTimeout(timer);
	}).mouseleave(function(){
		timer=window.setTimeout(start,delay);
	}).click(function(){
		obj.find(".btnscro>.on").trigger("click");
		});
	obj.find(".btnscro>div").mouseover(function(){
		window.clearTimeout(timer);
		var xu=PZ.n($(this).attr("xu"));
		on=xu;
		start();
		});
	var start=function(){
		if(len<=1)return;
		window.clearTimeout(timer);
		var ban=obj.find(".banner>span:eq(0)");
		obj.find(".btnscro>.on").removeClass("on").addClass("out");
		obj.find(".btnscro>div:eq("+on+")").removeClass("out").addClass("on");
		var ani=animate;
		if(animate==0)ani=PZ.n3(2,8);
		switch(ani){
			case 2: //渐显
				ban.before("<span style=\"background-image:url('"+list[on].img+"')\"></span>").fadeOut(500,function(){
					ban.remove();
					});
			break;
			case 3:	//左到右
				var aw=obj.outerWidth();
				ban.before("<span style=\"background-image:url('"+list[on].img+"');left:-"+aw+"px\"></span>");
				ban.animate({left:aw},200,function(){ban.remove()});
				obj.find(".banner>span:eq(0)").animate({left:0},300);
			break;
			case 4:	//右到左
				var aw=obj.outerWidth();
				ban.before("<span style=\"background-image:url('"+list[on].img+"');left:"+aw+"px\"></span>");
				ban.animate({left:-aw},200,function(){ban.remove()});
				obj.find(".banner>span:eq(0)").animate({left:0},300);
			break;
			case 5:	//放大消失
				ban.before("<span style=\"background-image:url('"+list[on].img+"')\"></span>");
				ban.animate({left:"-50%",top:"-50%",width:"200%",height:"200%",opacity:"toggle"},500,function(){ban.remove()});
			break;
			case 6:	//缩小消失
				ban.before("<span style=\"background-image:url('"+list[on].img+"')\"></span>");
				ban.animate({left:"50%",top:"50%",width:"0",height:"0",opacity:"toggle"},500,function(){ban.remove()});
			break;
			case 7:	//向上消失
				ban.before("<span style=\"background-image:url('"+list[on].img+"')\"></span>");
				ban.animate({top:"-50%",opacity:"toggle"},500,function(){ban.remove()});
			break;
			case 8:	//向下消失
				ban.before("<span style=\"background-image:url('"+list[on].img+"')\"></span>");
				ban.animate({top:"50%",opacity:"toggle"},500,function(){ban.remove()});
			break;
			default:
				ban.before("<span style=\"background-image:url('"+list[on].img+"')\"></span>");
				ban.remove();
			break;
			};
		
		if(order==2){
			on=PZ.n3(0,len-1);
		}else{
			on++;
			if(on>=len)on=0;
			};
		timer=window.setTimeout(start,delay);
		};
	timer=window.setTimeout(start,delay);
	};
/*---------------获取点击数---------------*/
this.getarticlehits=function(did,modtype,dataid){
	$.ajax({
		type:"POST",
		url:"/?log=post&desc=getarticlehits",
		data:"modtype="+modtype+"&dataid="+dataid,
		cache:false,
		dataType:"json",
		success:function(res){
			switch(res.status){
				case 0:
					$("#"+did).html(res.data);
				break;
				};
			}
		});
	};
/*---------------评论留言---------------*/
this.comment=function(args){
	var log=args.log || "start";
	var mod=args.mod || 1;
	var size=args.size || 1;
	switch(log){
		case "start":
			var langlist=qyklang.comment.list;
			var langbtn=qyklang.comment.send.btn;
			if(tczAppsui.argid==0)mod=2;
			var postdata="mod="+mod+"&size="+size;
			if(mod==2){
				langlist=qyklang.comment.list2;
				langbtn=qyklang.comment.send.btn2;
			}else{
				postdata+="&dataid="+tczAppsui.argid+"&bcat="+tczAppsui.argbcat;
				};
			var ht=args.ht || 30;
			var ht_on=args.ht_on || 100;
			var status=$("#tcz_comment").attr("status");
			if(status=="no"){
				var html='<div class="comm_send"><div class="comm_send_inp comm_send_inp_out" contentEditable=true id="tcz_comment_content" isedit="no" style="height:'+ht+'px">'+qyklang.comment.send.text+'</div>'
				+'<div class="comm_send_btn"><div class="comm_send_btn_left"><a class="comm_btn_face" href="javascript:" title="'+qyklang.comment.send.face+'"><img src="'+tczAppsui.path+'images/b.gif"></a></div><div class="comm_send_btn_right"><input type="button" value="'+langbtn+'" onclick="PZ.comment({log:\'send\',mod:'+mod+',size:'+size+'})"></div><div class="comm_send_btn_name"><span>'+qyklang.comment.send.name+'</span><input type="text" id="tcz_comment_name" value="'+tczAppsui.cook.uname+'" maxlength=30 onblur="if(this.value==\'\')this.value=\''+tczAppsui.cook.uname+'\'"></div></div></div>'
				+'<div class="comm_top"><div class="comm_top_left">'+langlist.title+'</div><div class="comm_top_right">'+langlist.tips1+'<span>...</span> '+langlist.tips2+'</div></div>'
				+'<ul class="comm_list" id="tcz_comment_list">...</ul>'
				+'<div class="comm_page" id="tcz_comment_page" style="display:none"></div>';
				$("#tcz_comment").attr("status","success").html(html);
				$("#tcz_comment_content").css({width:$("#tcz_comment_content").width()+"px"});
				$(".comm_btn_face").bind("click",function(){
					PZ.comment({log:"instr",cata:"face"});
					});
				$("#tcz_comment_content").bind("focus",function(){
					if($(this).attr("isedit")!="ok")$(this).attr("isedit","ok").html("");
					$(this).removeClass("comm_send_inp_out").addClass("comm_send_inp_on").animate({height:ht_on},200);
				}).bind("blur",function(){
					if($(this).html()==""){
						$(this).attr("isedit","no").html(qyklang.comment.send.text).removeClass("comm_send_inp_on").addClass("comm_send_inp_out").animate({height:ht},200);
						};
				}).keypress(function(e){
					if(window.event.keyCode==13||e.which==13){
						e.which=0;
						window.event.keyCode=0;
						PZ.comment({log:"instr",cata:"br"});
						return false;
						};
				}).keydown(function(e){
					if(e.ctrlKey&&(e.which==86||e.which==66||e.which==13)){
						switch(e.which){
							case 13:PZ.comment({log:"send",mod:mod,size:size});break;
							case 86:
							break;
							case 66:
							break;
							};
						e.which=0;
						window.event.keyCode=0;
						return false;
						};
					});
				};
			var page=args.page || 1;
			var scro=args.scro || false;
			var load=args.load || false;
			if(load)PZ.load({log:"open"});
			$.ajax({
				type:"POST",
				url:"/?log=post&desc=comment_list&page="+page,
				data:postdata,
				cache:false,
				dataType:"json",
				success:function(res){
					PZ.load({log:"close"});
					//alert(res);return;
					switch(res.status){
						case 0:
							$("#tcz_comment").find(".comm_top_right>span").html(res.cont);
							$("#tcz_comment_list").html(res.data);
							$("#tcz_comment_page").show().html(res.other);
							if(scro){
								var pos=$("#tcz_comment").find(".comm_top").offset();
								var h=pos.top;
								$("html,body").animate({scrollTop:h},500);
								};
						break;
						default:PZ.fly({msg:qyklang.comment.start.error});break;
						};
					}
				});
		break;
		case "instr":
			var cata=args.cata || "face";
			var text=args.text || "";
			switch(cata){
				case "br":
					var msg=$("#tcz_comment_content").html();
					text="<br>";
					var reg=/(<br\/?[^>]*>)$/;
					if(msg.match(reg)==null){
						text+="<br>";
						};
				break;
				case "face":
					if(args.url){
						text="<img class=\"face\" src=\""+tczAppsui.path+"images/face/"+args.url+"\">";
						if($("#tcz_face").length>0)$("#tcz_face").hide();
					}else{
						if($("#tcz_face").length>0){
							$("#tcz_face").show();
						}else{
							var html="<div class='ui_modwin' id='tcz_face' mod='face'><div class='tab' mod='face'><a class='on' href='javascript:' mod='face'>经典</a><a class='out' href='javascript:' mod='face'>毛笔</a></div>";
							html+="<div class='scro scro1' id='tcz_face_scro0'><div class='icon'>";
							for(var i=1;i<=105;i++){
								html+="<img src='"+tczAppsui.path+"images/b.gif' onclick=\"PZ.comment({log:'instr',cata:'face',url:'qq/"+(i-1)+".gif'})\" onmouseover=\"this.style.backgroundColor='#fff';this.style.backgroundImage='url("+tczAppsui.path+"images/face/qq/"+(i-1)+".gif)'\" onmouseout=\"this.style.background=''\">";
								};
							html+="</div></div>";
							html+="<div class='scro scro2' id='tcz_face_scro1' style='display:none'><div class='icon'>";
							for(var i=1;i<=26;i++){
								var xu=i<10?"0"+i:i;
								html+="<img src='"+tczAppsui.path+"images/b.gif' onclick=\"PZ.comment({log:'instr',cata:'face',url:'maobi/"+xu+".gif'})\" onmouseover=\"this.style.backgroundColor='#fff';this.style.backgroundImage='url("+tczAppsui.path+"images/face/maobi/"+xu+".gif)'\" onmouseout=\"this.style.background=''\">";
								};
							html+="</div></div>";
							html+="</div>";
							$(document.body).append(html);
							$("#tcz_face>.tab>a").each(function(i){
								$(this).bind("mouseover",function(){
									$("#tcz_face>.tab>a").attr("class","out");
									$(this).attr("class","on");
									$("#tcz_face>.scro").hide();
									$("#tcz_face_scro"+i).show();
									});
								});
							};
						var pos=$("#tcz_comment").find(".comm_send_btn").offset();
						var l=pos.left-1;
						var t=pos.top-$("#tcz_face").outerHeight()+1;
						if(t<0)t=0;
						$("#tcz_face").css({top:t+"px",left:l+"px"});
						$(document.body).bind("mouseup",function(e){
							var mod=$(e.target).attr("mod") || null;
							if(mod!="face"){
								$("#tcz_face").hide();
								$(document.body).unbind();
								};
							});
						return;
						};
				break;
				};
			var range,node;
			if(!$("#tcz_comment_content").is(":focus"))$("#tcz_comment_content").focus();
			if (window.getSelection&&window.getSelection().getRangeAt){
				range=window.getSelection().getRangeAt(0);
				range.collapse(false);
				node=range.createContextualFragment(text);
				var c=node.lastChild;
				range.insertNode(node);
				if(c){
					range.setEndAfter(c);
					range.setStartAfter(c)
					};
				//alert(range);
				var j = window.getSelection();
				j.removeAllRanges();
				j.addRange(range);
			}else if(document.selection&&document.selection.createRange){
				document.selection.createRange().pasteHTML(text);
				};
		break;
		case "send":
			var isedit=$("#tcz_comment_content").attr("isedit");
			var msg=PZ.trim($("#tcz_comment_content").html());
			if(isedit=="no")msg="";
			else{
				msg=msg.replace(/^\n+|\n+$/ig,"");	//过滤头尾回车
				msg=msg.replace(/&nbsp;/ig," ");	//转换空格
				msg=msg.replace(/(^\s*)|(\s*$)/ig,"");	//过滤头尾空格
				msg=msg.replace(/<br\/?[^>]*>/ig,"{br}");	//转换行
				msg=msg.replace(/<p\/?[^>]*>/ig,"{br}");	//转换行
				msg=msg.replace(/<\/p\/?[^>]*>/ig,"");	//转换行
				msg=msg.replace(/\<img(.*?)src\=\"\/([^\"]+)"([^>]?)>/ig,"{img:$2}"); //转换图片
				msg=msg.replace(/<\/?[^>]*>/ig,"");	//过滤所有HTML
				msg=msg.replace(/((\{br\})+)/ig,"{br}"); //将多个空行合并成一行
				msg=msg.replace(/^((\{br\})+)|(\{br\})*$/ig,""); //过滤头尾换行
				var msg2=msg.replace(/ |　|&nbsp;|\{br\}/g,"");
				};
			if(msg==""||msg2==""){
				PZ.tip({delay:3000,obj:$("#tcz_comment_content"),msg:qyklang.comment.send.error1,upclose:"ok"});
				$("#tcz_comment_content").html("").focus();
				return;
				};
			var msg2=msg2.replace(/\{img:([^\}]+)\}/ig,"");
			if(msg2==""){
				PZ.tip({delay:3000,obj:$("#tcz_comment_content"),msg:qyklang.comment.send.error2,upclose:"ok"});
				$("#tcz_comment_content").focus();
				return;
				};
			msg3=msg.replace(/\{br\}|\{img:([^\}]+)\}/ig,"-");	//一个换行及表情算1个字符
			var msg_len=PZ.getlen(msg3);
			if(msg_len>1000){
				PZ.tip({delay:3000,obj:$("#tcz_comment_content"),msg:qyklang.comment.send.error3+"<span class='blue'>"+msg_len+" / 1000</span>",upclose:"ok"});
				$("#tcz_comment_content").focus();
				return;
				};
			PZ.load({log:"open",msg:qyklang.comment.send.load});
			var name=PZ.en($("#tcz_comment_name").val());
			msg=PZ.en(msg);
			var postdata="name="+name+"&content="+msg;
			if(mod==1)postdata+="&dataid="+tczAppsui.argid+"&bcat="+tczAppsui.argbcat;
			$.ajax({
				type:"POST",
				url:"/?log=post&desc=comment",
				data:postdata,
				cache:false,
				dataType:"json",
				success:function(res){
					PZ.load({log:"close"});
					switch(res.status){
						case 0:
							$("#tcz_comment_content").html("").trigger("blur");
							if(res.data=="yes"){
							PZ.fly({delay:500,msg:qyklang.comment.send.success,callback:function(){
								PZ.load({msg:qyklang.comment.send.reload});
								PZ.comment({log:"start",mod:mod,size:size,scro:true});
								}});
							}else{
								PZ.e({msg:qyklang.comment.send.success_no});
								};
						break;
						default:PZ.e({ico:"error",msg:res.data});break;
						};
					}
				});
		break;
		};
	};
/*---------------心情---------------*/
this.expmood=function(args){
	var log=args.log || "start";
	var text=args.text || qyklang.expmood.text;
	var did=args.did;
	var xu=args.xu || "0";
	var text_arr=text.split(",");
	switch(log){
		case "start":
			var icon=args.icon || "1";
			var html="<div class=\"moodout\"><a href=\"javascript:\" class=\"mood1\"><div class=\"moodtip moodtip_"+icon+"\">"+text_arr[0]+"</div><div class=\"moodnum\"></div></a></div>"
			+"<div class=\"moodout\"><a href=\"javascript:\" class=\"mood2\"><div class=\"moodtip moodtip_"+icon+"\">"+text_arr[1]+"</div><div class=\"moodnum\"></div></a></div>"
			+"<div class=\"moodout\"><a href=\"javascript:\" class=\"mood3\"><div class=\"moodtip moodtip_"+icon+"\">"+text_arr[2]+"</div><div class=\"moodnum\"></div></a></div>"
			+"<div class=\"moodout\"><a href=\"javascript:\" class=\"mood4\"><div class=\"moodtip moodtip_"+icon+"\">"+text_arr[3]+"</div><div class=\"moodnum\"></div></a></div>"
			+"<div class=\"moodout\"><a href=\"javascript:\" class=\"mood5\"><div class=\"moodtip moodtip_"+icon+"\">"+text_arr[4]+"</div><div class=\"moodnum\"></div></a></div>";
			$("#"+did).html(html);
			$("#"+did+">div").bind("click",function(){
				var xu=$(this).index()+1;
				PZ.expmood({did:did,log:"click",xu:xu});
				});
		break;
		case "click":
			$("#"+did+">div").unbind();
			var obj=$("#"+did+">div>a:eq("+(xu-1)+")");
			obj.addClass("moodclick");
		break;
		};
	var func=function(){
		$.ajax({
			type:"POST",
			url:"/?log=post&desc=expmood",
			data:"xu="+xu+"&bcat="+tczAppsui.argbcat+"&id="+tczAppsui.argid,
			cache:false,
			dataType:"json",
			success:function(res){
				switch(res.status){
					case 0:
						eval("var arr="+res.data);
						for(var i=0;i<5;i++)$("#"+did).find(".moodnum:eq("+i+")").html("<span>"+arr[i].num+"</span> ("+arr[i].scale+")");
					break;
					default:
						PZ.fly({msg:res.data});
					break;
					};
				}
			});
		};
	func();
	};
/*---------------日历---------------*/
this.calendar=function(args){
	var id=args.id;
	var ym=args.ym || "";
	var mark=args.mark || "";
	$.ajax({
		type:"POST",
		url:"/?log=post&desc=calendar",
		data:"mark="+mark+"&ym="+PZ.en(ym)+"&did="+id,
		cache:false,
		dataType:"json",
		success:function(res){
			switch(res.status){
				case 0:
					$("#"+id).html(res.data);
				break;
				};
			}
		});
	};
/*---------------代码高亮---------------*/
this.showcode=function(){
	if($("#qyk_showcode").length>0)return;
	var showfunc=function(){
		$("pre").each(function(){
			var cc=$(this).attr("class");
			if(/brush/i.test(cc)){
				SyntaxHighlighter.highlight(cc);
				};
			});
		$(".syntaxhighlighter").each(function(){
			var _this=$(this);
			$(this).find(".container>.line").each(function(){
				var h=$(this).height();
				var xu=$(this).index();
				//alert(xu+" - "+h+"\n"+$(this).html());
				_this.find(".gutter>.line:eq("+xu+")").css({height:h+"px"});
				});
			});
		};
	$.getScript(tczAppsui.path+"res/SyntaxHighlighter/shCore.js",function(){
		$.ajax({
			url:tczAppsui.path+"res/SyntaxHighlighter/shCoreDefault.css",
			success:function(data){
				$('<style type="text/css">' + data.replace(/url\(images/g, 'url(/css/images') + '</style>').appendTo('head'); 
				showfunc();
				}
			});
		//$("<link>").attr({id:"qyk_showcode",rel:"stylesheet",type:"text/css",href:tczAppsui.path+"res/SyntaxHighlighter/shCoreDefault.css?t="+PZ.n1(8)}).appendTo("head");
		});
	};
/*---------------搜索---------------*/
this.search=function(args){
	var log=args.log || "start";
	var mark=args.mark || "news";
	var seartype=args.seartype || "aaa";
	if($("#qyk_sear_type").length>0)seartype=$("#qyk_sear_type").val();
	switch(log){
		case "load":
			if($("select[id='qyk_sear_mark']").length>0)PZ.select({sid:"qyk_sear_mark",w:110});
			$("#qyk_sear_word").bind("keypress",function(event){
				if(event.keyCode=="13"){
					PZ.search({mark:mark});
					};
				});
			if($("#qyk_sear_word").val()!=""&&tczAppsui.seartype==seartype){
				$("#qyk_sear_word").select();
			}else{
				$("#qyk_sear_word").val("");
				};
		break;
		case "start":
			var word=$("#qyk_sear_word").val();
			if($("#qyk_sear_mark").length>0)mark=$("#qyk_sear_mark").val();
			if(word==""){
				$("#qyk_sear_word").focus();
				PZ.fly({msg:qyklang.search.error});
				return;
				};
			if(mark==""){
				PZ.fly({msg:qyklang.search.error2});
				return;
				};
			if(seartype==""){
				PZ.fly({msg:qyklang.search.error3});
				return;
				};
			var url="/?log="+mark+"&seartype="+seartype+"&word="+PZ.en(word);
			if(tczAppsui.usestatic)url="/"+mark+"/so?word="+PZ.en(word)+"&seartype="+seartype;
			location.href=url;
		break;
		};
	};
/*---------------上一页链接---------------*/
this.gopre=function(url){
var pre=document.referrer;
if(pre!="")url=pre;
location.href=url;
};
/*---------------手机中快速响应点击---------------*/
this.mlink=function(func){
func();
};
/*---------------事件任务---------------*/
this.ready=function(args){
	var log=args.log || "add";
	switch(log){
		case "start":
			for(var i=0;i<ready_callback.length;i++){
				var lib=ready_callback[i];
				if(lib.delay>0){
					window.setTimeout(lib.func,lib.delay);
					}else lib.func();
				};
		break;
		case "add":
			var delay=args.delay || 0;
			ready_callback.push({delay:delay,func:args.callback});
		break;
		};
	};
};
$(document).ready(function(){
PZ.ready({log:"start"});
var timer;
var changewin=function(){
	var func=function(){
		if($(".win_block").length>0){
			$(".win_block").each(function(){
				PZ.win({id:$(this).attr("id"),log:"change",animate:99});
				});
			};
		$("#tcz_window").css({height:$(document).height()+"px"});
		};
	window.clearTimeout(timer);
	timer=window.setTimeout(func,100);
	};
$(window).bind("resize",changewin);
PZ.photobox({qykphoto:"qyk_photobox"});
});