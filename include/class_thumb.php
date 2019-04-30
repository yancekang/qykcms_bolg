<?php
class photo {
var $img_old;			//原始图片资源句柄
var $img_new;			//新图片资源句柄
var $img_type;			//图片文件类型
var $img_quality=100;	//图片生成质量
var $img_sharp=0;		//锐化程度 默认0为不锐化
var $img_resize='no';	//yes:画布强制大小，非图象区域用白底填充

var $open_path;			//目标文件
var $save_path;			//保存路径

var $photo_w;			//原始图片宽度
var $photo_h;			//原始图片高度

var $thumb_w=0;			//新图图象宽度
var $thumb_h=0;			//新图图象高度
var $over_w;			//新图画布宽度
var $over_h;			//新图画布高度

var $over_end_w=0;		//从新图中再裁剪图片宽
var $over_end_h=0;		//从新图中再裁剪图片高

var $cut_x=0;			//裁剪起始X坐标
var $cut_y=0;			//裁剪起始X坐标
var $cut_w;				//裁剪宽度
var $cut_h;				//裁剪高度

var $mark_path="";		//水印图片 空表示无水印
var $mark_alpha;		//水印透明度
var $mark_xy;			//水印位置 1-9
var $mark_side;			//距离边多少象素
var $mark_min;			//小于此尺寸不打水印
//文件类型定义,及输出图片的函数
var $all_type = array(
	"jpg"=>array("output"=>"imagejpeg"),
	"gif"=>array("output"=>"imagegif"),
	"png"=>array("output"=>"imagepng"),
	"wbmp"=>array("output"=>"image2wbmp"),
	"jpeg"=>array("output"=>"imagejpeg")
	);
//设置原始图片路径 初始化
function setOpenpath($path){
	if(!file_exists($path))ajaxreturn(1,'原始图片不存在：'.$path);
	$this->img_type=getFiletype($path);
	if($this->img_type=='bmp'||$this->img_type=='png'){
		$mi=$this->readBMP($path);
		//$path=str_replace('.bmp','.jpg',$path);
		//echo $path;
		@imagejpeg($mi,$path);
		$this->img_type='jpg';
		//echo $path;
		};
	$src=file_get_contents($path);
	if(empty($src))die('图片源为空');
	$this->img_old=ImageCreateFromString($src);
	$this->open_path=$path;
	if($this->img_type=='png')$this->img_quality=9;
	$this->photo_w=$this->thumb_w=$this->cut_w=$this->over_w=imagesx($this->img_old);
	$this->photo_h=$this->thumb_h=$this->cut_h=$this->over_h=imagesy($this->img_old);
	}
//设置保存位置
function setSavepath($path){
	createDirs($path);
	$this->save_path=$path;
	}
//设置质量
function setImgquality($nums){
	$this->img_quality=$nums;
	}
//设置水印图片及位置，透明度
function setImgmark($markpath,$markxy=1,$markalpha=50,$markside=0,$markmin=100){
	$this->mark_path=$markpath;
	$this->mark_xy=$markxy;
	$this->mark_alpha=$markalpha;
	$this->mark_side=$markside;
	$this->mark_min=$markmin;
	}
//设置整张图片大小 按比例
function setImgsize($w=0,$h=0,$lw=0,$lh=0){
	if($this->thumb_w<$lw){
		$this->thumb_h=(int)$lw/$this->thumb_w*$this->thumb_h;
		$this->thumb_w=(int)$lw;
		}
	if($this->thumb_h<$lh){
		$this->thumb_w=(int)$lh/$this->thumb_h*$this->thumb_w;
		$this->thumb_h=(int)$lh;
		}
	if($this->thumb_w>$w&&$w>0){
		$this->thumb_h=(int)$w/$this->thumb_w*$this->thumb_h;
		$this->thumb_w=(int)$w;
		}
	if($this->thumb_h>$h&&$h>0){
		$this->thumb_w=(int)$h/$this->thumb_h*$this->thumb_w;
		$this->thumb_h=(int)$h;
		}
	if($this->img_resize=='yes'){
		if($w>0)$this->over_w=$w;
		if($h>0)$this->over_h=$h;
	}else{
		$this->setImgresize('no');
		}
	}
//yes: 当图象比画布小时，画布将强制大小，以白底填充多余位置
function setImgresize($type){
	$this->img_resize=$type;
	if($type=='no'){
		$this->over_w=$this->thumb_w;
		$this->over_h=$this->thumb_h;
		}
	}
//设置裁剪规格范围
function setCutsize($x=0,$y=0,$w=0,$h=0){
	$w=(int)$w;
	$h=(int)$h;
	if($w<=0)$w=$this->over_w;
	if($h<=0)$h=$this->over_h;
	$this->cut_x=(int)$x;
	$this->cut_y=(int)$y;
	$this->cut_w=$this->thumb_w=$this->over_w=$w;
	$this->cut_h=$this->thumb_h=$this->over_h=$h;
	}
//从结果图片中再次裁剪
function setCutsize_end($w=0,$h){
	$this->over_end_w=$w;
	$this->over_end_h=$h;
	}
//设置保存图片路径
function setImgnew($path){
	$this->img_new=$path;
	}
//设置锐化程度 默认0 不锐化 ，取值建议 0.6
function setImgsharp($sharp){
	$this->img_sharp=$sharp;
	}
//BMP处理函数
function readBMP($filename){
	if(!$f1=fopen($filename,"rb"))return FALSE;
	$FILE=unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset",fread($f1,14));
	if($FILE['file_type']!=19778)return FALSE;
	$BMP=unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.'/Vvert_resolution/Vcolors_used/Vcolors_important',fread($f1,40));
	$BMP['colors']=pow(2,$BMP['bits_per_pixel']);
	if($BMP['size_bitmap']==0)$BMP['size_bitmap']=$FILE['file_size']-$FILE['bitmap_offset'];
	$BMP['bytes_per_pixel']=$BMP['bits_per_pixel']/8;
	$BMP['bytes_per_pixel2']=ceil($BMP['bytes_per_pixel']);
	$BMP['decal']=($BMP['width']*$BMP['bytes_per_pixel']/4);
	$BMP['decal']-=floor($BMP['width']*$BMP['bytes_per_pixel']/4);
	$BMP['decal']=4-(4*$BMP['decal']);
	if($BMP['decal']==4)$BMP['decal']=0;

	$PALETTE=array();
	if($BMP['colors']<16777216){
		$PALETTE=unpack('V'.$BMP['colors'],fread($f1,$BMP['colors']*4));
		}
	$IMG=fread($f1,$BMP['size_bitmap']);
	$VIDE=chr(0);
	$res=imagecreatetruecolor($BMP['width'],$BMP['height']);
	$P=0;
	$Y=$BMP['height']-1;
	while($Y>=0){
		$X=0;
		while($X<$BMP['width']){
			if($BMP['bits_per_pixel']==24){
				$COLOR=unpack("V",substr($IMG,$P,3).$VIDE);
			}else if($BMP['bits_per_pixel']==16){
				$COLOR=unpack("n",substr($IMG,$P,2));
				$COLOR[1]=$PALETTE[$COLOR[1]+1];
			}else if($BMP['bits_per_pixel']==8){
				$COLOR=unpack("n",$VIDE.substr($IMG,$P,1));
				$COLOR[1]=$PALETTE[$COLOR[1]+1];
			}else if($BMP['bits_per_pixel']==4){
				$COLOR=unpack("n",$VIDE.substr($IMG,floor($P),1));
				if(($P*2)%2==0)$COLOR[1]=($COLOR[1]>>4);else $COLOR[1]=($COLOR[1]&0x0F);
				$COLOR[1]=$PALETTE[$COLOR[1]+1];
			}else if($BMP['bits_per_pixel']==1){
				$COLOR=unpack("n",$VIDE.substr($IMG,floor($P),1));
				if(($P*8)%8==0)$COLOR[1]=$COLOR[1]>>7;
				else if(($P*8)%8==1)$COLOR[1]=($COLOR[1]&0x40)>>6;
				else if(($P*8)%8==2)$COLOR[1]=($COLOR[1]&0x20)>>5;
				else if(($P*8)%8==3)$COLOR[1]=($COLOR[1]&0x10)>>4;
				else if(($P*8)%8==4)$COLOR[1]=($COLOR[1]&0x8)>>3;
				else if(($P*8)%8==5)$COLOR[1]=($COLOR[1]&0x4)>>2;
				else if(($P*8)%8==6)$COLOR[1]=($COLOR[1]&0x2)>>1;
				else if(($P*8)%8==7)$COLOR[1]=($COLOR[1]&0x1);
				$COLOR[1]=$PALETTE[$COLOR[1]+1];
			}else return FALSE;
			imagesetpixel($res,$X,$Y,$COLOR[1]);
			$X++;
			$P+=$BMP['bytes_per_pixel'];
		}
		$Y--;
		$P+=$BMP['decal'];
		}
	fclose($f1);
	return $res;
	}
//改写png-24图片支持alpha
function imagecopymerge_alpha($dst_im,$src_im,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$pct){
	$w=imagesx($src_im);
	$h=imagesy($src_im);
	$cut=imagecreatetruecolor($src_w, $src_h);
	imagecopy($cut,$dst_im,0,0,$dst_x,$dst_y,$src_w,$src_h);
	imagecopy($cut,$src_im,0,0,$src_x,$src_y,$src_w,$src_h);
	imagecopymerge($dst_im,$cut,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$pct);
	}
//主函数
function createImg(){
	//创建新图画布 并指定宽高
	//$this->img_new=imagecreatetruecolor($this->over_w,$this->over_h);
	$hb_w=$this->over_w;
	$hb_h=$this->over_h;
	if($this->over_end_w>0&&$this->over_end_w<$hb_w){
		$hb_w=$this->over_end_w;
		}
	if($this->over_end_h>0&&$this->over_end_h<$hb_h){
		$hb_h=$this->over_end_h;
		}
	$this->img_new=imagecreatetruecolor($hb_w,$hb_h);
	//填充画布背景色
	$white=ImageColorAllocate($this->img_new,255,255,255);
	imagefilledrectangle($this->img_new,0,0,$this->over_w,$this->over_h,$white);
	//计算出图象居中于画布中的坐标
	$over_x=(int)($this->over_w-$this->thumb_w)/2;
	$over_y=(int)($this->over_h-$this->thumb_h)/2;
    imagecopyresampled(
		$this->img_new,	//新图片资源
		$this->img_old,	//原始图片资源
		$over_x,		//新图显示的起始X坐标 
		$over_y,		//新图显示的起始Y坐标
		$this->cut_x,	//原始图片裁剪起始X坐标
		$this->cut_y,	//原始图片裁剪起始Y坐标
		$this->thumb_w,	//放置图片的区域大小
		$this->thumb_h,
		$this->cut_w,	//复制图片的区域大小
		$this->cut_h
		);
	//if($this->over_h_true>0)imagecopy($this->img_new,$this->img_new,0,0,0,0,480,360);
	//如果需要锐化 不建议频繁使用
	if($this->img_sharp>0){
		//$this->over_h=5;
		$cnt=0;
		for($x=1;$x<$this->over_w;$x++){
			for($y=1;$y<$this->over_h;$y++){
				$src_clr1=imagecolorsforindex($this->img_new,imagecolorat($this->img_new,$x-1,$y-1));
				$src_clr2=imagecolorsforindex($this->img_new,imagecolorat($this->img_new,$x,$y));
				$r=intval($src_clr2["red"]+$this->img_sharp*($src_clr2["red"]-$src_clr1["red"]));
				$g=intval($src_clr2["green"]+$this->img_sharp*($src_clr2["green"]-$src_clr1["green"]));
				$b=intval($src_clr2["blue"]+$this->img_sharp*($src_clr2["blue"]-$src_clr1["blue"]));
				$r=min(255,max($r,0));
				$g=min(255,max($g,0));
				$b=min(255,max($b,0));
				if(($DST_CLR=imagecolorexact($this->img_new, $r, $g, $b))==-1)$DST_CLR=imagecolorallocate($this->img_new,$r,$g,$b);
				$cnt++;
				if($DST_CLR==-1)die("color allocate faile at $x,$y($cnt).");
				imagesetpixel($this->img_new,$x,$y,$DST_CLR);
				}
			}
		}
	//如果需要添加水印
	if($this->mark_path!=''&&$hb_w>=$this->mark_min&&$hb_h>=$this->mark_min){
		ImageAlphaBlending($this->img_new,true);	//设置为混色模式
		$img_mark=imagecreatefrompng($this->mark_path);
		$markwh=array(imagesx($img_mark),imagesy($img_mark));
		$markxy=array($this->mark_side,$this->mark_side);	//左上角
		switch($this->mark_xy){
			case 2:$markxy=array(($hb_w-$markwh[0])/2,$this->mark_side);break;	//顶部居中
			case 3:$markxy=array($hb_w-$markwh[0]-$this->mark_side,$this->mark_side);break;		//右上角
			case 4:$markxy=array($this->mark_side,($hb_h-$markwh[1])/2);break;	//左居中
			case 5:$markxy=array(($hb_w-$markwh[0])/2,($hb_h-$markwh[1])/2);break;	//居中
			case 6:$markxy=array($hb_w-$markwh[0]-$this->mark_side,($hb_h-$markwh[1])/2);break;	//右居中
			case 7:$markxy=array($this->mark_side,$hb_h-$markwh[1]-$this->mark_side);break;//左下角
			case 8:$markxy=array(($hb_w-$markwh[0])/2,$hb_h-$markwh[1]-$this->mark_side);break;//底部居中
			case 9:$markxy=array($hb_w-$markwh[0]-$this->mark_side,$hb_h-$markwh[1]-$this->mark_side);break;//右下角
			};
		//imagecopymerge(背景图,水印图,水印位置x,水印位置y,0,0,水印宽,水印高,透明度0-100); png图片格式建议为 PNG-8
		$this->imagecopymerge_alpha($this->img_new,$img_mark,$markxy[0],$markxy[1],0,0,$markwh[0],$markwh[1],$this->mark_alpha);
		}
	//输出图片或保存
	$imgfunc=$this->all_type[$this->img_type]['output'];
	if(function_exists($imgfunc)){
		// 判断浏览器,若是IE就不发送头
		if(isset($_SERVER['HTTP_USER_AGENT'])){
		$ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
		if(!preg_match('/^.*MSIE.*\)$/i',$ua)){
			header("Content-type:".$this->img_type);
			}
		}
		$imgfunc($this->img_new,$this->save_path,$this->img_quality);
	}else{
		return false;
		}
	//释放
	if(imagedestroy($this->img_old)&&imagedestroy($this->img_new)){
		Return true;
	}else{
		Return false;
		}
	}
}
//$ps=new photo();
//$ps->setOpenpath('upload/test.jpg');		//原始文件路径 必需项
//$ps->setSavepath('upload/test2.jpg');		//设置保存路径，不设置此项将直接输出图片到页面
//$ps->setImgquality(80);					//设置图片质量，默认 100 此设置对png类型图片无效
//$ps->setCutsize(0,0,200,200);				//设置裁剪的起始x坐标、y坐标及裁剪区域宽、高，默认不裁剪
//$ps->setImgsize(200,200,0,0);				//设置图片最大宽高及最小宽高，默认原始大小
//$ps->setImgresize(false);					//默认true: 当图象比画布小时，画布将强制大小，以白底填充多余位置 false：画布自适应图象规格
//$ps->setImgsharp("0.6");					//设置锐化程度 默认0不锐化 ，建议 0.6，锐化时间较长
//$ps->setImgmark("images/mark.png",1,50,10);	//设置水印图片及水印位置 默认空无水印 第二个参数为位置1-9  第三个参数为水印透明度 第四个参数为距离边界象素
//$ps->createImg();							//主函数，必需项
?>