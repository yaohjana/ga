<?php

function addJquery($code){
	return "<script type='text/javascript'>$(function(){".$code."})</script>"; 
}
function addJava($content,$src=null){
	return "<script type='text/javascript'".(($src<>null)?"src=".$src:'').">".$content."</script>"; 
}
function addCss($css,$src=null){
	if($src<>null){
		return "<link rel='stylesheet' href='$src' type='text/css'/>";
		}else{
		return "<style type='text/css'>$css</style>"; 
	}
}


//處裡加上slash的問題
function help_MQ(){
	// echo 'help_MQinghelp_MQinghelp_MQinghelp_MQinghelp_MQinghelp_MQinghelp_MQing';
		if (get_magic_quotes_gpc()){
			function stripslashes_deep($value){
				$value = is_array($value) ? array_map('stripslashes_deep',$value) : stripslashes($value);
				return $value;
			}
			$_POST = array_map('stripslashes_deep', $_POST);
			$_GET = array_map('stripslashes_deep', $_GET);
			$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	}
}
//處理XSS的問題
//Method:xss_protect
//Referenced:http://jstiles.com/Blog/How-To-Protect-Your-Site-From-XSS-With-PHP
//Purpose:過濾可能使用XSS(cross-site scripting attacks)的字串
//@param $data 該字串
//@param $strip_tags 設定為true可以去除所有html標籤
//@ allowed_tags 允許的標籤清單
//return:傳回過濾過的字串
function xss_protect($data, $strip_tags = false, $allowed_tags = "") {
    if($strip_tags) {
        $data = strip_tags($data, $allowed_tags . "<b>");
    }
    if(stripos($data, "script") !== false) {
        $data = str_replace("script","scr<b></b>ipt", htmlentities($data, ENT_QUOTES,"UTF-8"));
    } else {
        $data = htmlentities($data, ENT_QUOTES,"UTF-8");
    }
    return $data;
}

function redirect($url,$str=null)
{
	echo "<script>";
	if($str<>null)echo "alert('{$str}');";
	echo "location.href='{$url}';";
	echo "</script>";
    exit;
}
function reload($str=null){
	redirect($_SERVER['HTTP_REFERER'],$str);
}

//擴充功能(限制IP)
$limit_ip[]="120.116.36";
$limit_ip[]="120.116.37";
$limit_ipv6="2001:288:75E4";
//$limit_ip[]="114.33.235.40";
//加上登入校內文件也可以閱讀

function in_school($errmsg='本功能限制校內使用'){
global $limit_ip,$limit_ipv6;
$IP=$_SERVER["REMOTE_ADDR"];
$IP_patten=substr($IP,0,10);
if(in_array($IP_patten,$limit_ip) or stristr($IP,$limit_ipv6) or isset($_SESSION['in_school']) or $_SESSION['l_id']){
return true;
}else{
echo "<script>alert('{$errmsg}');
	history.go(-1);
	</script>";
exit;
}
}

function in_school_bool(){
global $limit_ip,$limit_ipv6;
$IP=$_SERVER["REMOTE_ADDR"];
$IP_patten=substr($IP,0,10);

if(in_array($IP_patten,$limit_ip) or stristr($IP,$limit_ipv6) or isset($_SESSION['in_school'])){
return true;
}else{
return false;
}
}
//取得使用者ip
function get_true_ip(){
	if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
		$myip = $_SERVER['REMOTE_ADDR'];  
	} else {  
		$myip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);  
		$myip = $myip[0];  
	}
	return $myip;
}
function redirect_self($waiting=null){
	if($waiting<>null){
	return addJava("setTimeout(function(){
		location.href='{$_SERVER['PHP_SELF']}';
		},$waiting)");
	}else{
	return addJava("
	location.href='{$_SERVER['PHP_SELF']}';
	");
	}
}

function goback($to=-1,$str=null,$waiting=null){
	echo "<script language=javascript>";
	if($str<>null){
		echo "alert('{$str}');";
	};
	if($waiting<>null){
		echo "sleep($waiting);";
	}
		echo "history.go({$to});";
	echo "</script>";
    exit;
}
function alert($str){
echo addJava("alert('{$str}');");
}
//增加屬性用工具
function addAttributes( $attr_ar ) {
        $str = ''; 
        // check minimized attributes 
        $min_atts = array('checked', 'disabled', 'readonly', 'multiple');
        foreach( $attr_ar as $key=>$val ) { 
            if ( in_array($key, $min_atts) ) { 
                if ( !empty($val) ) {  
                    $str .= " $key=\"$key\""; 
                } 
            } else { 
                $str .= " $key=\"$val\""; 
            } 
        }
        return $str; 
    }
//將東西包到某div中
function addDiv($content,$divId=null,$divClass=null,$style=null,$attr_ar = array()){
	return "<div ".
		(($divId<>null)?" id=$divId ":'').
		(($divClass<>null)?" class=$divClass ":'').
		(($style<>null)?" style=$style ":'').
		(($attr_ar)?addAttributes($attr_ar):'').
		">".$content."</div>";
}
//將東西包到某span中
function addSpan($content,$spanId=null,$spanClass=null,$style=null,$attr_ar = array()){
	return "<span ".
		(($spanId<>null)?" id=$spanId ":'').
		(($spanClass<>null)?" class=$spanClass ":'').
		(($style<>null)?" style=$style ":'').
		(($attr_ar)?addAttributes($attr_ar):'').
		">".$content."</span>";
}
//新增圖片
function addImage($src,$width=null,$height=null,$style=null,$attr_ar = array()){
	return "<image ".
		' src='.$src.
		(($width<>null)?" width=$width ":'').
		(($height<>null)?" height=$height ":'').
		(($style<>null)?" style='$style' ":'').
		(($attr_ar)?addAttributes($attr_ar):'').
		" />";
}
function time_elapsed($secs){
	date_default_timezone_set("Asia/Taipei");
	$ret=array();
    $bit = array(
       // ' 年'        => $secs / 31556926 % 12,
       // ' 星期'        => $secs / 604800 % 52,
       // ' 天'        => $secs / 86400 % 7,
		' 天'        => $secs / 86400,
        ' 小時'        => $secs / 3600 % 24,
        ' 分鐘'    => $secs / 60 % 60,
       // ' 秒'    => $secs % 60
        );
       
    foreach($bit as $k => $v){
        if($v > 1)$ret[] =(int)$v.$k;
        if($v == 1)$ret[] =$v.$k;
	}
	if(count($ret)>0){
		array_splice($ret, count($ret)-1, 0);
		return '還有'.join(' ', $ret);
	}else{
		return false;
	}
	
}
?>
