<?php
$db = $this->Model();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();
?>

<h1>Edit User Info</h1>
<?php
$form->ShowFormErrors($errmsg, $OKMsg);
$data = $db->ReadUser();
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="">
<?php
$form->ShowHiddenField ('UserID', $UserID);
$form->ShowTextField ('First Name', 'FirstName', 20, 20, $data->firstname, 1);
$form->ShowTextField ('Last Name', 'LastName', 20, 20, $data->lastname, 1);
$form->ShowTextField ('Initials', 'Initials', 4, 4, $data->initials, 1);
$form->ShowEmailField ('Email Address', 'Email', 100, $data->email, true);

$data = $db->ReadUserContactEditFields($UserID);
$cnt=0;
foreach ($data as $fld) {
	$maxlen = $fld->maxlen;
	$size = ($maxlen > 70) ? 70 : $maxlen;
	$form->ShowTextField ($fld->name, 'Field'.$cnt, $maxlen, $size, $fld->value, $fld->required);
	$form->ShowHiddenField('FieldID'.$cnt, $fld->fieldid);
	$cnt++;
}
$form->ShowHiddenField('FieldCnt', $cnt);

//$form->ShowNumericField ('Pay Rate', 'PayRate', 0, 200, 5, $data->payrate);
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
<?php
$form->FormValidation();
?>