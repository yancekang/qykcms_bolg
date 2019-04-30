<?php
$languages=arg('lang','post','url');
$cus=db_getshow('customer','*','webid='.$website['webid'].' and id='.$tcz['id']);
if(!$cus){
	$cus=array(
		'languages'=>$languages,
		'bcat'=>1,
		'name'=>'',
		'pos'=>'客服',
		'head'=>'',
		'qqnum'=>'',
		'email'=>'',
		'phone'=>'',
		'sort'=>1,
		'isok'=>0
		);
	}
if($cus['languages']=='')ajaxreturn(1,'未知的语言版本');
$bcatarr=explode(',',setup_customer_bcat);
$bcatsel='<select id="post_bcat">';
foreach($bcatarr as $k=>$b){
	$bcatsel.='<option value="'.$k.'"'.goif($cus['bcat']==$k,' selected').'>'.$b.'</option>';
	}
$bcatsel.='</select>';
$res='<div class="win_ajax ajax_user"><table class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td1">姓名/称呼</td><td class="td2"><input type="hidden" id="post_languages" value="'.$cus['languages'].'"><input class="inp" type="text" id="post_name" value="'.$cus['name'].'"></td><td class="td1">上传头像</td><td class="td2"><textarea onfocus="this.blur()" placeholder="单击开始上传" onclick="uploadimg({log:\'start\',types:\'customer_head\'})" class="inp_up" id="post_head">'.$cus['head'].'</textarea></td></tr>
<tr><td class="td1">软件帐号</td><td class="td2"><input class="inp" type="text" id="post_qqnum" value="'.$cus['qqnum'].'"></td><td class="td1">职务岗位</td><td class="td2"><input class="inp" type="text" id="post_pos" value="'.$cus['pos'].'"></td></tr>
<tr><td class="td1">电子邮箱</td><td class="td2"><input class="inp" type="text" id="post_email" value="'.$cus['email'].'"></td><td class="td1">电话号码</td><td class="td2"><input class="inp" type="text" id="post_phone" value="'.$cus['phone'].'"></td></tr>
<tr><td class="td1">所属分组</td><td class="td2">'.$bcatsel.'</td><td class="td1">显示顺序</td><td class="td2"><input style="width:140px;margin-right:10px" class="inp" type="text" id="post_sort" value="'.$cus['sort'].'"><select id="post_isok"><option value="0">显示</option><option value="1"'.goif($cus['isok']==1,' selected').'>隐藏</option></select></td>
</tr>
</table></div>';