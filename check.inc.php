<?php
if(!isset($_SESSION))session_start();
//此檔案用來檢驗登入的權限
header('Content-Type: text/html; charset=utf-8');
include_once("config.php");
//驗證的function區塊
class opmycheck{
//檢查是否為超級管理員，若加上$user_id進一步檢查是否為擁有者
	public function isSuperAdmin($user_id=null){
		if($user_id==null){
			return (isset($_SESSION['yaoh2']['isSuperAdmin']) and $_SESSION['yaoh2']['isSuperAdmin']==true)?true:false;
		}else{
			return (isset($_SESSION['yaoh2']['isSuperAdmin']) and $_SESSION['yaoh2']['isSuperAdmin']==true and $user_id==$_SESSION['yaoh2']['UserID'])?true:false;
		}
	}
	//網管檢查
	public function isMIS($user_id=null){
		if($user_id==null){
			return (isset($_SESSION['yaoh2']['isMIS']) and $_SESSION['yaoh2']['isMIS']==true)?true:false;
		}else{
			return ($_SESSION['yaoh2']['isMIS'] and $user_id==$_SESSION['yaoh2']['UserID'])?true:false;

		}
	}
	public function isOurMan($user_id=null){
		//if(!isset($_SESSION['yaoh2']['SchoolCode']))exit;
		if($user_id==null){
			return (isset($_SESSION['yaoh2']['SchoolCode']) and $_SESSION['yaoh2']['SchoolCode']==SCHOOL_CODE);
		}else{
			return (isset($_SESSION['yaoh2']['SchoolCode']) and $_SESSION['yaoh2']['SchoolCode']==SCHOOL_CODE and $user_id==$_SESSION['yaoh2']['UserID'])?true:false;
		}
	}
	public function isAdmin($user_id=null){
		if($user_id==null){
			return (isset($_SESSION['yaoh2']['isAdmin']) and $_SESSION['yaoh2']['isAdmin']==true)?true:false;
		}else{
			return ($_SESSION['yaoh2']['isAdmin'] and $user_id==$_SESSION['yaoh2']['UserID'])?true:false;

		}
	}
	public function isUser($user_id=null){
		if($user_id==null){
			return (isset($_SESSION['yaoh2']['UserID']) and $_SESSION['yaoh2']['UserID']==true)?true:false;
		}else{
			return (isset($_SESSION['yaoh2']['UserID']) and $user_id==$_SESSION['yaoh2']['UserID'])?true:false;
		}

	}
	public function check($auth_id,$mypwd){
		//取得帳號密碼
		if(isset($auth_id)){
	//台南市驗證部分
			/*專用function*/
			function ob2ar($ob)
			{
				$_array = is_object($ob) ? get_object_vars($ob) : $ob;

				foreach ($_array as $key => $value) {
					$value = (is_array($value) || is_object($value)) ? ob2ar($value) : $value;
					$array[$key] = $value;
				}

				return $array;
			}
			function get_tn_user_arr($ob){
			  $arr=ob2ar($ob);
			  foreach($arr as $k=>$v){
				if($k=="Roles"){
				  foreach($v['UserRoles'] as $i=>$vv){
					//$all_roles[]=$vv['SchoolName']." ".$vv['RoleName'];組合校名與角色
					$all_roles[]=$vv['RoleName'];//單純角色
				  }
				  $user_arr['Roles']=implode(",",$all_roles);
				  $user_arr['OneRole']=(isset($all_roles[2])and $all_roles[2]<>null)?$all_roles[2]:((isset($all_roles[0])and $all_roles[0]<>null)?$all_roles[0]:'教師');
				  $user_arr['UserGroup']=$all_roles[0];
				}else{
				  $user_arr[$k]=$v;
				}
			  }
			  return $user_arr;
			 }
			 /*判斷區塊*/
			define('_TN_TOKEN','58948578-bad6-44f4-9d8f-39e4014f310d');
			$client = new SoapClient("http://epassport.tn.edu.tw/wsepassport/wslogin.asmx?WSDL");
			$ret=$client->GetUserRolesbyUserID(array("token"=>_TN_TOKEN,"UserID"=>$auth_id, "Password"=>$mypwd));
			if(is_object($ret)){
				$userDN=array();
				$user=get_tn_user_arr($ret->GetUserRolesbyUserIDResult->UserProfile);
				foreach($user as $tn_key=>$tn_value) {
					//  $userDN[$tn_key]=array(utf8_encode($tn_value));
					$userDN[$tn_key]=$tn_value;
				}
				//權限設定
					if($userDN['UserID']<>null){
					$_SESSION['yaoh2']['SchoolName']=$userDN['SchoolName'];
					$_SESSION['yaoh2']['SchoolCode']=$userDN['SchoolCode'];
					$_SESSION['yaoh2']['UserName']=$userDN['UserName'];
					//$_SESSION['yaoh2']['UserID']=substr(md5($userDN['UserID']),0,10);//使用者id
					$_SESSION['yaoh2']['UserID']=substr(md5($userDN['UserID']),0,30);
					$_SESSION['yaoh2']['Email']=$userDN['Email'];
					$_SESSION['yaoh2']['OneRole']=$userDN['OneRole'];
					$_SESSION['yaoh2']['Roles']=$userDN['Roles'];
					$_SESSION['yaoh2']['UserGroup']=$userDN['UserGroup'];
					if(strstr($userDN['Roles'],'組長') 
						or strstr($userDN['Roles'],'主任')
						or strstr($userDN['Roles'],'幹事')
						or strstr($userDN['Roles'],'護理')
						or strstr($userDN['Roles'],'校長'))
					{
						$_SESSION['yaoh2']['isAdmin']=true;
					}else{
						$_SESSION['yaoh2']['isAdmin']=false;
					}

					$_SESSION['yaoh2']['isMIS']=(strstr($userDN['Roles'],'資訊組長'))?true:false;
					
					$_SESSION['yaoh2']['isSuperAdmin']=($_SESSION['yaoh2']['SchoolCode']==SCHOOL_CODE and $_SESSION['yaoh2']['isAdmin'])?true:false;
					//權限別
					if($_SESSION['yaoh2']['isMIS']){
						$_SESSION['yaoh2']['PowerLevel']='網管(完全管理權)';
					}elseif($_SESSION['yaoh2']['isSuperAdmin']){
						$_SESSION['yaoh2']['PowerLevel']='本校行政同仁(擁有管理權)';
					}elseif($this->isOurMan()){
						$_SESSION['yaoh2']['PowerLevel']='本校同仁(可校內分享)';
					}else{
						$_SESSION['yaoh2']['PowerLevel']='南市教育夥伴';

					}
					return true;
					}else{
					return false;
					}
			}else{
				return false;
			}
	//台南市驗證部分end

	}
	}

public function echocheck($auth_id,$mypwd){
	if($this->check($auth_id,$mypwd)){
		return "<script>alert('歡迎{$_SESSION['yaoh2']['UserName']}老師使用本系統');</script>"; 
	}else{
		return "<script>alert('帳號或密碼錯誤');history.go(-1);</script>"; 
}
}
public function list_login_form(){
return <<<login
	<table class='table table-striped table-bordered'>
	<tr><th colspan=2>登入系統(請使用*學習護照帳號)</th></tr>
	<form name="form1" method="post" action="{$_SERVER['PHP_SELF']}?op=check">
	<tr><th>
	帳號：<input type="text" name="username"><br />
	</th></tr><tr><th>
	密碼：<input type="password" name="userpass"><br />
			  <input type="submit" name="Submit" value="登入">
	</th></tr>
	</form>
	</table>
	*通常是身分證字號
login;
}
}
$check=new opmycheck();
?>
