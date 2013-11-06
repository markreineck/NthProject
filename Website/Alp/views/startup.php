<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$form = $this->Forms();
$this->LoadCSS();
$this->LoadCSSFile('login');
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nth-Project</title>
</head>
<body>
<?php
$this->LoadView('externalheader');
?>
<div class="formbody">
<h1>Nth Project Startup</h1>
<p>
Please enter you information for your initial administrsative account. 
</p>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="">
<?php
$form->ShowTextField ('Organization', 'Organization', 80, 80, '', 1);
$form->ShowTextField ('First Name', 'FirstName', 20, 20, '', 1);
$form->ShowTextField ('Last Name', 'LastName', 20, 20, '', 1);
$form->ShowTextField ('Initials', 'Initials', 4, 4, '', 1);
$form->ShowEmailField ('Email Address', 'Email', 100, '', true);
$form->ShowPasswordTextField ('Password', 'Password', '', 3);
$form->ShowPasswordTextField ('Confirm Password', 'Password2', '', 3);
?>
		<tr>
			<td>
			</td>
			<td>
<?php
$form->ShowSubmitButton();
?>
		</td>
		</tr>
</table>
</form>
</div>
<?php
$form->FormValidation();
//}
?>
</body></html>
