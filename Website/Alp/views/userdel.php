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
/*
$form->ShowListField ('Organization', 'Organization', $orglist, 2, $OrgID);
$form->ShowTextField ('First Name', 'FirstName', 20, 20, $data->firstname, 1);
$form->ShowTextField ('Last Name', 'LastName', 20, 20, $data->lastname, 1);
$form->ShowTextField ('Initials', 'Initials', 4, 4, $data->initials, 1);
$form->ShowEmailField ('Email Address', 'Email', 100, $data->email, true);
$form->ShowListField ('User Type', 'UserType', $typelist, 1, $data->status);
*/
$usertbl->ShowBoundListField ('Organization', $orglist);
$usertbl->ShowBoundListField ('Status', $typelist);
$usertbl->ShowBoundTextField ('FirstName');
$usertbl->ShowBoundTextField ('LastName');
$usertbl->ShowBoundTextField ('Initials');
$usertbl->ShowBoundTextField ('Email');

$data = $db->ReadUserContactEditFields($UserID);
$cnt=0;
foreach ($data as $fld) {
	$maxlen = $fld->maxlen;
	$size = ($maxlen > 70) ? 70 : $maxlen;
	$form->ShowTextField ($fld->name, 'Field'.$cnt, $maxlen, $size, $fld->value, $fld->required);
	$form->ShowHiddenField('FieldID'.$cnt, $fld->fieldid);
	$cnt++;
}

//$form->ShowNumericField ('Pay Rate', 'PayRate', 0, 200, 5, $data->payrate);
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
//$form->ShowHiddenField ('UserID', $UserID);
?>
		</td>
		</tr>
</table>
</form>
<?php
$form->FormValidation();
}
?>