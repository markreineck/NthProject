<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$this->LoadCSS();
$this->LoadCSSFile('login');
$form = $this->Forms();
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nth-Project</title>
</head>
<body>
<?php
$this->LoadView('externalheader');
?>
<div class="formbody">
<div style="height:80px;"></div>
<?php
$form->ShowFormErrors($ErrorMsg);

$args = '';
foreach ($_GET as $name => $val) {
	if ($name == 'n')
		$nextpage = $val;
	else if ($args)
		$args .= "&$name=$val";
	else
		$args .= "?$name=$val";
}
if ($nextpage)
?>
	<form id="LoginForm" name="LoginForm" method="post" action="<?php echo $this->SiteURL(); ?>" class="login" >
<?php
$form->ShowHiddenField('NextPage', $nextpage . $args);
?>
		<h1>Log In</h1>
		<label style="color:#FF3030;"><?php if (isset($msg)) echo $msg; ?></label>
		<label>Email Address</label><br />

		<input  type="text" name="UserName" id="UserName" size="60" />
		<br /><br />
		<label>Password</label><br />
		<input name="Password" type="Password" id="Password" />
		<br />
     	<input type="submit" name="signin" id="signin" value="Sign In" style=" margin:10px 0 0 55px;" class="btn blue" />
	</form>
</div>
</body>
</html>
