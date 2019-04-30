<?php
$languages=arg('lang','post','url');
$advert=db_getshow('advert','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$advert){
	$advert=array(
		'languages'=>$languages,
		'adtype'=>1,
		'title'=>'',
		'link'=>'',
		'fileurl'=>'',
		'other'=>'',
		'status'=>1,
		'sort'=>1,
		'content'=>''
		);
	}
if($advert['languages']=='')ajaxreturn(1,'未知的语言版本');
$alladtype=1;
$topbcat=db_getone('advert','adtype','webid='.$website['webid'].' and languages="'.$advert['languages'].'" order by adtype desc');
if($topbcat){
	$alladtype=$topbcat;
	$topcount=db_count('advert','webid='.$website['webid'].' and languages="'.$advert['languages'].'" group by adtype')+0;
	if($topbcat==$topcount)$alladtype++;
	}
$adtypesel='<select id="post_adtype">';
for($i=1;$i<=$alladtype;$i++){
	$adtypesel.='<option value="'.$i.'"'.goif($advert['adtype']==$i,' selected').'>第 '.$i.' 组</option>';
	}
$adtypesel.='</select>';
$res='<div class="win_ajax ajax_user"><table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">标 题</td><td class="td2"><input type="hidden" id="post_languages" value="'.$advert['languages'].'"><input class="inp" type="text" id="post_title" value="'.$advert['title'].'"></td><td class="td1"><span class="help" title="在网站模板中调用广告是按组调用的，如幻灯片希望调用第1组的图片，可以插入标签：'.setup_prefix.'advert=1'.setup_suffix.'，标签中的数字1表示第1组">所属分组</span></td><td class="td2">'.$adtypesel.'</td></tr>
<tr><td class="td1">上传图片</td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'advert\'})" class="inp_up" id="post_fileurl">'.$advert['fileurl'].'</textarea></td><td class="td1">链接地址</td><td class="td2"><input class="inp" type="text" id="post_link" value="'.$advert['link'].'"></td></tr>
<tr><td class="td1"><span class="help" title="此项需要根据主题的需求而输入，通常没有特别说明时留空即可">附加参数</span></td><td class="td2"><input class="inp" type="text" id="post_other" value="'.$advert['other'].'"></td><td class="td1">显示顺序</td><td class="td2"><input style="width:140px;margin-right:10px" class="inp" type="text" id="post_sort" value="'.$advert['sort'].'"><select id="post_status"><option value="1">显示</option><option value="2"'.goif($advert['status']==2,' selected').'>隐藏</option></select></td></tr>
<tr><td class="td1">内部备注</td><td class="td5" colspan=3><input class="inp" style="width:701px" type="text" id="post_content" value="'.$advert['content'].'"></td></tr>
</table></div>';