<?php
$db = $this->Database();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();
?>
<h1>log out</h1>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
	<table>
<?php
$form->ShowPasswordTextField ('Password', 'Password', '', 1);
$form->ShowTextAreaField ('Comment', 'Comment', 4, 60);
?>
		<tr><td></td><td>
<?php
$form->ShowSubmitButton('Sign Out');
?>
		</td></tr>
	</table>
</form>
<?php
$form->FormValidation();
?>