<?php
class dbloaded {
private static $db;
static function getDb(){
	if(!self::$db)self::$db=new mysql();
	return self::$db;
	}
}
class mysql{
var $querynum=0;
var $link;
private $dbhost=db_hostname;
private $dbname=db_database;
private $dbuser=db_username;
private $dbpw=db_password;
private $dbcharset = db_charset;
private $tiptype=1;
/*
* @param string $dbhost 主机名
* @param string $dbuser 用户
* @param string $dbpw 密码
* @param string $dbname 数据库名
* @param int $pconnect 是否持续连接
*/
function mysql($tiptype=0,$dbhost='',$dbuser='',$dbpw='',$dbname='',$pconnect=0){
	$dbhost==''?$dbhost=$this->dbhost:$dbhost;
	$dbuser==''?$dbuser=$this->dbuser:$dbuser;
	$dbpw==''?$dbpw=$this->dbpw:$dbpw;
	$dbname==''?$dbname=$this->dbname:$dbname;
	if($tiptype)$this->tiptype=$tiptype;
	if($pconnect){
		if(!$this->link = @mysql_pconnect($dbhost,$dbuser,$dbpw)){
		$this->halt('无法连接到mysql数据库，请检查数据库服务器、用户名、密码是否正确（error 101）');
		}
	}else{
		if(!$this->link=@mysql_connect($dbhost,$dbuser,$dbpw)){
			$this->halt('无法连接到mysql数据库，请检查数据库服务器、用户名、密码是否正确（error 102）');
			}
		}
	if($this->version()>'4.1'){
		if($this->dbcharset){
			mysql_query("SET character_set_connection=$this->dbcharset,character_set_results=$this->dbcharset,character_set_client=binary",$this->link);
			}
		if($this->version() > '5.0.1') {
			mysql_query("SET sql_mode=''",$this->link);
			}
		}
	if($dbname){
		$dblink=mysql_select_db($dbname,$this->link);
		if(!$dblink)$this->halt('无法打开数据库，请检查数据库 '.$dbname.' 是否已成功创建');
		}
	}
/**
* 选择数据库
* @param string $dbname
* @return
*/
function select_db($dbname){
	return mysql_select_db($dbname, $this->link);
	}
/**
* 取出结果集中一条记录
* @param object $query
* @param int $result_type
* @return array
*/
function fetch_array($query,$result_type=MYSQL_ASSOC) {
	return mysql_fetch_array($query, $result_type);
	}
/**
* 取出所有结果
* @param object $query
* @param int $result_type
* @return array
*/
function fetch_all($query, $result_type = MYSQL_ASSOC) {
	$result = array();
	$num = 0;
	while($ret = mysql_fetch_array($query, $result_type)){
		$result[$num++] = $ret;
		}
	return $result;
	}
/*
* 从结果集中取得一行作为枚举数组
* @param object $query
* @return array
*/
function fetch_row($query){
	$query = mysql_fetch_row($query);
	return $query;
	}
/*
* 返回查询结果
* @param object $query
* @param string $row
* @return mixed
*/
function result($query,$row){
	$query = @mysql_result($query, $row);
	return $query;
	}
/*
* 查询SQL
* @param string $sql
* @param string $type
* @return object
*/
function query($sql, $type = ''){
	$func=$type=='UNBUFFERED'&&@function_exists('mysql_unbuffered_query')?'mysql_unbuffered_query' : 'mysql_query';
	if(!($query=$func($sql, $this->link))&&$type!='SILENT'){
		if($this->tiptype==3)return false;
		else $this->halt('MySQL Query Error: ', $sql);
		}
	$this->querynum++;
	return $query;
	}
/*
* 取影响条数
* @return int
*/
function affected_rows(){
	return mysql_affected_rows($this->link);
	}
/*
* 返回错误信息
* @return array
*/
function error(){
	return (($this->link) ? mysql_error($this->link) : mysql_error());
	}
/*
* 返回错误代码
* @return int
*/
function errno(){
	return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}
/*
* 结果条数
* @param object $query
* @return int
*/
function num_rows($query){
	$query = mysql_num_rows($query);
	return $query;
	}
/*
* 取字段总数
* @param object $query
* @return int
*/
function num_fields($query) {
	return mysql_num_fields($query);
	}
/*
* 释放结果集
* @param object $query
* @return bool
*/
function free_result($query){
	return mysql_free_result($query);
	}
/**
* 返回自增ID
* @return int
*/
function insert_id(){
	return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
/**
* 从结果集中取得列信息并作为对象返回
* @param object $query
* @return object
*/
function fetch_fields($query){
	return mysql_fetch_field($query);
	}
/**
* 返回mysql版本
*
* @return string
*/
function version(){
	return mysql_get_server_info($this->link);
	}
/**
* 关闭连接
* @return bool
*/
function close() {
	return mysql_close($this->link);
	}
/**
* 输出错误信息
* @param string $message
* @param string $sql
*/
function chatip($ctype){
	$this->tiptype=(int)$ctype;
	}
function halt($message='',$sql=''){
	if($this->tiptype==2){
		ajaxreturn(9,'<div class="alertscro" onmouseover="onseltext=true" onmouseout="onseltext=false" style="width:250px;height:80px">'.$message.goif($sql!='','<br>'.$sql.'</div>'));
	}else if($this->tiptype==3){
		ajaxreturn(9,$message.goif($sql!='','<br>'.$sql));
	}else{
		tipmsg('<b>'.$message.'</b><br>'.$sql,true);
		}
	exit;
	}
}
function tabname($tname){
return db_tabfirst.$tname;
}
function db_run($sql){
global $db;
return $db->query($sql);
}
function db_del($tname,$where='',$affected=false){
global $db;
$db->query('delete from '.tabname($tname).goif($where!='',' where '.$where));
if($affected)return $db->affected_rows();
}
function db_getshow($tname,$tab,$where=''){
global $db;
$sql='select '.$tab.' from '.tabname($tname).goif($where!='',' where '.$where);
$rs=$db->fetch_array($db->query($sql));
return $rs;
}
function db_upshow($tname,$val,$where='',$affected=false){
global $db;
$db->query('update '.tabname($tname).' set '.$val.goif($where!='',' where '.$where));
if($affected)return $db->affected_rows();
}
function db_uparr($tname,$arr,$where='',$affected=false){
global $db;
$val='';
foreach($arr as $n=>$v){
	$v=str_replace('"','\"',$v);
	$val.=goif($val!='',',').$n.'="'.$v.'"';
	}
$sql='update '.tabname($tname).' set '.$val.goif($where!='',' where '.$where);
$db->query($sql);
if($affected)return $db->affected_rows();
}
function db_intoshow($tname,$tab,$val,$affected=false){
global $db;
$db->query('insert into '.tabname($tname).' ('.$tab.') values ('.$val.')');
if($affected)return $db->insert_id();
}
function db_intoarr($tname,$arr,$affected=false){
global $db;
$tab='';
$val='';
foreach($arr as $n=>$v){
	$v=str_replace('"','\"',$v);
	$tab.=goif($tab!='',',').$n;
	$val.=goif($val!='',',').'"'.$v.'"';
	}
$sql='insert into '.tabname($tname).' ('.$tab.') values ('.$val.')';
$db->query($sql);
if($affected)return $db->insert_id();
}
function db_count($tname,$where=''){
global $db;
$sql='select id from '.tabname($tname).goif($where!='',' where '.$where);
return $db->num_rows($db->query($sql));
}
function db_getpage($sql,$size=2,$page=1,$pagetext='',$module='full',$pagebtn=2){
global $db;
$res=array();
$res['num']=$db->num_rows($db->query($sql));
$start=$page*$size-$size;
$res['list']=$db->fetch_all($db->query($sql.' limit '.$start.','.$size));
$page=new pagelist();
$page->pn=$pagebtn;
$res['page']=$page->show($res['num'],$size=$size,$link='',$module,true,$pagetext);
return $res;
}
function db_getpage2($sql,$size=12,$page=1,$pagetext=''){
global $db;
$res=array();
$res['num']=$db->num_rows($db->query($sql));
if($size<1)$size=1;
$start=$page*$size-$size;
$sql.=' where id >=(SELECT id FROM '.tabname('member_moneylog').' LIMIT '.$start.',1) order by id desc LIMIT '.$size;
$res['list']=$db->fetch_all($db->query($sql));
$page=new pagelist();
$res['page']=$page->show($res['num'],$size=$size,$link='',$module='full',true,$pagetext);
return $res;
}
function db_getall($tname,$tab='*',$where='',$size=0){
global $db;
$sql='select '.$tab.' from '.tabname($tname).goif($where!='',' where '.$where);
$sql.=goif($size,' limit 0,'.$size);
$res=$db->fetch_all($db->query($sql));
return $res;
}
function db_getlist($sql,$size=0){
global $db;
$sql.=goif($size,' limit 0,'.$size);
$res=$db->fetch_all($db->query($sql));
return $res;
}
function db_getone($tname,$val,$where=''){
global $db;
$query=$db->query('select '.$val.' from '.tabname($tname).goif($where!='',' where '.$where));
$res=$db->result($query,0);
return $res;
}
?>