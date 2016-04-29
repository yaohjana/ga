<?php
/*-----------先載函數----------------*/
ob_start();
if(!isset($_SESSION))session_start();
/*-----------引入檔案區--------------*/
include_once('config.php');
include_once('function.php');
include_once('check.inc.php');
include_once('table.inc.php');

class ClassArrange{
	protected $student_csv;
	protected $class_csv;
	protected $student;
	protected $class;
	protected $studentNoClass;
	protected $class_title;
	protected function shuffle_assoc(&$list) { //亂數排序，並保留key值
	  if (!is_array($list)) return $list; 
	  $keys = array_keys($list); 
	  shuffle($keys); 
	  $random = array(); 
	  foreach ($keys as $key) 
		$random[$key] = $list[$key]; 

	  $list=$random; 
	}
	protected function doArrange(){//執行志願排列，重覆執行至排列完成
		$this->shuffle_assoc($this->student);//亂數排序
		foreach((array)$this->student as $a=>$b){
			if(empty($a))continue;
			$choice_class=array_shift($this->student[$a]);
		//若遇到空白或誤填給兩次機會
		if(empty($choice_class) or empty($this->class[$choice_class]))$choice_class=array_shift($this->student[$a]);
		if(empty($choice_class) or empty($this->class[$choice_class]))$choice_class=array_shift($this->student[$a]);
		//再不行就刪了這位學生，將學生列入沒選到清單
		if(empty($choice_class) or empty($this->class[$choice_class])){
			$this->studentNoClass[]=$a;
			unset($this->student[$a]);
			continue;
			}
		if(!isset($this->class[$choice_class]['student']))$this->class[$choice_class]['student']=array();
		if(count($this->class[$choice_class]['student'])<$this->class[$choice_class]['limit']){
			$this->class[$choice_class]['student'][]=$a;
			unset($this->student[$a]);
		}
		}
		if(count($this->student)==0)return true;
		return $this->doArrange();
	}
	protected function csv_to_table($csv){//將csv格式的文字轉換成表格，並且將第一列設定為標題列
		if(!$rows=$this->csv_to_array_assoc($csv))return false;
		$table=new opmytable();
		
		$table->addTitle($this->class_title=array_keys($rows[0]));
			foreach ($rows as $row) {
					$table->addRow($row);
			}
			return $table->render();
	}
	protected function csv_to_array_assoc($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n") {//將csv的文字轉換成陣列，且是assoc陣列
		if(empty($csv))return false;
		$return = array();
		$rows = explode($terminator,trim($csv)); 
		//標題陣列
		$title_row = array_shift($rows); 
		$key_array = explode($delimiter,trim($title_row));
		//資料部分
		$ColNum = count($key_array); 
		foreach ($rows as $row) {
				$values = str_getcsv($row,$delimiter,$enclosure,$escape); 
				if (!$values) $values = array_fill(0,$ColNum,null);//自動補齊空白
				for($i=0;$i<$ColNum;$i++){//自動補齊空白
					if(!isset($values[$i]) or empty($values[$i]))$values[$i]=null;
				}
				$ValNum=count($values);
				if($ValNum==$ColNum){//陣列長度相同才合併

					$return[] = array_combine($key_array,$values);
				}elseif($ValNum>$ColNum){//如果值比標題還多欄，則去掉沒有標題的值
					$values=array_slice($values,0,$ColNum);
					$return[] = array_combine($key_array,$values);
				}
		}
		return $return; 
	}
	protected function dataPreset($text,$title_col){//將前面n欄的資料做成key其他資料則存成陣列
		$rows = $this->csv_to_array_assoc($text);
		$studentArray=array();
		foreach($rows as $row){
				$key='';
			for($i=0;$i<$title_col;$i++){
				$key.=array_shift($row);
			}
			$studentArray[$key]=$row;
		}
		return $studentArray;
	}
	protected function xss_protect($data, $strip_tags = false, $allowed_tags = "") {//篩選惡意程式碼
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
	protected function iconv2big5($str){//CSV格式UTF8轉BIG5
		return iconv('utf-8','big5',$str);
	}
	public function setStudentData($data,$title_col=1){//設定學生志願
		if(empty($data))return false;
		$data=$this->xss_protect($data,true);
		$this->student=$this->dataPreset($data,$title_col);
		$this->student_csv=$data;
	}
	public function setClassData($data,$title_col=1){
		if(empty($data))return false;
		$data=$this->xss_protect($data,true);
		$this->class=$this->dataPreset($data,$title_col);
		$this->class_csv=$data;
	}
	public function renderStudentTable(){
		if(empty($this->student_csv))$this->setStudentData(readFileCSV('group_student.csv'),1);
		return $this->csv_to_table($this->student_csv);
	}
	public function renderClassTable(){
		if(empty($this->class_csv))$this->setClassData(readFileCSV('group_class.csv'),1);
		return $this->csv_to_table($this->class_csv);
	}
	public function renderResult(){
		$return='';
		if(empty($this->student_csv))$this->setStudentData(readFileCSV('group_student.csv'),1);
		if(empty($this->class_csv))$this->setClassData(readFileCSV('group_class.csv'),1);
		$this->doArrange();
		$countNoClass=count($this->studentNoClass);
		if($countNoClass>0)$return.= "<font color=red>恭喜您，配對已順利完成，關閉瀏覽器即可清除資料</font><div class='alert alert-block'><strong>沒選到課的同學，共有{$countNoClass}人：".join(',',$this->studentNoClass)."</strong></div>";
		$table=new opmytable();
		$table->addTitle(array("社團名稱","人數上限","選上人數","選上清單"));
		foreach((array)$this->class as $a=>$b){
			if(isset($b['student']) and count($b['student']>0) and $b['student']<>null){
				$table->addRow(array($a,'上限'.$b['limit'].'人','選上'.count($b['student']).'人',join(',',$b['student'])));
			}else{
				$table->addRow(array($a,'上限'.$b['limit'].'人','<font color=red>沒人選上</font>'));
			}
		}
			$return.= $table->render();

			return $return;
	}
	public function renderResultToCSV(){
			$this->renderResult();
			$crlf="\r\n";
			echo $this->iconv2big5("社團名稱,人數上限,選上清單").$crlf;
			header("Content-type: text/csv; charset=big5"); 
			header("Content-Disposition: attachment; filename=result.csv");
			foreach((array)$this->class as $a=>$b){
				if(isset($b['student']) and count($b['student']>0) and $b['student']<>null){
				echo	$this->iconv2big5("{$a},上限{$b['limit']}人,選上".count($b['student'])."人,".join(',',$b['student'])).$crlf;
				}else{
				echo	$this->iconv2big5("{$a},上限{$b['limit']}人,"."沒人選上").$crlf;
				}
			}

}
	public function getStudentTemplateCSV(){
		$crlf="\r\n";
		header("Content-type: text/csv; charset=big5"); 
		header("Content-Disposition: attachment; filename=student_template.csv");
		echo $this->iconv2big5("學生識別,志願一,志願二,志願三,志願四,志願五,志願六,志願七,志願八,志願九,志願十".$crlf."(請刪除此行)志願數量可以自行擴充或減少(/請刪除此行)".$crlf);
	}
	public function getClassTemplateCSV(){
		$crlf="\r\n";
		header("Content-type: text/csv; charset=big5"); 
		header("Content-Disposition: attachment; filename=class_template.csv");
		echo $this->iconv2big5("Subject,limit,student".$crlf."(請刪除此行)除了student欄必須維持空白之外，其他欄位務必填滿(/請刪除此行，注意第一欄標題請勿更改)");
	}
	// public function renderResultToCookie(){
		// if(empty($_COOKIE['result'])){
			// $result=$this->renderResult();
			// setcookie('result',$result,time()+3600);
		// }else{
			// $result=$_COOKIE['result'];
		// }
		// return $result;
	// }
	// public function renderResultToCookieReNew(){
			// $result=$this->renderResult();
			// setcookie('result',$result,time()+3600);
		// return $result;
	// }
}

function file_upload_form($type){//上傳表單
	require_once(YAOH_ROOT_PATH."form.inc.php");
	$return= "<h2>上傳資料請填妥下列資料</h2>";
	$form=new myform();
	$return.=$form->startForm("{$_SERVER['PHP_SELF']}?op=upload_do&type={$type}","post",'upload_form',array('enctype'=>'multipart/form-data', 'class'=>'myformclass', 'onsubmit'=>''));
	//表格創建
	require_once(YAOH_ROOT_PATH."table.inc.php");
	$table=new opmytable('table table-bordered');
	$table->addRow(array(
					'檔案上傳',
					$form->addInput('file', 'file_name', '', array('id'=>'file_name'))
					));
	$table->addRow(array('處理',
					"<input type='submit' name='Submit' value='上傳' onclick=sjfn('{$_SERVER['PHP_SELF']}?op=update&type=upload','show','upload_form')>"
					));
	$return.=$table->render();
	$return.=$form->endForm();
	return $return;
}
function upload_do($type){
		$file=$_FILES['file_name']['tmp_name'];
		$filename=str_replace(' ','_',$_FILES['file_name']['name']);
		$ext = strtolower(strrchr($filename,'.'));
		if($ext<>'.csv')exit('格式錯誤，請確認格式為csv');
		$new_name=session_id().'group_'.$type.$ext;
		$dest=YAOH_DATA.$new_name;
		if(move_uploaded_file($file,$dest))return true;
}
function readFileCSV($file_name){
	$dest=YAOH_DATA.session_id().$file_name;
	if(!$handle = @fopen($dest, "r"))return false;
	$return = '';
	if ($handle) {
		while (!feof($handle)) {
			$return.= fgets($handle, 10);
		}
		fclose($handle);
	}
	return iconv('big5','utf-8',$return);
}
function remove_result(){
	$file1='../../'.session_id().'group_student.csv';
	$file2='../../'.session_id().'group_class.csv';
	unlink($file1);
	unlink($file2);
	var_dump($file1);
	if(session_unset())return true;
}
/*-----------執行動作判斷區----------*/
$op=empty($_REQUEST['op'])?"":$_REQUEST['op'];
$type=empty($_REQUEST['type'])?"":$_REQUEST['type'];
$id=empty($_REQUEST['id'])?"":(int)$_REQUEST['id'];
if(!isset($ca))$ca=new ClassArrange();

switch($op){
 //權限控制
	case 'login':
		echo addJquery("$('#msg').append('123');").$check->list_login_form();
		exit;
	break;
	case 'check':
		include_once(YAOH_ROOT_PATH."check.inc.php");
		echo $check->echocheck($_POST['username'],$_POST['userpass']);
		reload();
		exit;
	break;
	case 'logout':
		header('Content-Type: text/html; charset=utf-8');
		session_destroy();
		$_SESSION=array();
    echo "<script>" .
           "alert('登出成功');" .
           "location.href='{$_SERVER['PHP_SELF']}';" .
           "</script>";
	break;
	case 'reset':
		if(remove_result())redirect($_SERVER['PHP_SELF']);
		exit;
	break;
case 'csv':
	$ca->renderResultToCSV();
	exit;
break;
case 'renew':

	echo $ca->renderResult();
	exit;
break;
case 'download':
	switch($type){
		case 'student':
			$ca->getStudentTemplateCSV();
			exit;
		break;
		case 'class':
			$ca->getClassTemplateCSV();
			exit;
		break;
	}
break;
case 'upload':
	switch($type){
		case 'student':
			echo file_upload_form('student');
			exit;
		break;
		case 'class':
			echo file_upload_form('class');
			exit;
		break;
	}
break;
case 'upload_do':
	switch($type){
		case 'student':
			if(upload_do('student')){
				redirect($_SERVER['PHP_SELF']);
			}
			exit;
		break;
		case 'class':
			if(upload_do('class')){
				redirect($_SERVER['PHP_SELF']);
			}
			exit;
		break;
	}
break;
default:

	include_once('tab.inc.php');
	$tab=new opmytab();
	$tab->addNav('tab_student','志願表step1',true);
	$tab_student="<div id='tab_student'><h1>志願表</h1><a href='{$_SERVER['PHP_SELF']}?op=download&type=student' class='btn btn-info'  title='下載範本檔'>step1.a下載範本檔</a><a onclick=sjgn('{$_SERVER['PHP_SELF']}?op=upload&type=student','student') class='btn btn-success'  title='上傳志願表'>step1.b上傳志願表</a>
	<div id='student'>".$ca->renderStudentTable()."</div></div>";
	$tab->setContent('tab_student',$tab_student,true);
	$tab->addNav('tab_class','開課狀況step2',false);
	$tab_class="<div id='tab_class'><h1>開課狀況</h1><a href='{$_SERVER['PHP_SELF']}?op=download&type=class' class='btn btn-info'  title='下載範本檔'>step2.a下載範本檔</a><a onclick=sjgn('{$_SERVER['PHP_SELF']}?op=upload&type=class','class') class='btn btn-success'  title='上傳課程'>step2.b上傳課程</a>
	<div id='class'>".$ca->renderClassTable()."</div></div>";
	$tab->setContent('tab_class',$tab_class,false);
	$tab->addNav('tab_result','志願結果step3',false);
	$tab_result="<div id='tab_result'><h1>志願結果</h1><a onclick=sjgn('{$_SERVER['PHP_SELF']}?op=renew','result') title='重新產生' class='btn btn-info'>step3.a重新安排</a><a href='{$_SERVER['PHP_SELF']}?op=csv' class='btn btn-success'  title='下載csv'>step3.b下載csv</a><div id='result'>".$ca->renderResult()."</div></div>";
	$tab->setContent('tab_result',$tab_result,false);
	$main="<div class='span12'><h1>志願選填自動配對系統</h1>".$tab->render()."</div>";
break;
}
/*-----------秀出結果區--------------*/
		include(YAOH_ROOT_PATH."head.php");
		echo $main;
		include(YAOH_ROOT_PATH."tail.php");
?>