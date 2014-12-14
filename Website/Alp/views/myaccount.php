<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
$errmsg = $db->ErrorMsg();
?>
<h1>Reset Password</h1>
<?php
$form->ShowFormErrors($errmsg, $OKMsg);
?>
<form method="post" action="" name="form1" id="form1" <?php $form->ShowOnSubmit(); ?>>
	<table border="0" cellpadding="3" cellspacing="0" class="">
<?php
$form->ShowPasswordTextField ('Password', 'Password', '', 3);
$form->ShowPasswordTextField ('Confirm Password', 'Password2', '', 3);
?>
	</table>
	<br>
<?php
$form->ShowSubmitButton('Change Password');
?>
</form>
<?php
$form->FormValidation();

?>