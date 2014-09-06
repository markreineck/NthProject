<?php
$db = $this->Model();
$form = $this->Forms();
$usertbl = $this->DBTable();
$c = $this->Cookie();
$errmsg = $db->ErrorMsg();

if ($db->IsUserManager()) {
?>

<h1><?php echo $Verb; ?> User</h1>
<?php
$form->ShowFormErrors($errmsg);
if ($UserID > 0) {
	$data = $db->ReadUser($UserID);
	$OrgID = $data->orgid;
} else {
}
$typelist = $db->ReadUserTypes();
$orglist = $db->ReadCompanyList();
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
$usertbl->Populate();
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="">
<?php
$usertbl->ShowListField ('Organization', $orglist);
$usertbl->ShowListField ('Status', $typelist);
$usertbl->ShowTextField ('FirstName');
$usertbl->ShowTextField ('LastName');
$usertbl->ShowTextField ('Initials');
$usertbl->ShowTextField ('Email');

$data = $db->ReadUserContactEditFields($UserID);
$cnt=0;
foreach ($data as $fld) {
	$maxlen = $fld->maxlen;
	$size = ($maxlen > 70) ? 70 : $maxlen;
	$form->ShowTextField ($fld->name, 'Field'.$cnt, $maxlen, $size, $fld->value, $fld->required);
	$form->ShowHiddenField('FieldID'.$cnt, $fld->fieldid);
	$cnt++;
}
?>
		<tr>
			<td>
			</td>
			<td>
<?php
$form->ShowSubmitButton();
$form->ShowRedirectButton('users');
if ($UserID)
	$form->ShowRedirectButton('userdel?userid='.$UserID, 'Delete');
$form->ShowHiddenField('FieldCnt', $cnt);
?>
		</td>
		</tr>
</table>
</form>
<?php
$form->FormValidation();
}
?>