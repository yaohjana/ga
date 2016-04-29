<?php	
ob_start();
if(!isset($_SESSION))session_start();
include_once('config.php');
include_once('jsloader.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>簡易線上志願配對系統</title>
		<?php echo jsloader::useMyjs().jsloader::useBootstrap();
			echo addCss("#header{
				background:rgba(94, 107, 159, 0.882353);
				z-index:99999;
				left:0px;
				color:snow;
				line-height:14px;
				font-size:14px;
				width:100%;
				padding-left:20px;
				}
				#msg{
				border-top: 1px dashed rgb(222, 222, 222);
				}
				#show{
				border-left:3px dashed black;
				padding-left:20px;
				min-height:1000px;
				padding-top:20px;
				}
				.btn20{width:20px;height:20px;}
				.box_style1{
					background-color:white;
					border:3px rgb(222,222,222) dashed;
					border-radius:5px;
					padding-left:20px;
				}
				");
				
		?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
<div id='globalContainer'>
<div id='header'  class="row span12">
<span id='auther'>
	  Designed by Yaoh | DCJH資訊組 | <a title="首頁" class='btn'
	href="<?php echo $_SERVER['PHP_SELF'];?>">回首頁</a>
</span>
</div>

<div id='container' class="row span12">
