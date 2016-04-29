<?php
//  ------------------------------------------------------------------------ //
// 本模組由 Yaoh 製作
// 製作日期：2013-10-27
// 修改日期： 201311/5
// jsloader.inc.php
// 功能：前台引用管理
//  ------------------------------------------------------------------------ //
define("JSLOADER_PATH",basename(dirname(__FILE__)) . '/');
define("JSLOADER_SERVER_WWW_URL",'http://'.$_SERVER['HTTP_HOST'].'/');
define("JSLOADER_URL",JSLOADER_SERVER_WWW_URL . JSLOADER_PATH);
//define("JSLOADER_URL",JSLOADER_SERVER_WWW_URL);

define("JS_FOLDER",JSLOADER_URL. "js/");
define("CSS_FOLDER",JSLOADER_URL. "css/");


class jsloader{
	private static $useMyjs=false;
	private static $useJquery=false;
	private static $useJqueryUI=false;
	private static $useBootstrap=false;
	private static $useJqueryTools=false;
	private static $useColorPicker=false;
	private static function addJava($content,$src=null){
        return "<script type='text/javascript'".(($src<>null)?"src=".$src:'').">".$content."</script>";
}
	//引用自訂的js
	public static function useMyjs(){
		$js_folder= JS_FOLDER;
		if(self::$useMyjs==false){
			self::$useMyjs = true;
			return "<script type='text/javascript' src='{$js_folder}myjs.js'></script>";
		}
	}
	//引用jquery
	public static function useJquery(){
		$js_folder= JS_FOLDER;
		if(self::$useJquery==false){
		self::$useJquery=true;
		return "<script type='text/javascript' src='{$js_folder}jquery.min.js'></script>";
		}
	}
//引用JqueryTools
	public static function useJqueryTools(){
		$js_folder= JS_FOLDER;
		$return='';
		if(self::$useJqueryTools==false){
		self::$useJqueryTools=true;
		//載入jquery
		$return.=self::useJquery();
		//js檢查
		$jquery=<<<useJqueryTools
         if(typeof jQuery.tools == "undefined"){ 
         var jst = document.createElement("script"); 
         jst.type = "text/javascript";
         jst.src = "{$js_folder}jquery.tools.min.js"; 
         document.getElementsByTagName("head")[0].appendChild(jst); 
        }
useJqueryTools;
		$return.=self::addJava($jquery);
		return $return;
		}
	}
//引用JqueryUI
	public static function useJqueryUI(){
		$js_folder= JS_FOLDER;
		$css_folder= CSS_FOLDER;
		$return='';
		if(self::$useJqueryUI==false){
		self::$useJqueryUI=true;
		//載入jquery
		$return.=self::useJquery();
		//js檢查
		$jqueryui=<<<useJqueryUI
         if(typeof jQuery.ui == "undefined"){ 
         var jsu = document.createElement("script"); 
         jsu.type = "text/javascript";
         jsu.src = "{$js_folder}jquery-ui.min.js"; 
         document.getElementsByTagName("head")[0].appendChild(jsu); 
        }
useJqueryUI;
		$return.=self::addJava($jqueryui);
		return "<link rel='stylesheet' href='{$css_folder}jquery-ui.min.css' />".$return;
		}
	}
//引用Bootstrap
	public static function useBootstrap(){
		$js_folder= JS_FOLDER;
		$css_folder= CSS_FOLDER;
		$return='';
		if(self::$useBootstrap==false){
			self::$useBootstrap=true;
			//載入jquery
			$return.=self::useJquery();
			//js檢查
$bootstrap=<<<useBootstrap
		 if(typeof bootstrap == "undefined"){ 
			var jsb = document.createElement("script");
			jsb.type = "text/javascript";
			jsb.src = "{$js_folder}bootstrap.min.js"; 
			document.getElementsByTagName("head")[0].appendChild(jsb); 
        }
useBootstrap;
		$return.="<link href='{$css_folder}bootstrap.min.css' rel='stylesheet' media='screen'>".
                                self::addJava($bootstrap);
		return $return;
		}
	}
//引用ColorPicker
	public static function useColorPicker(){
		$js_folder= JS_FOLDER;
		$css_folder= CSS_FOLDER;
		$return='';
		if(self::$useColorPicker==false){
			self::$useColorPicker=true;
			//載入jquery
			$return.=self::useJquery();
			//js檢查
$colorpicker=<<<useColorPicker
		 if(typeof colorpicker == "undefined"){ 
			var jsc = document.createElement("script");
			jsc.type = "text/javascript";
			jsc.src = "{$js_folder}spectrum.js"; 
			document.getElementsByTagName("head")[0].appendChild(jsc); 
        }
useColorPicker;
		return "<link href='{$css_folder}spectrum.css' rel='stylesheet' media='screen'>".
				self::addJava($colorpicker);
		}
	}
}
?>
