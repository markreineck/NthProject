<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$db = $this->Model();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();
$this->LoadCSSFile('login');
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nth-Project</title>
</head>
<body>
<div class="header">
	<p>Project Management Portal</p>
</div>
<div class="formbody">
<?php
$form->ShowFormErrors($errmsg);
?>
	<form id="RegisterForm" name="RegisterForm" method="post" action="" >
		<h1>Register</h1>
		<table border="0" cellpadding="4" cellspacing="5">
<?php
$qstnlist = array(
	"What is your mother's maiden name?",
	"What street did you live on during your 6th birthday?",
	"What is the name of your favorite pet?",
	"What was your high school mascot?"
);
$form->ShowTextField ('Organization Name', 'OrgName', 80, 80, '', 1);
$form->ShowTextField ('First Name', 'FirstName', 20, 20, '', 1);
$form->ShowTextField ('Last Name', 'LastName', 20, 20, '', 1);
$form->ShowEmailField ('Email Address', 'Email', 100, '', true);
$form->ShowPasswordTextField ('Password', 'Password', '', 3);
$form->ShowPasswordTextField ('Confirm Password', 'Password2', '', 3);
$form->ShowListField ('Security Question', 'SecQstn', $qstnlist, 2);
$form->ShowTextField ('Answer', 'SecAns', 80, 80, '', 1);
?>
		<tr>
			<td>
			</td>
			<td>
<?php
$form->ShowSubmitButton();
$form->ShowRedirectButton('/');
?>
		</td>
		</tr>
	</table>
	</form>
</div>
</body>
</html>
