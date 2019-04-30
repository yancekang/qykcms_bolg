<?php
class calendar{
protected $_table;
protected $_link;
protected $_year;
protected $_month;
protected $_days;
protected $_dayofweek;
protected $_carr;
public function calendar($y=2015,$m=1,$carr=array(),$link=''){
	$this->_table=""; 
	$this->_year=$y; 
	$this->_month=$m;
	if ($this->_month>12){
		$this->_month=1; 
		$this->_year++; 
		}
	if ($this->_month<1){
		$this->_month=12; 
		$this->_year--; 
		}
	$this->_link=$link;
	$this->_carr=$carr;
	$this->_days = date("t",mktime(0,0,0,$this->_month,1,$this->_year));
	$this->_dayofweek = date("w",mktime(0,0,0,$this->_month,1,$this->_year));
	}
protected function _showTitle(){
	$w=getlang('calendar','week');
	$this->_table="<table>"; 
	$this->_table.="<tbody><tr class='cale_th'>"; 
	$this->_table .="<th class='week_0'>".$w[0]."</th>";
	$this->_table .="<th class='week_1'>".$w[1]."</th>";
	$this->_table .="<th class='week_1'>".$w[2]."</th>";
	$this->_table .="<th class='week_1'>".$w[3]."</th>";
	$this->_table .="<th class='week_1'>".$w[4]."</th>";
	$this->_table .="<th class='week_1'>".$w[5]."</th>";
	$this->_table .="<th class='week_6'>".$w[6]."</th>";
	$this->_table.="</tr>"; 
	}
protected function _showDate(){
	$nums=$this->_dayofweek+1;
	for ($i=1;$i<=$this->_dayofweek;$i++){
		$this->_table.="<td> </td>";
		}
	for($i=1;$i<=$this->_days;$i++){
		$isup=0;
		if(isset($this->_carr[$i]))$isup=$this->_carr[$i];
		if($nums%7==0){
			$this->_table.="<td ".goif($isup," title='".$isup."' class='cale_up' onclick=\"location.href='".getlink($this->_link.urlencode($this->_year.'-'.$this->_month.'-'.$i))."'\"").">$i</td></tr><tr class='cale_td'>";
		}else{
			$this->_table.="<td ".goif($isup," title='".$isup."' class='cale_up' onclick=\"location.href='".getlink($this->_link.urlencode($this->_year.'-'.$this->_month.'-'.$i))."'\"").">$i</td>";
			}
		$nums++;
		}
	$this->_table.="</tbody></table>"; 
	}
public function showCalendar(){ 
	$this->_showTitle();
	$this->_showDate();
	return $this->_table;
	}
}
?>