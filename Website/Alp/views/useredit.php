<?php
$db = $this->Database();
$form = $this->Forms();
$usertbl = $this->DBTable();
$c = $this->Cookie();

if ($db->IsUserManager()) {
?>

<h1><?php echo $Verb; ?> User</h1>
<?php
	$form->ShowFormErrors($db->ErrorMsg());
	$typelist = $db->ReadUserTypes();
	$orglist = $db->ReadCompanyList();
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	if ($UserID > 0) {
		$usertbl->Populate();
		$OrgID = $usertbl->orgid;
	} else {
		$usertbl->NewData();
	}
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="">
<?php
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
?>
		<tr>
			<td>
			</td>
			<td>
<?php
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('users');
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