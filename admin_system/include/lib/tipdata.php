<?php
$feedback_id=arg('feedback_id','post','int');
$feedback_news=0;
if(ispower($admin_group,'book_tips')){	//留言提醒
	$feedback_news=db_count('feedback','webid='.$website['webid'].' and time_view=0');
	if($feedback_news){
		$feedback_id=db_getone('feedback','id','webid='.$website['webid'].' and time_view=0 order by id desc');
		}
	}
$res='{"feedback_news":'.$feedback_news.',"feedback_id":'.$feedback_id.'}';
ajaxreturn(0,$res);