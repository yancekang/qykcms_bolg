<?php
$res='<div class="win_ajax ajax_edit">
<div class="ajax_imgview2"><img id="user_head_img" src="http://'.$website['setup_weburl'].getfile_admin('head',$admin_check['user_head'],200).'"></div>
<table id="admin_show_1" class="ajax_tablist" cellpadding="12" cellspacing="1">
<tr><td class="td6_1">当前帐号</td><td class="td6_2"><input type="text" class="inp_no" value="'.$tcz['admin'].'" readonly></td></tr>
<tr><td class="td6_1">上传头像</td><td class="td6_2"><textarea onfocus="this.blur()" onclick="uploadimg({log:\'start\',types:\'admin_head\'})" placeholder="建议大小200*200像素" class="inp_up" id="user_head">'.$admin_check['user_head'].'</textarea></td></tr>
</table></div>';