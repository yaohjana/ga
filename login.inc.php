<?php
echo <<<login
<div id='login_form'>
	<form name="form1" method="post" action="{$_SERVER['PHP_SELF']}?op=check">
	帳號：<input type="text" name="username"><br />
	密碼：<input type="password" name="userpass"><br />
			  <input type="submit" name="Submit" value="登入">
			  <input type="reset" name="reset" value="清除">
			  <input type="button" name="reg" onclick=jumpto('./member/registry1.php') value="註冊">
	</form>
</div>
login;
?>
