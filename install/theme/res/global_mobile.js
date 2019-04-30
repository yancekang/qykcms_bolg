function openmenu(){
if($("#qyk_menulist").is(":hidden")){
	$("#qyk_menulist").slideDown();
}else{
	$("#qyk_menulist").slideUp();
	};
};
$(document).ready(function(){
if($(".article_photobox").length>0)PZ.photobox({css:"article_photobox"});
switch(tczAppsui.arglog){
	case "album":
		window.setTimeout(function(){PZ.select({log:"check",val:"photo",sid:"qyk_sear_mark"})},100);
	break;
	case "photo":
		if(tczAppsui.argid>0){
			$("#qyk_coverlink>img").one("load",function() {
				var html="<a title='上一张' href=\"javascript:\" onclick=\"if($('#tcz_prephoto').find('a').length>0){location.href=$('#tcz_prephoto').find('a').attr('href')}else{PZ.fly({msg:'当前为第一张'})}\" class='prelink'><span></span></a><a title='下一张' href=\"javascript:\" onclick=\"if($('#tcz_nextphoto').find('a').length>0){location.href=$('#tcz_nextphoto').find('a').attr('href')}else{PZ.fly({msg:'没有下一张了'})}\" class='nextlink'><span></span></a>";
				$("#qyk_coverlink").append(html);
			}).each(function() {
			  if(this.complete) $(this).load();
				});
			};
	break;
	};
});

/*---------------出错提示---------------*/
window.onerror=function(sMessage,sUrl,sLine){
return true;
var str="页面脚本运行出错，请截图联系工作人员，谢谢您的合作！\n\n";
str+="信息："+sMessage+"\n\n";
str+="地址："+sUrl+"\n\n";
str+="行数："+sLine;
alert(str);
//PZ.e({ico:"no",msg:str});
return true;
};