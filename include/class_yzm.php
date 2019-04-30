<?php
header("Content-type:image/png");
class authnum {
private $im;
private $im_num=4;
private $im_width=200; 
private $im_height=60;
private $im_text='123456789123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
private $im_ttf='/res/bleeding_cowboys.ttf';
private $im_size=14;
function create(){
	$im=@imagecreatetruecolor($this->im_width,$this->im_height) or die("建立图像失败");
	imagefill($im,0,0,imagecolorallocate($im,mt_rand(230,255),mt_rand(230,255),mt_rand(230,255)));
	for($i=0;$i<8;$i++){
		$line_color=imagecolorallocate($im,mt_rand(150,255),mt_rand(150,255),mt_rand(150,255));
		imageline($im,mt_rand(0,$this->im_width/2),mt_rand(0,$this->im_height),mt_rand($this->im_width/2,$this->im_width),mt_rand(0,$this->im_height),$line_color);
		}
	for($i=0;$i<60;$i++){
		$x1=rand(0,$this->im_width-1);$y1=rand(0,$this->im_height-1);
		imagesetpixel($im,$x1,$y1,imagecolorallocate($im,rand(120,250),rand(100,180),rand(120,250)));
		}
	$font_width=$this->im_width/$this->im_num;
	$text='';
	for($i=0;$i<$this->im_num;$i++){
		$imgstr=array(
			'text'=>$this->im_text{mt_rand(0,35)},
			'color'=>imagecolorallocate($im,rand(50,220),rand(30,180),rand(50,220)),
			'x'=>(int)($font_width*$i)+mt_rand(1,5),
			'y'=>mt_rand(5,$this->im_height-12)
			);
		imagettftext($im,$this->im_size,mt_rand(-20,20),$imgstr['x'],$imgstr['y']+10,$imgstr['color'],$this->im_ttf,$imgstr['text']);
		$text.=$imgstr['text'];
		}
	session_start();
	@$v_url=$_SERVER['HTTP_REFERER'];
	$im_vurl=$_SERVER['SERVER_NAME'];
	if(!strpos($v_url,$im_vurl))$_SESSION['session_yzm']=strtolower($text);
	else $_SESSION['session_yzm']=strtolower($text);
	imagepng($im);
	imagedestroy($im);
	}
}
$yzm=new authnum();
$yzm->create();
?> 