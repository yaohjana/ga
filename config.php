<?php
/*--------------常數定義區-----------*/
//Path
define("SERVER_WWW_PATH",dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define("YAOH_PATH" , basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR ); 
define("YAOH_ROOT_PATH",SERVER_WWW_PATH . YAOH_PATH);
define("YAOH_STORAGE_PATH", YAOH_ROOT_PATH . "data" . DIRECTORY_SEPARATOR);
define("YAOH_CACHE_PATH", YAOH_STORAGE_PATH . "cache" . DIRECTORY_SEPARATOR);
define("YAOH_IMAGES_PATH", YAOH_ROOT_PATH . "images" . DIRECTORY_SEPARATOR);
define("YAOH_DATA",dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."ga_ex".DIRECTORY_SEPARATOR);
//URL
define("YAOH_PATH_FOR_URL",basename(dirname(__FILE__)) . '/');
define("SERVER_WWW_URL",'http://'.$_SERVER['HTTP_HOST'].'/');
define("YAOH_WWW_URL",SERVER_WWW_URL . YAOH_PATH_FOR_URL);
define("YAOH_STORAGE_URL" ,YAOH_WWW_URL . 'data' . '/');
define("YAOH_CACHE_URL" ,YAOH_STORAGE_URL . 'cache' . '/');
define("YAOH_IMAGES_URL" ,YAOH_WWW_URL . 'images' . '/');
define("GUEST_LOGIN_STATUS_WITH_LOGOUT_BTN","本程式提供簡易線上志願配對使用，免登入即可享用");
?>
