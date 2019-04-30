<?php
class pagelist {
var $pn=2;
function plink($link,$page=1){
	global $tcz;
	$link=str_replace('{P}',$page,$link);
	if(setup_static){
		if(!preg_match('/^javascript:/i',$link))$link=getlink($link);
		}
	$link='href="'.$link.'"';
	return $link;
	}
function show($list=0,$size=12,$link='',$module='full',$record=true){
	global $tcz;
	if($module=='')$module='full';
	if($link==''){
		@$link='?&'.$_SERVER["QUERY_STRING"].'&';
		$link=str_replace('&page='.$tcz['page'].'&','&',$link);
		$link=str_replace('?&','',$link).'page={P}';
		$link='?'.$link;
		}
	if($size<1)$size=1;
	$allpage=ceil($list/$size);
	if($list==0)$tcz['page']=0;
	$html=goif($module=='full','<a class="desc">'.getlang('page','pagination').': <b class="page">'.$tcz['page'].'</b> / <b class="all">'.$allpage.'</b>'.goif($record,'　'.getlang('page','record').': <b class="list">'.$list.'</b>').'</a>');
	$html.='<span class="pnum">';
	if($tcz['page']>1){
		$html.=goif($module=='full','<a '.$this->plink($link,1).' class="out" hidefocus="true">&laquo; '.getlang('page','first').'</a>').'<a '.$this->plink($link,$tcz['page']-1).' class="out" hidefocus="true">&lsaquo; '.getlang('page','previous').'</a>';
	}else{
		$html.=goif($module=='full','<a class="dis">&laquo; '.getlang('page','first').'</a>').'<a class="dis">&lsaquo; '.getlang('page','previous').'</a>';
		}
	if($module!='mobile'){
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
		}
	if($tcz['page']<$allpage){
		$html.='<a '.$this->plink($link,$tcz['page']+1).' class="out" hidefocus="true">'.getlang('page','next').' &rsaquo;</a>'.goif($module=='full','<a '.$this->plink($link,$allpage).' class="out" hidefocus="true">'.getlang('page','last').' &raquo;</a>');
	}else{
		$html.='<a class="dis">'.getlang('page','next').' &rsaquo;</a>'.goif($module=='full','<a class="dis">'.getlang('page','last').' &raquo;</a>');
		}
	$html='<div class="ui_page ui_page_'.$module.'">'.$html.'</span></div>';
	return $html;
	}
}
?>