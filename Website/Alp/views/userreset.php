<?php
$db = $this->Database();
$form = $this->Forms();
$c = $this->Cookie();
$errmsg = $db->ErrorMsg();

if ($db->IsUserManager() || $UserID == $db->GetUserID()) {
?>

<h1>Reset User Password</h1>
<div style="color:#0063C6; font-weight:600;"> Reset User Account for
<?php
echo $this->Database()->GetUserName($UserID);
?>
</div>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" action="" name="form1" id="form1" <?php $form->ShowOnSubmit(); ?>>
	<table border="0" cellpadding="3" cellspacing="0" class="">
<?php
$form->ShowHiddenField('UserID', $UserID);
$form->ShowPasswordTextField ('Password', 'Password', '', 3);
$form->ShowPasswordTextField ('Confirm Password', 'Password2', '', 3);
?>
	</table>
<?php
$form->ShowSubmitButton();
$form->ShowRedirectButton('users');
?>
	<input type="submit" name="deactivate" value="Deactivate" class="ButtonClass" onClick="return confirm('Are you sure you want to deactivate this User?');" />
</form>
<?php
	$form->FormValidation();
}
?>