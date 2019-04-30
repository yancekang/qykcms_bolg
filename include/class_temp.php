<?php
include('include/config.php');
include('include/class_mysql.php');
include('include/class_page.php');
include('include/function.php');
include('include/common.php');
class qyk {
function createHTML(){
	global $db,$tcz,$cook,$web;
	$web['tempcache']=$tcz['log'];
	$web['tempfile']=$tcz['log'].'.'.setup_temptype;
	if($tcz['word']!='')$web['datacache_status']=0;
	if($web['datacache_status']){
		$web['datacache']=getdatacache($web['id'],$web['tempfolder']);
		if(is_file($web['datacache'])){
			$status=filemtime($web['datacache'])+$web['datacache_status']>time();
			if($status){
				@$html=file_get_contents($web['datacache']);
				echo $html.self::addhtml('foot');
				exit;
				}
			}
		}
	switch($tcz['log']){
		case 'post':
			include('include/lib_'.$tcz['log'].'.php');
			exit;
		break;
		case 'special':
		case 'special_list':
			include('include/lib_special.php');
		break;
		default:
			$module=db_getshow('module','*','webid='.$web['id'].' and languages="'.$web['templang'].'" and bcat=0 and mark="'.$tcz['log'].'"');
			if($module){
				switch($module['modtype']){
					case 9:
						if($module['mark']!='index'){
							gotourl($module['linkurl']);
							exit;
							}
					break;
					case 8:
						if($module['mark']!='index')self::addpos($module['title'],'log='.$tcz['log']);
					break;
					default:
						include('include/lib_module.php');
					break;
					}
			}else{
				$web['ismod']=false;
				}
		break;
		};
	if($tcz['word']!=''){
		self::addpos(getlang('search','title').$tcz['word'],'log='.$tcz['log'].'&seartype='.$tcz['seartype'].'&word='.$tcz['word']);
		}
	$web['tempfile']=setup_webfolder.$web['id'].'/'.$web['tempfolder'].'/'.$web['tempfolder_file'].'/'.$web['tempfile'];
	$web['tempcache_path']=setup_webfolder.$web['id'].'/runtime/temp/'.$web['tempfolder'].'/';
	$tempcache_save=$web['tempcache'];
	if(setup_tempcache_md5)$tempcache_save=md5($tempcache_save);
	$web['tempcache']=$web['tempcache_path'].$tempcache_save.'.php';
	if(preg_match('/ SELECT /i',$web['tempcache_path']))tipmsg('非法的缓存参数',true);
	if(setup_tempcache){
		if(is_file($web['tempcache'])){
			if($web['datacache_status']){
				createDirs($web['datacache']);
				ob_start();
				require_once($web['tempcache']);
				$content=ob_get_contents();
				ob_end_clean();
				@$file=fopen($web['datacache'],'w');
				fwrite($file,$content);
				fclose($file);
				echo $content;
			}else{
				require_once($web['tempcache']);
				}
			echo self::addhtml('foot');
			exit;
			}
		}
	if($web['ismod'])$html=readtemp($web['tempfile'],'读取模板文件出错，请检查该文件是否存在：<br>');
	else $html=readtemp($web['tempfile'],'很抱歉，您当前浏览的页面不存在：'.$tcz['log'],false);
	$html=trim($html);
	if(preg_match('/<\?/',$html)){
		$html=str_replace('<?',htmlspecialchars('<?'),$html);
		$html=str_replace('?>',htmlspecialchars('?>'),$html);
		}
	for($k=1;$k<=10;$k++){
		$arr=explode(setup_prefix,$html);
		if(count($arr)<=1)break;
		for($i=1;$i<count($arr);$i++){
			$tagcont=current(explode(setup_suffix,$arr[$i]));
			$reps=setup_prefix.$tagcont.setup_suffix;
			$tagname=current(explode('=',$tagcont.'='));
			switch($tagname){
				case 'noheader':
					$web['themeheader']=false;
					$html=str_replace($reps,'',$html);
				break;
				case 'nocache':
					$web['datacache_status']=0;
					$html=str_replace($reps,'<?php $web[\'datacache_status\']=0;?>',$html);
				break;
				case 'position':
					$args=self::tojson($tagcont);
					if(!isset($args['position']))$args['position']='';
					if($args['position']==''){
						$locamark='<span class="locamark"> » </span>';
						if(isset($args['locamark']))$locamark=$args['locamark'];
						$html=str_replace($reps,'<'.'?php echo str_replace(\'{_LOCAMARK_}\',\''.$locamark.'\',$web[\'location\']);?'.'>',$html);
					}else{
						if(!isset($args['url']))$args['url']='';
						self::addpos($args['position'],$args['url']);
						$html=str_replace($reps,'',$html);
						}
				break;
				case 'column':case 'modname':
					$html=str_replace($reps,'<?php echo $web[\'column\'];?>',$html);
				break;
				case 'file':case 'include':
					$args=self::tojson($tagcont);
					$incs=readtemp(setup_webfolder.$web['id'].'/'.$web['tempfolder'].'/'.$web['tempfolder_file'].'/'.$args[$tagname].'.'.setup_temptype,'模板标签指定的包含文件不存在：');
					$html=str_replace($reps,$incs,$html);
				break;
				case 'require':
					$args=self::tojson($tagcont);
					@require_once(setup_webfolder.$web['id'].'/lib/'.$args['require'].'.php');
					$html=str_replace($reps,'',$html);
				break;
				case 'link':
					$tagcont=preg_replace('/<([^\>]+)\>/','\'.$1.\'',$tagcont);
					$args=self::tojson($tagcont);
					if(!isset($args['link'])){
						$link='getlink(\'log='.$tcz['log'].'\')';
						$html=str_replace($reps,'<?php echo '.$link.';?>',$html);
					}else if($args['link']=='article'){
						$mod='full';
						$extlink='true';
						$titlelen=0;
						if(isset($args['mod']))$mod=$args['mod'];
						if(isset($args['titlelen']))$titlelen=$args['titlelen'];
						if(isset($args['extlink'])){
							if($args['extlink']==false)$extlink='false';
							}
						$html=str_replace($reps,'<?php echo qyk::articlelink($val,"'.$mod.'",'.$extlink.',false,'.$titlelen.');?>',$html);
					}else if($args['link']=='module'){
						$mod='full';
						$extlink='true';
						if(isset($args['mod']))$mod=$args['mod'];
						if(isset($args['extlink'])){
							if($args['extlink']==false)$extlink='false';
							}
						$html=str_replace($reps,'<?php echo qyk::modulelink($val,"'.$mod.'",'.$extlink.');?>',$html);
					}else{
						$link='getlink(\''.$args['link'].'\')';
						if(isset($args['url'])&&!empty($args['url'])){
							$link='goif(\''.$args['url'].'\'!=\'\',\''.$args['url'].'\','.$link.')';
							}
						$html=str_replace($reps,'<?php echo '.$link.';?>',$html);
						}
				break;
				case 'id':case 'log':case 'bcat':case 'scat':case 'lcat':
					$html=str_replace($reps,$tcz[$tagname],$html);
				break;
				case 'lib':
					$args=self::tojson($tagcont);
					$html=str_replace($reps,'<?php '.$args['lib'].';?>',$html);
				break;
				case 'label':
					$args=self::tojson($tagcont);
					$html=str_replace($reps,'<?php echo qyk::getlabel('.$args['label'].');?>',$html);
				break;
				case 'table':
					$args=self::tojson($tagcont);
					$xhval='$val';
					$xhsort='$sort';
					if(isset($args['as']))$xhval=$args['as'];
					if(isset($args['sort']))$xhsort=$args['sort'];
					$html=str_replace($reps,'<?php foreach('.$args['table'].' as '.$xhsort.'=>'.$xhval.'){ ?>',$html);
				break;
				case 'advert':
					$advertid=0;
					$mod='full';
					$size=0;
					$args=self::tojson($tagcont);
					if(isset($args['advert']))$advertid=(int)$args['advert'];
					if(isset($args['mod']))$mod=$args['mod'];
					if(isset($args['size']))$size=(int)$args['size'];
					if($mod=='list'){
						$html=str_replace($reps,'<?php $datalist=qyk::getadvert('.$advertid.',\''.$mod.'\','.$size.');foreach($datalist as $sort=>$val){?>',$html);
					}else{
						$delay=5000;
						$width="100%";
						$height="378px";
						$btn='dot';
						$pos='center';
						$ani=5;
						$target="";
						if(isset($args['delay']))$delay=(int)$args['delay'];
						if(isset($args['width']))$width=$args['width'];
						if(isset($args['height']))$height=$args['height'];
						if(isset($args['btn']))$btn=$args['btn'];
						if(isset($args['pos']))$pos=$args['pos'];
						if(isset($args['target']))$target=$args['target'];
						if(isset($args['ani']))$ani=(int)$args['ani'];
						$html=str_replace($reps,'<?php echo qyk::getadvert('.$advertid.',\''.$mod.'\','.$size.','.$delay.',\''.$width.'\',\''.$height.'\',\''.$btn.'\',\''.$pos.'\','.$ani.',\''.$target.'\');?>',$html);
						}
				break;
				case 'calendar':
					$args=self::tojson($tagcont);
					$mark=$tcz['log'];
					$ym='';
					if(isset($args['calendar']))$mark=$args['calendar'];
					if(isset($args['ym']))$ym=$args['ym'];
					$res='<?php $caleid=\'tcz_calendar_\'.rand(11111,99999);?><div id="<?php echo $caleid;?>" class="ui_calendar"></div><script>PZ.ready({callback:function(){PZ.calendar({id:"<?php echo $caleid;?>",mark:"'.$mark.'",ym:"'.$ym.'"})}})</script>';
					$html=str_replace($reps,$res,$html);
				break;
				case 'customer':
					$customerid=0;
					$size=0;
					$args=self::tojson($tagcont);
					if(isset($args['customer']))$customerid=(int)$args['customer'];
					if(isset($args['size']))$size=(int)$args['size'];
					$html=str_replace($reps,'<?php $datalist=qyk::getcustomer('.$customerid.','.$size.');foreach($datalist as $sort=>$val){?>',$html);
				break;
				case 'menu':
					if($tagcont==$tagname){
						$html=str_replace($reps,'<?php $datalist=qyk::modulelist(0);foreach($datalist as $sort=>$val){?>',$html);
					}else{
						$args=self::tojson($tagcont);
						$bcat='';
						$scat='';
						$size=0;
						$xhval='$val';
						$xhsort='$sort';
						$none='';
						$mod='list';
						$menutype=99;
						if(isset($args['bcat'])){
							$bcat=$args['bcat'];
							$xhval.='1';
							$xhsort.='1';
							}
						if(isset($args['scat'])){
							$scat=$args['scat'];
							$xhval.='2';
							$xhsort.='2';
							}
						if(isset($args['as']))$xhval=$args['as'];
						if(isset($args['sort']))$xhsort=$args['sort'];
						if(isset($args['size']))$size=(int)$args['size'];
						if(isset($args['none']))$none=$args['none'];
						if(isset($args['menutype']))$menutype=$args['menutype'];
						if(isset($args['mod']))$mod=$args['mod'];
						if($mod=='list'){
							$html=str_replace($reps,'<?php $datalist=qyk::modulelist('.$menutype.',"'.$args['menu'].'","'.$bcat.'","'.$scat.'",'.$size.');'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as '.$xhsort.'=>'.$xhval.'){?>',$html);
						}else{
							$html=str_replace($reps,'<?php echo qyk::modulelist('.$menutype.',"'.$args['menu'].'","'.$bcat.'","'.$scat.'",'.$size.',"full");?>',$html);
							}
						}
				break;
				case 'feedback':
					$args=qyk::tojson($tagcont);
					$types=1;
					$size=10;
					$page='true';
					$pagebtn=2;
					$order='';
					$xhval='$val';
					$xhsort='$sort';
					$mod='full';
					$none='';
					$contlen=36;
					if(isset($args['as']))$xhval=$args['as'];
					if(isset($args['sort']))$xhsort=$args['sort'];
					if(isset($args['feedback']))$types=(int)$args['feedback'];
					if(isset($args['size']))$size=(int)$args['size'];
					if(isset($args['order']))$order=$args['order'];
					if(isset($args['page'])){
						if($args['page']==false)$page='false';
						}
					if(isset($args['none']))$none=$args['none'];
					if(isset($args['pagebtn']))$pagebtn=(int)$args['pagebtn'];
					if(isset($args['contlen']))$contlen=(int)$args['contlen'];
					if(isset($args['mod']))$mod=$args['mod'];
					if($mod=='list'){
						$html=str_replace($reps,'<?php $datalist=qyk::feedbacklist('.$types.','.$size.','.$page.','.$pagebtn.',"list",'.$contlen.',"'.$order.'");'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as '.$xhsort.'=>'.$xhval.'){?>',$html);
					}else{
						$html=str_replace($reps,'<?php echo qyk::feedbacklist('.$types.','.$size.','.$page.','.$pagebtn.',"full",'.$contlen.',"'.$order.'");?>',$html);
						}
				break;
				case 'option':
					$args=qyk::tojson($tagcont);
					$types='';
					$bcat=0;
					$size=0;
					$order='';
					$xhval='$val';
					$xhsort='$sort';
					$none='';
					if(isset($args['as']))$xhval=$args['as'];
					if(isset($args['sort']))$xhsort=$args['sort'];
					if(isset($args['option']))$types=$args['option'];
					if(isset($args['bcat']))$bcat=(int)$args['bcat'];
					if(isset($args['size']))$size=(int)$args['size'];
					if(isset($args['order']))$order=$args['order'];
					if(isset($args['none']))$none=$args['none'];
					$html=str_replace($reps,'<?php $datalist=qyk::optionlist("'.$types.'",'.$bcat.','.$size.',"'.$order.'");'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as '.$xhsort.'=>'.$xhval.'){?>',$html);
				break;
				case 'search':
					$mark='';
					$args=qyk::tojson($tagcont);
					$searfie='title';
					$sel='true';
					$width=290;
					if(isset($args['search']))$mark=$args['search'];
					if(isset($args['searfie']))$searfie=$args['searfie'];
					if(isset($args['width']))$width=(int)$args['width'];
					if(isset($args['sel'])){
						if($args['sel']==false)$sel='false';
						}
					$html=str_replace($reps,'<?php echo qyk::getsearch(\''.$mark.'\',\''.$searfie.'\','.$width.','.$sel.');?>',$html);
				break;
				case 'list':
					$args=self::tojson($tagcont);
					$mark='';
					$page='true';
					if($tcz['log']=='index')$page='false';
					$bcat=0;
					$scat='';
					$scat_more=true;
					$lcat='';
					$lcat_more=true;
					$size=0;
					$order='';
					$star=0;
					$xhval='$val';
					$xhsort='$sort';
					$none='';
					$word='';
					$searfie='title';
					$pagebtn=2;
					$mod=0;
					$auto='true';
					if(isset($args['list']))$mark=$args['list'];
					if($mark=='')$mark=$tcz['log'];
					if(isset($args['as']))$xhval=$args['as'];
					if(isset($args['sort']))$xhsort=$args['sort'];
					if(isset($args['bcat']))$bcat=(int)$args['bcat'];
					if(isset($args['scat']))$scat=$args['scat'];
					if($scat!=''&&!preg_match('/^([0-9\,]+)$/i',$scat))$scat_more=false;
					if(isset($args['lcat']))$lcat=$args['lcat'];
					if($lcat!=''&&!preg_match('/^([0-9\,]+)$/i',$lcat))$lcat_more=false;
					if(isset($args['size']))$size=(int)$args['size'];
					if(isset($args['order']))$order=$args['order'];
					if(isset($args['star']))$star=(int)$args['star'];
					if(isset($args['word']))$word=$args['word'];
					if(isset($args['none']))$none=$args['none'];
					if(isset($args['searfie']))$searfie=$args['searfie'];
					if(isset($args['pagebtn']))$pagebtn=(int)$args['pagebtn'];
					if(isset($args['mod']))$mod=(int)$args['mod'];
					if(isset($args['page'])){
						if($args['page']==false)$page='false';
						else $page='true';
						}
					if(isset($args['auto'])){
						if($args['auto']==false)$auto='false';
						else $auto='true';
						}
					if($mod){
						$titlelen=0;
						$cover=2;
						$width="400px";
						$height="300px";
						$view="photo";
						$contlen=68;
						$target="";
						if(isset($args['titlelen']))$titlelen=(int)$args['titlelen'];
						if(isset($args['cover']))$cover=(int)$args['cover'];
						if(isset($args['contlen']))$contlen=(int)$args['contlen'];
						if(isset($args['width']))$width=$args['width'];
						if(isset($args['height']))$height=$args['height'];
						if(isset($args['view']))$view=$args['view'];
						if(isset($args['target']))$target=$args['target'];
						$res='<ul class="ui_artlt_'.$mod.'"><'.'?php $datalist=qyk::articlelist("'.$mark.'",'.$page.','.$pagebtn.','.$auto.','.$bcat.','.goif($scat_more,'"'.$scat.'"',$scat).','.goif($lcat_more,'"'.$lcat.'"',$lcat).','.$size.','.$star.',"'.$word.'","'.$order.'","'.$searfie.'");'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as $sort=>$val){?'.'>';
						switch($mod){
							case 1:
								$res.='<li><div class="artlt_time"><div class="artlt_day"><?php echo date(\'d\',$val[\'time_add\']);?></div><div class="artlt_year"><?php echo sprintf(getlang(\'time\',\'ym\'),date(\'Y\',$val[\'time_add\']),date(\'m\',$val[\'time_add\']));?></div></div>
<?php if($val[\'cover\']!=\'\'){?><div class="artlt_cover"><a href="<?php echo qyk::articlelink($val,\'url\');?>"><img src="<?php echo getfile(\'pic\',$val[\'cover\'],\'s\');?>"></a></div>
<div class="artlt_text artlt_text_pdl"><?php }else{?><div class="artlt_text"><?php }?>
	<div class="artlt_tit"><?php echo qyk::articlelink($val,\'full\',true,false,'.$titlelen.','.goif($page,'$tcz[\'word\']').');?></div>
	<div class="artlt_desc"><?php echo tipshort(getreset($val[\'content\']),'.$contlen.'+goif($val[\'cover\']!=\'\',0,20));?></div>
	<div class="artlt_count"><span class="artlt_comment<?php echo goif($val[\'comment\'],\' artlt_light\');?>"><?php echo $val[\'comment\'];?></span><span class="artlt_hits"><?php echo $val[\'hits\'];?></span><span class="artlt_update"><?php echo date(\'Y-m-d H:i:s\',$val[\'time_update\']);?></span></div>
</div></li>';
							break;
							case 2:
								$res.='<?php if($sort<'.$cover.'){?><li class="artlt_list_cover">
<div class="artlt_cover"><a href="<?php echo qyk::articlelink($val,\'url\');?>"><img src="<?php echo getfile(\'pic\',$val[\'cover\'],\'s\');?>"></a></div>
<div class="artlt_text">
	<div class="artlt_tit"><?php echo qyk::articlelink($val,\'full\',true,false,'.$titlelen.','.goif($page,'$tcz[\'word\']').');?></div>
	<div class="artlt_count"><span class="artlt_comment<?php echo goif($val[\'comment\'],\' artlt_light\');?>"><?php echo $val[\'comment\'];?></span><span class="artlt_hits"><?php echo $val[\'hits\'];?></span><span class="artlt_update"><?php echo date(\'Y-m-d H:i:s\',$val[\'time_update\']);?></span></div>
</div></li><?php }else{?><li class="artlt_list_text">
<div class="artlt_tit"><?php echo qyk::articlelink($val,\'full\',true,false,'.$titlelen.','.goif($page,'$tcz[\'word\']').');?></div>
<div class="artlt_count"><span class="artlt_comment<?php echo goif($val[\'comment\'],\' artlt_light\');?>"><?php echo $val[\'comment\'];?></span><span class="artlt_hits"><?php echo $val[\'hits\'];?></span><span class="artlt_update"><?php echo date(\'Y-m-d H:i:s\',$val[\'time_update\']);?></span></div></li><?php }?>';
							break;
							case 3:
								$res.='<li style="width:'.$width.';height:'.$height.'<?php echo goif($sort%'.$cover.'==0,\';margin-left:0\');?>">'.goif($view=='photo','<a title="<?php echo $val[\'title\'];?>" class="artlt_outer" qykphoto="qyk_photobox" href="<?php echo getfile(\'pic\',$val[\'cover\'],\'b\');?>"','<a title="<?php echo $val[\'title\'];?>" class="artlt_view" qykphoto="qyk_photobox" href="<?php echo getfile(\'pic\',$val[\'cover\'],\'b\');?>"></a><a class="artlt_outer" href="<?php echo qyk::articlelink($val,\'url\');?>"').' title="<?php echo $val[\'title\'];?>"><div class="artlt_cover"><img src="<?php echo getfile(\'pic\',$val[\'cover\'],\'s\');?>"></div>
<div class="artlt_text"><div class="artlt_tit"><?php echo $val[\'title\'];?></div>
<div class="artlt_count"><span class="artlt_comment<?php echo goif($val[\'comment\'],\' artlt_light\');?>"><?php echo $val[\'comment\'];?></span><span class="artlt_hits"><?php echo $val[\'hits\'];?></span><span class="artlt_update"><?php echo date(\'Y-m-d H:i:s\',$val[\'time_update\']);?></span></div>
</div></a></li>';
							break;
							case 4:
								$res.='<li><span><?echo date(\'m-d\',$val[\'time_add\']);?></span><?php echo qyk::articlelink($val,\'full\',true,false,'.$titlelen.','.goif($page,'$tcz[\'word\']').');?></li>';
							break;
							case 5:
								$res.='<li><?php echo qyk::articlelink($val,\'full\',true,false,'.$titlelen.','.goif($page,'$tcz[\'word\']').');?></li>';
							break;
							}
						$res.='<?php }?></ul>';
						$html=str_replace($reps,$res,$html);
					}else{
						$html=str_replace($reps,'<?php $datalist=qyk::articlelist("'.$mark.'",'.$page.','.$pagebtn.','.$auto.','.$bcat.','.goif($scat_more,'"'.$scat.'"',$scat).','.goif($lcat_more,'"'.$lcat.'"',$lcat).','.$size.','.$star.',"'.$word.'","'.$order.'","'.$searfie.'");'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as '.$xhsort.'=>'.$xhval.'){?>',$html);
						}
				break;
				case 'special':
					$args=self::tojson($tagcont);
					$mark='';
					$page='true';
					$bcat=0;
					$scat='';
					$scat_more=true;
					$lcat='';
					$lcat_more=true;
					$size=0;
					$order='';
					$star=0;
					$xhval='$val';
					$xhsort='$sort';
					$none='';
					$searfie='title';
					$pagebtn=2;
					$mod=0;
					$auto='false';
					$word='';
					if(isset($args['special']))$mark=$args['special'];
					if($mark==''&&!$bcat&&$tcz['bcat'])$bcat=$tcz['bcat'];
					if(isset($args['as']))$xhval=$args['as'];
					if(isset($args['sort']))$xhsort=$args['sort'];
					if(isset($args['bcat']))$bcat=(int)$args['bcat'];
					if(isset($args['scat']))$scat=$args['scat'];
					if($scat!=''&&!preg_match('/^([0-9\,]+)$/i',$scat))$scat_more=false;
					if(isset($args['lcat']))$lcat=$args['lcat'];
					if($lcat!=''&&!preg_match('/^([0-9\,]+)$/i',$lcat))$lcat_more=false;
					if(isset($args['size']))$size=(int)$args['size'];
					if(isset($args['order']))$order=$args['order'];
					if(isset($args['star']))$star=(int)$args['star'];
					if(isset($args['none']))$none=$args['none'];
					if(isset($args['searfie']))$searfie=$args['searfie'];
					if(isset($args['pagebtn']))$pagebtn=(int)$args['pagebtn'];
					if(isset($args['mod']))$mod=(int)$args['mod'];
					if(isset($args['page'])){
						if($args['page']==false)$page='false';
						else $page='true';
						}
					if(isset($args['word']))$word=$args['word'];
					if(isset($args['auto'])){
						if($args['auto']==false)$auto='false';
						else $auto='true';
						}
					if($mod){
						$titlelen=0;
						$cover=2;
						$width="400px";
						$height="300px";
						$contlen=68;
						$log=$tcz['log'];
						if(isset($args['titlelen']))$titlelen=(int)$args['titlelen'];
						if(isset($args['cover']))$cover=(int)$args['cover'];
						if(isset($args['contlen']))$contlen=(int)$args['contlen'];
						if(isset($args['width']))$width=$args['width'];
						if(isset($args['height']))$height=$args['height'];
						if(isset($args['log']))$log=$args['log'];
						$res='<ul class="ui_artlt_'.$mod.'"><'.'?php $datalist=qyk::speciallist("'.$mark.'",'.$page.','.$pagebtn.','.$auto.','.$bcat.','.goif($scat_more,'"'.$scat.'"',$scat).','.goif($lcat_more,'"'.$lcat.'"',$lcat).','.$size.','.$star.',"'.$word.'","'.$order.'","'.$searfie.'");'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as $sort=>$val){?'.'>';
						switch($mod){
							case 3:
								$res.='<li style="width:'.$width.';height:'.$height.'<?php echo goif($sort%'.$cover.'==0,\';margin-left:0\');?>"><a title="<?php echo $val[\'title\'];?>" class="artlt_view" qykphoto="qyk_photobox" href="<?php echo getfile(\'pic\',$val[\'cover\'],\'b\');?>"></a><a class="artlt_outer" href="<?php echo getlink(\'log='.$log.'&id=\'.$val[\'dataid\']);?>" title="<?php echo $val[\'title\'];?>"><div class="artlt_cover"><img src="<?php echo getfile(\'pic\',$val[\'cover\'],\'s\');?>"></div>
<div class="artlt_text"><div class="artlt_tit"><?php echo $val[\'title\'];?></div>
<div class="artlt_count"><span class="artlt_comment<?php echo goif($val[\'comment\'],\' artlt_light\');?>"><?php echo $val[\'comment\'];?></span><span class="artlt_hits"><?php echo $val[\'hits\'];?></span><span class="artlt_update"><?php echo date(\'Y-m-d H:i:s\',$val[\'time_update\']);?></span></div>
</div></a></li>';
							break;
							}
						$res.='<?php }?></ul>';
						$html=str_replace($reps,$res,$html);
					}else{
						$html=str_replace($reps,'<?php $datalist=qyk::speciallist("'.$mark.'",'.$page.','.$pagebtn.','.$auto.','.$bcat.','.goif($scat_more,'"'.$scat.'"',$scat).','.goif($lcat_more,'"'.$lcat.'"',$lcat).','.$size.','.$star.',"'.$word.'","'.$order.'","'.$searfie.'");'.goif($none!='','if(!count($datalist)){echo "'.$none.'";}').'foreach($datalist as '.$xhsort.'=>'.$xhval.'){?>',$html);
						}
				break;
				case 'list.menu':
					$html=str_replace($reps,'<?php echo qyk::modulelist(99,"'.$tcz['log'].'","","",0,"full");?>',$html);
				break;
				case 'list.record':
					$html=str_replace($reps,'<?php echo $web[\'list_record\'];?>',$html);
				break;
				case 'list.page':
				case 'list_page':
					$args=self::tojson($tagcont);
					$html=str_replace($reps,'<?php echo $web[\'list_page\'];?>',$html);
				break;
				case 'if':
					$args=self::tojson($tagcont);
					$html=str_replace($reps,'<?php if('.$args['if'].'){?>',$html);
				break;
				case 'else':
					$html=str_replace(setup_prefix.'else'.setup_suffix,'<?php }else{?>',$html);
				break;
				case 'elseif':
					$args=self::tojson($tagcont);
					$html=str_replace($reps,'<?php }else if('.$args['elseif'].'){?>',$html);
				break;
				case 'end':
					$html=str_replace($reps,'<?php }?>',$html);
				break;
				case 'val':
				case 'art':
				case 'article':
				case 'spe':
					$variable='val';
					if($tagname=='val'){
						$variable='val';
					}else if($tagname=='spe'){
						$variable='special';
					}else{
						$variable='article';
						if(!isset($$variable)){
							$html=str_replace($reps,setup_errortag,$html);
							continue;
							}
						}
					$args=self::tojson($tagcont);
					switch($args[$tagname]){
						case 'link':
							$extlink='true';
							if(isset($args['extlink'])){
								if($args['extlink']==false)$extlink='false';
								}
							$res='<?php echo qyk::articlelink($'.$variable.',$mod=\'url\','.$extlink.',true);?>';
						break;
						case 'hits_ajax':
							$res='<?php $hits_ajaxid=\'hits_ajax_\'.rand(11111,99999);echo \'<span id="\'.$hits_ajaxid.\'">...</span><script type="text/javascript">PZ.ready({callback:function(){PZ.getarticlehits("\'.$hits_ajaxid.\'",\'.$'.$variable.'[\'modtype\'].\',\'.$'.$variable.'[\'dataid\'].\')}});</script>\'?>';
						break;
						case 'title_res':
							$res='<?php echo goif($'.$variable.'["time_color"]!="","<font color=\"".$'.$variable.'["time_color"]."\">".$'.$variable.'["title"]."</font>",$'.$variable.'["title"]);?>';
						break;
						case 'cover':
							$size='s';
							if(isset($args['size'])){
								if($args['size']=='b')$size='b';
								}
							$res='<?php echo getfile(\'pic\',$'.$variable.'[\''.$args[$tagname].'\'],\''.$size.'\');?>';
						break;
						case 'classname':
							$mod='full';
							if(isset($args['mod']))$mod=$args['mod'];
							$res='<?php echo qyk::getclassname($'.$variable.',\''.$mod.'\');?>';
						break;
						case 'content':
						case 'content1':
						case 'content2':
						case 'content3':
						case 'content4':
							$showcode='false';
							if(isset($args['showcode'])){
								if($args['showcode']==true)$showcode='true';
								}
							$res='<?php echo getreset($'.$variable.'[\''.$args[$tagname].'\'],true,'.$showcode.');?>';
						break;
						case 'time_add':
						case 'time_update':
							$mod=1;
							if(isset($args['mod']))$mod=(int)$args['mod'];
							switch($mod){
								case 2:
									$res='<?php echo sprintf(getlang(\'time\',\'long\'),date(\'Y\',$'.$variable.'[\''.$args[$tagname].'\']),date(\'m\',$'.$variable.'[\''.$args[$tagname].'\']),date(\'d\',$'.$variable.'[\''.$args[$tagname].'\']));?>';
								break;
								case 3:
									$res='<?php echo sprintf(getlang(\'time\',\'short\'),date(\'m\',$'.$variable.'[\''.$args[$tagname].'\']),date(\'d\',$'.$variable.'[\''.$args[$tagname].'\']));?>';
								break;
								default:
									$res='<?php echo getimes($'.$variable.'[\''.$args[$tagname].'\']);?>';
								break;
								}
						break;
						default:
							if(isset($args['tag'])){
								switch($args['tag']){
									case 'br':
										$res='<?php echo nl2br($'.$variable.'[\''.$args[$tagname].'\']);?>';
									break;
									case 'hide':
										$mod='ip';
										if(isset($args['mod']))$mod=$args['mod'];
										$res='<?php echo hideaddress($'.$variable.'[\''.$args[$tagname].'\'],\''.$mod.'\');?>';
									break;
									case 'time':
										$mod=1;
										if(isset($args['mod']))$mod=(int)$args['mod'];
										switch($mod){
											case 2:
												$res='<?php echo sprintf(getlang(\'time\',\'long\'),date(\'Y\',$'.$variable.'[\''.$args[$tagname].'\']),date(\'m\',$'.$variable.'[\''.$args[$tagname].'\']),date(\'d\',$'.$variable.'[\''.$args[$tagname].'\']));?>';
											break;
											case 3:
												$res='<?php echo sprintf(getlang(\'time\',\'short\'),date(\'m\',$'.$variable.'[\''.$args[$tagname].'\']),date(\'d\',$'.$variable.'[\''.$args[$tagname].'\']));?>';
											break;
											default:
												$res='<?php echo getimes($'.$variable.'[\''.$args[$tagname].'\']);?>';
											break;
											}
									break;
									case 'file':
										$res='<?php echo getfile(\'file\',$'.$variable.'[\''.$args[$tagname].'\']);?>';
									break;
									case 'cover':
										$size='s';
										if(isset($args['size'])){
											if($args['size']=='b')$size='b';
											}
										$res='<?php echo getfile(\'pic\',$'.$variable.'[\''.$args[$tagname].'\'],\''.$size.'\');?>';
									break;
									case 'content':
										$showcode='false';
										if(isset($args['showcode'])){
											if($args['showcode']==true)$showcode='true';
											}
										$page=true;
										if(isset($args['page'])){
											if($args['page']=='false')$page=false;
											}
										$res='<?php echo getreset($'.$variable.'[\''.$args[$tagname].'\'],true,'.$showcode.');?>';
									break;
									default:
										$res='<?php echo $'.$variable.'[\''.$args[$tagname].'\'];?>';
									break;
									}
							}else{
								$res='<?php echo $'.$variable.'[\''.$args[$tagname].'\'];?>';
								}
						break;
						}
					$html=str_replace($reps,$res,$html);
				break;
				case 'artfind':
					$args=self::tojson($tagcont);
					$findtype='next';
					$extlink=true;
					$titlelen=0;
					$none=getlang('sys','nextdata');
					if(isset($args['artfind'])){
						if($args['artfind']=='pre'){
							$findtype='pre';
							$none=getlang('sys','predata');
							}
						}
					if(isset($args['extlink'])){
						if($args['extlink']==false)$extlink='false';
						}
					if(isset($args['titlelen']))$titlelen=(int)$args['titlelen'];
					if(isset($args['none']))$none=$args['none'];
					if($findtype=='next')$res='<?php echo qyk::getnextarticle($article,'.$extlink.','.$titlelen.',"'.$none.'");?>';
					else $res='<?php echo qyk::getprearticle($article,'.$extlink.','.$titlelen.',"'.$none.'");?>';
					$html=str_replace($reps,$res,$html);
				break;
				case 'art.mood':
				case 'mood':
					$args=self::tojson($tagcont);
					$icon=1;
					if(isset($args['mood']))$icon=(int)$args['mood'];
					$res='<div class="ui_expmood" id="tcz_expmood"></div><script type="text/javascript">PZ.ready({callback:function(){PZ.expmood({did:"tcz_expmood",icon:'.$icon.'})}});</script>';
					$html=str_replace($reps,$res,$html);
				break;
				case 'comment':
					$args=self::tojson($tagcont);
					$mod=1;
					$ht=30;
					$ht_on=100;
					$size=setup_comment_size;
					if(isset($args['ht']))$ht=(int)$args['ht'];
					if(isset($args['ht_on']))$ht_on=(int)$args['ht_on'];
					if(isset($args['comment']))$mod=(int)$args['comment'];
					if(isset($args['size']))$size=(int)$args['size'];
					$res='<div class="ui_comment" id="tcz_comment" status="no"></div><script type="text/javascript">PZ.ready({callback:function(){PZ.comment({log:"start",mod:'.$mod.',size:'.$size.',ht:'.$ht.',ht_on:'.$ht_on.'})}});</script>';
					$html=str_replace($reps,$res,$html);
				break;
				case 'web':
					$args=self::tojson($tagcont);
					$res='$web[\''.$args[$tagname].'\']';
					$html=str_replace($reps,'<?php echo '.$res.';?>',$html);
				break;
				case 'copyright':
					$html=str_replace($reps,'<?php echo urldecode(\'Powered%20by%20%3Ca%20target=_blank%20href=%22http://cms.qingyunke.com%22%20title=%22%E9%9D%92%E4%BA%91%E5%AE%A2CMS%22%3EQYKCMS%204.0%3C/a%3E\');?>',$html);
				break;
				case 'run':
					$html=str_replace($reps,'<?php echo \'Processed in <span id="qyk_runtime">...</span> second(s)，<span id="qyk_queries">...</span> queries，Memory：<span id="qyk_memory">...</span>kb，Cache：\'.goif($web[\'datacache_status\'],\'On\',\'Off\').\'<script>PZ.ready({callback:function(){$("#qyk_runtime").html(tczAppsui.runtime);$("#qyk_queries").html(tczAppsui.queries);$("#qyk_memory").html(tczAppsui.memory);}})</script>\';?>',$html);
				break;
				default:
					$html=str_replace($reps,'<?php echo '.$tagcont.';?>',$html);
				break;
				}
			}
		}
	$html=preg_replace('/([ ]?)\?\><\?php([ ]?)/','',$html);
	if($web['themeheader']&&$web['themeframe'])$html=self::addhtml('head').$html;
	if(setup_tool&&!$web['mobiletemp']&&$web['themeheader']&&$web['themeframe']==2){
		$html.='<script type="text/javascript">PZ.ready({delay:100,callback:function(){tczAppsui.tool=true;tczAppsui.tool_feedback='.goif(setup_tool_feedback,'true','false').';';
		if(setup_tool_customer){
			$html.='tczAppsui.tool_customer="<div class=\"tit_qq\"></div>';
			$datalist=self::getcustomer(1);
			foreach($datalist as $sort=>$val){
				$html.='<a href=\"http://wpa.qq.com/msgrd?v=3&uin='.$val['qqnum'].'&site=qq&menu=yes\" class=\"qq_on\" target=_blank>'.$val['name'].'</a>';
				}
			$html.='";';
			}
		if(setup_tool_skype){
			$html.='tczAppsui.tool_skype="<div class=\"tit_skype\"></div>';
			$datalist=self::getcustomer(2);
			foreach($datalist as $sort=>$val){
				$html.='<a href=\"javascript:alert(\''.$val['qqnum'].'\')\" class=\"skype_on\">'.$val['name'].'</a>';
				}
			$html.='";';
			}
		if(setup_tool_weixin)$html.='tczAppsui.tool_weixin="<div class=\"tit_wx\"></div><div class=\"wx\"><img src=\''.getfile('pic',setup_tool_weixin_img).'\'><br>'.setup_tool_weixin_num.'<br>（官方微信公众号）</div>";';
		if(setup_tool_tel){
			$html.='tczAppsui.tool_phone="<div class=\"tit_phone\"></div>';
			$telnum=explode(',',setup_tool_tel_num);
			foreach($telnum as $t)$html.='<a href=\"javascript:\" class=\"all_on\">'.$t.'</a>';
			$html.='";';
			}
		$html.='PZ.tool({});}});</script>';
		}
	createDirs($web['tempcache_path']);
	@$file=fopen($web['tempcache'],'w');
	if(!$file)tipmsg('缓存模板文件写入失败：<br>'.$web['tempcache'],true);
	fwrite($file,$html);
	fclose($file);
	if($web['datacache_status']){
		createDirs($web['datacache']);
		ob_start();
		require_once($web['tempcache']);
		$content=ob_get_contents();
		ob_end_clean();
		@$file=fopen($web['datacache'],'w');
		fwrite($file,$content);
		fclose($file);
		echo $content;
	}else{
		require_once($web['tempcache']);
		}
	echo self::addhtml('foot');
	}
function addhtml($atype){
	global $web,$tcz;
	switch($atype){
		case 'head':
			$html='<!DOCTYPE html>
<html lang="'.getlang('sys','header').'">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"><?php echo goif($web[\'mobiletemp\'],\'
<meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">\');?>
<title><?php echo $web[\'title\'];?></title>
<meta name="description" content="<?php echo $web[\'description\'];?>" />
<meta name="keywords" content="<?php echo $web[\'keyword\'];?>" />';
			if($web['themeframe']==2){
				$html.='<script type="text/javascript" src="/js/min.js?version=1.7.1"></script>
<script type="text/javascript" src="/js/lang/<?php echo $web[\'templang\'];?>.js?t=<?php echo setup_filerand;?>"></script>
<script type="text/javascript" src="/js/tczAppsui.js?t=<?php echo setup_filerand;?>"></script>
<link href="/images/style.css?t=<?php echo setup_filerand;?>" rel="stylesheet" type="text/css" />';
				}
			if($web['themeframe']==3){
				$html.='
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
<!--[if lt IE 9]>
<script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->';
				}
			if($web['themeframe']==4){
				$html.='
<link rel="stylesheet" href="/res/jquery.mobile/themes/default/jquery.mobile-1.4.5.min.css">
<script src="/res/jquery.mobile/jquery.min.js"></script>
<script src="/res/jquery.mobile/jquery.mobile-1.4.5.min.js"></script>';
				}
			if($web['themeframe_basic']){
				$html.='
<link href="/<?php echo setup_webfolder.$web[\'id\'].\'/\'.$web[\'tempfolder\'].\'/ui/style_global.css?t=\'.setup_filerand;?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo \'/\'.setup_webfolder.$web[\'id\'].\'/res/global\'.goif($web[\'templang\']!=\'cn\',\'_\'.$web[\'templang\']).goif($web[\'mobiletemp\'],\'_mobile\').\'.js?t=\'.setup_filerand;?>"></script>';
				}
			if(!$web['mobiletemp']&&setup_header_pc!='')$html.='
'.htmlspecialchars_decode(setup_header_pc);
			if($web['mobiletemp']&&setup_header_mobile!='')$html.='
'.htmlspecialchars_decode(setup_header_mobile);
			if(setup_favicon!='')$html.='
<link rel="icon" href="'.getfile('file',setup_favicon,'none',true).'" mce_href="'.getfile('file',setup_favicon,'none',true).'" type="image/x-icon">
<link rel="shortcut icon" type="image/x-icon" href="'.getfile('file',setup_favicon,'none',true).'" />';
			$html.='
<?php echo \'<script type="text/javascript">tczAppsui={path:"/",domain:"\'.str_replace(\'www.\',\'\',setup_weburl).\'",qykapi:"\'.setup_qyk_api.\'",appid:\'.setup_qyk_appid.\',lang:"\'.$web[\'templang\'].\'",webname:"\'.setup_shortname.\'",arglog:"\'.$tcz[\'log\'].\'",argdesc:"\'.$tcz[\'desc\'].\'",argid:\'.$tcz[\'id\'].\',argbcat:\'.$tcz[\'bcat\'].\',argscat:\'.$tcz[\'scat\'].\',arglcat:\'.$tcz[\'lcat\'].\',argpage:\'.$tcz[\'page\'].\',tool:false,tool_customer:"",tool_skype:"",tool_weixin:"",tool_phone:"",tool_feedback:false};</script>\';?>
</head><body>';
		break;
		case 'foot':
			global $db,$tcz,$cook,$tczruntime;
			//$err=error_get_last();
			//tipmsg($err,true);
			$tczruntime=round(microtime(true)-$tczruntime,6);
			$html='
<script type="text/javascript">try{if(typeof(tczAppsui)=="undefined"){var tczAppsui={}}}catch(e){var tczAppsui={}};tczAppsui.mobile='.goif($web['mobile'],'true','false').';tczAppsui.runtime="'.$tczruntime.'";tczAppsui.memory='.ceil(memory_get_usage()/1024).';tczAppsui.queries='.$db->querynum.';tczAppsui.time='.time().';tczAppsui.usestatic='.goif(setup_static,'true','false').';tczAppsui.login='.goif($cook['login'],'true','false').';tczAppsui.seartype="'.$tcz['seartype'].'";tczAppsui.cook={uid:'.$cook['uid'].',uname:"'.$cook['uname'].'",ucode:"'.$cook['ucode'].'"};</script>';
			if($web['themeframe']==3)$html.='
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>';
			$html.='
</body></html>';
		break;
		}
	return $html;
	}
function getcustomer($bcat=0,$size=0){
	global $web;
	$list=db_getlist('select * from '.tabname('customer').' where webid='.$web['id'].' and isok=0 and languages="'.$web['templang'].'"'.goif($bcat,' and bcat='.$bcat).' order by sort asc,id asc',$size);
	return $list;
	}
function getadvert($adtype=0,$mod='full',$size=0,$delay=4000,$width='100%',$height='100%',$btn='dot',$pos='center',$ani=5,$target=''){
	global $web;
	$list=db_getlist('select * from '.tabname('advert').' where webid='.$web['id'].' and status=1 and languages="'.$web['templang'].'"'.goif($adtype,' and adtype='.$adtype).' order by sort asc,id asc',$size);
	switch($mod){
		case 'list':
			return $list;
		break;
		case 'img':
			$res='';
			foreach($list as $sort=>$val){
				$res.='<img src="'.getfile('pic',$val['fileurl']).'" alt="'.$val['title'].'" link="'.$val['link'].'" other="'.$val['other'].'">';
				}
		break;
		case 'link':
			$res='';
			foreach($list as $sort=>$val){
				$res.='<a title="'.$val['title'].'" href="'.$val['link'].'" other="'.$val['other'].'"'.goif($target!='',' target="'.$target.'"').'><img src="'.getfile('pic',$val['fileurl']).'" alt="'.$val['title'].'"></a>';
				}
		break;
		default:
			$res='';
			foreach($list as $sort=>$val){
				$res.='<a title="'.$val['title'].'" href="'.$val['link'].'" other="'.$val['other'].'"'.goif($target!='',' target="'.$target.'"').'><img src="'.getfile('pic',$val['fileurl']).'" alt="'.$val['title'].'"></a>';
				}
			$bannerid='tcz_advert_'.rand(11111,99999);
			$res='<div class="ui_advert" id="'.$bannerid.'" style="width:'.$width.';height:'.$height.'">'.$res.'</div>
<script type="text/javascript">PZ.ready({callback:function(){PZ.banner({obj:$("#'.$bannerid.'"),delay:'.$delay.',btn:"'.$btn.'",pos:"'.$pos.'",animate:'.$ani.',target:"'.$target.'"})}});</script>';
		break;
		}
	return $res;
	}
function getlabel($id){
	global $web;
	$lab=db_getshow('label','*','webid='.$web['id'].' and dataid='.$id);
	if(!$lab)return setup_errortag;
	$cont=$lab['content'];
	if($cont!=''){
		if($lab['edtype']==2)$cont=getreset($cont);
		else $cont=nl2br($cont);
		}
	return $cont;
	}
function getmodtitle($bcat=0,$scat=0,$lcat=0){
	global $web;
	$cata=db_getone('module','title','webid='.$web['id'].' and classid='.goif($lcat,$lcat,goif($scat,$scat,$bcat)));
	return $cata;
	}
function modulelist($menutype=99,$mark='',$bcat='',$scat='',$size=0,$mod='list'){
	global $web,$tcz;
	$sql='select * from '.tabname('module').' where webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'" '.goif($menutype=='99','and menutype=1','and menutype='.$menutype).goif($mark!='',' and mark="'.$mark.'"').goif($bcat!='',' and bcat in('.$bcat.')').goif($scat!='',' and scat in('.$scat.')').' order by menutype asc,sort asc,id asc';
	$list=db_getlist($sql,$size);
	switch($mod){
		case 'full':
			$res='';
			foreach($list as $sort=>$val){
				$res.='<a title="'.$val['title'].'" class="scat'.goif($tcz['scat']==$val['classid'],' scat_on',' scat_out').'" href="'.qyk::modulelink($val,'url').'">'.$val['title'].'</a>';
				$list2=db_getlist('select * from '.tabname('module').' where webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'" and scat='.$val['classid'].' order by menutype asc,sort asc,classid asc',$size);
				$res2='';
				foreach($list2 as $sort2=>$val2){
					$res2.='<a title="'.$val2['title'].'" class="lcat'.goif($tcz['lcat']==$val2['classid'],' lcat_on',' lcat_out').'" href="'.qyk::modulelink($val2,'url').'">'.$val2['title'].'</a>';
					}
				$res.=goif($res2!='',$res2);
				}
			return $res;
		break;
		case 'list':
			$list2=array();
			if($menutype==99){
				foreach($list as $scat){
					array_push($list2,$scat);
					$lcat=db_getall('module','*','webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'" and scat='.$scat['classid'].' order by menutype asc,sort asc,classid asc',$size);
					if($lcat){
						foreach($lcat as $l)array_push($list2,$l);
						}
					}
			}else return $list;
			return $list2;
		break;
		}
	}
function modulelink($data,$mod='full',$extlink=true){
	switch($mod){
		case 'url':
			$link='log='.$data['mark'];
			if($data['scat'])$link.='&lcat='.$data['classid'];
			else if($data['bcat'])$link.='&scat='.$data['classid'];
			$res=goif($data['linkurl']!=''&&$extlink,$data['linkurl'],getlink($link));
		break;
		default:
			$res='<a title="'.$data['title'].'"'.goif($data['linkurl']!=''&&$extlink,' href="'.$data['linkurl'].'" target="_blank" rel="nofollow"',' href="'.getlink('log='.$data['mark'].goif($data['bcat']||$data['scat'],'&'.goif($data['bcat'],'scat','lcat').'='.$data['classid'])).'"').'>'.$data['title'].'</a>';
		break;
		}
	return $res;
	}
function optionlist($types='',$bcat=0,$size=0,$order=''){
	global $web;
	$order_text='sort desc,id desc';
	if($order!='')$order_text=$order;
	$sql='select * from '.tabname('select').' where webid='.$web['id'].goif($types!='',' and types="'.$types.'"').goif($bcat,' and bcat='.$bcat).' order by '.$order_text;
	$list=db_getlist($sql,$size);
	return $list;
	}
function feedbacklist($types=1,$size=10,$ispage=true,$pagebtn=2,$mod='full',$contlen=36,$order=''){
	global $web,$tcz;
	$order_text='time_add desc,id desc';
	if($order!='')$order_text=$order;
	if($web['list_page'])$ispage=false;
	$sql='select * from '.tabname('feedback').' where webid='.$web['id'].' and isok=0';
	if($types==1)$sql.=' and dataid=0 order by '.$order_text;
	else if($types==2)$sql.=' and dataid>0 order by '.$order_text;
	if($ispage){
		$list=db_getpage($sql,$size,$tcz['page'],'',goif($web['mobiletemp'],'mobile'),$pagebtn);
		$web['list_record']=$list['num'];
		$web['list_page']=$list['page'];
		$datalist=$list['list'];
	}else{
		$datalist=db_getlist($sql,$size);
		}
	if($mod=='list')return $datalist;
	$res='<ul class="ui_bookitem">';
	foreach($datalist as $val){
		$res.='<li><div class="bookitem_top"><div class="bookitem_name">'.$val['name'].'：</div><div class="bookitem_time">'.getimes($val['time_add']).'</div></div><div class="bookitem_cont">'.tipshort(getcomment($val['content'],2),$contlen).'</div></li>';
		}
	$res.='</ul>';
	return $res;
	}
function getclassname($art,$mod='full'){
	global $web;
	if(!isset($art))return setup_errortag;
	$classid=$art['bcat'];
	if($art['scat'])$classid=$art['scat'];
	if($art['lcat'])$classid=$art['lcat'];
	$class=db_getshow('module','*','webid='.$web['id'].' and classid='.$classid);
	if($class){
		if($mod=='full')return qyk::modulelink($class);
		return $classname;
		}
	else return '';
	}
function speciallist($mark='',$ispage=true,$pagebtn=2,$auto=true,$bcat=0,$scat='',$lcat='',$size=0,$star=0,$word='',$order='',$searfie='title'){
	global $web,$tcz;
	$order_text='sort desc,time_add desc,dataid desc';
	if($order!='')$order_text=$order;
	$searsql='';
	$seartype='title';
	if($web['list_page']!='')$ispage=false;
	if($ispage){
		if($word=='')$word=$tcz['word'];
		if($word!=''){
			if($tcz['seartype']!=''){
				if(strstr(','.$searfie.',',','.$tcz['seartype'].','))$seartype=$tcz['seartype'];
				}
			if(strstr($word,',')){
				$wlist=explode(',',$word);
				foreach($wlist as $w)$searsql.=goif($searsql!='',' or ').'LOCATE("'.$w.'",`'.$seartype.'`)>0';
				$searsql=' and ('.$searsql.')';
				}else $searsql=' and LOCATE("'.$word.'",`'.$seartype.'`)>0';
			}
		if(!$size)$size=$web['list_size'];
		if(!$bcat&&$tcz['bcat']&&$mark==''&&$auto)$bcat=$tcz['bcat'];
		if($scat==''&&$tcz['scat']&&$auto)$scat=$tcz['scat'];
		if($lcat==''&&$tcz['lcat']&&$auto)$lcat=$tcz['lcat'];
		$sql='select * from '.tabname('special').' where webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'"'.goif($star,goif($star==9,' and star>0',' and star='.$star)).goif($mark!='',' and mark="'.$mark.'"').goif($bcat,' and bcat='.$bcat).goif($scat!='',' and scat in('.$scat.')').goif($lcat!='',' and lcat in('.$lcat.')').goif($searsql!='',$searsql).' order by '.$order_text;
		$list=db_getpage($sql,$size,$tcz['page'],'',goif($web['mobiletemp'],'mobile'),$pagebtn);
		$web['list_record']=$list['num'];
		$web['list_page']=$list['page'];
		return $list['list'];
	}else{
		$sql='select *,if(time_top>'.time().',1,0) as xu from '.tabname($tname).' where webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'"'.goif($star,goif($star==9,' and star>0',' and star='.$star)).goif($mark!='',' and mark="'.$mark.'"').goif($bcat,' and bcat='.$bcat).goif($scat!='',' and scat in('.$scat.')').goif($lcat!='',' and lcat in('.$lcat.')').goif($word!='',' and LOCATE("'.$word.'",`'.$seartype.'`)>0').' order by '.$order_text;
		$list=db_getlist($sql,$size);
		return $list;
		}
	}
function articlelist($mark='',$ispage=true,$pagebtn=2,$auto=true,$bcat=0,$scat='',$lcat='',$size=0,$star=0,$word='',$order='',$searfie='title'){
	global $web,$tcz;
	if($mark=='special'){
		if(!$size)$size=$web['list_size'];
		$sql='select * from '.tabname('special_list').' where webid='.$web['id'].' and isdel=0 and special_id='.$tcz['id'].' order by dataid desc';
		$spelist=db_getpage($sql,$size,$tcz['page'],'',goif($web['mobiletemp'],'mobile'),$pagebtn);
		$list=array();
		$web['list_record']=$spelist['num'];
		$web['list_page']=$spelist['page'];
		foreach($spelist['list'] as $spe){
			$tname='article';
			if($spe['modtype']>10)$tname.='_'.$web['id'].'_'.$spe['modtype'];
			$art=db_getshow($tname,'*','webid='.$web['id'].' and isok=0 and dataid='.$spe['dataid']);
			array_push($list,$art);
			}
		return $list;
		}
	$order_text='xu desc,sort desc,time_add desc,dataid desc';
	if($order!='')$order_text=$order;
	$tname='article';
	$modtype=db_getone('module','modtype','webid='.$web['id'].goif($bcat,' and classid='.$bcat).goif($mark!='',' and mark="'.$mark.'"'));
	if($modtype>10)$tname.='_'.$web['id'].'_'.$modtype;
	$searsql='';
	$seartype='title';
	if($web['list_page']!='')$ispage=false;
	if($ispage){
		if($word=='')$word=$tcz['word'];
		if($word!=''){
			switch($tcz['seartype']){
				case 'date':
					$t1=strtotime($word.' 00:00:00');
					$t2=strtotime($word.' 23:59:59');
					if(empty($t1)){
						tipmsg('非法的搜索关键词：'.$word,true);
						}
					$searsql=' and time_add>='.$t1.' and time_add<='.$t2;
				break;
				default:
					if($tcz['seartype']!=''){
						if(strstr(','.$searfie.',',','.$tcz['seartype'].','))$seartype=$tcz['seartype'];
						}
					if(strstr($word,',')){
						$wlist=explode(',',$word);
						foreach($wlist as $w)$searsql.=goif($searsql!='',' or ').'LOCATE("'.$w.'",`'.$seartype.'`)>0';
						$searsql=' and ('.$searsql.')';
					}else $searsql=' and LOCATE("'.$word.'",`'.$seartype.'`)>0';
				break;
				}
			}
		if(!$size)$size=$web['list_size'];
		if(!$bcat&&$tcz['bcat']&&$mark==''&&$auto)$bcat=$tcz['bcat'];
		if($scat==''&&$tcz['scat']&&$auto)$scat=$tcz['scat'];
		if($lcat==''&&$tcz['lcat']&&$auto)$lcat=$tcz['lcat'];
		$sql='select *,if(time_top>'.time().',1,0) as xu from '.tabname($tname).' where webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'"'.goif($star,goif($star==9,' and star>0',' and star='.$star)).goif($mark!='',' and mark="'.$mark.'"').goif($bcat,' and bcat='.$bcat).goif($scat!='',' and scat in('.$scat.')').goif($lcat!='',' and lcat in('.$lcat.')').goif($searsql!='',$searsql).' order by '.$order_text;
		$list=db_getpage($sql,$size,$tcz['page'],'',goif($web['mobiletemp'],'mobile'),$pagebtn);
		$web['list_record']=$list['num'];
		$web['list_page']=$list['page'];
		return $list['list'];
	}else{
		$sql='select *,if(time_top>'.time().',1,0) as xu from '.tabname($tname).' where webid='.$web['id'].' and isok=0 and '.goif($web['mobiletemp'],'mobile','computer').'=0 and languages="'.$web['templang'].'"'.goif($star,goif($star==9,' and star>0',' and star='.$star)).goif($mark!='',' and mark="'.$mark.'"').goif($bcat,' and bcat='.$bcat).goif($scat!='',' and scat in('.$scat.')').goif($lcat!='',' and lcat in('.$lcat.')').goif($word!='',' and LOCATE("'.$word.'",`'.$seartype.'`)>0').' order by '.$order_text;
		$list=db_getlist($sql,$size);
		return $list;
		}
	}
function articlelink($art,$mod='full',$extlink=true,$weburl=false,$titlelen=0,$word='',$target=''){
	switch($mod){
		case 'color':
			$res=goif($art['time_color']!='',$art['time_color'],'inherit');
		break;
		case 'url':
			$res=goif($art['linkurl']!=''&&$extlink,$art['linkurl'],goif($weburl,'http://'.setup_weburl).getlink('log='.$art['mark'].'&id='.$art['dataid']));
		break;
		default:
			$title=goif($titlelen,tipshort($art['title'],$titlelen),$art['title']);
			if($word!='')$title=str_replace($word,'<span class="red">'.$word.'</span>',$title);
			$res='<a title="'.$art['title'].'"'.goif($art['time_color']!='',' style="color:'.$art['time_color'].'"').goif($art['linkurl']!=''&&$extlink,' href="'.$art['linkurl'].'" target="_blank" rel="nofollow"',' href="'.getlink('log='.$art['mark'].'&id='.$art['dataid']).'"'.goif($target=='new',' target=_blank')).'>'.$title.'</a>';
		break;
		}
	return $res;
	}
function getprearticle($article,$extlink=true,$titlelen=0,$none='没有上一篇了'){
	$tname='article';
	if($article['modtype']>10)$tname.='_'.$article['webid'].'_'.$article['modtype'];
	$art=db_getshow($tname,'*','webid='.$article['webid'].' and isok=0 and bcat='.$article['bcat'].' and time_add<'.$article['time_add'].' order by sort desc,time_add desc,id desc');
	if($art)$res=self::articlelink($art,$mod='full',$extlink=true,$weburl=false,$titlelen=0);
	else $res=$none;
	return $res;
	}
function getnextarticle($article,$extlink=true,$titlelen=0,$none='没有下一篇了'){
	$tname='article';
	if($article['modtype']>10)$tname.='_'.$article['webid'].'_'.$article['modtype'];
	$art=db_getshow($tname,'*','webid='.$article['webid'].' and isok=0 and bcat='.$article['bcat'].' and time_add>'.$article['time_add'].' order by sort asc,time_add asc,id asc');
	if($art)$res=self::articlelink($art,$mod='full',$extlink=true,$weburl=false,$titlelen=0);
	else $res=$none;
	return $res;
	}
function getsearch($mark='',$searfie='title',$width=290,$sel=true){
	global $web,$tcz;
	$width_word=$width-122-40;
	if($sel){
		$sql='webid='.$web['id'].' and isok=0 and menutype=0 and ((modtype>1 and modtype<8) or modtype>10)';
		if($mark!=''){
			$mark2='"'.str_replace(',','","',$mark).'"';
			$sql.='and mark in('.$mark2.')';
			}
		$mark.=' order by sort asc,classid asc'; 
		$searmod=db_getall('module','mark,title',$sql);
		$searhtml='';
		foreach($searmod as $val){
			$searhtml.='<option value="'.$val['mark'].'"'.goif($tcz['log']==$val['mark'],' selected').'>'.$val['title'].'</option>';
			}
		$searhtml='<select id="qyk_sear_mark">'.$searhtml.'</select>';
	}else{
		$width_word+=122;
		if($mark==''||strstr($mark,','))tipmsg('搜索标签{:search}，如果设置参数sel=false时，search参数必须设置且只能设置一个，如 {:search="news",sel=false}',true);
		$searhtml='<input type="hidden" id="qyk_sear_mark" value="'.$mark.'">';
		}
	$searhtml='<div class="ui_search" style="width:'.$width.'px">'.$searhtml.'<input type="hidden" id="qyk_sear_type" value="'.$searfie.'"><input style="width:'.($width_word).'px" maxlength="30" placeholder="'.getlang('search','tips').'" value="'.$tcz['word'].'" class="sear_word" type="text" id="qyk_sear_word"><a class="sear_but" href="javascript:" onclick="PZ.search({log:\'start\'})">搜索</a></div><script>PZ.ready({callback:function(){PZ.search({log:"load",mark:"'.$mark.'"})}})</script>';
	return $searhtml;
	}
function addpos($title,$link='',$column=true,$keyword=true,$locamark='{_LOCAMARK_}'){
	global $web;
	if($link!='')$web['location'].=$locamark.'<a href="'.getlink($link).'" title="'.$title.'">'.tipshort($title,10).'</a>';
	$web['title']=$title.'-'.$web['title'];
	if($keyword)$web['keyword']=$title.','.$web['keyword'];
	if($column)$web['column']=$title;
	}
function tojson($str){
	$str='{'.$str.'}';
	$data=preg_replace('/^{([a-z0-9]+)\.([a-z0-9]+)=/i','{$1_$2=',$str);
	$data=preg_replace('/([{\,]{1})(([a-zA-Z0-9_]+))=/i','$1"$2":',$data);
	$jn=json_decode($data,true);
	return $jn;
	}
}
?>