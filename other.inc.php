<?php
//用來輸出切換其他程式的路徑
//要把檔案放到程式跟目錄的images夾下

/*-----------設定區-------------------*/

include_once('check.inc.php');
//切換到
function other(){
	global $check;
	$return='';
	if($check->isMIS()){
	$return.= "<img class='btn btn20' title='教師管理'onclick=jumpto('ti.php')
									src='".YAOH_IMAGES_URL."ti.png'>";
	$return.= "<img class='btn btn20' title='檔案交換' onclick=jumpto('upload.php')
									src='".YAOH_IMAGES_URL."upload_task.png'>";
	$return.= "<img class='btn btn20' title='IP管理' onclick=jumpto('ip.php')
									src='".YAOH_IMAGES_URL."ip.png'>";
	$return.= "<img class='btn btn20' title='設備管理' onclick=jumpto('equip.php')
									src='".YAOH_IMAGES_URL."equip.png'>";
	$return.= "<img class='btn btn20' title='大橋之光' onclick=jumpto('light.php')
									src='".YAOH_IMAGES_URL."light.png'>";
	$return.= "<img class='btn btn20' title='RSS教材建構系統' onclick=jumpto('rsstts.php')
									src='".YAOH_IMAGES_URL."rsstts.png'>";
	$return.= "<img class='btn btn20' title='調查與選舉系統' onclick=jumpto('poll.php')
									src='".YAOH_IMAGES_URL."fillit.png'>";
	
}else{

	$return.= "<img class='btn btn20' title='檔案交換' onclick=jumpto('upload.php')
									src='".YAOH_IMAGES_URL."upload_task.png'>";
}
	return $return;
}
?>