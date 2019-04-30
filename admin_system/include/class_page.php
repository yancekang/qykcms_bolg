<?php
class pagelist {
var $pn=3;
function plink($link,$page=1){
	global $tcz;
	$link=str_replace('{P}',$page,$link);
	$link='href="javascript:" onclick="'.$link.';return false"';
	return $link;
	}
function show($list=0,$size=0,$link='',$module='full',$record=true,$pagetext=''){
	global $tcz;
	$link='openpage({page:{P}})';
	$allpage=ceil($list/$size);
	if($list==0)$tcz['page']=0;
	$html='';
	$html.='<span class="pnum">';
	if($tcz['page']>1){
		$html.=goif($module=='full','<a '.$this->plink($link,1).' class="out" hidefocus="true">&laquo; 首页</a>').'<a '.$this->plink($link,$tcz['page']-1).' class="out" hidefocus="true">&lsaquo; 上一页</a>';
	}else{
		$html.=goif($module=='full','<a class="dis">&laquo; 首页</a>').'<a class="dis">&lsaquo; 上一页</a>';
		}
	$starn=$tcz['page']-$this->pn;
	if($starn<1)$starn=1;
	$overn=$starn+$this->pn*2;
	if($overn>$allpage){
		$overn=$allpage;
		$starn=$overn-$this->pn*2;
		if($starn<1)$starn=1;
		}
	if($starn>1&&$module!='full')$html.='<a '.$this->plink($link,1).' class="out" hidefocus="true">1</a><span class="more">...</span>';
	for($i=$starn;$i<=$overn;$i++){
		if($i==$tcz['page']){
			$html.='<a class="on">'.$i.'</a>';
		}else{
			$html.='<a '.$this->plink($link,$i).' class="out" hidefocus="true">'.$i.'</a>';
			}
		}
	if($overn<$allpage&&$module!='full')$html.='<span class="more">...</span><a '.$this->plink($link,$allpage).' class="out" hidefocus="true">'.$allpage.'</a>';
	if($tcz['page']<$allpage){
		$html.='<a '.$this->plink($link,$tcz['page']+1).' class="out" hidefocus="true">下一页 &rsaquo;</a>'.goif($module=='full','<a '.$this->plink($link,$allpage).' class="out" hidefocus="true">尾页 &raquo;</a>');
	}else{
		$html.='<a class="dis">下一页 &rsaquo;</a>'.goif($module=='full','<a class="dis">尾页 &raquo;</a>');
		}
	$html=$html.'</span>';
	$html.=goif($module=='full','<a href="javascript:checkall()" class="desc" title="全选/取消选择">页码：<b class="page">'.$tcz['page'].'</b> / <b class="all">'.$allpage.'</b>'.goif($record,'　记录：<b class="list">'.$list.'</b>').goif($pagetext!='','　'.$pagetext).'</a>');
	return $html;
	}
}
?>