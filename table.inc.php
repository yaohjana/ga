<?php
class opmytable{
	private $table_str='';
	
	public function __construct($class_name='table'){
		$this->table_str.="<table class='$class_name'>";
	}
	public function addTitle($array=array()){
		$return='';
		if(count($array)>0){
			$return.="<tr>";
			foreach($array as $a){
				$return.="<th>$a</th>";
			}
			$return.="</tr>";
		}
		$this->table_str.=$return;
	}
	public function addRow($array=array()){
		$return='';
		if(count($array)>0){
			$return.="<tr>";
			foreach($array as $a){
				$return.="<td>$a</td>";
			}
			$return.="</tr>";
		}
		$this->table_str.=$return;
	}
		
	public function render() { 
			$this->table_str.="</table>"; 
			return $this->table_str;
		}
}
?>
