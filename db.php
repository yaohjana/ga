<?php
//分頁部分參考自SaffaWeb ron@cozareg.co.za pagination.class.php 2013.5.13

	class opmysql{
	private $host = 'localhost';			//伺服器地址
	private $name = 'root';					//登錄帳號
	private $pwd = '';				//登錄密碼
	private $dBase = 'yaoh2';			//資料庫名稱
	private $conn = '';						//資料庫鏈結資源
	private $result = '';					//結果集
	private $msg = '';						//返回結果
	private $insert_id = '';				//返回紀錄編號
	private $fields;						//返回欄位
	private $fieldsNum = 0;					//返回欄位數
	private $rowsNum = 0;					//返回結果數
	private $rowsRst = '';					//返回單條記錄的欄位陣列
	private $filesArray = array();			//返回欄位陣列
	private $rowsArray = array();			//返回結果陣列


	//分頁用
	var $range = '';
	var $page = '';
	//產生分頁顯示幾頁的標
	var $limit=10;
	var $pageRange = 5;
	var $pages;
	var $make_pager_ed=false;
	
	//初始化類
	function __construct($host='',$name='',$pwd='',$dBase=''){
		if($host != '')
			$this->host = $host;
		if($name != '')
			$this->name = $name;
		if($pwd != '')
			$this->pwd = $pwd;
		if($dBase != '')
			$this->dBase = $dBase;
			
		
		$this->getRangeTxt();
		$this->init_conn();
	}
	//取得分頁參數
	public function security_clean($var){
        if(is_array($var))
        {
            foreach($var as $k=>$v){
                $var[$k] = filter_var($v, FILTER_SANITIZE_MAGIC_QUOTES);
            }
        }else{
            $var = filter_var($var, FILTER_SANITIZE_MAGIC_QUOTES);
        }
        return $var;
    }	
	private function getRangeTxt()
	{
		if(isset($_GET['l'])&&$_GET['l']!=''){
    		$this->limit = $this->security_clean($_GET['l']);
    	}

		if(isset($_GET['d'])&&$_GET['d']!='')
			$dir = $this->security_clean($_GET['d']);
		else
			$dir = 'ASC';

		if(isset($_GET['o'])&&$_GET['o']!='')$order_txt = " ORDER BY ".$this->security_clean($_GET['o'])." ".$dir;
		
    	if(isset($_GET['p'])&& $_GET['p']!='')
    	{
    		$page = (int)$this->security_clean($_GET['p']);
    		$offset = ($page-1)*$this->limit;
    	}
    	else
    	{
    		$page = 1;
    		$offset = 0;
    	}
    	$this->page = $page;    	

    	$rec_limit =($this->limit<>'')? " LIMIT " .$offset.",".$this->limit:"";

    	$this->range = ((isset($order_txt) and $order_txt<>'')?$order_txt:"")." ".(($rec_limit<>'')?$rec_limit:"");
	}

	//產生頁數
	public function make_pager($oa_i=array('first_link','prev_link','main_link','next_link','last_link'))
	{

	      $first_link_txt="<img width=10 height=10 src='./images/first.png'>";              
	      $prev_link_txt="<img width=10 height=10 src='./images/prev.png'>";
              $next_link_txt="<img width=10 height=10 src='./images/next.png'>";
              $last_link_txt="<img width=10 height=10 src='./images/last.png'>";

		//解析get字串成陣列
		$uriArray = explode('&',$_SERVER['QUERY_STRING']);
		$newArray =array();
		foreach($uriArray as $a_array){
			$b_array=explode('=',$a_array);
			$newArray[$b_array[0]]=$b_array[1];		
		}
		//var_dump($newArray);
		//產生頁碼
		$nextPage = $this->page+1;
		if ($nextPage > $this->pages) {
		$nextPage = 1;
		}		
		$prevPage = $this->page-1;
		if ($prevPage < 1) {
		$prevPage = $this->pages;
		} 
		$oa=array();
		$link='';
		//讀取分頁資料
		if(isset($_GET['o']))$newArray['o']=$_GET['o'];
		if(isset($_GET['l']))$newArray['l']=$_GET['l'];
		if(isset($_GET['d']))$newArray['d']=$_GET['d'];
		$get_string='';
		//將陣列組裝回get字串
		//$get_string='?';
		foreach($newArray as $a=>$b){
			//o l d p 不裝載
			if($a=='o' or $a=='l' or $a=='d' or $a=='p')continue;
			$get_string.=$a."=".$b."&";
		}
		$get_string=substr($get_string,0,-1);
		
		
		$uri = $_SERVER['PHP_SELF'];

				$oa['first_link'] = "<li><a href='$uri?p=1&{$get_string}'>{$first_link_txt}</a></li>";
				$oa['prev_link'] = "<li><a href='$uri?p={$prevPage}&{$get_string}'>{$prev_link_txt}</a></li>";
				$oa['main_link']="";
			if($this->page < $this->pageRange){
				$start_page=1;
			}else{
				$start_page=$this->page-$this->pageRange+1;
			}
			for ($i = $start_page ; $i < (($start_page + $this->pageRange) + 1); $i++) {
											
				if($i>0 && $i<=$this->pages) {
					
					if($i==$this->page)
					{
						$oa['main_link'] .= "<li class='active'><a style='background:#f5f5f5' href='#'>$i</a></li>";
					}else{								
						$oa['main_link'] .= "<li><a href='$uri?p={$i}&{$get_string}'>$i</a></li>";				
					}
				}
			}
			$oa['next_link']= "<li><a href='$uri?p={$nextPage}&{$get_string}'>{$next_link_txt}</a></li>";
			$oa['last_link'] = "<li><a href='$uri?p={$this->pages}&{$get_string}'>{$last_link_txt}</a></li>";
	$link.="<div style='padding:0px;margin:0px;margin-bottom:-6px' class='pagination pagination-centered'><ul>";
		foreach($oa_i as $i){
				$link.=$oa[$i];
		}
	$link.="</ul></div>";
		return $link;
	}

	
	
	//鏈結資料庫
	function init_conn(){
		$this->conn=@mysql_connect($this->host,$this->name,$this->pwd)or die("connection error");
		@mysql_select_db($this->dBase,$this->conn);
		//mysql_query("set names utf8");
		mysql_set_charset('utf8', $this->conn);
	}

	//查詢結果
	function query($sql,$make_pages_flag=null){

		if($this->conn == ''){
			$this->init_conn();
		}
		//萃取分頁方法
		if(isset($make_pages_flag) and $make_pages_flag<>''){
			$this->pages=ceil($this->countRows($sql)/$this->limit);
			$pagesql=" SELECT * FROM ( {$sql} ) as `a` ".$this->range;
		}else{
			$pagesql=$sql;
		}
					$this->result=@mysql_query($pagesql,$this->conn);
		return $this->result;
	}
	//取得欄位數 
	function getFieldsNum($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		$this->fieldsNum = @mysql_num_fields($this->result);
	}
	//取得查詢結果數
	function countRows($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		if(mysql_errno() == 0){
			return @mysql_num_rows($this->result);
		}else{
			return '';
		}	
	}
	//取得記錄陣列（單條記錄）
	function getRow($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		if(mysql_error() == 0){
			$this->rowsRst = @mysql_fetch_array($this->result,MYSQL_ASSOC);
			return $this->rowsRst;
		}else{
			return '';
		}
	}
	//取得單一數值
		function getOne($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		if(mysql_error() == 0){
			$this->rowsRst = mysql_fetch_row($this->result);
			return $this->rowsRst[0];
		}else{
			return '';
		}
	}
	//取得一個一維陣列，只取第一個欄位
	function getF1($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		if(mysql_error() == 0){
			while($row = mysql_fetch_array($this->result,MYSQL_NUM)) {
				$this->rowsArray[] = $row[0];
			}
			return $this->rowsArray;
		}else{
			return '';
		}
	}
	//取得一個一維陣列，只取$fields欄位的值，可以是名字或者數字索引
	function getFn($sql,$field_name){
		$this->query($sql);
		if(mysql_errno() == 0){
			if(mysql_num_rows($this->result) > 0){
				$tmpfld = @mysql_fetch_row($this->result);
				$this->fields = $tmpfld[$field_name];
				
			}
			return $this->fields;
		}else{
			return '';
		}
	}
	//取得記錄陣列（MYSQL_NUM）
	function getRowsN($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		if(mysql_errno() == 0){
			while($row = mysql_fetch_array($this->result,MYSQL_NUM)) {
				$this->rowsArray[] = $row;
			}
			return $this->rowsArray;
		}else{
			return '';
		}
	}
	
	//取得記錄陣列（MYSQL_ASSOC）
	function getRowsA($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);
		if(mysql_errno() == 0){
			while($row = mysql_fetch_array($this->result,MYSQL_ASSOC)) {
				$this->rowsArray[] = $row;
			}
			return $this->rowsArray;
		}else{
			return '';
		}
	}
	//取得記錄陣列（多條記錄）
	function getRows($sql,$make_pages_flag=null){
		$this->query($sql,$make_pages_flag);

		if(mysql_errno() == 0){
			while($row = mysql_fetch_array($this->result,MYSQL_BOTH)) {
				$this->rowsArray[] = $row;
			}
			return $this->rowsArray;
		}else{
			return '';
		}
	}

	//快速輸出表格，只要輸入sql及標題陣列就可以了喔
	//2012/10/18新增功能
	function getTable($sql,$td_rows=array()){
	if($this->countRows($sql)>0){
		$tablestr="共有".$this->countRows($sql)."筆資料";
		$tablestr.="<table class='table'>";
		if(is_array($td_rows)){
			$tablestr.= "<tr>";
			if(count($td_rows)>0){
			foreach($td_rows as $td_row){
				$tablestr.="<th>";
				$tablestr.=$td_row;
				$tablestr.="</th>";
				
			}
			}
			$tablestr.= "</tr>";
		}
		$rows=$this->getRowsA($sql);
		foreach($rows as $row){
		$tablestr.= "<tr>";
		foreach($row as $data){
			$tablestr.= "<td>";
			$tablestr.= $data;

			$tablestr.= "</td>";
		}
		$tablestr.= "</tr>";

	}
			$tablestr.="</table>";
			return $tablestr;
		}else{
			return '尚無資料';
		}
	}
	//更新、刪除、添加記錄數
	function uidRst($sql){
		if($this->conn == ''){
			$this->init_conn();
		}
		@mysql_query($sql);
		$this->rowsNum = @mysql_affected_rows();
		if(mysql_errno() == 0){
			return $this->rowsNum;
		}else{
			return '';
		}
	}
		//取得上一個更改的id
	function getInsertId(){
		if($this->conn == ''){
			$this->init_conn();
		}
		//@mysql_query($sql);
		$this->insert_id = @mysql_insert_id();
		if(mysql_errno() == 0){
			return $this->insert_id;
		}else{
			return '';
		}
	}

	
	//錯誤資訊
	function msg_error(){
		if(mysql_errno() != 0) {
			$this->msg = mysql_error();
		}
		return $this->msg;
	}
	//釋放結果集
	function close(){
		@mysql_free_result($this->result);
		$this->msg = '';
		$this->fieldsNum = 0;
		$this->rowsNum = 0;
		$this->filesArray = '';
		$this->rowsArray = '';
	}
	//關閉資料庫
	function close_conn(){
		$this->close();
		@mysql_close($this->conn);
		$this->conn = '';
	}
}
$conne = new opmysql();
 ?>
