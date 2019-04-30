<?php
$args=db_getall('config','*','webid=1 order by id asc');
$arr=array();
foreach($args as $v){
	$v=deltable($v,'id');
	array_push($arr,$v);
	}
$data=serialize($arr);
$res='<div class="win_ajax ajax_user"><div class="ajax_content"><textarea class="tex" style="height:380px">'.$data.'</textarea></div></div>';
ajaxreturn(0,$res);